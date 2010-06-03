<?php
require_once('tao/actions/CommonModule.class.php');
require_once('tao/actions/TaoModule.class.php');

/**
 * Subjects Controller provide actions performed from url resolution
 * 
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoSubjects
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * 
 */

class Subjects extends TaoModule {

	/**
	 * constructor: initialize the service and the default data
	 * @return Subjects
	 */
	public function __construct(){
		
		parent::__construct();
		
		//the service is initialized by default
		$this->service = tao_models_classes_ServiceFactory::get('Subjects');
		$this->defaultData();
	}
	
/*
 * conveniance methods
 */
	
	/**
	 * get the instancee of the current subject regarding the 'uri' and 'classUri' request parameters
	 * @return core_kernel_classes_Resource the subject instance
	 */
	protected function getCurrentInstance(){
		
		$uri = tao_helpers_Uri::decode($this->getRequestParameter('uri'));
		if(is_null($uri) || empty($uri)){
			throw new Exception("No valid uri found");
		}
		
		$clazz = $this->getCurrentClass();
		
		$subject = $this->service->getSubject($uri, 'uri', $clazz);
		if(is_null($subject)){
			throw new Exception("No subject found for the uri {$uri}");
		}
		
		return $subject;
	}
	
	/**
	 * get the main class
	 * @return core_kernel_classes_Classes
	 */
	protected function getRootClass(){
		return $this->service->getSubjectClass();
	}
	
/*
 * controller actions
 */

	
	/**
	 * edit an subject instance
	 */
	public function editSubject(){
		$clazz = $this->getCurrentClass();
		$subject = $this->getCurrentInstance();
		
		$addMode = false;
		$login = (string)$subject->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_USER_LOGIN));
		if(empty($login)){
			$addMode = true;
			$this->setData('loginUri', tao_helpers_Uri::encode(PROPERTY_USER_LOGIN));
		}
		
		$myFormContainer = new tao_actions_form_Users($clazz, $subject, $addMode);
		$myForm = $myFormContainer->getForm();
		
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				$values = $myForm->getValues();
				
				if($addMode){
					$values[PROPERTY_USER_PASSWORD] = md5($values['password1']);
					unset($values['password1']);
					unset($values['password2']);
				}
				else{
					if(!empty($values['password1']) && !empty($values['password2'])){
						$values[PROPERTY_USER_PASSWORD] = md5($values['password2']);
					}
					
					unset($values['password0']);
					unset($values['password1']);
					unset($values['password2']);
					unset($values['password3']);
				}
				
				$subject = $this->service->bindProperties($subject, $myForm->getValues());
				
				$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($subject->uriResource));
				
				$message = __('Testee saved');
				
				if($addMode){
					$params =  array(
						'uri' 		=> tao_helpers_Uri::encode($subject->uriResource),
						'classUri' 	=> tao_helpers_Uri::encode($clazz->uriResource),
						'message'	=> $message
					);
					$this->redirect(_url('editSubject', null, null, $params));
				}
				
				$this->setData('message', $message);
				$this->setData('reload',  true);
				
			}
		}
		
		
		$this->setData('subjectGroups', json_encode(array_map("tao_helpers_Uri::encode", $this->service->getSubjectGroups($subject))));
		
		$this->setData('checkLogin', $addMode);
		$this->setData('formTitle', __('Edit subject'));
		$this->setData('myForm', $myForm->render());
		$this->setView('form_subjects.tpl');
	}
	
	/**
	 * add a subject model (subclass Subject)
	 */
	public function addSubjectClass(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$clazz = $this->service->createSubjectClass($this->getCurrentClass());
		if(!is_null($clazz) && $clazz instanceof core_kernel_classes_Class){
			echo json_encode(array(
				'label'	=> $clazz->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($clazz->uriResource)
			));
		}
	}
	
	/**
	 * Edit a subject model (edit a class)
	 */
	public function editSubjectClass(){
		$clazz = $this->getCurrentClass();
		$myForm = $this->editClass($clazz, $this->service->getSubjectClass());
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				if($clazz instanceof core_kernel_classes_Resource){
					$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($clazz->uriResource));
				}
				$this->setData('message', __('Class saved'));
				$this->setData('reload', true);
			}
		}
		$this->setData('formTitle', __('Edit subject class'));
		$this->setData('myForm', $myForm->render());
		$this->setView('form.tpl', true);
	}
	
	/**
	 * delete a subject or a subject model
	 * called via ajax
	 */
	public function delete(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$deleted = false;
		if($this->getRequestParameter('uri')){
			$deleted = $this->service->deleteSubject($this->getCurrentInstance());
		}
		else{
			$deleted = $this->service->deleteSubjectClass($this->getCurrentClass());
		}
		
		echo json_encode(array('deleted'	=> $deleted));
	}
	
	
	/**
	 * get the list of groups to populate the checkbox tree of groups to link with
	 * @return void
	 */
	public function getGroups(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		echo json_encode($this->service->toTree( new core_kernel_classes_Class(TAO_GROUP_CLASS), true, true, ''));
	}
	
	/**
	 * save from the checkbox tree the groups to link with 
	 * @return void
	 */
	public function saveGroups(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$saved = false;
		$groups = array();
		foreach($this->getRequestParameters() as $key => $value){
			if(preg_match("/^instance_/", $key)){
				array_push($groups, tao_helpers_Uri::decode($value));
			}
		}
		$subject = $this->getCurrentInstance();
		
		if($this->service->setSubjectGroups($subject, $groups)){
			$saved = true;
		}
		echo json_encode(array('saved'	=> $saved));
	}
	
}
?>