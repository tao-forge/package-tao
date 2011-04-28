<?php

error_reporting(E_ALL);

/**
 * TAO - taoDelivery/models/classes/class.ResultServerService.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 10.11.2010, 17:10:06 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoDelivery
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * The Service class is an abstraction of each service instance. 
 * Used to centralize the behavior related to every servcie instances.
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('tao/models/classes/class.GenerisService.php');

/* user defined includes */
// section 10-13-1-39-5129ca57:1276133a327:-8000:000000000000201D-includes begin
// section 10-13-1-39-5129ca57:1276133a327:-8000:000000000000201D-includes end

/* user defined constants */
// section 10-13-1-39-5129ca57:1276133a327:-8000:000000000000201D-constants begin
// section 10-13-1-39-5129ca57:1276133a327:-8000:000000000000201D-constants end

/**
 * Short description of class taoDelivery_models_classes_ResultServerService
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoDelivery
 * @subpackage models_classes
 */
class taoDelivery_models_classes_ResultServerService
    extends tao_models_classes_GenerisService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute resultServerClass
     *
     * @access protected
     * @var Class
     */
    protected $resultServerClass = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    public function __construct()
    {
        // section 10-13-1-39-5129ca57:1276133a327:-8000:000000000000216F begin
        
    	parent::__construct();
		
		$this->resultServerClass = new core_kernel_classes_Class(TAO_DELIVERY_RESULTSERVER_CLASS);
    	
        // section 10-13-1-39-5129ca57:1276133a327:-8000:000000000000216F end
    }

    /**
     * Short description of method createResultServerClass
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Class clazz
     * @param  string label
     * @param  array properties
     * @return core_kernel_classes_Class
     */
    public function createResultServerClass( core_kernel_classes_Class $clazz = null, $label = '', $properties = array())
    {
        $returnValue = null;

        // section 10-13-1-39-5129ca57:1276133a327:-8000:0000000000002171 begin
        
    	if(is_null($clazz)){
			$clazz = $this->resultServerClass;
		}
		
		if($this->isResultServerClass($clazz)){
		
			$resultServerClass = $this->createSubClass($clazz, $label);//call method form TAO_model_service
			
			foreach($properties as $propertyName => $propertyValue){
				$myProperty = $resultServerClass->createProperty(
					$propertyName,
					$propertyName . ' ' . $label .' resultServer property from ' . get_class($this) . ' the '. date('Y-m-d h:i:s') 
				);
			}
			$returnValue = $resultServerClass;
		}
        
        // section 10-13-1-39-5129ca57:1276133a327:-8000:0000000000002171 end

        return $returnValue;
    }

    /**
     * Short description of method deleteResultServer
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource resultServer
     * @return boolean
     */
    public function deleteResultServer( core_kernel_classes_Resource $resultServer)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-5129ca57:1276133a327:-8000:0000000000002173 begin
        
    	if(!is_null($resultServer)){
			$returnValue = $resultServer->delete();
		}
        
        // section 10-13-1-39-5129ca57:1276133a327:-8000:0000000000002173 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method deleteResultServerClass
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Class clazz
     * @return boolean
     */
    public function deleteResultServerClass( core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-5129ca57:1276133a327:-8000:0000000000002175 begin
        
    	if(!is_null($clazz)){
			if($this->isResultServerClass($clazz) && $clazz->uriResource != $this->resultServerClass->uriResource){
				$returnValue = $clazz->delete();
			}
		}
        
        // section 10-13-1-39-5129ca57:1276133a327:-8000:0000000000002175 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getRelatedDeliveries
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource resultServer
     * @return array
     */
    public function getRelatedDeliveries( core_kernel_classes_Resource $resultServer)
    {
        $returnValue = array();

        // section 10-13-1-39-5129ca57:1276133a327:-8000:0000000000002177 begin
        
    if(!is_null($resultServer)){
		
			$deliveries = core_kernel_impl_ApiModelOO::singleton()->getSubject(TAO_DELIVERY_RESULTSERVER_PROP, $resultServer->uriResource);
			foreach ($deliveries->getIterator() as $delivery){
				if($delivery instanceof core_kernel_classes_Resource ){
					$returnValue[] = $delivery->uriResource;
				}
			}
		}
        
        // section 10-13-1-39-5129ca57:1276133a327:-8000:0000000000002177 end

        return (array) $returnValue;
    }

    /**
     * Short description of method getResultServer
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string identifier
     * @param  string mode
     * @param  Class clazz
     * @return core_kernel_classes_Resource
     */
    public function getResultServer($identifier, $mode = 'uri',  core_kernel_classes_Class $clazz = null)
    {
        $returnValue = null;

        // section 10-13-1-39-5129ca57:1276133a327:-8000:0000000000002179 begin
        
   		if(is_null($clazz)){
			$clazz = $this->resultServerClass;
		}
		if($this->isResultServerClass($clazz)){
			$returnValue = $this->getOneInstanceBy( $clazz, $identifier, $mode);
		}
        
        // section 10-13-1-39-5129ca57:1276133a327:-8000:0000000000002179 end

        return $returnValue;
    }

    /**
     * Short description of method setRelatedDeliveries
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource resultServer
     * @param  array deliveries
     * @return boolean
     */
    public function setRelatedDeliveries( core_kernel_classes_Resource $resultServer, $deliveries = array())
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-5129ca57:1276133a327:-8000:000000000000217B begin
        
   	 if(!is_null($resultServer)){
			//the property of the DELIVERIES that will be modified
			$resultServerProp = new core_kernel_classes_Property(TAO_DELIVERY_RESULTSERVER_PROP);
			
			//a way to remove the resultServer property value of the delivery that are used to be associated to THIS resultServer
			$oldRelatedDeliveries = core_kernel_impl_ApiModelOO::singleton()->getSubject(TAO_DELIVERY_RESULTSERVER_PROP, $resultServer->uriResource);
			foreach ($oldRelatedDeliveries->getIterator() as $oldRelatedDelivery) {
				//TODO check if it is a delivery instance
				
				//find a way to remove the property value associated to THIS resultServer ONLY
				core_kernel_impl_ApiModelOO::singleton()->removeStatement($oldRelatedDelivery->uriResource, TAO_DELIVERY_RESULTSERVER_PROP, $resultServer->uriResource, '');
				
				// $oldRelatedDelivery->removePropertyValues($resultServerProp);//issue with this implementation: delete all property values
			}
			
			//assign the current compaign to the selected deliveries	
			$done = 0;
			foreach($deliveries as $delivery){
				//the delivery instance to be modified
				$deliveryInstance=new core_kernel_classes_Resource($delivery);
			
				//remove the property value associated to another delivery in case ONE delivery can ONLY be associated to ONE resultServer
				//if so, then change the widget from comboBox to treeView in the delivery property definition
				$deliveryInstance->removePropertyValues($resultServerProp);
				
				//now, truly assigning the resultServer uri to the affected deliveries
				if($deliveryInstance->setPropertyValue($resultServerProp, $resultServer->uriResource)){
					$done++;
				}
			}
			if($done == count($deliveries)){
				$returnValue = true;
			}
		}
        
        // section 10-13-1-39-5129ca57:1276133a327:-8000:000000000000217B end

        return (bool) $returnValue;
    }

    /**
     * Short description of method isResultServerClass
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Class clazz
     * @return boolean
     */
    public function isResultServerClass( core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-5129ca57:1276133a327:-8000:000000000000217D begin
        
    	if($clazz->uriResource == $this->resultServerClass->uriResource){
			$returnValue = true;	
		}
		else{
			foreach($this->resultServerClass->getSubClasses(true) as $subclass){
				if($clazz->uriResource == $subclass->uriResource){
					$returnValue = true;
					break;	
				}
			}
		}
        
        // section 10-13-1-39-5129ca57:1276133a327:-8000:000000000000217D end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getResultServerClass
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string uri
     * @return core_kernel_classes_Class
     */
    public function getResultServerClass($uri = '')
    {
        $returnValue = null;

        // section 10-13-1-39-5129ca57:1276133a327:-8000:000000000000217F begin
        
    	if(empty($uri) && !is_null($this->resultServerClass)){
			$returnValue = $this->resultServerClass;
		}
		else{
			$clazz = new core_kernel_classes_Class($uri);
			if($this->isResultServerClass($clazz)){
				$returnValue = $clazz;
			}
		}
        
        // section 10-13-1-39-5129ca57:1276133a327:-8000:000000000000217F end

        return $returnValue;
    }

    /**
     * Short description of method getDelpoymentParameters
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource resultServer
     * @return array
     */
    public function getDelpoymentParameters( core_kernel_classes_Resource $resultServer)
    {
        $returnValue = array();

        // section 127-0-1-1--1fd8ff6b:12c3688e878:-8000:00000000000028A1 begin
        
        if(!is_null($resultServer)){
        	
        	$resultUrl 		= (string) $resultServer->getOnePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_RESULTSERVER_RESULT_URL_PROP));
        	$eventUrl		= (string) $resultServer->getOnePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_RESULTSERVER_EVENT_URL_PROP));
        	$matchingUrl	= (string) $resultServer->getOnePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_RESULTSERVER_MATCHING_URL_PROP));
        	$matchingSide 	= $resultServer->getOnePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_RESULTSERVER_MATCHING_SERVER_PROP));
        	
        	$returnValue = array(
        		'save_result_url' 	=> preg_match('/^\//',$resultUrl)? ROOT_URL.$resultUrl : $resultUrl,
        		'save_event_url' 	=> preg_match('/^\//',$eventUrl)? ROOT_URL.$eventUrl : $eventUrl,
        		'matching_url' 		=> preg_match('/^\//',$matchingUrl)? ROOT_URL.$matchingUrl : $matchingUrl,
        		'matching_server' 	=> ($matchingSide->uriResource == GENERIS_TRUE)
        	);
        }
		
        // section 127-0-1-1--1fd8ff6b:12c3688e878:-8000:00000000000028A1 end

        return (array) $returnValue;
    }

} /* end of class taoDelivery_models_classes_ResultServerService */

?>