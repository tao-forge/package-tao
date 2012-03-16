<?php
/**
 * ResultServer Controller provide actions performed from url resolution
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoDelivery
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
 
class taoDelivery_actions_ResultServer extends tao_actions_TaoModule {
	
	/**
	 * constructor: initialize the service and the default data
	 * @return Delivery
	 */
	public function __construct(){
		
		parent::__construct();
		
		//the service is initialized by default
		$this->service = new taoDelivery_models_classes_ResultServerAuthoringService();
		$this->defaultData();
		
		Session::setAttribute('currentSection', 'result_server');
	}
	
/*
 * conveniance methods
 */
	
	/**
	 * get the selected resultServer from the current context (from the uri and classUri parameter in the request)
	 * @return core_kernel_classes_Resource $resultServer
	 */
	private function getCurrentResultServer(){
		$uri = tao_helpers_Uri::decode($this->getRequestParameter('uri'));
		if(is_null($uri) || empty($uri)){
			throw new Exception("No valid uri found");
		}
		
		$clazz = $this->getCurrentClass();
		
		$resultServer = $this->service->getResultServer($uri, 'uri', $clazz);
		if(is_null($resultServer)){
			throw new Exception("No resultServer found for the uri {$uri}");
		}
		
		return $resultServer;
	}
	
	/**
	 * @see TaoModule::getCurrentInstance
	 * @return core_kernel_classes_Resource
	 */
	protected function getCurrentInstance(){
		return $this->getCurrentResultServer();
	}
	
	/**
	 * @see TaoModule::getRootClass
	 * @return core_kernel_classes_Classes
	 */
	protected function getRootClass(){
		return $this->service->getResultServerClass();
	}
	
