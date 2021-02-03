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

namespace oat\taoItems\model;

use common_Exception;
use common_report_Report;
use core_kernel_classes_Property;
use core_kernel_classes_Resource;
use oat\taoItems\helpers\Deployment;
use tao_models_classes_Compiler;
use tao_models_classes_service_ConstantParameter;
use tao_models_classes_service_ServiceCall;
use tao_models_classes_service_StorageDirectory;

/**
 * Generic item compiler.
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @package taoItems

 */
abstract class ItemCompiler extends tao_models_classes_Compiler
{
    /**
     * Get the languages in use for the item content.
     *
     * @return array An array of language tags (string).
     */
    protected function getContentUsedLanguages()
    {
        return $this->getResource()->getUsedLanguages(new core_kernel_classes_Property(ItemsService::PROPERTY_ITEM_CONTENT));
    }
    
    /**
     * deploys the item into the given absolute directory
     *
     * @param core_kernel_classes_Resource $item
     * @param string $languageCode
     * @param string $compiledDirectory
     * @return common_report_Report
     */
    protected function deployItem(core_kernel_classes_Resource $item, $languageCode, $compiledDirectory)
    {
        $itemService = ItemsService::singleton();
            
        // copy local files
        $sourceDir = $itemService->getItemDirectory($item, $languageCode);
        $success = Deployment::copyResources($sourceDir->getPrefix(), $compiledDirectory, ['index.html']);
        if (!$success) {
            return $this->fail(__('Unable to copy resources for language %s', $languageCode));
        }
        
        // render item
        
        $xhtml = $itemService->render($item, $languageCode);
            
        // retrieve external resources
        $subReport = Deployment::retrieveExternalResources($xhtml, $compiledDirectory);
        if ($subReport->getType() == common_report_Report::TYPE_SUCCESS) {
            $xhtml = $subReport->getData();
            // write index.html
            file_put_contents($compiledDirectory . 'index.html', $xhtml);
            return new common_report_Report(
                common_report_Report::TYPE_SUCCESS,
                __('Published "%1$s" in language "%2$s"', $item->getLabel(), $languageCode)
            );
        } else {
            return $subReport;
        }
    }
    
    /**
     * Create the item's ServiceCall.
     *
     * @param core_kernel_classes_Resource $item
     * @param tao_models_classes_service_StorageDirectory $destinationDirectory
     * @return tao_models_classes_service_ServiceCall
     */
    protected function createService(core_kernel_classes_Resource $item, tao_models_classes_service_StorageDirectory $destinationDirectory)
    {
        $service = new tao_models_classes_service_ServiceCall(new core_kernel_classes_Resource(ItemsService::INSTANCE_SERVICE_ITEM_RUNNER));
        $service->addInParameter(new tao_models_classes_service_ConstantParameter(
            new core_kernel_classes_Resource(ItemsService::INSTANCE_FORMAL_PARAM_ITEM_PATH),
            $destinationDirectory->getId()
        ));
        $service->addInParameter(new tao_models_classes_service_ConstantParameter(
            new core_kernel_classes_Resource(ItemsService::INSTANCE_FORMAL_PARAM_ITEM_URI),
            $item
        ));
        
        return $service;
    }
    
    protected function getSubCompilerClass(core_kernel_classes_Resource $resource)
    {
        throw new common_Exception('Items cannot include other resources');
    }
}
