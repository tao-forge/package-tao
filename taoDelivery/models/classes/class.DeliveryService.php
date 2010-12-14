<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * This file is part of Generis Object Oriented API.
 *
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoDelivery
 * @subpackage models_classes
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * The Service class is an abstraction of each service instance. 
 * Used to centralize the behavior related to every service instances.
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 */
require_once('tao/models/classes/class.Service.php');
require_once(dirname(__FILE__).'/class.DeliveryProcessGenerator.php');
/**
 * The Precompilator class provides many useful methods to accomplish the test compilation task
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 */


/**
 * The taoDelivery_models_classes_DeliveryService class provides methods to connect to several ontologies and interact with them.
 *
 * @access public
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoDelivery
 * @subpackage models_classes
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class taoDelivery_models_classes_DeliveryService
    extends tao_models_classes_GenerisService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * The attribute deliveryClass contains the default TAO Delivery Class
     *
     * @access protected
     * @var Class
     */
    protected $deliveryClass = null;
		
		
    // --- OPERATIONS ---

	/**
     * The method __construct intiates the DeliveryService class and loads the required ontologies from the other extensions 
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @return mixed
     */	
    public function __construct()
    {
		parent::__construct();
		
		$this->deliveryClass = new core_kernel_classes_Class(TAO_DELIVERY_CLASS);
    }
	
	/**
     * The method getDeliveryClass return the current Delivery Class
	 * (not used yet in the current implementation)
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  string uri
     * @return core_kernel_classes_Class
     */
    public function getDeliveryClass($uri = '')
    {
        $returnValue = null;

		if(empty($uri) && !is_null($this->deliveryClass)){
			$returnValue = $this->deliveryClass;
		}
		else{
			$clazz = new core_kernel_classes_Class($uri);
			if($this->isDeliveryClass($clazz)){
				$returnValue = $clazz;
			}
		}

        return $returnValue;
    }
		
	/**
     * Returns a delivery by providing either its uri (default) or its label and the delivery class
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  string identifier
     * @param  string mode
     * @param  Class clazz
     * @return core_kernel_classes_Resource
     */
    public function getDelivery($identifier, $mode = 'uri',  core_kernel_classes_Class $clazz = null)
    {
        $returnValue = null;

		if(is_null($clazz)){
			$clazz = $this->deliveryClass;
		}
		if($this->isDeliveryClass($clazz)){
			$returnValue = $this->getOneInstanceBy( $clazz, $identifier, $mode);
		}
		
        return $returnValue;
    }
	
	 /**
     * Create a new class of Delivery, which is basically always a subclass of an existing Delivery class.
	 * Require an array('propertyName' => 'propertyValue')
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  Class clazz
     * @param  string label
     * @param  array properties
     * @return core_kernel_classes_Class
     */
    public function createDeliveryClass( core_kernel_classes_Class $clazz = null, $label = '', $properties = array())
    {
        $returnValue = null;

		if(is_null($clazz)){
			$clazz = $this->deliveryClass;
		}
		
		if($this->isDeliveryClass($clazz)){
		
			$deliveryClass = $this->createSubClass($clazz, $label);//call method form TAO_model_service
			
			foreach($properties as $propertyName => $propertyValue){
				$myProperty = $deliveryClass->createProperty(
					$propertyName,
					$propertyName . ' ' . $label .' delivery property created from ' . get_class($this) . ' the '. date('Y-m-d h:i:s') 
				);
			}
			$returnValue = $deliveryClass;
		}

        return $returnValue;
    }
	
	/**
     * Method to be called to delete a delivery instance
     * (Method is not used in the current implementation yet)
	 *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  Resource delivery
     * @return boolean
     */
    public function deleteDelivery( core_kernel_classes_Resource $delivery)
    {
        $returnValue = (bool) false;
		
		if(!is_null($delivery)){
			//delete the process associated to the delivery:
			$process = $delivery->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_DELIVERYCONTENT));
			$processAuthoringService = tao_models_classes_ServiceFactory::get('taoDelivery_models_classes_DeliveryAuthoringService');
			$processAuthoringService->deleteProcess($process);
			
			$returnValue = $delivery->delete();
		}

        return (bool) $returnValue;
    }

    /**
     * Method to be called to delete a delivery class
     * (Method is not used in the current implementation yet)
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  Class clazz
     * @return boolean
     */
    public function deleteDeliveryClass( core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;

		if(!is_null($clazz)){
			if($this->isDeliveryClass($clazz) && $clazz->uriResource != $this->deliveryClass->uriResource){
				$returnValue = $clazz->delete();
			}
		}

        return (bool) $returnValue;
    }

    /**
     * Check whether the object is a delivery class
     * (Method is not used in the current implementation yet)
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  Class clazz
     * @return boolean
     */
    public function isDeliveryClass( core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;

		if($clazz->uriResource == $this->deliveryClass->uriResource){
			$returnValue = true;	
		}
		else{
			foreach($this->deliveryClass->getSubClasses(true) as $subclass){
				if($clazz->uriResource == $subclass->uriResource){
					$returnValue = true;
					break;	
				}
			}
		}

        return (bool) $returnValue;
    }
			
		
	/**
     * Get all deliveries available for the identified subject.
     * This method is used on the Delivery Server and uses direct access to the database for performance purposes.
	 * It returns an array containing the uri of selected deliveries or an empty array otherwise.
	 * To be tested when core_kernel_impl_ApiModelOO::getObject() is implemented
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  string subjectUri
     * @return array
     */
	public function getDeliveriesBySubject($subjectUri){
		
		$returnValue=array();
		
		$groups = core_kernel_impl_ApiModelOO::singleton()->getSubject('http://www.tao.lu/Ontologies/TAOGroup.rdf#Members' , $subjectUri);
		$deliveries = new core_kernel_classes_ContainerCollection(new common_Object());
		foreach ($groups->getIterator() as $group) {
			$deliveries = $deliveries->union(core_kernel_impl_ApiModelOO::singleton()->getObject($group->uriResource, 'http://www.tao.lu/Ontologies/TAOGroup.rdf#Deliveries'));
		}
		//TODO: eliminate duplicate deliveries (with a function like unique_array() ):
		$returnValue = $deliveries;
		
		
		return $returnValue;
	}
	
	/**
     * The methods getTestStatus checks the value of the property "active" OR "compiled" for a given test instance (a ressource)
     * (Note: For the old implementation of delivery when 1 delivery = 1 test)
	 *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource aTestInstance
	 * @param  string status
     * @return boolean
     */
	public function getTestStatus(core_kernel_classes_Resource $aTestInstance, $status){
		
		$returnValue=false;
		
		if(!($aTestInstance instanceof core_kernel_classes_Resource) ){
			throw new Exception("wrong resource in getTestStatus parameter");
			return $returnValue;
		}
		
		switch($status){
			case "active":
				$property=TEST_ACTIVE_PROP;
				break;
				
			case "compiled":
				$property=TEST_COMPILED_PROP;
				
				//check if the compiled folder exists:
				/*
				$testId=tao_helpers_Uri::getUniqueId($aTestInstance->uriResource);
				
				if(!is_dir(BASE_PATH."/compiled/$testId/")){
					return $returnValue;
				}*/ 
				break;
				
			default:
				throw new Exception("wrong test status parameter");
				return $returnValue;
		}
		
		foreach ($aTestInstance->getPropertyValuesCollection(new core_kernel_classes_Property($property))->getIterator() as $value){
			if($value instanceof core_kernel_classes_Resource ){
				if ($value->uriResource == GENERIS_TRUE){
					$returnValue=true;
				}
			}
		}
		
		return $returnValue;
	}
	
	/**
     * The method isCompiled checks the value of the property "compiled" for a given delivery instance
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  Resource aDeliveryInstance
     * @return boolean
     */
	public function isCompiled(core_kernel_classes_Resource $aDeliveryInstance){
		
		$returnValue=false;
		
		if(!($aDeliveryInstance instanceof core_kernel_classes_Resource) ){
			throw new Exception("wrong resource in getTestStatus parameter");
			return $returnValue;
		}
		
		//could use the function getOnePropertyValue($prop, true) instead
		foreach ($aDeliveryInstance->getPropertyValuesCollection(new core_kernel_classes_Property(TAO_DELIVERY_COMPILED_PROP))->getIterator() as $value){
			if($value instanceof core_kernel_classes_Resource ){
				if ($value->uriResource == GENERIS_TRUE){
					$returnValue=true;
					break;
				}
			}
		}
		
		return $returnValue;
	}
		
	 /**
     * get the list of excluded subjects of the delivery
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  Resource delivery
     * @return array
     */
    public function getExcludedSubjects( core_kernel_classes_Resource $delivery)
    {
        $returnValue = array();
		
		if(!is_null($delivery)){
			$subjects = $delivery->getPropertyValues(new core_kernel_classes_Property(TAO_DELIVERY_EXCLUDEDSUBJECTS_PROP));
		
			if(count($subjects) > 0){
				$subjectClass = new core_kernel_classes_Class(TAO_SUBJECT_CLASS);
				$subjectSubClasses = array();
				foreach($subjectClass->getSubClasses(true) as $subjectSubClass){
					$subjectSubClasses[] = $subjectSubClass->uriResource;
				}
				foreach($subjects as $subjectUri){
					$clazz = $this->getClass(new core_kernel_classes_Resource($subjectUri));
					if(!is_null($clazz)){
						if(in_array($clazz->uriResource, $subjectSubClasses)){
							$returnValue[] = $clazz->uriResource;
						}
					}
					$returnValue[] = $subjectUri;
				}
			}
		}
		
        return (array) $returnValue;
    }

    /**
     * define the list of the subjects that are excluded from a delivery
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  Resource delivery
     * @param  array subjects
     * @return boolean
     */
    public function setExcludedSubjects( core_kernel_classes_Resource $delivery, $subjects = array())
    {
        $returnValue = (bool) false;
		
		if(!is_null($delivery)){
			
			$memberProp = new core_kernel_classes_Property(TAO_DELIVERY_EXCLUDEDSUBJECTS_PROP);
			
			$delivery->removePropertyValues($memberProp);
			$done = 0;
			foreach($subjects as $subject){
				if($delivery->setPropertyValue($memberProp, $subject)){
					$done++;
				}
			}
			if($done == count($subjects)){
				$returnValue = true;
			}
		}

        return (bool) $returnValue;
    }

    /**
     * get the list of tests in the delivery in parameter
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  Resource delivery
     * @return array
     */
    public function getRelatedCampaigns( core_kernel_classes_Resource $delivery)
    {
        $returnValue = array();
		
		if(!is_null($delivery)){
			$campaigns = $delivery->getPropertyValues(new core_kernel_classes_Property(TAO_DELIVERY_CAMPAIGN_PROP));
		
			if(count($campaigns)>0){
				$campaignClass =  new core_kernel_classes_Class(TAO_DELIVERY_CAMPAIGN_CLASS);
				$campaignSubClasses = array();
				foreach($campaignClass->getSubClasses(true) as $campaignSubClass){
					$campaignSubClasses[] = $campaignSubClass->uriResource;
				}
				foreach($campaigns as $campaignUri){
					$clazz = $this->getClass(new core_kernel_classes_Resource($campaignUri));
					if(!is_null($clazz)){
						if(in_array($clazz->uriResource, $campaignSubClasses)){
							$returnValue[] = $clazz->uriResource;
						}
					}
					$returnValue[] = $campaignUri;
				}
			}
		}

        return (array) $returnValue;
    }

    /**
     * define the list of campaigns the delivery is associated to
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  Resource delivery
     * @param  array campaigns
     * @return boolean
     */
    public function setRelatedCampaigns( core_kernel_classes_Resource $delivery, $campaigns = array())
    {
        $returnValue = (bool) false;
		
		if(!is_null($delivery)){
			
			$campaignProp = new core_kernel_classes_Property(TAO_DELIVERY_CAMPAIGN_PROP);
			
			$delivery->removePropertyValues($campaignProp);
			$done = 0;
			foreach($campaigns as $campaign){
				if($delivery->setPropertyValue($campaignProp, $campaign)){
					$done++;
				}
			}
			if($done == count($campaigns)){
				$returnValue = true;
			}
		}

        return (bool) $returnValue;
    }
	
	/**
     * add history to a delivery execution
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  string deliveryUri
	 * @param  string subjectUri
     * @return core_kernel_classes_ContainerCollection
     */
	public function getHistory(core_kernel_classes_Resource $delivery, core_kernel_classes_Resource $subject = null){
	
		$historyCollection = new core_kernel_classes_ContainerCollection(new common_Object());
		
		if(empty($delivery)){
			throw new Exception("the delivery instance cannot be empty");
		}
		
		if(empty($subject)){
			//select History by delivery only (subject independent listing, i.e. select for all subjects)
			$historyCollection = core_kernel_impl_ApiModelOO::singleton()->getSubject(TAO_DELIVERY_HISTORY_DELIVERY_PROP, $delivery->uriResource);
		}else{
			//select history by delivery and subject
			$historyByDelivery = core_kernel_impl_ApiModelOO::singleton()->getSubject(TAO_DELIVERY_HISTORY_DELIVERY_PROP, $delivery->uriResource);
			$historyBySubject = core_kernel_impl_ApiModelOO::singleton()->getSubject(TAO_DELIVERY_HISTORY_SUBJECT_PROP, $subject->uriResource);
			$historyCollection = $historyByDelivery->intersect($historyBySubject);
		}
		
		return $historyCollection;
	}
	
	public function deleteHistory(core_kernel_classes_Resource $history){
		$returnValue = false;
		// print_r($history);
		if(!is_null($history)){
			$returnValue = $history->delete();
		}
		return $returnValue;
	}
	/**
     * create a delivery instance, and at the same time the process instance associated to it
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Class clazz
	 * @param  string label
     * @return core_kernel_classes_Resource
     */
	public function createInstance(core_kernel_classes_Class $clazz, $label = ''){
		$deliveryInstance = parent::createInstance($clazz, $label);
		
		//create a process instance at the same time:
		$processInstance = parent::createInstance(new core_kernel_classes_Class(CLASS_PROCESS),'process generated with deliveryService');
		
		//set ACL right to delivery process initialization:
		$processInstance->editPropertyValues(new core_kernel_classes_Property(PROPERTY_PROCESS_INIT_ACL_MODE), INSTANCE_ACL_ROLE);
		$processInstance->editPropertyValues(new core_kernel_classes_Property(PROPERTY_PROCESS_INIT_RESTRICTED_ROLE), CLASS_ROLE_SUBJECT);
			
		$deliveryInstance->setPropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_DELIVERYCONTENT), $processInstance->uriResource);
		$this->updateProcessLabel($deliveryInstance);
		
		//set the the default authoring mode to the 'simple mode':
		$deliveryInstance->setPropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_AUTHORINGMODE_PROP), TAO_DELIVERY_SIMPLEMODE);
		
		//set the default delivery server:
		$deliveryInstance->setPropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_RESULTSERVER_PROP), TAO_DELIVERY_DEFAULT_RESULT_SERVER);
		
		return $deliveryInstance;		
	}
	
	 /**
     * Clone a delivery instance, and thus its property values, except deliveryContent, which is a process instance, and the compiled property value
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource instance
     * @param  core_kernel_classes_Class clazz
     * @return core_kernel_classes_Resource
     */
    public function cloneDelivery( core_kernel_classes_Resource $instance,  core_kernel_classes_Class $clazz)
    {
        $returnValue = null;
		
		//call the parent create instance to prevent useless process test to be created:
		$clone = parent::createInstance($clazz, $instance->getLabel()." bis");
		
		if(!is_null($clone)){
			$noCloningProperties = array(
				TAO_DELIVERY_DELIVERYCONTENT,
				TAO_DELIVERY_COMPILED_PROP,
				TAO_DELIVERY_PROCESS,
				RDF_TYPE
			);
		
			foreach($clazz->getProperties(true) as $property){
			
				if(!in_array($property->uriResource, $noCloningProperties)){
					//allow clone of every property value but the deliverycontent, which is a process:
					foreach($instance->getPropertyValues($property) as $propertyValue){
						$clone->setPropertyValue($property, $propertyValue);
					}
				}
				
			}
			
			//clone the process:
			$propInstanceContent = new core_kernel_classes_Property(TAO_DELIVERY_DELIVERYCONTENT);
			try{
				$process = $instance->getUniquePropertyValue($propInstanceContent);
			}catch(Exception $e){}
			if(!is_null($process)){
				$processCloner = new wfEngine_models_classes_ProcessCloner();
				$processClone = $processCloner->cloneProcess($process);
				$clone->editPropertyValues($propInstanceContent, $processClone->uriResource);
			}else{
				throw new Exception("the delivery process cannot be found");
			}
			
			$this->updateProcessLabel($clone);
			$returnValue = $clone;
		}
		
        return $returnValue;
    }
	
	/**
     * Make sure that the delivery and the associated process have the same label
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource delivery
     * @return void
     */
	public function updateProcessLabel(core_kernel_classes_Resource $delivery){
		$process = $delivery->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_DELIVERYCONTENT));
		$process->setLabel("Process ".$delivery->getLabel());
	}

	/**
	 * Get all the tests composing a delivery
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}	 
	 * @param core_kernel_classes_Resource delivery 
	 * @return array of core_kernel_classes_Resource for each Test instance 
	 */
	public function getRelatedTests(core_kernel_classes_Resource $delivery){
		 $returnValue = array();
	
		if(!is_null($delivery)){
		
			try{
			 	$authoringService = tao_models_classes_ServiceFactory::get('taoDelivery_models_classes_DeliveryAuthoringService');
			 	$process = $delivery->getUniquePropertyValue(
					new core_kernel_classes_Property(TAO_DELIVERY_DELIVERYCONTENT)
				);
				if(!is_null($process)){
					$activities = $authoringService->getActivitiesByProcess($process);
				
					foreach($activities as $activity){
						$test = $authoringService->getTestByActivity($activity);
						if(!is_null($test) && $test instanceof core_kernel_classes_Resource){
							$returnValue[$test->uriResource] = $test;
						}
					}
				}
			}
			catch(Exception $e){}
		
		}
		
		return $returnValue;
	}
	
	
	
	
	/**
     * Build a sequential process for the delivery from an array of tests 
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource delivery
	 * @param  array tests
     * @return boolean
     */
	public function setDeliveryTests(core_kernel_classes_Resource $delivery, $tests){
		
		$returnValue = false;
		
		$authoringService = tao_models_classes_ServiceFactory::get('taoDelivery_models_classes_DeliveryAuthoringService');
		
		// get the current process:
		$process = $delivery->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_DELIVERYCONTENT));
					
		//delete all related activities:
		$activities = $authoringService->getActivitiesByProcess($process);
		foreach($activities as $activity){
			if(!$authoringService->deleteActivity($activity)){
				return $returnValue;
			}
		}
		
		//create the list of activities and interactive services and tests plus their appropriate property values:
		$totalNumber = count($tests);//0...n
		$previousConnector = null; 
		for($i=0;$i<$totalNumber;$i++){
			$test = $tests[$i];
			if(!($test instanceof core_kernel_classes_Resource)){
				throw new Exception("the array element n$i is not a Resource");
			}
			
			//create an activity
			$activity = null;
			$activity = $authoringService->createActivity($process, "test: {$test->getLabel()}");
			if($i==0){
				//set the property value as initial
				$activity->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL), GENERIS_TRUE);
			}
			
			$interactiveService = $authoringService->setTestByActivity($activity, $test);
			if(is_null($interactiveService)){
				throw new core_kernel_classes_Resource("the interactive test of test {$test->getlabel()}({$test->uriResource})");
			}
			
			if($totalNumber == 1){
				if(!is_null($interactiveService) && $interactiveService instanceof core_kernel_classes_Resource){
					return true;
				}
			}
			if($i<$totalNumber-1){
				//get the connector created as the same time as the activity and set the type to "sequential" and the next activity as the selected service definition:
				$connector = $authoringService->createConnector($activity);
				if(!($connector instanceof core_kernel_classes_Resource) || is_null($connector)){
					throw new Exception("the created connector is not a resource");
					return $returnValue;
				}
			
				$connector->setPropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE), INSTANCE_TYPEOFCONNECTORS_SEQUENCE);
				
				if(!is_null($previousConnector)){
					$previousConnector->setPropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES), $activity->uriResource);
				}
				$previousConnector = $connector;//set the current connector as "the previous one" for the next loop	
			}
			else{
				//if it is the last test of the array, no need to add a connector: just connect the previous connector to the last activity
				$previousConnector->setPropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES), $activity->uriResource);
				//every action is performed:
				$returnValue = true;
			}
		}
		
		return $returnValue;
	}
	
	/**
     * Get an ordered array of tests that make up a sequential process
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource delivery
     * @return array
     */
	public function getDeliveryTests(core_kernel_classes_Resource $delivery){
		
		$tests = array();
		$authoringService = tao_models_classes_ServiceFactory::get('taoDelivery_models_classes_DeliveryAuthoringService');
		
		//get the associated process:
		$process = $delivery->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_DELIVERYCONTENT));
		
		//get list of all activities:
		$activities = $authoringService->getActivitiesByProcess($process);
		$totalNumber = count($activities);
		
		//find the first one: property isinitial == true (must be only one, if not error) and set as the currentActivity:
		$currentActivity = null;
		foreach($activities as $activity){
			
			$isIntial = $activity->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL));
			if(!is_null($isIntial) && $isIntial instanceof core_kernel_classes_Resource){
				if($isIntial->uriResource == GENERIS_TRUE){
					$currentActivity = $activity;
					break;
				}
			}
		}
		
		if(is_null($currentActivity)){
			return $tests;
		}
		
		//start the loop:
		for($i=0;$i<$totalNumber;$i++){
			$test = $authoringService->getTestByActivity($currentActivity);
			if(!is_null($test)){
				$tests[$i] = $test;
			}
			
			//get its connector (check the type is "sequential) if ok, get the next activity
			$connectorCollection = core_kernel_impl_ApiModelOO::getSubject(PROPERTY_CONNECTORS_PRECACTIVITIES, $currentActivity->uriResource);
			$nextActivity = null;
			foreach($connectorCollection->getIterator() as $connector){
				$connectorType = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE));
				if($connectorType->uriResource = INSTANCE_TYPEOFCONNECTORS_SEQUENCE){
					$nextActivity = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES));
					break;
				}
			}
			if(!is_null($nextActivity)){
				$currentActivity = $nextActivity;
			}else{
				if($i == $totalNumber-1){
					//it is normal, since it is the last activity and test
				}else{
					throw new Exception('the next activity of the connector is not found');
				}	
			}
		}
		
		$returnValue = array();
		
		if(count($tests) > 0){
			
			ksort($tests);
			
			$testClass = new core_kernel_classes_Class(TAO_TEST_CLASS);
			$testSubClasses = array();
			foreach($testClass->getSubClasses(true) as $testSubClass){
				$testSubClasses[] = $testSubClass->uriResource;
			}
			
			foreach($tests as $test){
				$clazz = $this->getClass($test);
				if(in_array($clazz->uriResource, $testSubClasses)){
					$returnValue[] = $clazz;
				}
				$returnValue[] = $test;
			}
		}
		
		
		return $returnValue;
	}
	
	/**
     * Short description of method getDeliveriesTests
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return array
     */
    public function getDeliveriesTests()
    {
        $returnValue = array();

        // section 127-0-1-1-35b227b4:127a93c45f1:-8000:0000000000002346 begin
        foreach($this->deliveryClass->getInstances(true) as $delivery){
        	$returnValue[$delivery->uriResource] =  $this->getRelatedTests($delivery);
        }
        
        // section 127-0-1-1-35b227b4:127a93c45f1:-8000:0000000000002346 end

        return (array) $returnValue;
    }
	
	/**
     * Get all available tests
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @return array
     */
	public function getAllTests(){
		
		$returnValue = array();
		
		$testClazz = new core_kernel_classes_Class(TAO_TEST_CLASS);
		foreach($testClazz->getInstances(true) as $instance){
			$returnValue[$instance->uriResource] = $instance->getLabel();
		}
		
		return $returnValue;
	}
	
	/**
     * Get the process variable with a given code
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource delivery
     * @return boolean
     */
	public function linearizeDeliveryProcess(core_kernel_classes_Resource $delivery){
		
		$returnValue = false;
		
		//get list of all tests in the delivery, without order:
		$tests = array();
		$authoringService = tao_models_classes_ServiceFactory::get('taoDelivery_models_classes_DeliveryAuthoringService');
		
		//get the associated process:
		$process = $delivery->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_DELIVERYCONTENT));
		
		//get list of all activities:
		$activities = $authoringService->getActivitiesByProcess($process);
		
		foreach($activities as $activity){
			
			//get the FIRST interactive service
			$iService = $activity->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_INTERACTIVESERVICES));
			if(!is_null($iService)){
				
				//get the service definition
				$serviceDefinition = $iService->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION));
				if(!is_null($serviceDefinition)){
					
					//get the url
					$serviceUrl = $serviceDefinition->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_SUPPORTSERVICES_URL));
					if(!is_null($serviceUrl)){
					
						//regenerate the test uri
						$testUri = taoDelivery_helpers_Compilator::getTestUri($serviceUrl);
						if(!empty($testUri)){
							//set the test in the table:
							$tests[$testUri] = new core_kernel_classes_Resource($testUri);
						}
						
					}
					
				}
				
			}
			
		}
		//the functuon setDeliveryTests require an array with numerical key 
		$numericalKeyTestArray = array();
		foreach($tests as $test){
			$numericalKeyTestArray[] = $test;
		}
		
		$returnValue = $this->setDeliveryTests($delivery, $numericalKeyTestArray);
		return $returnValue;
	}
	
	/**
     * Get the process variable with a given code
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  string code
     * @return core_kernel_classes_Resource or null
     */
	public function getProcessVariable($code){
		$procVar = null;
		
		$varCollection = core_kernel_impl_ApiModelOO::singleton()->getSubject(PROPERTY_CODE, $code);
		if(!$varCollection->isEmpty()){
			if($varCollection->get(0) instanceof core_kernel_classes_Resource){
			$procVar = $varCollection->get(0);
			}
		}
		
		return $procVar;
	}
	
	/**
     * get the  selected result server
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  Resource delivery
     * @return core_kernel_classes_Resource
     */
	public function getResultServer(core_kernel_classes_Resource $delivery){
		
		$returnValue= null;
		
		if(!is_null($delivery)){
		
			$returnValue = $delivery->getOnePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_RESULTSERVER_PROP));
		}
		
		return $returnValue;
	}
	
	/**
	 * Perform all operations required to compile a test
	 */
	public function compileTest(core_kernel_classes_Resource $delivery, core_kernel_classes_Resource $test){
		
		$resultArray = array(
			'success' => 1,
			'failed' => array()
		);
				
		//preliminary check
		if(is_null($test)){
			throw new Exception('no empty test is allowed in compilation');
		}
		
		$testService = tao_models_classes_ServiceFactory::get('Tests');
		$itemService = tao_models_classes_ServiceFactory::get('Items');
		$items = $testService->getRelatedItems($test);
		
		$compilationResult = array();
		$propRDFtype = new core_kernel_classes_Property(RDF_TYPE);
		
		foreach($items as $item){
			//check if the item exists: if not, append to the test failure message
			$itemClass = $item->getOnePropertyValue($propRDFtype);
			if(!is_null($itemClass)){
				try{
					
					$itemFolderName = substr($item->uriResource, strpos($item->uriResource, '#') + 1);
					$deliveryFolderName = substr($delivery->uriResource, strpos($delivery->uriResource, '#') + 1);
					$testFolderName = substr($test->uriResource, strpos($test->uriResource, '#') + 1);
					
					//create the compilation folder for the delivery-test-item:
					$compiledFolder = BASE_PATH."/compiled/$deliveryFolderName";
					if(!is_dir($compiledFolder)){
						mkdir($compiledFolder);
					}
					$compiledFolder .= "/$testFolderName";
					if(!is_dir($compiledFolder)){
						mkdir($compiledFolder);
					}
					$compiledFolder .= "/$itemFolderName";
					if(!is_dir($compiledFolder)){
						mkdir($compiledFolder);
					}
					$itemPath = "{$compiledFolder}/index.html";
					$itemUrl = str_replace(ROOT_PATH , ROOT_URL, $itemPath);
					
					$compilator = new taoDelivery_helpers_Compilator($delivery->uriResource, $test->uriResource, $item->uriResource, $compiledFolder);
					$compilator->clearCompiledFolder();
					
					$www = dirname($itemUrl);
				
					$deployParams = array(
						'delivery_server_mode'	=> true,
						'preview_mode'			=> false,
						'tao_base_www'			=> $www,
						'qti_base_www'			=> $www,
						'base_www' 				=> $www,
						'root_url'				=> $www
					);
				
					//deploy the item
					$itemService->deployItem($item, $itemPath, $itemUrl,  $deployParams);
					
					$compilator->copyPlugins();
					
					//directory where all files required to launch the test will be collected
					$directory = $compilator->getCompiledPath();
					
					//parse the XML file with the helper Precompilator: media files are downloaded and a new xml file is generated, by replacing the new path for these media with the old ones
					$itemContent = $compilator->itemParser(file_get_contents($itemPath), $directory, "index.html");//rename to parserItem()
							
					//create and write the new xml file in the folder of the test of the delivery being compiled (need for this so to enable LOCAL COMPILED access to the media)
					$compilator->stringToFile($itemContent, $directory, "index.html");
					
					$compilationResult[] = $compilator->result();
					
					
				}
				catch(Exception $e){
					$resultArray["failed"]["errorMsg"][] = $e->getMessage();
				}
			}else{
				//the item no longer exists, set error message and break the loop and thus the compilation:
				if(!isset($resultArray["failed"]['unexistingItems'])){
					$resultArray["failed"]['unexistingItems'] = array();
				}
				$resultArray["failed"]['unexistingItems'][] = $item->uriResource;
				continue;
			}
		}		
		
		if(empty($resultArray["failed"])){
			$resultArray["success"] = 1;
		}
		
		//legacy to be reformatted:
		/*
		//then send the success message to the user
		$resultArray = array();
		$compilationResult = $compilator->result();
		
		if( empty($compilationResult["failed"]["copiedFiles"]) 
			&& empty($compilationResult["failed"]["createdFiles"]) 
			&& empty($compilationResult["failed"]["errorMsg"]) 
			&& empty($compilationResult["failed"]["untranslatedItems"])
			){
			//compilation succeeded
			$resultArray["success"]=1;
			
			//if everything works well, set the property of the delivery(for now, one single test only) "compiled" to "True" 
			$aTestInstance->editPropertyValues(new core_kernel_classes_Property(TEST_COMPILED_PROP),GENERIS_TRUE);
			
		}elseif( 
			empty($compilationResult["failed"]["createdFiles"])
			&& empty($compilationResult["failed"]["copiedFiles"]["delivery_runtime"])
			&& empty($compilationResult["failed"]["errorMsg"])
			){
			//success with warning: 
			//1.media missing (!empty($compilationResult["failed"]["copiedFiles"])): some file copying failed but, every required runtime plugin is successfully copied.
			//2.untranslated items: !empty($compilationResult["failed"]["untranslatedItems"])
			$resultArray["success"]=2;
			$resultArray["failed"]=$compilationResult["failed"];
			
			//unquote the following line if the compilation can be considered completed
			$aTestInstance->editPropertyValues(new core_kernel_classes_Property(TEST_COMPILED_PROP),GENERIS_TRUE);
			
		}else{
			//other cases: the compilation has failed
			$resultArray["success"]=0;
			$resultArray["failed"]=$compilationResult["failed"];
			
		}
		*/
		
		return $resultArray;
	}
	
	/**
     * retrieve the list of groups where the delivery has been set
     *
     * @access public
     * @param  Resource delivery
     * @return array
     */
    public function getDeliveryGroups( core_kernel_classes_Resource $delivery){
	
        $returnValue = array();

		if(!is_null($delivery)){
			$groupClass 		= new core_kernel_classes_Class(TAO_GROUP_CLASS);
			$deliveriesProperty	= new core_kernel_classes_Property(TAO_GROUP_DELIVERIES_PROP);
			
			$groups = array();
			
			foreach($groupClass->getInstances(true) as $instance){
				foreach($instance->getPropertyValues($deliveriesProperty) as $aDelivery){
					if($aDelivery == $delivery->uriResource){
						$groups[] = $instance->uriResource;
						break;
					}
				}
			}
			
			if(count($groups) > 0){
				$groupSubClasses = array();
				foreach($groupClass->getSubClasses(true) as $groupSubClass){
					$groupSubClasses[] = $groupSubClass->uriResource;
				}
				foreach($groups as $groupUri){
					$clazz = $this->getClass(new core_kernel_classes_Resource($groupUri));
					if(!is_null($clazz)){
						if(in_array($clazz->uriResource, $groupSubClasses)){
							$returnValue[] = $clazz->uriResource;
						}
					}
					$returnValue[] = $groupUri;
				}
			}
			
		}

        return (array) $returnValue;
    }

    /**
     * set the list of groups where the subject is
     *
     * @access public
     * @param  Resource delivery
     * @param  array groups
     * @return boolean
     */
    public function setDeliveryGroups( core_kernel_classes_Resource $delivery, $groups = array())
    {
        $returnValue = (bool) false;

		if(!is_null($delivery)){
			$groupClass 		= new core_kernel_classes_Class(TAO_GROUP_CLASS);
			$deliveriesProperty	= new core_kernel_classes_Property(TAO_GROUP_DELIVERIES_PROP);
			
			$done = 0;
			foreach($groupClass->getInstances(true) as $instance){
				$newDeliveries = array();
				$updateIt = false;
				foreach($instance->getPropertyValues($deliveriesProperty) as $aDelivery){
					if($aDelivery == $delivery->uriResource){
						$updateIt = true;
					}
					else{
						$newDeliveries[] = $aDelivery;
					}
				}
				if($updateIt){
					$instance->removePropertyValues($deliveriesProperty);
					foreach($newDeliveries as $newDelivery){
						$instance->setPropertyValue($deliveriesProperty, $newDelivery);
					}
				}
				if(in_array($instance->uriResource, $groups)){
					if($instance->setPropertyValue($deliveriesProperty, $delivery->uriResource)){
						$done++;
					}
				}
			}
			if($done == count($groups)){
				$returnValue = true;
			}
		}

        return (bool) $returnValue;
    }
	
	public function setAuthoringMode(core_kernel_classes_Resource $delivery, $mode){
	
		$property = new core_kernel_classes_Property(TAO_DELIVERY_AUTHORINGMODE_PROP);
		switch(strtolower($mode)){
			case 'simple':{
				$delivery->editPropertyValues($property, TAO_DELIVERY_SIMPLEMODE);
				//linearization required:
				$this->linearizeDeliveryProcess($delivery);
				break;
			}
			case 'advanced':{
				$delivery->editPropertyValues($property, TAO_DELIVERY_ADVANCEDMODE);
				break;
			}
			default:{
				return false;
			}
		}
		
		
	}
	
	public function generateProcess(core_kernel_classes_Resource $delivery){
	
		$deliveryProcess = null;
		
		$deliveryProcessGenerator = new taoDelivery_models_classes_DeliveryProcessGenerator();
		
		$deliveryProcess = $deliveryProcessGenerator->generateDeliveryProcess($delivery);
		
		//delete the old delivery process if exists:
		$propDeliveryProcess = new core_kernel_classes_Property(TAO_DELIVERY_PROCESS);
		$oldDeliveryProcess = $delivery->getOnePropertyValue($propDeliveryProcess);
		
		// print_r($oldDeliveryProcess);
		if($oldDeliveryProcess instanceof core_kernel_classes_Resource){
			$authoringService = tao_models_classes_ServiceFactory::get('taoDelivery_models_classes_DeliveryAuthoringService');
			$authoringService->deleteProcess($oldDeliveryProcess);
		}
		//then save it in TAO_DELIVERY_PROCESS prop:
		$delivery->editPropertyValues($propDeliveryProcess, $deliveryProcess->uriResource);
		
		return $deliveryProcess;
	}
} /* end of class taoDelivery_models_classes_DeliveryService */

?>
