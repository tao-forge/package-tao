<?php
require_once('tao/actions/CommonModule.class.php');
require_once('tao/actions/TaoModule.class.php');

/**
 * Delivery Controller provide actions performed from url resolution
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoDelivery
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
 
class Delivery extends TaoModule {
	
	/**
	 * constructor: initialize the service and the default data
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @return Delivery
	 */
	public function __construct(){
		
		parent::__construct();
		
		//the service is initialized by default
		$this->service = tao_models_classes_ServiceFactory::get('Delivery');
		$this->defaultData();
		
		Session::setAttribute('currentSection', 'delivery');
	}
	
/*
 * conveniance methods
 */
	
	/**
	 * get the selected delivery from the current context (from the uri and classUri parameter in the request)
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @return core_kernel_classes_Resource $delivery
	 */
	private function getCurrentDelivery(){
		$uri = tao_helpers_Uri::decode($this->getRequestParameter('uri'));
		if(is_null($uri) || empty($uri)){
			throw new Exception("No valid uri found");
		}
		
		$clazz = $this->getCurrentClass();
		$delivery = $this->service->getDelivery($uri, 'uri', $clazz);
		if(is_null($delivery)){
			throw new Exception("No delivery found for the uri {$uri}");
		}
		
		return $delivery;
	}
	
	/**
	 * @see TaoModule::getCurrentInstance
	 * @return core_kernel_classes_Resource
	 */
	protected function getCurrentInstance(){
		return $this->getCurrentDelivery();
	}
	
	/**
	 * @see TaoModule::getRootClass
	 * @return core_kernel_classes_Classes
	 */
	protected function getRootClass(){
		return $this->service->getDeliveryClass();
	}
	
