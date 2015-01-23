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
 * Service to manage the authoring of deliveries
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoDelivery
 */
class taoDelivery_models_classes_DeliveryAssemblyService extends tao_models_classes_ClassService
{

    /**
     * (non-PHPdoc)
     * 
     * @see tao_models_classes_ClassService::getRootClass()
     */
    public function getRootClass()
    {
        return new core_kernel_classes_Class(CLASS_COMPILEDDELIVERY);
    }
    
    /**
     * Creates a new assembly from the provided template
     * and desactivates other assemblies crearted from the same template
     * 
     * @param core_kernel_classes_Resource $deliveryTemplate
     * @throws taoDelivery_models_classes_EmptyDeliveryException
     * @return common_report_Report
     */
    public function createAssemblyFromTemplate(core_kernel_classes_Resource $deliveryTemplate) {
        
        $assemblyClass = $this->getRootClass();
        
        $content = taoDelivery_models_classes_DeliveryTemplateService::singleton()->getContent($deliveryTemplate);
        if (is_null($content)) {
            throw new taoDelivery_models_classes_EmptyDeliveryException('Delivery '.$deliveryTemplate->getUri().' has no content');
        }

        $props = $deliveryTemplate->getPropertiesValues(array(
            RDFS_LABEL,
            TAO_DELIVERY_RESULTSERVER_PROP,
            TAO_DELIVERY_MAXEXEC_PROP,
            TAO_DELIVERY_START_PROP,
            TAO_DELIVERY_END_PROP,
            TAO_DELIVERY_EXCLUDEDSUBJECTS_PROP
        ));
        $props[PROPERTY_COMPILEDDELIVERY_DELIVERY] = array($deliveryTemplate);
        
        $report = $this->createAssembly($assemblyClass, $content, $props);
        
        return $report;
    }
    
    
    protected function getCompiler(core_kernel_classes_Resource $content){
        return taoDelivery_models_classes_DeliveryCompiler::createCompiler($content);
    }
    
    /**
     * 
     * @param core_kernel_classes_Class $deliveryClass
     * @param core_kernel_classes_Resource $content
     * @param unknown $properties
     * @return common_report_Report
     */
    public function createAssembly(core_kernel_classes_Class $deliveryClass, core_kernel_classes_Resource $content, $properties = array()) {

        // report will be replaced unless an exception occures
        $report = new common_report_Report(common_report_Report::TYPE_ERROR, __('Delivery could not be published'));
        try {
            $compiler = $this->getCompiler($content);
            $report = $compiler->compile();
            if ($report->getType() == common_report_Report::TYPE_SUCCESS) {
                $serviceCall = $report->getData();
                
                $properties[PROPERTY_COMPILEDDELIVERY_DIRECTORY] = $compiler->getSpawnedDirectoryIds();
                
                $compilationInstance = $this->createAssemblyFromServiceCall($deliveryClass, $serviceCall, $properties);
                $report->setData($compilationInstance);
            }
        } catch (Exception $e) {
            if ($e instanceof common_exception_UserReadableException) {
                $report->add($e);
            } else {
                common_Logger::w($e->getMessage());
            }
        }
        return $report;
        
    }
    
    public function createAssemblyFromServiceCall(core_kernel_classes_Class $deliveryClass, tao_models_classes_service_ServiceCall $serviceCall, $properties = array()) {

        $properties[PROPERTY_COMPILEDDELIVERY_TIME]      = time();
        $properties[PROPERTY_COMPILEDDELIVERY_RUNTIME]   = $serviceCall->toOntology();
        
        if (!isset($properties[TAO_DELIVERY_RESULTSERVER_PROP])) {
            $properties[TAO_DELIVERY_RESULTSERVER_PROP] = taoResultServer_models_classes_ResultServerAuthoringService::singleton()->getDefaultResultServer();
        }
        
        $compilationInstance = $deliveryClass->createInstanceWithProperties($properties);
        
        return $compilationInstance;
    }
    
    /**
     * Returns all assemblies marked as active
     * 
     * @return array
     */
    public function getAllAssemblies() {
        return $this->getRootClass()->getInstances(true);
    }
    
    public function deleteInstance(core_kernel_classes_Resource $assembly)
    {
        // stop all executions
        
        taoDelivery_models_classes_execution_ServiceProxy::singleton()->getActiveDeliveryExecutions($assembly);
        $groupClass = new core_kernel_classes_Class(TAO_GROUP_CLASS);
        $assignationProperty = new core_kernel_classes_Property(PROPERTY_GROUP_DELVIERY);
        $assigned = $groupClass->searchInstances(array(
            PROPERTY_GROUP_DELVIERY => $assembly
        ), array('like' => false, 'recursive' => true));
        foreach ($assigned as $groupInstance) {
            $groupInstance->removePropertyValue($assignationProperty, $assembly);
        }
        $runtimeResource = $assembly->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_COMPILEDDELIVERY_RUNTIME));
        $runtimeResource->delete();
        // cleanup data
        return $assembly->delete();
    }
    
    /**
     * Gets the service call to run this assembly
     *
     * @param core_kernel_classes_Resource $assembly
     * @return tao_models_classes_service_ServiceCall
     */
    public function getRuntime( core_kernel_classes_Resource $assembly) {
        $runtimeResource = $assembly->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_COMPILEDDELIVERY_RUNTIME));
        return tao_models_classes_service_ServiceCall::fromResource($runtimeResource);
    }
    
    /**
     * Returns the date of the compilation of an assembly as a timestamp
     *
     * @param core_kernel_classes_Resource $assembly
     * @return string
     */
    public function getCompilationDate( core_kernel_classes_Resource $assembly) {
        return (string)$assembly->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_COMPILEDDELIVERY_TIME));
    }
    
    /**
     * Returns the implementation from the content
     *
     * @param core_kernel_classes_Resource $test
     * @return taoDelivery_models_classes_ContentModel
     */
    public function getImplementationByContent(core_kernel_classes_Resource $content)
    {
        foreach ($content->getTypes() as $type) {
            if ($type->isSubClassOf(new core_kernel_classes_Class(CLASS_ABSTRACT_DELIVERYCONTENT))) {
                return $this->getImplementationByContentClass($type);
            }
        }
        throw new common_exception_NoImplementation('No implementation found for DeliveryContent ' . $content->getUri());
    }
    
    /**
     * Returns the implementation from the content class
     *
     * @param core_kernel_classes_Class $contentClass
     * @return taoDelivery_models_classes_ContentModel
     */
    public function getImplementationByContentClass(core_kernel_classes_Class $contentClass)
    {
        if (empty($contentClass)) {
            throw new common_exception_NoImplementation(__FUNCTION__ . ' called on a NULL contentClass');
        }
        $classname = (string) $contentClass->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CONTENTCLASS_IMPLEMENTATION));
        if (empty($classname)) {
            throw new common_exception_NoImplementation('No implementation found for contentClass ' . $contentClass->getUri());
        }
        if (! class_exists($classname) || ! in_array('taoDelivery_models_classes_ContentModel', class_implements($classname))) {
            throw new common_exception_Error('Content implementation '.$classname.' not found, or not compatible for content class '.$contentClass->getUri());
             
        }
        return new $classname();
    }
}