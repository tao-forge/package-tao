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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2002-2008 (update and modification) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
namespace oat\taoTestTaker\actions;

use common_report_Report;
use core_kernel_classes_Resource;
use oat\generis\model\GenerisRdf;
use oat\taoTestTaker\models\CsvImporter;
use oat\taoTestTaker\models\events\TestTakerImportedEvent;
use oat\taoTestTaker\models\TestTakerSavePasswordInMemory;
use tao_helpers_form_FormFactory;

/**
 * Extends the common Import class to exchange the generic
 * CsvImporter with a subject specific one
 * 
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package c
 * 
 */
class Import extends \tao_actions_Import 
{
    
    /**
     * (non-PHPdoc)
     * @see tao_actions_Import::getAvailableImportHandlers()
     */
	public function getAvailableImportHandlers() 
	{
		$returnValue = parent::getAvailableImportHandlers();
		
		foreach (array_keys($returnValue) as $key) {
		    if ($returnValue[$key] instanceof \tao_models_classes_import_CsvImporter) {
		        $importer =  new CsvImporter();
				$importer->setValidators($this->getValidators());
				$returnValue[$key] = $importer;
		    }
		}
        		
		return $returnValue;
	}

	protected function getValidators(){
		return array(
			GenerisRdf::PROPERTY_USER_LOGIN => array(tao_helpers_form_FormFactory::getValidator('Unique')),
		);
	}

    /**
     * @param common_report_Report $report
     * @throws \core_kernel_persistence_Exception
     */
    protected function onAfterImport(common_report_Report $report)
    {
        /** @var common_report_Report $success */
        foreach ($report->getSuccesses() as $success) {
            $resource = $success->getData();
            if ($resource instanceof core_kernel_classes_Resource) {
                $this->getEventManager()->trigger(new TestTakerImportedEvent($resource->getUri(), $this->getProperties($resource)));
            }
        }
    }

    /**
     * @param core_kernel_classes_Resource $resource
     * @return array
     * @throws \core_kernel_persistence_Exception
     * @throws \common_ext_ExtensionException
     */
    protected function getProperties($resource)
    {
        /** @var \common_ext_ExtensionsManager $extManager */
        $extManager = $this->getServiceLocator()->get(\common_ext_ExtensionsManager::SERVICE_ID);
        $taoTestTaker = $extManager->getExtensionById('taoTestTaker');
        $config = $taoTestTaker->getConfig('csvImporterCallbacks');

        if ((bool)$config['use_properties_for_event']){
            return [
                'plainPassword' => TestTakerSavePasswordInMemory::getPassword(),
                GenerisRdf::PROPERTY_USER_PASSWORD => $resource->getOnePropertyValue(
                    new \core_kernel_classes_Property(GenerisRdf::PROPERTY_USER_PASSWORD)
                )->literal
            ];
        }

        return [];
    }
}
