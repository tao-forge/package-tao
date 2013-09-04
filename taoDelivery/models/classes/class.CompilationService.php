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

/**
 * returns the folder to store the compiled delivery
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoDelivery
 * @subpackage models_classes
 */
class taoDelivery_models_classes_CompilationService extends taoDelivery_models_classes_DeliveryService
{

    public function getCompiledContent(core_kernel_classes_Resource $delivery) {
        return $delivery->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_DELIVERY_COMPILED));
    }
    
    public function compileDelivery(core_kernel_classes_Resource $delivery) {
        $content = $this->getContent($delivery);
        if (is_null($content)) {
            throw new taoDelivery_models_classes_CompilationFailedException('Delivery has no content');
        }
        $impl = $this->getImplementationByContent($content);
        $directory = $this->getCompilationDirectory($delivery);
        $serviceCall = $impl->compile($content, $directory);
        
        $compiled = $this->createCompiledContent($serviceCall, $directory, $delivery->getLabel());
        $delivery->editPropertyValues(new core_kernel_classes_Property(PROPERTY_DELIVERY_COMPILED), $compiled);
        return true;
    }
    
    /**
     * Returns the date of the compilation of a delivery
     * 
     * @param core_kernel_classes_Resource $compiledDelivery
     * @return string
     */
    public function getCompilationDate( core_kernel_classes_Resource $compiledDelivery) {
        return (string)$compiledDelivery->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_COMPILEDDELIVERY_TIME));
    }
        
    /**
     * Gets the service call to run this delivery
     * 
     * @param core_kernel_classes_Resource $compiledDelivery
     * @return tao_models_classes_service_ServiceCall
     */
    public function getCompilationRuntime( core_kernel_classes_Resource $compiledDelivery) {
        $runtimeResource = $compiledDelivery->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_COMPILEDDELIVERY_RUNTIME));
        return tao_models_classes_service_ServiceCall::fromResource($runtimeResource);
    }
    
    public function getRuntime( core_kernel_classes_Resource $compiledDelivery, $variables = array()) {
        return $compiledDelivery->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_COMPILEDDELIVERY_RUNTIME));
    }
    
    /**
     * returns the folder to store the compiled delivery
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource delivery
     * @return core_kernel_file_File
     */
    protected function getCompilationDirectory( core_kernel_classes_Resource $delivery)
    {
        $returnValue = (string) '';
        
        $fs = taoDelivery_models_classes_RuntimeAccess::getFileSystem();
        $basePath = $fs->getPath();
        $relPath = substr($delivery->getUri(), strpos($delivery->getUri(), '#') + 1).DIRECTORY_SEPARATOR;
        $absPath = $fs->getPath().$relPath;
        
        if (! is_dir($absPath)) {
            if (! mkdir($absPath)) {
                throw new taoDelivery_models_classes_CompilationFailedException('Could not create delivery directory \'' . $absPath . '\'');
            }
        }
        
        return $fs->createFile('', $relPath);
    }
    
    /**
     * Serialise the compiled content
     * 
     * @param tao_models_classes_service_ServiceCall $call
     * @param unknown $folder
     * @param string $label
     * @return core_kernel_classes_Resource
     */
    protected function createCompiledContent(tao_models_classes_service_ServiceCall $call, core_kernel_file_File $directory, $label = '') {
        $serviceCallClass = new core_kernel_classes_Class(CLASS_COMPILEDDELIVERY);
	    return $serviceCallClass->createInstanceWithProperties(array(
	        RDFS_LABEL                         => $label,
            PROPERTY_COMPILEDDELIVERY_TIME     => time(),
            PROPERTY_COMPILEDDELIVERY_RUNTIME  => $call->serialize(),
	        PROPERTY_COMPILEDDELIVERY_FOLDER   => $directory
        ));
    }
}