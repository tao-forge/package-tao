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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               2013-2020 (update and modification) Open Assessment Technologies SA;
 *
 */

use oat\generis\model\data\Ontology;
use oat\generis\model\kernel\persistence\smoothsql\search\ComplexSearchService;
use oat\oatbox\service\exception\InvalidService;
use oat\oatbox\service\exception\InvalidServiceManagerException;
use core_kernel_persistence_smoothsql_SmoothModel as SmoothModel;
use common_cache_Cache as CommonCache;

/**
 * Custom extension installer for generis
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package generis
 *
 */
class common_ext_GenerisInstaller extends common_ext_ExtensionInstaller
{

    /**
     * Setup the ontology configuration
     *
     * @access public
     * @throws common_Exception
     * @throws common_ext_ExtensionException
     * @throws common_ext_InstallationException
     * @throws common_ext_ManifestNotFoundException
     */
    public function install()
    {
        if ($this->extension->getId() != 'generis') {
            throw new common_ext_ExtensionException('Tried to install "' . $this->extension->getId() . '" extension using the GenerisInstaller');
        }
 
        $this->installLoadDefaultConfig();
        $this->configureOntology();
        $this->installOntology();
        $this->installRegisterExt();
        
        common_cache_FileCache::singleton()->purge();
        
        $this->log('d', 'Installing custom script for extension ' . $this->extension->getId());
        $this->installCustomScript();
    }

    /**
     * @throws common_Exception
     */
    protected function configureOntology()
    {
        if (!$this->getServiceManager()->has(Ontology::SERVICE_ID)) {
            $smoothModel = new \core_kernel_persistence_smoothsql_SmoothModel([
                SmoothModel::OPTION_PERSISTENCE => 'default',
                SmoothModel::OPTION_READABLE_MODELS =>
                    [SmoothModel::DEFAULT_WRITABLE_MODEL, SmoothModel::DEFAULT_READABLE_MODEL],
                SmoothModel::OPTION_WRITEABLE_MODELS =>
                    [SmoothModel::DEFAULT_WRITABLE_MODEL],
                SmoothModel::OPTION_NEW_TRIPLE_MODEL =>
                    SmoothModel::DEFAULT_WRITABLE_MODEL,
                SmoothModel::OPTION_SEARCH_SERVICE => ComplexSearchService::SERVICE_ID,
                SmoothModel::OPTION_CACHE_SERVICE => CommonCache::SERVICE_ID
            ]);
            $this->getServiceManager()->register(Ontology::SERVICE_ID, $smoothModel);
        }
    }
}
