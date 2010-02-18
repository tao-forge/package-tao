<?php
require_once('tao/actions/CommonModule.class.php');
require_once('tao/actions/TaoModule.class.php');

/**
 * Groups Controller provide actions performed from url resolution
 * 
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoGroups
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class Groups extends TaoModule {

	/**
	 * constructor: initialize the service and the default data
	 * @return Groups
	 */
	public function __construct(){
		
		parent::__construct();
		
		//the service is initialized by default
		$this->service = tao_models_classes_ServiceFactory::get('Groups');
		$this->defaultData();
	}
	
/*
 * conveniance methods
 */
	
	/**
	 * get the selected group from the current context (from the uri and classUri parameter in the request)
	 * @return core_kernel_classes_Resource $group
	 */
	protected function getCurrentInstance(){
		$uri = tao_helpers_Uri::decode($this->getRequestParameter('uri'));
		if(is_null($uri) || empty($uri)){
			throw new Exception("No valid uri found");
		}
		
		$clazz = $this->getCurrentClass();
		$group = $this->service->getGroup($uri, 'uri', $clazz);
		if(is_null($group)){
			throw new Exception("No group found for the uri {$uri}");
		}
		
		return $group;
	}
	
	/**
	 * get the main class
	 * @return core_kernel_classes_Classes
	 */
	protected function getRootClass(){
		return $this->service->getGroupClass();
	}
	
/*
 * controller actions
 */
	
	/**
	 * Edit a group class
	 * @see tao_helpers_form_GenerisFormFactory::classEditor
	 * @return void
	 */
	public function editGroupClass(){
		$clazz = $this->getCurrentClass();
		$myForm = $this->editClass($clazz, $this->service->getGroupClass());
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				if($clazz instanceof core_kernel_classes_Resource){
					$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($clazz->uriResource));
				}
				$this->setData('message', __('Class saved'));
				$this->setData('reload', true);
			}
		}
		$this->setData('formTitle', __('Edit group class'));
		$this->setData('myForm', $myForm->render());
		$this->setView('form.tpl', true);
	}
	
	/**
	 * Edit a group instance
	 * @see tao_helpers_form_GenerisFormFactory::instanceEditor
	 * @return void
	 */
	public function editGroup(){
		$clazz = $this->getCurrentClass();
		$group = $this->getCurrentInstance();
		$myForm = tao_helpers_form_GenerisFormFactory::instanceEditor($clazz, $group);
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				
				$group = $this->service->bindProperties($group, $myForm->getValues());
				
				$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($group->uriResource));
				$this->setData('message', __('Group saved'));
				$this->setData('reload', true);
			}
		}
		
		$relatedSubjects = $this->service->getRelatedSubjects($group);
		$relatedSubjects = array_map("tao_helpers_Uri::encode", $relatedSubjects);
		$this->setData('relatedSubjects', json_encode($relatedSubjects));
		
		$relatedTests = $this->service->getRelatedTests($group);
		$relatedTests = array_map("tao_helpers_Uri::encode", $relatedTests);
		$this->setData('relatedTests', json_encode($relatedTests));
		
		$this->setData('formTitle', 'Edit group');
		$this->setData('myForm', $myForm->render());
		$this->setView('form_group.tpl');
	}
	
	
	/**
	 * Add a group subclass
	 * @return void
	 */
	public function addGroupClass(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$clazz = $this->service->createGroupClass($this->getCurrentClass());
		if(!is_null($clazz) && $clazz instanceof core_kernel_classes_Class){
			echo json_encode(array(
				'label'	=> $clazz->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($clazz->uriResource)
			));
		}
	}
	
	/**
	 * Delete a group or a group class
	 * @return void
	 */
	public function delete(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$deleted = false;
		if($this->getRequestParameter('uri')){
			$deleted = $this->service->deleteGroup($this->getCurrentInstance());
		}
		else{
			$deleted = $this->service->deleteGroupClass($this->getCurrentClass());
		}
		
		echo json_encode(array('deleted'	=> $deleted));
	}
	
	
	/**
	 * Get the data to populate the tree of group's subjects
	 * @return void
	 */
	public function getMembers(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		echo json_encode($this->service->toTree( new core_kernel_classes_Class(TAO_SUBJECT_CLASS), true, true, ''));
	}
	
	/**
	 * Save the group related subjects
	 * @return void
	 */
	public function saveMembers(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$saved = false;
		
		$members = array();
		foreach($this->getRequestParameters() as $key => $value){
			if(preg_match("/^instance_/", $key)){
				array_push($members, tao_helpers_Uri::decode($value));
			}
		}
		$group = $this->getCurrentInstance();
		
		if($this->service->setRelatedSubjects($group, $members)){
			$saved = true;
		}
		echo json_encode(array('saved'	=> $saved));
	}
	
	/**
	 * Get the data to populate the tree of group's tests
	 * @return void
	 */
	public function getTests(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		echo json_encode($this->service->toTree( new core_kernel_classes_Class(TAO_TEST_CLASS), true, true, ''));
	}
	
	/**
	 * Save the group related subjects
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
				array_push($tests, tao_helpers_Uri::decode($value));
			}
		}
		$group = $this->getCurrentInstance();
		
		if($this->service->setRelatedTests($group, $tests)){
			$saved = true;
		}
		echo json_encode(array('saved'	=> $saved));
	}
	
	
}
?>