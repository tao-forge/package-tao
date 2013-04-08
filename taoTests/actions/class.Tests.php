<?php
/*  
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
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php

/**
 * Tests Controller provide actions performed from url resolution
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoTests
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 *
 */
class taoTests_actions_Tests extends tao_actions_TaoModule {

	/**
	 * constructor: initialize the service and the default data
	 */
	public function __construct()
	{

		parent::__construct();

		//the service is initialized by default
		$this->service = taoTests_models_classes_TestsService::singleton();
		$this->defaultData();
	}

/*
 * conveniance methods
 */

	/**
	 * get the main class
	 * @return core_kernel_classes_Classes
	 */
	protected function getRootClass()
	{
		return $this->service->getTestClass();
	}


/*
 * controller actions
 */


	/**
	 * edit a test instance
	 * @return void
	 */
	public function editTest()
	{
		$clazz = $this->getCurrentClass();
		$test = $this->getCurrentInstance();

		$formContainer = new tao_actions_form_Instance($clazz, $test);
		$myForm = $formContainer->getForm();
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				$propertyValues = $myForm->getValues();

				//check if the authoring mode has changed: if advanced->simple, modify the related process to make it compatible
				if(array_key_exists(TAO_TEST_AUTHORINGMODE_PROP, $propertyValues)){
					if($propertyValues[TAO_TEST_AUTHORINGMODE_PROP] == TAO_TEST_SIMPLEMODE){
						if($test->getUniquePropertyValue(new core_kernel_classes_Property(TAO_TEST_AUTHORINGMODE_PROP))->uriResource == TAO_TEST_ADVANCEDMODE){
							//get all tests from the process, then save them:
							$this->service->linearizeTestProcess($test);
						}
					}
				}

				//then save the property values as usual
				$binder = new tao_models_classes_dataBinding_GenerisFormDataBinder($test);
				$test = $binder->bind($propertyValues);

				//edit process label:
				$this->service->updateProcessLabel($test);

				$this->setData('message', __('Test saved'));
				$this->setData('reload', true);
			}
		}
		$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($test->uriResource));

		//test authoring mode:
		$this->setData('authoringMode', 'simple');
		$authoringMode = $test->getUniquePropertyValue(new core_kernel_classes_Property(TAO_TEST_AUTHORINGMODE_PROP));
		$myForm->removeElement(tao_helpers_Uri::encode(TAO_TEST_AUTHORINGMODE_PROP));

		if($authoringMode->uriResource == TAO_TEST_ADVANCEDMODE){
			$this->setData('authoringMode', 'advanced');
		}else{
			//remove the authoring button
			$myForm->removeElement(tao_helpers_Uri::encode(TEST_TESTCONTENT_PROP));
			
			$itemSequence = array();
			$itemUris = array();
			$i = 1;
			foreach($this->service->getTestItems($test) as $item){
				$itemUris[] = $item->getUri();
				$itemSequence[$i] = array(
					'uri' 	=> tao_helpers_Uri::encode($item->getUri()),
					'label' => $item->getLabel()
				);
				$i++;
			}

			// data for item sequence
			$allItems = array();
			foreach($this->service->getAllItems() as $itemUri => $itemLabel){
				$allItems['item_'.tao_helpers_Uri::encode($itemUri)] = $itemLabel;
			}
			$this->setData('allItems', json_encode($allItems));
			$this->setData('itemSequence', $itemSequence);
			
			// data for generis tree form
			$this->setData('relatedItems', json_encode(tao_helpers_Uri::encodeArray($itemUris)));
			$openNodes = tao_models_classes_GenerisTreeFactory::getNodesToOpen($itemUris, new core_kernel_classes_Class(TAO_ITEM_CLASS));
			$this->setData('itemRootNode', TAO_ITEM_CLASS);
			$this->setData('itemOpenNodes', $openNodes);
		}

		$this->setData('uri', tao_helpers_Uri::encode($test->getUri()));
		$this->setData('classUri', tao_helpers_Uri::encode($clazz->getUri()));
		$this->setData('formTitle', __('Test properties'));
		$this->setData('myForm', $myForm->render());
		$this->setView('form_test.tpl');
	}

	/**
	 * add a test (subclass Test)
	 * @return void
	 */
	public function addTestClass()
	{
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
	public function editTestClass()
	{
		$clazz = $this->getCurrentClass();

		if($this->hasRequestParameter('property_mode')){
			$this->setSessionAttribute('property_mode', $this->getRequestParameter('property_mode'));
		}

		$myForm = $this->editClass($clazz, $this->service->getTestClass());
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				if($clazz instanceof core_kernel_classes_Resource){
					$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($clazz->uriResource));
				}
				$this->setData('message', __('Class saved'));
				$this->setData('reload', true);
			}
		}
		$this->setData('formTitle', __('Edit test class'));
		$this->setData('myForm', $myForm->render());
		$this->setView('form.tpl');
	}

	/**
	 * delete a test or a test class
	 * called via ajax
	 * @return void
	 */
	public function delete()
	{
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}

		$deleted = false;
		if($this->getRequestParameter('uri')){
			$deleted = $this->service->deleteTest($this->getCurrentInstance());
		}
		else{
			$deleted = $this->service->deleteTestClass($this->getCurrentClass());
		}

		echo json_encode(array('deleted'	=> $deleted));
	}



	/**
	 * display the authoring  template
	 * @return void
	 */
	public function authoring()
	{
		$this->setData('error', false);
		try{

			//get process instance to be authored
			 $test = $this->getCurrentInstance();
			 $processDefinition = $test->getUniquePropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP));
			$this->setData('processUri', tao_helpers_Uri::encode($processDefinition->uriResource));
		}
		catch(Exception $e){
			$this->setData('error', true);
			$this->setData('errorMessage', $e);
		}
		$this->setView('authoring/process_authoring_tool.tpl');
	}

	/**
	 * get the list of items to populate the checkbox tree of related items
	 * @return void
	 */
	public function getItems()
	{
		if (!tao_helpers_Request::isAjax()) {
			throw new Exception("wrong request mode");
		}
		$options = array('chunk' => false);
		if ($this->hasRequestParameter('classUri')) {
			$clazz = $this->getCurrentClass();
			$options['chunk'] = true;
		} else {
			$clazz = new core_kernel_classes_Class(TAO_ITEM_CLASS);
		}
		if ($this->hasRequestParameter('selected')) {
			$selected = $this->getRequestParameter('selected');
			if (!is_array($selected)) {
				$selected = array($selected);
			}
			$options['browse'] = $selected;
		}
		if ($this->hasRequestParameter('offset')) {
			$options['offset'] = $this->getRequestParameter('offset');
		}
		if ($this->hasRequestParameter('limit')) {
			$options['limit'] = $this->getRequestParameter('limit');
		}
		if ($this->hasRequestParameter('subclasses')) {
			$options['subclasses'] = $this->getRequestParameter('subclasses');
		}
		echo json_encode($this->service->toTree($clazz, $options));
	}

	/**
	 * save the related items from the checkbox tree
	 * @return void
	 */
	public function saveItems()
	{
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$saved = false;

		$items = array();
		foreach($this->getRequestParameters() as $key => $value){
			if(preg_match("/^instance_/", $key)){
				$item = new core_kernel_classes_Resource(tao_helpers_Uri::decode($value));
				if ($item->isInstanceOf(new core_kernel_classes_Class(TAO_ITEM_CLASS))) {
					$itemModel = $item->getOnePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY));
					$supported = false;
					if (!is_null($itemModel)) {
						foreach ($itemModel->getPropertyValues(new core_kernel_classes_Property(TAO_ITEM_MODELTARGET_PROPERTY)) as $targeturi) {
							if ($targeturi == TAO_ITEM_ONLINE_TARGET) {
								$supported = true;
								break;
							}
						}
					}
					if ($supported) {
						array_push($items, $item);
					} else {
						throw new common_Exception($item->getLabel().' cannot be added to a test');
					}
				} else {
					// work around for bug in treeview form
					// @todo remove once treeview is rewritten
					common_Logger::w('Tried to add non Item to test');
				}
			}
		}
		if($this->service->setTestItems($this->getCurrentInstance(), $items)){
			$saved = true;
		}
		echo json_encode(array('saved'	=> $saved));
	}

	public function advancedMode()
	{
		$this->setAuthoringMode('advanced');
	}

	public function simpleMode()
	{
		$this->setAuthoringMode('simple');
	}

	private function setAuthoringMode($mode)
	{
		$mode = strtolower($mode);
		if($mode != 'simple' && $mode != 'advanced'){
			throw new Exception('invalid mode');
		}

		$test = $this->getCurrentInstance();
		$clazz = $this->getCurrentClass();

		$this->service->setAuthoringMode($test, $mode);

		$param = array(
			'uri' => tao_helpers_Uri::encode($test->uriResource),
			'classUri' => tao_helpers_Uri::encode($clazz->uriResource)
		);

		//reload the form, thus let the advanced authoring tab be available
		$this->redirect(tao_helpers_Uri::url('editTest', 'Tests', null, $param));
	}
}
?>