/*
 * controller actions
 */
	/**
	 * Render json data to populate the result servers tree 
	 * 'modelType' must be in the request parameters
	 * @return void
	 */
	public function getResultServers(){
		
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$options = array(
			'subclasses' => true, 
			'instances' => true, 
			'highlightUri' => '', 
			'labelFilter' => '', 
			'chunk' => false
		);
		if($this->hasRequestParameter('filter')){
			$options['labelFilter'] = $this->getRequestParameter('filter');
		}
		if($this->hasRequestParameter('classUri')){
			$clazz = $this->getCurrentClass();
			$options['chunk'] = true;
		}
		else{
			$clazz = $this->service->getResultServerClass();
		}
		
		echo json_encode( $this->service->toTree($clazz , $options));
	}
	
	/**
	 * Edit a resultServer class
	 * @return void
	 */
	public function editResultServerClass(){
		$clazz = $this->getCurrentClass();
		
		if($this->hasRequestParameter('property_mode')){
			$this->setSessionAttribute('property_mode', $this->getRequestParameter('property_mode'));
		}
		
		$myForm = $this->editClass($clazz, $this->service->getResultServerClass());
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				if($clazz instanceof core_kernel_classes_Resource){
					$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($clazz->uriResource));
				}
				$this->setData('message', __('Result Server class saved'));
				$this->setData('reload', true);
			}
		}
		$this->setData('formTitle', __('Edit resultServer class'));
		$this->setData('myForm', $myForm->render());
		$this->setView('form.tpl');
	}
	
	/**
	 * Edit a delviery instance
	 * @return void
	 */
	public function editResultServer(){
		$clazz = $this->getCurrentClass();
		
		$resultServer = $this->getCurrentResultServer();
		
		$formContainer = new tao_actions_form_Instance($clazz, $resultServer);
		$myForm = $formContainer->getForm();
		
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				
				$resultServer = $this->service->bindProperties($resultServer, $myForm->getValues());
				$this->setData('message', __('Result Server saved'));
				$this->setData('reload', true);
			}
		}
		
		$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($resultServer->uriResource));
		
		//get the deliveries related to this delivery resultServer
		$relatedDeliveries = tao_helpers_Uri::encodeArray($this->service->getRelatedDeliveries($resultServer), tao_helpers_Uri::ENCODE_ARRAY_VALUES);
		$this->setData('relatedDeliveries', json_encode($relatedDeliveries));
		$this->setData('index', '2');
		
		
		$this->setData('formTitle', __('Edit ResultServer'));
		$this->setData('myForm', $myForm->render());
		$this->setView('form_resultserver.tpl');
	}
	
	/**
	 * Add a resultServer instance        
	 * @return void
	 */
	public function addResultServer(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$clazz = $this->getCurrentClass();
		$resultServer = $this->service->createInstance($clazz, $this->service->createUniqueLabel($clazz));
		if(!is_null($resultServer) && $resultServer instanceof core_kernel_classes_Resource){
			echo json_encode(array(
				'label'	=> $resultServer->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($resultServer->uriResource)
			));
		}
	}
	
	/**
	 * Add a resultServer subclass
	 * @return void
	 */
	public function addResultServerClass(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$clazz = $this->service->createResultServerClass($this->getCurrentClass());
		if(!is_null($clazz) && $clazz instanceof core_kernel_classes_Class){
			echo json_encode(array(
				'label'	=> $clazz->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($clazz->uriResource)
			));
		}
	}
	
	/**
	 * Delete a resultServer or a resultServer class
	 * @return void
	 */
	public function delete(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$deleted = false;
		if($this->getRequestParameter('uri')){
			$deleted = $this->service->deleteResultServer($this->getCurrentResultServer());
		}
		else{
			$deleted = $this->service->deleteResultServerClass($this->getCurrentClass());
		}
		
		echo json_encode(array('deleted'	=> $deleted));
	}
	
	/**
	 * Duplicate a resultServer instance
	 * @return void
	 */
	public function cloneResultServer(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$resultServer = $this->getCurrentResultServer();
		$clazz = $this->getCurrentClass();
		
		$clone = $this->service->createInstance($clazz);
		if(!is_null($clone)){
			
			foreach($clazz->getProperties() as $property){
				foreach($resultServer->getPropertyValues($property) as $propertyValue){
					$clone->setPropertyValue($property, $propertyValue);
				}
			}
			$clone->setLabel($resultServer->getLabel()."'");
			echo json_encode(array(
				'label'	=> $clone->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($clone->uriResource)
			));
		}
	}
	
	/**
	 * Get the data to populate the tree of deliveries
	 * @return void
	 */
	public function getDeliveries(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$options = array('chunk' => false);
		if($this->hasRequestParameter('classUri')) {
			$clazz = $this->getCurrentClass();
			$options['chunk'] = true;
		}
		else{
			$clazz = new core_kernel_classes_Class(TAO_DELIVERY_CLASS);
		}
		if($this->hasRequestParameter('selected')){
			$selected = $this->getRequestParameter('selected');
			if(!is_array($selected)){
				$selected = array($selected);
			}
			$options['browse'] = $selected;
		}
		echo json_encode($this->service->toTree($clazz, $options));
	}
	
	/**
	 * Save the related deliveries
	 * @return void
	 */
	public function saveDeliveries(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$saved = false;
		
		$deliveries = array();
			
		foreach($this->getRequestParameters() as $key => $value){
			if(preg_match("/^instance_/", $key)){
				array_push($deliveries, tao_helpers_Uri::decode($value));
			}
		}
		
		if($this->service->setRelatedDeliveries($this->getCurrentResultServer(), $deliveries)){
			$saved = true;
		}
		echo json_encode(array('saved'	=> $saved));
	}
	
	/**
	 * Main action
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @return void
	 */
	public function index(){
		
		if($this->getData('reload') == true){
			unset($_SESSION[SESSION_NAMESPACE]['uri']);
			unset($_SESSION[SESSION_NAMESPACE]['classUri']);
		}
		$this->setView('index_resultserver.tpl');
	}
		
	
	/*
	 * @TODO implement the following actions
	 */
	
	public function getMetaData(){
		throw new Exception("Not implemented yet");
	}
	
	public function saveComment(){
		throw new Exception("Not implemented yet");
	}
		
}
?>