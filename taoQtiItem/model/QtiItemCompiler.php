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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

namespace oat\taoQtiItem\model;

use oat\taoQtiItem\model\QtiItemCompiler;
use \taoItems_models_classes_ItemCompiler;
use \core_kernel_classes_Resource;
use \tao_models_classes_service_StorageDirectory;
use \tao_models_classes_service_ServiceCall;
use \tao_models_classes_service_ConstantParameter;
use \taoItems_models_classes_ItemsService;
use \taoItems_helpers_Deployment;
use \common_report_Report;
use \common_ext_ExtensionsManager;
use \common_Logger;
use oat\taoQtiItem\model\qti\Service;
use \tao_helpers_File;
use qtism\data\storage\xml\XmlAssessmentItemDocument;

/**
 * The QTI Item Compiler
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoItems

 */
class QtiItemCompiler extends taoItems_models_classes_ItemCompiler
{

    /**
     * Compile qti item
     * 
     * @todo move this
     * @param core_kernel_file_File $destinationDirectory
     * @throws taoItems_models_classes_CompilationFailedException
     * @return tao_models_classes_service_ServiceCall
     */
    public function compile(){

        $destinationDirectory = $this->spawnPublicDirectory();
        $privateDirectory = $this->spawnPrivateDirectory();
        $item = $this->getResource();

        $report = new common_report_Report(common_report_Report::TYPE_SUCCESS, __('Published %s', $item->getLabel()));
        if(!taoItems_models_classes_ItemsService::singleton()->isItemModelDefined($item)){
            return $this->fail(__('Item \'%s\' has no model', $item->getLabel()));
        }

        $langs = $this->getContentUsedLanguages();
        foreach($langs as $compilationLanguage){
            $compiledFolder = $this->getLanguageCompilationPath($destinationDirectory, $compilationLanguage);
            if(!is_dir($compiledFolder)){
                if(!@mkdir($compiledFolder)){
                    common_Logger::e('Could not create directory '.$compiledFolder, 'COMPILER');
                    return $this->fail(__('Could not create language specific directory for item \'%s\'', $item->getLabel()));
                }
            }

            $privateFolder = $this->getLanguageCompilationPath($privateDirectory, $compilationLanguage);
            if(!is_dir($compiledFolder)){
                if(!@mkdir($compiledFolder)){
                    common_Logger::e('Could not create directory '.$compiledFolder, 'COMPILER');
                    return $this->fail(__('Could not create language specific directory for item \'%s\'', $item->getLabel()));
                }
            }


            $langReport = $this->deployItem($item, $compilationLanguage, $compiledFolder, $privateFolder);
            $report->add($langReport);
            if($langReport->getType() == common_report_Report::TYPE_ERROR){
                $report->setType(common_report_Report::TYPE_ERROR);
                break;
            }
        }
        if($report->getType() == common_report_Report::TYPE_SUCCESS){
            $report->setData($this->createService($item, $destinationDirectory, $privateDirectory));
        }else{
            $report->setMessage(__('Failed to publish %s', $item->getLabel()));
        }
        return $report;
    }

    protected function createService(core_kernel_classes_Resource $item, tao_models_classes_service_StorageDirectory $destinationDirectory, tao_models_classes_service_StorageDirectory $privateDirectory){

        $service = new tao_models_classes_service_ServiceCall(new core_kernel_classes_Resource(INSTANCE_QTI_SERVICE_ITEMRUNNER));
        $service->addInParameter(new tao_models_classes_service_ConstantParameter(
                new core_kernel_classes_Resource(INSTANCE_FORMALPARAM_ITEMPATH), $destinationDirectory->getId()
        ));
        $service->addInParameter(new tao_models_classes_service_ConstantParameter(
                new core_kernel_classes_Resource(INSTANCE_FORMALPARAM_ITEMDATAPATH), $privateDirectory->getId()
        ));
        $service->addInParameter(new tao_models_classes_service_ConstantParameter(
                new core_kernel_classes_Resource(INSTANCE_FORMALPARAM_ITEMURI), $item
        ));

        return $service;
    }

    /**
     * (non-PHPdoc)
     * @see taoItems_models_classes_ItemCompiler::deployItem()
     */
    protected function deployItem(core_kernel_classes_Resource $item, $language, $destination, $privateFolder){

//        start debugging here
//        common_Logger::d('destination original '.$destination.' '.$privateFolder);

        $itemService = taoItems_models_classes_ItemsService::singleton();
        $qtiService = Service::singleton();

        //copy all item folder (but the qti.xml)
        $itemFolder = $itemService->getItemFolder($item, $language);
        taoItems_helpers_Deployment::copyResources($itemFolder, $destination, array('qti.xml'));

        //copy item.xml file to private directory
        tao_helpers_File::copy($itemFolder.'qti.xml', $privateFolder.'qti.xml', false);

        //store variable qti elements data into the private directory
        $qtiItem = $qtiService->getDataItemByRdfItem($item, $language);
        $variableElements = $qtiService->getVariableElements($qtiItem);
        $serializedVariableElements = json_encode($variableElements);
        file_put_contents($privateFolder.'variableElements.json', $serializedVariableElements);

        // render item
        $xhtml = $itemService->render($item, $language);

        // retrieve external resources
        $report = taoItems_helpers_Deployment::retrieveExternalResources($xhtml, $destination);//@todo (optional) : exclude 'require.js' from copying
        if($report->getType() == common_report_Report::TYPE_SUCCESS){
            $xhtml = $report->getData();
        }else{
            return $report;
        }

        //note : no need to manually copy qti or other third party lib files, all dependencies are managed by requirejs
        // write index.html
        file_put_contents($destination.'index.html', $xhtml);

        //copy the event.xml if not present
        $eventsXmlFile = $destination.'events.xml';
        if(!file_exists($eventsXmlFile)){
            $eventXml = file_get_contents(ROOT_PATH.'/taoItems/data/events_ref.xml');
            if(is_string($eventXml) && !empty($eventXml)){
                $eventXml = str_replace('{ITEM_URI}', $item->getUri(), $eventXml);
                @file_put_contents($eventsXmlFile, $eventXml);
            }
        }

        // --- Include QTI specific compilation stuff here.
        // At this moment, we should have compiled raw Items. We now have
        // to put the qti.xml file for each language in the compilation folder.

        return new common_report_Report(
                common_report_Report::TYPE_SUCCESS, __('Successfully compiled "%s"', $language)
        );
    }

}