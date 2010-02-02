<?php
require_once('tao/actions/CommonModule.class.php');
require_once('tao/actions/TaoModule.class.php');

/**
 * Tests Controller provide actions performed from url resolution
 * 
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoTests
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * 
 */
class Tests extends TaoModule {

	/**
	 * constructor: initialize the service and the default data
	 */
	public function __construct(){
	
		parent::__construct();
	
		//the service is initialized by default
		$this->service = tao_models_classes_ServiceFactory::get('Tests');
		$this->defaultData();
	}
	
	
	/**
	 * Override auth method
	 * @see TaoModule::_isAllowed
	 * @return boolean
	 */	
	protected function _isAllowed(){
		$context = Context::getInstance();
		if($context->getActionName() != 'getTestContent'){
			return parent::_isAllowed();
		}
		return true;
	}
	
	
/*
 * conveniance methods
 */
	
	/**
	 * get the instancee of the current test regarding the 'uri' and 'classUri' request parameters
	 * @return core_kernel_classes_Resource the test instance
	 */
	private function getCurrentTest(){
		
		$uri = tao_helpers_Uri::decode($this->getRequestParameter('uri'));
		if(is_null($uri) || empty($uri)){
			throw new Exception("No valid uri found");
		}
		
		$clazz = $this->getCurrentClass();
		
		$test = $this->service->getTest($uri, 'uri', $clazz);
		if(is_null($test)){
			throw new Exception("No test found for the uri {$uri}");
		}
		
		return $test;
	}
	
	
/*
 * controller actions
 */
	
	/**
	 * main action
	 * @return void
	 */
	public function index(){
		
		if($this->getData('reload') == true){
			unset($_SESSION[SESSION_NAMESPACE]['uri']);
			unset($_SESSION[SESSION_NAMESPACE]['classUri']);
		}
		$this->setView('index.tpl');
	}
	
	/**
	 * Render json data to populate the tests tree 
	 * 'modelType' must be in request parameter
	 * @return void
	 */
	public function getTests(){
		
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$filter = '';
		if($this->hasRequestParameter('filter')){
			$filter = $this->getRequestParameter('filter');
		}
		echo json_encode( $this->service->toTree( $this->service->getTestClass(), true, true, '', $filter));
	}
	
	/**
	 * Add an test instance
	 * @return void
	 */
	public function addTest(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$clazz = $this->getCurrentClass();
		$test = $this->service->createInstance($clazz);
		if(!is_null($test) && $test instanceof core_kernel_classes_Resource){
			echo json_encode(array(
				'label'	=> $test->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($test->uriResource)
			));
		}
	}
	
