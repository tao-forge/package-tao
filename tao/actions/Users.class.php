<?php
/**
 * This controller provide the actions to manage the application users (list/add/edit/delete)
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package tao
 * @subpackage action
 *
 */
class Users extends CommonModule {

	protected $userService = null;
	
	/**
	 * Constructor performs initializations actions
	 * @return void
	 */
	public function __construct(){		
		
    	$this->userService = tao_models_classes_ServiceFactory::get('tao_models_classes_UserService');
		$this->defaultData();
	}

	/**
	 * Show the list of users
	 * @return void
	 */
	public function index(){
		$this->setData('data', __('list the users'));
		$this->setView('user/list.tpl');
	}
	
	/**
	 * provide the user list data via json
	 * @return void
	 */
	public function data(){
		$page = $this->getRequestParameter('page'); 
		$limit = $this->getRequestParameter('rows'); 
		$sidx = $this->getRequestParameter('sidx');  
		$sord = $this->getRequestParameter('sord'); 
		$start = $limit * $page - $limit; 
		
		if(!$sidx) $sidx =1; 
		
		$users = $this->userService->getAllUsers(array(
			'order' 	=> $sidx,
			'orderDir'	=> $sord,
			'start'		=> $start,
			'end'		=> $limit
		));
		
		$count = count($users); 
		if( $count >0 ) { 
			$total_pages = ceil($count/$limit); 
		} 
		else { 
			$total_pages = 0; 
		} 
		if ($page > $total_pages){
			$page = $total_pages; 
		}
		
		$loginProperty 		= new core_kernel_classes_Property(PROPERTY_USER_LOGIN);
		$firstNameProperty 	= new core_kernel_classes_Property(PROPERTY_USER_FIRTNAME);
		$lastNameProperty 	= new core_kernel_classes_Property(PROPERTY_USER_LASTNAME);
		$mailProperty 		= new core_kernel_classes_Property(PROPERTY_USER_MAIL);
		$deflgProperty 		= new core_kernel_classes_Property(PROPERTY_USER_DEFLG);
		$uilgProperty 		= new core_kernel_classes_Property(PROPERTY_USER_UILG);
		
		$response = new stdClass();
		$response->page = $page; 
		$response->total = $total_pages; 
		$response->records = $count; 
		$i = 0; 
		foreach($users as $user) { 
			$login 		= (string)$user->getUniquePropertyValue($loginProperty);
			$firstName 	= (string)$user->getOnePropertyValue($firstNameProperty);
			$lastName 	= (string)$user->getOnePropertyValue($lastNameProperty);
			$email 		= (string)$user->getOnePropertyValue($mailProperty);
			$defLg 		= $user->getOnePropertyValue($deflgProperty);
			$uiLg 		= $user->getOnePropertyValue($uilgProperty);
			
			$defaultLang = '';
			if(!is_null($defLg)){
				$defaultLang = __($defLg->getLabel());
			}
			$uiLang = '';
			if(!is_null($uiLg)){
				$uiLang = __($uiLg->getLabel());
			}
			
			$response->rows[$i]['id']= tao_helpers_Uri::encode($user->uriResource);
			$response->rows[$i]['cell']= array(
				$login,
				$firstName.' '.$lastName,
				$email,
				$defaultLang,
				$uiLang,
				''
			);
			$i++;
		} 
		echo json_encode($response); 
	}
	
	/**
	 * Remove a user
	 * The request must contains the user's login to remove
	 * @return vois
	 */
	public function delete(){
		$message = __('An error occured during user deletion');
		if($this->hasRequestParameter('uri')){
			$user = new core_kernel_classes_Resource(tao_helpers_Uri::decode($this->getRequestParameter('uri')));
			if($this->userService->removeUser($user)){
				$message = __('User deleted successfully');
			}
		}
		$this->redirect(_url('index', 'Main', 'tao', array('extension' => 'users', 'message' => $message)));
	}
	
	/**
	 * form to add a user
	 * @return void
	 */
	public function add(){
		
		$myFormContainer = new tao_actions_form_Users(array(), array('mode' => 'add'));
		$myForm = $myFormContainer->getForm();
		
		if($myForm->isSubmited()){
			
			if($myForm->isValid()){
				$values = $myForm->getValues();
				$values['password'] = md5($values['password1']);
				unset($values['password1']);
				unset($values['password2']);
				
				
				if($this->userService->saveUser($values)){
					$this->setData('message', __('User added'));
					$this->setData('exit', true);
				}
			}
		}
		$this->setData('formTitle', __('Add a user'));
		$this->setData('myForm', $myForm->render());
		$this->setView('user/form.tpl');
	}
	
	/**
	 * Form to edit a user
	 * User login must be set in parameter
	 * @return  void
	 */
	public function edit(){
		
		if(!$this->hasRequestParameter('uri')){
			throw new Exception('Please set the user uri in request parameter');
		}
		
		$user = new core_kernel_classes_Resource(tao_helpers_Uri::decode($this->getRequestParameter('uri')));
		
		$myFormContainer = new tao_actions_form_Users($this->userService->getClass($user), $user);
		$myForm = $myFormContainer->getForm();
		
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				$values = $myForm->getValues();
				
				if(!empty($values['password1']) && !empty($values['password2'])){
					$values['password'] = md5($values['password2']);
				}
				else{
					$values['password'] = $values['password0'];
				}
				unset($values['password0']);
				unset($values['password1']);
				unset($values['password2']);
				unset($values['password3']);
				
				if($this->userService->saveUser($values)){
					$this->setData('message', __('User saved'));
					$this->setData('exit', true);
				}
			}
		}
		
		$this->setData('formTitle', __('Edit a user'));
		$this->setData('myForm', $myForm->render());
		$this->setView('user/form.tpl');
	}
	
}
?>