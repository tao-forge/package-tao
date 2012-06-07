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
class tao_actions_Users extends tao_actions_CommonModule {

	/**
	 * @var tao_models_classes_UserService
	 */
	protected $userService = null;

	/**
	 * Constructor performs initializations actions
	 * @return void
	 */
	public function __construct(){
		parent::__construct();
		$this->userService = tao_models_classes_UserService::singleton();
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
		$searchField = $this->getRequestParameter('searchField');
		$searchOper = $this->getRequestParameter('searchOper');
		$searchString = $this->getRequestParameter('searchString');
		$start = $limit * $page - $limit;

		if (!$sidx) $sidx = 1;
		$gau = array(
			'order' 	=> $sidx,
			'orderDir'	=> $sord,
			'start'		=> $start,
			'end'		=> $limit
		);
		if (!is_null($searchField)) {
		  $gau['search'] = array(
		      'field' => $searchField,
			  'op' => $searchOper,
			  'string' => $searchString
		  );
		}
		$users = $this->userService->getAllUsers($gau);

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
		$i = 0;
		foreach($users as $user) {
			$cellData = array();

			$cellData[0]		= (string)$user->getUniquePropertyValue($loginProperty);

			$firstName 		= (string)$user->getOnePropertyValue($firstNameProperty);
			$lastName 		= (string)$user->getOnePropertyValue($lastNameProperty);
			$cellData[1]	= $firstName.' '.$lastName;

			$cellData[2] 	= (string)$user->getOnePropertyValue($mailProperty);

			$types = $user->getTypes();
			$labels = array();
			foreach ($types as $uri => $r) $labels[] = $r->getLabel();
			$cellData[3] = implode(', ', $labels);

			$defLg 			= $user->getOnePropertyValue($deflgProperty);
			$cellData[4] 	= '';
			if(!is_null($defLg)){
				if($defLg instanceof core_kernel_classes_Literal){
					$cellData[4] = __((string)$defLg);
				}
				if($defLg instanceof core_kernel_classes_Resource){
					$cellData[4] = __($defLg->getLabel());
				}
			}
			$uiLg 			= $user->getOnePropertyValue($uilgProperty);
			$cellData[5] 	= '';
			if(!is_null($uiLg)){
				if($uiLg instanceof core_kernel_classes_Literal){
					$cellData[5] = __((string)$uiLg);
				}
				if($uiLg instanceof core_kernel_classes_Resource){
					$cellData[5] = __($uiLg->getLabel());
				}
			}

			$cellData[6]	= '';

			$response->rows[$i]['id']= tao_helpers_Uri::encode($user->uriResource);
			$response->rows[$i]['cell'] = $cellData;
			$i++;
		}

		//Like class.UserService.php:130 (getAllUsers)
		$userClass = new core_kernel_classes_Class(CLASS_GENERIS_USER);
		$types = array();
		foreach ($this->userService->getAllowedRoles() as $roleUri) {
			$types[] = $roleUri;
		}

		$opts = array('recursive' => 0, 'like' => false);
		$opts['offset'] = $start;
		$opts['limit'] = $limit;
		$opts['additionalClasses'] = $types;
		$counti = $userClass->countInstances(array(PROPERTY_USER_LOGIN => '*'), $opts);

		$response->page = $page;
		$response->total = ceil($counti / $limit);//$total_pages;
		$response->records = count($users);

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
		$this->redirect(_url('index', 'Main', 'tao', array('structure' => 'users', 'message' => $message)));
	}

	/**
	 * form to add a user
	 * @return void
	 */
	public function add(){

		$myFormContainer = new tao_actions_form_Users(new core_kernel_classes_Class(CLASS_ROLE_TAOMANAGER));
		$myForm = $myFormContainer->getForm();

		if($myForm->isSubmited()){
			if($myForm->isValid()){
				$values = $myForm->getValues();
				$values[PROPERTY_USER_PASSWORD] = md5($values['password1']);
				unset($values['password1']);
				unset($values['password2']);

				if($this->userService->saveUser($myFormContainer->getUser(), $values)){
					$this->setData('message', __('User added'));
					$this->setData('exit', true);
				}
			}
		}
		$this->setData('loginUri', tao_helpers_Uri::encode(PROPERTY_USER_LOGIN));
		$this->setData('formTitle', __('Add a user'));
		$this->setData('myForm', $myForm->render());
		$this->setView('user/form.tpl');
	}

	/**
	 * action used to check if a login can be used
	 * @return void
	 */
	public function checkLogin(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}

		$data = array('available' => false);
		if($this->hasRequestParameter('login')){
			$data['available'] = $this->userService->loginAvailable($this->getRequestParameter('login'));
		}
		echo json_encode($data);
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
					$values[PROPERTY_USER_PASSWORD] = md5($values['password2']);
				}

				unset($values['password0']);
				unset($values['password1']);
				unset($values['password2']);
				unset($values['password3']);

				if(!preg_match("/[A-Z]{2,4}$/", trim($values[PROPERTY_USER_UILG]))){
					unset($values[PROPERTY_USER_UILG]);
				}
				if(!preg_match("/[A-Z]{2,4}$/", trim($values[PROPERTY_USER_DEFLG]))){
					unset($values[PROPERTY_USER_DEFLG]);
				}

				if($this->userService->saveUser($user, $values)){
					$this->setData('message', __('User saved'));
					$this->setData('exit', true);
				}
			}
		}

		$this->setData('formTitle', __('Edit a user'));
		$this->setData('Roles', json_encode(""));
		$this->setData('myForm', $myForm->render());
		$this->setView('user/form.tpl');
	}
}
?>