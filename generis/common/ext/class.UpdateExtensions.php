<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

use oat\oatbox\service\ServiceManager;
use oat\oatbox\action\Action;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Psr\Log\LoggerAwareInterface;
use oat\oatbox\log\LoggerAwareTrait;

/**
 * Run the extension updater
 *
 * @access public
 * @package generis
 * @see @license  GNU General Public (GPL) Version 2 http://www.opensource.org/licenses/gpl-2.0.php
 */
class common_ext_UpdateExtensions implements Action, ServiceLocatorAwareInterface, LoggerAwareInterface
{
    use ServiceLocatorAwareTrait;
    use LoggerAwareTrait;
    
    /**
     * (non-PHPdoc)
     * @see \oat\oatbox\action\Action::__invoke()
     */
    public function __invoke($params)
    {
        $report = new common_report_Report(common_report_Report::TYPE_INFO, 'Running extension update');
        $extManager = $this->getExtensionManager();
        $this->installMissingExtensions($report);
        $sorted = \helpers_ExtensionHelper::sortByDependencies($extManager->getInstalledExtensions());

        foreach ($sorted as $ext) {
            try {
                $report->add($this->updateExtension($ext));
            } catch (common_ext_MissingExtensionException $ex) {
                $report->add(new common_report_Report(common_report_Report::TYPE_ERROR, $ex->getMessage()));
                break;
            } catch (common_ext_OutdatedVersionException $ex) {
                $report->add(new common_report_Report(common_report_Report::TYPE_ERROR, $ex->getMessage()));
                break;
            } catch (Exception $e) {
                $this->logError('Exception during update of ' . $ext->getId() . ': ' . get_class($e) . ' "' . $e->getMessage() . '"');
                $report->setType(common_report_Report::TYPE_ERROR);
                $report->setTitle('Update failed');
                $report->add(new common_report_Report(common_report_Report::TYPE_ERROR, 'Exception during update of ' . $ext->getId() . '.'));
                break;
            }
        }
        $this->logInfo(helpers_Report::renderToCommandline($report, false));
        return $report;
    }

    /**
     * Update a specific extension
     *
     * @param common_ext_Extension $ext
     * @return common_report_Report
     * @throws common_exception_Error
     * @throws common_ext_ManifestNotFoundException
     * @throws common_ext_MissingExtensionException
     * @throws common_ext_OutdatedVersionException
     */
    protected function updateExtension(common_ext_Extension $ext)
    {
        helpers_ExtensionHelper::checkRequiredExtensions($ext);
        $installed = $this->getExtensionManager()->getInstalledVersion($ext->getId());
        $codeVersion = $ext->getVersion();
        if ($installed !== $codeVersion) {
            $report = new common_report_Report(common_report_Report::TYPE_INFO, $ext->getName() . ' requires update from ' . $installed . ' to ' . $codeVersion);
            $updaterClass = $ext->getManifest()->getUpdateHandler();
            if (is_null($updaterClass)) {
                $report = new common_report_Report(common_report_Report::TYPE_WARNING, 'No Updater found for  ' . $ext->getName());
            } elseif (!class_exists($updaterClass)) {
                $report = new common_report_Report(common_report_Report::TYPE_ERROR, 'Updater ' . $updaterClass . ' not found');
            } else {
                $updater = new $updaterClass($ext);
                $returnedVersion = $updater->update($installed);
                $currentVersion = $this->getExtensionManager()->getInstalledVersion($ext->getId());
                
                if (!is_null($returnedVersion) && $returnedVersion != $currentVersion) {
                    $this->getExtensionManager()->updateVersion($ext, $returnedVersion);
                    $report->add(new common_report_Report(common_report_Report::TYPE_WARNING, 'Manually saved extension version'));
                    $currentVersion = $returnedVersion;
                }

                if ($currentVersion == $codeVersion) {
                    $versionReport = new common_report_Report(common_report_Report::TYPE_SUCCESS, 'Successfully updated ' . $ext->getName() . ' to ' . $currentVersion);
                } else {
                    $versionReport = new common_report_Report(common_report_Report::TYPE_WARNING, 'Update of ' . $ext->getName() . ' exited with version ' . $currentVersion);
                }

                foreach ($updater->getReports() as $updaterReport) {
                    $versionReport->add($updaterReport);
                }

                $report->add($versionReport);

                common_cache_FileCache::singleton()->purge();
            }
        } else {
            $report = new common_report_Report(common_report_Report::TYPE_INFO, $ext->getName() . ' already up to date');
        }
        return $report;
    }
    
    protected function getMissingExtensions()
    {
        $missingId = \helpers_ExtensionHelper::getMissingExtensionIds($this->getExtensionManager()->getInstalledExtensions());
        
        $missingExt = [];
        foreach ($missingId as $extId) {
            $ext = $this->getExtensionManager()->getExtensionById($extId);
            $missingExt[$extId] = $ext;
        }
        return $missingExt;
    }

    /**
     * @param common_report_Report $report
     * @throws common_exception_Error
     * @throws common_ext_AlreadyInstalledException
     * @throws common_ext_ForbiddenActionException
     */
    private function installMissingExtensions(common_report_Report $report)
    {
        $merged = array_merge($this->getMissingExtensions(), $this->getExtensionManager()->getInstalledExtensions());
        $sorted = \helpers_ExtensionHelper::sortByDependencies($merged);
        foreach ($sorted as $ext) {
            if (!$this->getExtensionManager()->isInstalled($ext->getId())) {
                $installer = new \tao_install_ExtensionInstaller($ext);
                $installer->install();
                $report->add(new common_report_Report(common_report_Report::TYPE_SUCCESS, 'Installed ' . $ext->getName()));
            }
        }
    }

    /**
     * @return common_ext_ExtensionsManager
     */
    private function getExtensionManager()
    {
        return $this->getServiceLocator()->get(common_ext_ExtensionsManager::SERVICE_ID);
    }

}