	/**
	 * edit a test instance
	 */
	public function editTest(){
		$clazz = $this->getCurrentClass();
		$test = $this->getCurrentTest();
		$myForm = tao_helpers_form_GenerisFormFactory::instanceEditor($clazz, $test);
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				
				$test = $this->service->bindProperties($test, $myForm->getValues());
				
				$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($test->uriResource));
				$this->setData('message', 'Test saved');
				$this->setData('reload', true);
				$this->forward('Tests', 'index');
			}
		}
		
		$allItems = array();
		foreach($this->service->getAllItems() as $itemUri => $itemLabel){
			$allItems['item_'.tao_helpers_Uri::encode($itemUri)] = $itemLabel;
		}
		$this->setData('allItems', json_encode($allItems));
		
		$relatedItems = $this->service->getRelatedItems($test, true);
		$this->setData('relatedItems', json_encode(array_map("tao_helpers_Uri::encode", $relatedItems)));
		
		$itemSequence = array();
		foreach($relatedItems as $index => $itemUri){
			$item = new core_kernel_classes_Resource($itemUri);
			$itemSequence[$index] = array(
				'uri' 	=> tao_helpers_Uri::encode($itemUri),
				'label' => $item->getLabel()
			);
		}
		$this->setData('itemSequence', $itemSequence);
		
		
		$this->setData('uri', tao_helpers_Uri::encode($test->uriResource));
		$this->setData('classUri', tao_helpers_Uri::encode($clazz->uriResource));
		$this->setData('formTitle', 'Edit test');
		$this->setData('myForm', $myForm->render());
		$this->setView('form_group.tpl');
	}
	
	/**
	 * add a test (subclass Test)
	 * @return void
	 */
	public function addTestClass(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$clazz = $this->service->createTestClass($this->getCurrentClass());
		if(!is_null($clazz) && $clazz instanceof core_kernel_classes_Class){
			echo json_encode(array(
				'label'	=> $clazz->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($clazz->uriResource)
			));
		}
	}
	
	/**
	 * Edit a test model (edit a class)
	 * @return void
	 */
	public function editTestClass(){
		$clazz = $this->getCurrentClass();
		$myForm = $this->editClass($clazz, $this->service->getTestClass());
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				if($clazz instanceof core_kernel_classes_Resource){
					$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($clazz->uriResource));
				}
				$this->setData('message', 'class saved');
				$this->setData('reload', true);
				$this->forward('Tests', 'index');
			}
		}
		$this->setData('formTitle', 'Edit test class');
		$this->setData('myForm', $myForm->render());
		$this->setView('form.tpl');
	}
	
	/**
	 * delete a test or a test class
	 * called via ajax
	 * @return void
	 */
	public function delete(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$deleted = false;
		if($this->getRequestParameter('uri')){
			$deleted = $this->service->deleteTest($this->getCurrentTest());
		}
		else{
			$deleted = $this->service->deleteTestClass($this->getCurrentClass());
		}
		
		echo json_encode(array('deleted'	=> $deleted));
	}
		
	/**
	 * duplicate a test instance by property copy
	 * @return void
	 */
	public function cloneTest(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$clone = $this->service->cloneInstance($this->getCurrentTest(), $this->getCurrentClass());
		if(!is_null($clone)){
			echo json_encode(array(
				'label'	=> $clone->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($clone->uriResource)
			));
		}
	}
	
	
	/**
	 * display the authoring  template (load the tool into an iframe)
	 * @return void
	 */
	public function authoring(){
		$this->setData('error', false);
		try{
			$test = $this->getCurrentTest();
			$clazz =  $this->getCurrentClass();
			$data = $this->service->getTestParameters($test);
			$data['uri'] = tao_helpers_Uri::encode($test->uriResource);
			$data['classUri'] = tao_helpers_Uri::encode($clazz->uriResource);
			$myFormContainer = new taoTests_actions_form_TestAuthoring($data);
			$myForm = $myFormContainer->getForm();
			
			if($myForm->isSubmited()){
				if($myForm->isValid()){
					if($this->service->saveTestParameters($test, $myForm->getValues())){
						$this->setData('message', __('test saved'));
					}
				}
			}
			$this->setData('formTitle', __('Test parameters'));
			$this->setData('myForm', $myForm->render());
			$this->setData('uri', tao_helpers_Uri::encode($test->uriResource));
			$this->setData('classUri', tao_helpers_Uri::encode($clazz->uriResource));
		}
		catch(Exception $e){
			$this->setData('error', true);
		}
		$this->setView('authoring.tpl');
	}
	
	/**
	 * display the item sequence tempalte and initialize the grid component
	 * @return void 
	 */
	public function itemSequence(){
		$test = $this->getCurrentTest();
		$clazz =  $this->getCurrentClass();
		
		$this->setData('uri', tao_helpers_Uri::encode($test->uriResource));
		$this->setData('classUri', tao_helpers_Uri::encode($clazz->uriResource));
		$this->setView('itemsequence.tpl');
	}
	
	/**
	 * provide the user list data via json
	 * @return void
	 */
	public function itemSequenceData(){
		$page = $this->getRequestParameter('page'); 
		$limit = $this->getRequestParameter('rows'); 
		$sidx = $this->getRequestParameter('sidx');  
		$sord = $this->getRequestParameter('sord'); 
		$start = $limit * $page - $limit; 
		
		if(!$sidx) $sidx =1; // connect to the database 
		
		$test = $this->getCurrentTest();
		$items = $this->service->getItemSequence($test, array(
			'order' 	=> $sidx,
			'orderDir'	=> $sord,
			'start'		=> $start,
			'end'		=> $limit
		));
		
		$count = count($items); 
		if( $count >0 ) { 
			$total_pages = ceil($count/$limit); 
		} 
		else { 
			$total_pages = 0; 
		} 
		if ($page > $total_pages){
			$page = $total_pages; 
		}
		
		$response = new stdClass();
		$response->page = $page; 
		$response->total = $total_pages; 
		$response->records = $count; 
		$index = 0;
		foreach($items as $i => $item) { 
			$response->rows[$index]['id']= (string)$item['sequence']; 
			$response->rows[$index]['cell']= array(
				(string)$item['sequence'], 
				tao_helpers_Uri::encode($item['uri']), 
				$item['label'], 
				$item['weight'], 
				$item['difficulty'], 
				$item['discrimination'],
				$item['guessing'],
				$item['model']
			);
			$index++;
		} 
		echo json_encode($response); 
	}
	
	/**
	 * save the sequence
	 * @return void
	 */
	public function saveItemSequence(){
		
		$response = array('saved' => false);
		
		$test = $this->getCurrentTest();
		$clazz =  $this->getCurrentClass();
		
		$sequence = array();
		foreach($this->getRequestParameters() as $key => $value){
			if(preg_match("/^item_/", $key)){
				$key = str_replace("item_", '', $key);
				$index = substr($key, 0, strpos($key, '_'));
				$key = substr($key, strpos($key, '_') + 1);
				if($key == 'uri'){
					$value = tao_helpers_Uri::decode($value);
				}
				$sequence[$index][$key] = $value;
			}
		}
		if($this->service->saveItemSequence($test, $sequence)){
			$response['saved'] = true;
		}
		
		echo json_encode($response);
	}
	
	/**
	 * get the xml content of a test over an http request 
	 * @return void 
	 */
	public function getTestContent(){
		header("Content-Type: text/xml; charset utf-8");
		try{
			print $this->service->getTestContent($this->getCurrentTest());
			return;
		}
		catch(Exception $e){}
		print '<?xml version="1.0" encoding="utf-8" ?>';
	}
	
	/**
	 * save the xml content of a test
	 * @return void
	 */
	public function saveTestContent(){
		
		$message = __('An error occured while saving the test');
		
		if(isset($_SESSION['instance']) && isset($_SESSION['xml'])){
		
			$test = $this->service->getTest($_SESSION['instance']);
			if($this->service->setTestContent($test, $_SESSION['xml'])){
				$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($test->uriResource));
				$message = __('Test saved successfully');
			}
			unset($_SESSION['instance']);
			unset($_SESSION['xml']);
		}
		
		$this->redirect('/tao/Main/index?extension=taoTests&message='.urlencode($message));
	}
	
	/**
	 * Test preview 
	 * @return void
	 */
	public function preview(){
		$this->setData('uri', $this->getRequestParameter('uri'));
		$this->setView('preview.tpl');
	}
	
	/**
	 * get the list of items to populate the checkbox tree of related items
	 * @return void
	 */
	public function getItems(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		echo json_encode($this->service->toTree( new core_kernel_classes_Class(TAO_ITEM_CLASS), true, true, ''));
	}
	
	/**
	 * save the related items from the checkbox tree
	 * @return void
	 */
	public function saveItems(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$saved = false;
		
		$items = array();
		foreach($this->getRequestParameters() as $key => $value){
			if(preg_match("/^instance_/", $key)){
				$items[str_replace('instance_', '', $key)] = tao_helpers_Uri::decode($value);
			}
		}
		$test = $this->getCurrentTest();
		
		if($this->service->setRelatedItems($test, $items, true)){
			$saved = true;
		}
		echo json_encode(array('saved'	=> $saved));
	}
	
	/*
	 * @TODO implement the following actions
	 */
	
	public function getMetaData(){
		throw new Exception("Not yet implemented");
	}
	
	public function saveComment(){
		throw new Exception("Not yet implemented");
	}
	
}
?>