/*
 * controller actions
 */
	/**
	 * Render json data to populate the delivery tree 
	 * 'modelType' must be in the request parameters
	 * @return void
	 */
	public function getDeliveries(){
		
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
			$clazz =  $this->service->getDeliveryClass();
		}
		
		echo json_encode( $this->service->toTree($clazz , $options));
	}
	
	/**
	 * Edit a delivery class
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @return void
	 */
	public function editDeliveryClass(){
		$clazz = $this->getCurrentClass();
		
		if($this->hasRequestParameter('property_mode')){
			$this->setSessionAttribute('property_mode', $this->getRequestParameter('property_mode'));
		}
		
		$myForm = $this->editClass($clazz, $this->service->getDeliveryClass());
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				if($clazz instanceof core_kernel_classes_Resource){
					$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($clazz->uriResource));
				}
				$this->setData('message', __('Delivery Class saved'));
				$this->setData('reload', true);
			}
		}
		$this->setData('formTitle', __('Edit delivery class'));
		$this->setData('myForm', $myForm->render());
		$this->setView('form.tpl');
	}
	
	/**
	 * Edit a delviery instance
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @return void
	 */
	public function editDelivery(){
		$clazz = $this->getCurrentClass();
		$delivery = $this->getCurrentDelivery();
		
		$formContainer = new tao_actions_form_Instance($clazz, $delivery);
		$myForm = $formContainer->getForm();
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				$propertyValues = $myForm->getValues();
				
				//check if the authoring mode has changed: if advanced->simple, modify the related process to make it compatible
				if(array_key_exists(TAO_DELIVERY_AUTHORINGMODE_PROP, $propertyValues)){
					if($propertyValues[TAO_DELIVERY_AUTHORINGMODE_PROP] == TAO_DELIVERY_SIMPLEMODE){
						if($delivery->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_AUTHORINGMODE_PROP))->uriResource == TAO_DELIVERY_ADVANCEDMODE){
							//get all tests from the process, then save them:
							$this->service->linearizeDeliveryProcess($delivery);
						}
					}
				}
				
				//then save the property values as usual
				$delivery = $this->service->bindProperties($delivery, $propertyValues);
				
				//edit process label:
				$this->service->updateProcessLabel($delivery);
				
				$this->setData('message', __('Delivery saved'));
				$this->setData('reload', true);
			}
		}
		$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($delivery->uriResource));
		
		//temporary disable authoring mode selection: 
		// $myForm->removeElement(tao_helpers_Uri::encode(TAO_DELIVERY_AUTHORINGMODE_PROP));
		
		//delivery authoring mode:
		$this->setData('authoringMode', 'simple');
		$authoringMode = $delivery->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_AUTHORINGMODE_PROP));
		
		if($authoringMode->uriResource == TAO_DELIVERY_ADVANCEDMODE){
			$this->setData('authoringMode', 'advanced');
		}else{
			//remove the authoring button
			$myForm->removeElement(tao_helpers_Uri::encode(TAO_DELIVERY_DELIVERYCONTENT));
			
			//the default option is the simple mode:
			$allTests = array();
			foreach($this->service->getAllTests() as $testUri => $testLabel){
				$allTests['test_'.tao_helpers_Uri::encode($testUri)] = $testLabel;
			}
			$this->setData('allTests', json_encode($allTests));
			
			$relatedTest = array();
			$testSequence = array();
			$i = 1;
			foreach($this->service->getDeliveryTests($delivery) as $test){
				$relatedTest[] = tao_helpers_Uri::encode($test->uriResource);
				if(!$test->isClass()){
					$testSequence[$i] = array(
						'uri' 	=> tao_helpers_Uri::encode($test->uriResource),
						'label' => $test->getLabel()
					);
					$i++;
				}
			}
			$this->setData('testSequence', $testSequence);
			
			$this->setData('relatedTests', json_encode($relatedTest));
		}
		
		//get the campaign(s) related to this delivery
		$relatedCampaigns = tao_helpers_Uri::encodeArray($this->service->getRelatedCampaigns($delivery), tao_helpers_Uri::ENCODE_ARRAY_VALUES);
		$this->setData('relatedCampaigns', json_encode($relatedCampaigns));
		
		//get the subjects related to the test(s) of the current delivery!	
		$excludedSubjects = tao_helpers_Uri::encodeArray($this->service->getExcludedSubjects($delivery), tao_helpers_Uri::ENCODE_ARRAY_VALUES);
		$this->setData('excludedSubjects', json_encode($excludedSubjects));
		
		//compilation state:
		$isCompiled = $this->service->isCompiled($delivery);
		$this->setData("isCompiled", $isCompiled);
		if($isCompiled){
			$compiledDate = $delivery->getLastModificationDate(new core_kernel_classes_Property(TAO_DELIVERY_COMPILED_PROP));
			$this->setData("compiledDate", $compiledDate->format('d/m/Y H:i:s'));
		}
		
		$this->setData('uri', tao_helpers_Uri::encode($delivery->uriResource));
		$this->setData('classUri', tao_helpers_Uri::encode($clazz->uriResource));
		$this->setData('formTitle', __('Edit delivery'));
		$this->setData('myForm', $myForm->render());
		$this->setView('form_delivery.tpl');
	}
		
	/**
	 * Add a delivery instance 
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}	 
	 * @return void
	 */
	public function addDelivery(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$clazz = $this->getCurrentClass();
		$delivery = $this->service->createInstance($clazz);
		
		if(!is_null($delivery) && $delivery instanceof core_kernel_classes_Resource){
			
			echo json_encode(array(
				'label'	=> $delivery->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($delivery->uriResource)
			));
		}
	}
	
	/**
	 * Add a delivery subclass
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @return void
	 */
	public function addDeliveryClass(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$clazz = $this->service->createDeliveryClass($this->getCurrentClass());
		if(!is_null($clazz) && $clazz instanceof core_kernel_classes_Class){
			echo json_encode(array(
				'label'	=> $clazz->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($clazz->uriResource)
			));
		}
	}
	
	/**
	 * Delete a delivery or a delivery class
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @return void
	 */
	public function delete(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$deleted = false;
		if($this->getRequestParameter('uri')){
			$deleted = $this->service->deleteDelivery($this->getCurrentDelivery());
		}
		else{
			$deleted = $this->service->deleteDeliveryClass($this->getCurrentClass());
		}
		
		echo json_encode(array('deleted'	=> $deleted));
	}
	
	/**
	 * Duplicate a delivery instance
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @return void
	 */
	public function cloneDelivery(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$clone = $this->service->cloneDelivery($this->getCurrentDelivery(), $this->getCurrentClass());
		if(!is_null($clone)){
			echo json_encode(array(
				'label'	=> $clone->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($clone->uriResource)
			));
		}
	}
	
	/**
	 * display the authoring  template
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}	 
	 * @return void
	 */
	public function authoring(){
		$this->setData('error', false);
		try{
			
			//get process instance to be authored
			 $delivery = $this->getCurrentDelivery();
			 $processDefinition = $delivery->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_DELIVERYCONTENT));
			// $processDefinition = new core_kernel_classes_Resource("http://127.0.0.1/middleware/demo.rdf#i1265636054002217401");		
			$this->setData('processUri', tao_helpers_Uri::encode($processDefinition->uriResource));
		}
		catch(Exception $e){
			$this->setData('error', true);
			$this->setData('errorMessage', $e);
		}
		$this->setView('authoring/process_authoring_tool.tpl');
		// $this->setView('process_authoring_tool_diagram.tpl');//template that integrate activities diagram
	}
	
	/**
	 * Get the data to populate the tree of delivery's subjects
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @return void
	 */
	public function getSubjects(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$options = array('chunk' => false);
		if($this->hasRequestParameter('classUri')) {
			$clazz = $this->getCurrentClass();
			$options['chunk'] = true;
		}
		else{
			$clazz = new core_kernel_classes_Class(TAO_SUBJECT_CLASS);
		}
		echo json_encode($this->service->toTree($clazz, $options));
	}
	
	/**
	 * Save the delivery excluded subjects
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @return void
	 */
	public function saveSubjects(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$saved = false;
		
		$subjects = array();
		foreach($this->getRequestParameters() as $key => $value){
			if(preg_match("/^instance_/", $key)){
				array_push($subjects, tao_helpers_Uri::decode($value));
			}
		}
		
		if($this->service->setExcludedSubjects($this->getCurrentDelivery(), $subjects)){
			$saved = true;
		}
		echo json_encode(array('saved'	=> $saved));
	}
	
	/**
	 * Get the data to populate the tree of delivery campaigns
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @return void
	 */
	public function getCampaigns(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$options = array('chunk' => false);
		if($this->hasRequestParameter('classUri')) {
			$clazz = $this->getCurrentClass();
			$options['chunk'] = true;
		}
		else{
			$clazz = new core_kernel_classes_Class(TAO_DELIVERY_CAMPAIGN_CLASS);
		}
		echo json_encode($this->service->toTree($clazz, $options));
	}
	
	/**
	 * Save the delivery related campaigns
	 * @access public
	 * @return void
	 */
	public function saveCampaigns(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$saved = false;
		
		$campaigns = array();
		foreach($this->getRequestParameters() as $key => $value){
			if(preg_match("/^instance_/", $key)){
				array_push($campaigns, tao_helpers_Uri::decode($value));
			}
		}
		
		if($this->service->setRelatedCampaigns($this->getCurrentDelivery(), $campaigns)){
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
		$this->setData('section',Session::getAttribute('currentSection'));
		$this->setView('index.tpl');
	}
	
	/**
	 * Render json data to populate the list of available delivery 
	 * It provides the value of the delivery properties such as label, uri and active and compiled status
	 * (Note: For the old implementation of delivery when 1 delivery = 1 test)
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @return void
	 */
	public function deliveryListing(){
		$allTestArray=$this->service->getTestClass()->getInstances(true);
		$testData=array();
		$i=0;
		foreach($allTestArray as $test){
		
			$testData[$i]=array();
			$testData[$i]["label"]=$test->getLabel();
			$testData[$i]["uri"]=$test->uriResource;
			$testData[$i]["id"]=tao_helpers_Uri::getUniqueId($test->uriResource);
			$testData[$i]["compiled"]=0;
			$testData[$i]["active"]=0;
			
			$isCompiled=$this->service->getTestStatus($test, "compiled");
			if($isCompiled){
				$testData[$i]["compiled"]=1;
				$testData[$i]["active"]=1;
			}else{
				//if not, check if it is active:
				$isActive=$this->service->getTestStatus($test, "active");
				if($isActive){
					$testData[$i]["active"]=1;
				}
			}
			$i++;
		}
		$result=array();
		$result["tests"]=$testData;
		echo json_encode($result);
	}
			
	/**
	 * Compile a test by providing its uri, via POST method.
	 * Its main purpose is to collect every required resource to run the test in a single folder so they become immediately available for the test launch, without any delay. 
	 * The resources are test and item runtime plugins and media files.
	 * This action parses the testContent and itemContent and save a copy of these files in the compiled test directory.
	 * The action compiles every available language for a given test at once.
	 * It provides a json string to indicate the success or failure of the test compilation
	 * The recompilation of an already compiled test will erase the previously created compiled files.
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @return void
	 */
	public function compile($uri){
		
		//get the unique id of the test to be compiled from POST
		// $testUri=$_POST["uri"];
		// $testId=tao_helpers_Uri::getUniqueId($testUri);
		
		$resultArray = array();
		
		if(empty($uri)){
			throw new Exception('no test uri given in compile action');
		}
		
		$resultArray = $this->service->compileTest($uri);
		
		echo json_encode($resultArray);
	}
		
	/**
	 * From the uri of a compiled test, this action will redirect the user to the compiled test folder
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @return void
	 */
	public function preview($testUri=''){
		//get encoded url
		$testUri=urldecode($_GET["uri"]);
		
		//firstly check if the delivery instance is compiled or not
		$aTestInstance = new core_kernel_classes_Resource($testUri);
		try{
			$testCompiled=$this->service->getTestStatus($aTestInstance, "compiled");
		}
		catch(Exception $e){ echo $e;}
		
		if($testCompiled){
			$testId=tao_helpers_Uri::getUniqueId($testUri);
			$testUrl=BASE_URL."/compiled/$testId/theTest.php?subject=previewer";
			header("location: $testUrl");
		}else{
			echo __("Sorry, the test seems not to be compiled.<br/> Please compile it then try again.");
		}
	}
	
	/**
	 * create history table
	 * @return void
	 */
	public function viewHistory(){
		
		$_SESSION['instances'] = array();
		foreach($this->getRequestParameters() as $key => $value){
			if(preg_match("/^uri_[0-9]+$/", $key)){
				$_SESSION['instances'][tao_helpers_Uri::decode($value)] = tao_helpers_Uri::decode($value);
			}
		}
		$this->setView("create_table.tpl");
	}
	
	/**
     * historyListing returns the execution history related to a given delivery (and subject)
     * 
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  string deliveryUri
	 * @param  string subjectUri
     * @return array
     */
	public function historyListing($deliveryUri = "", $subjectUri = ""){
		
		$returnValue = array();
		$historyCollection = null;
		
		//check deliveryUri validity
		if(empty($deliveryUri)){
			$currentDelivery = $this->getCurrentDelivery();
			if(is_null($currentDelivery)){
				//no need to throw en exception here because it has already be done in the getCurrentDelivery() function
				return $returnValue;
			}else{
				$deliveryUri = $currentDelivery->resourceUri;
			}
		}
		
		$historyCollection=$this->service->getHistory($deliveryUri, $subjectUri);
		
		$i=0;
		foreach ($historyCollection->getIterator() as $history) {
		
			$returnValue[$i]=array();
			
			if(!empty($subjectUri)){
				$subject=$history->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_HISTORY_SUBJECT_PROP));
				$returnValue[$i]["subject"] = $subject->getLabel(); //or $subject->literal to get the uri
			}
			$timestamp=$history->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_HISTORY_TIMESTAMP_PROP));
			$returnValue[$i]["timestamp"] = date('d-m-Y', $timestamp->literal);
			
			// $delivery=$history->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_HISTORY_DELIVERY_PROP));//useless
			
			$i++;
		}
			
		return $returnValue;
		//TODO: history listing per subject???
	}
		
	/*
	public function cache(){
		//get the id of subscribed modules and connect to them
		core_kernel_impl_ApiModelOO::getSubscription() : array(ids) NOT defined yet?
		core_kernel_impl_ApiModelOO::connectOnRemoteModule($idSubscription) : boolean NIY
		
		//get rdf file of these modules AND parse the rdf files (input: rdf dom object? or file location?)
		core_kernel_impl_ApiModelOO::exportXmlRdf() : String NIY

		//generate a new and unique namespace (thus new modelId) for the (each?) modules
		//store the triplets with this namespace:
		core_kernel_impl_ApiModelOO::importXmlRdf( java_lang_String $targetNameSpace,  java_lang_String $fileLocation) : Boolean NIY

		//save the reference to these new local namespaces ("s" for several modules) in the cache properties (one value for each cached module) of the 
		//q? given a delivery and a module, allow multiple cache? should be no, since delivery is aimed at being used within a limited time period
		
		//return the success/failure status of each distant module caching
	}
	*/
	
	/**
	 * services to render the delivery tests
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @return 
	 */
	public function getTests(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		if($this->hasRequestParameter('uri')){
			$tests = $this->service->getRelatedTests(
				$this->getCurrentInstance()
			);
			$this->setData('tests', $tests);
			$this->setView('deliveryTests.tpl');
		}
	}
	
	/**
	 * get all the tests instances in a json response
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @return void
	 */
	public function getDeliveriesTests(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$tests = tao_helpers_Uri::encodeArray($this->service->getDeliveriesTests(), tao_helpers_Uri::ENCODE_ARRAY_KEYS);
		echo json_encode(array('data' => $tests));
	}
	
	/**
	 * get all the tests instances in a json response
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @return void
	 */
	public function getAllTests(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$options = array('chunk' => false);
		if($this->hasRequestParameter('classUri')) {
			$clazz = $this->getCurrentClass();
			$options['chunk'] = true;
		}
		else{
			$clazz = new core_kernel_classes_Class(TAO_TEST_CLASS);
		}
		echo json_encode($this->service->toTree($clazz, $options));
	}
	
	/**
	 * Save the delivery related tests
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @return void
	 */
	public function saveTests(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$saved = false;
		
		$tests = array();
		foreach($this->getRequestParameters() as $key => $value){
			if(preg_match("/^instance_/", $key)){
				array_push($tests, new core_kernel_classes_Resource(tao_helpers_Uri::decode($value)));
			}
		}
		if($this->service->setDeliveryTests($this->getCurrentDelivery(), $tests)){
			$saved = true;
		}
		echo json_encode(array('saved'	=> $saved));
	}
	
	/**
	 * get the compilation view
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @return void
	 */
	public function compileView(){
	
		$delivery = $this->getCurrentDelivery();
		
		$this->setData("processUri", tao_helpers_Uri::encode($delivery->uriResource));//currentprocess
		$this->setData("processLabel", $delivery->getLabel());
		$this->setData("deliveryClass", tao_helpers_Uri::encode($this->getCurrentClass()->uriResource));
		
		//compilation state:
		$isCompiled = $this->service->isCompiled($delivery);
		$this->setData("isCompiled", $isCompiled);
		if($isCompiled){
			$compiledDate = $delivery->getLastModificationDate(new core_kernel_classes_Property(TAO_DELIVERY_COMPILED_PROP));
			$this->setData("compiledDate", $compiledDate->format('d/m/Y H:i:s'));
		}
		
		$this->setView("delivery_compiling.tpl");
	}
	
	/**
	 * get the compilation view
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @return void
	 */
	public function initCompilation(){
		
		$delivery = $this->getCurrentDelivery();
		
		//init the value to be returned	
		$deliveryData=array();
		
		$deliveryData["uri"] = $delivery->uriResource;
		
		//check if a wsdl contract is set to upload the result:
		$resultServer = $this->service->getResultServer($delivery);
		$deliveryData['resultServer'] = $resultServer;
		
		$deliveryData['tests'] = array();
		if(!empty($resultServer)){//a "valid" wsdl contract has been found
		//TODO: check validity of the wsdl
		
			//get the tests list from the delivery id: likely, by parsing the deliveryContent property value
			//array of resource, test set
			$tests = array();
			$tests = $this->service->getRelatedTests($delivery);
			
			foreach($tests as $test){
				$deliveryData['tests'][] = array(
					"label" => $test->getLabel(),
					"uri" => $test->uriResource
				);//url encode maybe?
			}
		}
		
		echo json_encode($deliveryData);
	}
	
	/**
	 * End the compilation of a delivery
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @return void
	 */
	public function endCompilation(){
	
		$delivery = $this->getCurrentDelivery();
		
		$response = array();
		$response["result"]=0;
		
		if ($delivery->editPropertyValues(new core_kernel_classes_Property(TAO_DELIVERY_COMPILED_PROP), GENERIS_TRUE)){
			$response["result"] = 1;
			$response["compiledDate"] = $delivery->getLastModificationDate(new core_kernel_classes_Property(TAO_DELIVERY_COMPILED_PROP))->format('d/m/Y H:i:s');
			
		}
		
		echo json_encode($response);
	}
	
}
?>