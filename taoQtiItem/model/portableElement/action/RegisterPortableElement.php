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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA;
 *
 * */

namespace oat\taoQtiItem\model\portableElement\action;

use common_ext_action_InstallAction;
use oat\oatbox\service\ServiceManager;
use oat\taoQtiItem\model\portableElement\exception\PortableElementVersionIncompatibilityException;
use oat\taoQtiItem\model\portableElement\PortableElementService;
use \common_report_Report as Report;

/**
 * Class RegisterPortableElement
 * Abstract invokable action to register a single portable element
 * The inherited class only need to implement getSourceDirectory()
 * 
 * @package oat\taoQtiItem\model\portableElement\action
 */
abstract class RegisterPortableElement extends common_ext_action_InstallAction
{
    public function __invoke($params)
    {

        $service = new PortableElementService();
        $service->setServiceLocator(ServiceManager::getServiceManager());

        $sourceDirectory = $this->getSourceDirectory();

        if (empty($sourceDirectory)) {
            return Report::createFailure('The file does is empty.');
        }

        if (!is_readable($sourceDirectory)) {
            return Report::createFailure('The file does not exists or is not readable: ' .$sourceDirectory);
        }

        try {
            $model = $service->getValidPortableElementFromDirectorySource($sourceDirectory);
            if(!empty($params)){
                $version = $params[0];
                if($version !== $model->getVersion()){
                    return Report::createFailure('The given version "'.$version.'" does not correspond to the version in manifest "'.$model->getVersion().'"' );
                }
            }
            $service->registerFromDirectorySource($sourceDirectory);
        } catch (PortableElementVersionIncompatibilityException $e) {
            return Report::createFailure('incompatible version: ' . $e->getMessage());
        }

        return Report::createSuccess('registered portable element "'.$model->getTypeIdentifier(). '" in version "'.$model->getVersion().'""');
    }

    abstract protected function getSourceDirectory();
}