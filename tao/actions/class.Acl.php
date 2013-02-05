<?php
/**
 * This controller provide the actions to manage the ACLs
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package tao
 * @subpackage actions
 *
 */
class tao_actions_Acl extends tao_actions_CommonModule {

	/**
	 * Constructor performs initializations actions
	 * @return void
	 */
	public function __construct(){
		parent::__construct();
		$this->defaultData();
	}

	/**
	 * Show the list of roles
	 * @return void
	 */
	public function index(){
		$rolesc = new core_kernel_classes_Class(CLASS_ROLE);
		$roles = array();
		foreach ($rolesc->getInstances(true) as $id => $r) {
			$roles[] = array('id' => $id, 'label' => $r->getLabel());
		}

		$this->setData('roles', $roles);
		$this->setView('acl/list.tpl');
	}

	public function getModules() {
		if (!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		else{
			$role = new core_kernel_classes_Class($this->getRequestParameter('role'));
			$profile = array();
			
			$extManager = common_ext_ExtensionsManager::singleton();
			$extensions = $extManager->getInstalledExtensions();
			$accessService = tao_models_classes_funcACL_AccessService::singleton();
			
			foreach ($extensions as $extId => $ext){
				$atLeastOneAccess = false;
				
				$profile[$extId] = array('modules' => array(), 
										 'has-access' => false,
										 'has-allaccess' => false, 
										 'uri' => $accessService->makeEMAUri($extId));
				
				foreach (tao_helpers_funcACL_Model::getModules($extId) as $modUri => $module){
					$moduleAccess = tao_helpers_funcACL_Cache::retrieveModule($module);
					$uri = explode('#', $modUri);
					list($type, $extId, $modId) = explode('_', $uri[1]);
					
					$profile[$extId]['modules'][$modId] = array('has-access' => false,
													 'has-allaccess' => false,
													 'uri' => $module->getUri());
					
					if (true === in_array($role->getUri(), $moduleAccess['module'])){
						$profile[$extId]['modules'][$modId]['has-allaccess'] = true;
						$atLeastOneAccess = true;
					}
					else {
						// have a look at actions.
						foreach ($moduleAccess['actions'] as $roles){
							if (in_array($role->getUri(), $roles)){
								$profile[$extId]['modules'][$modId]['has-access'] = true;
								$atLeastOneAccess = true;
							}
						}
					}
				}
				
				if (true === $atLeastOneAccess){
					$profile[$extId]['has-access'] = true;
				}
			}
			
			if (!empty($profile['generis'])){
				unset($profile['generis']);
			}
			
			echo json_encode($profile);
		}
	}

	public function getActions() {
		if (!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		else{
			$role = new core_kernel_classes_Resource($this->getRequestParameter('role'));
			$module = new core_kernel_classes_Resource($this->getRequestParameter('module'));
			$moduleAccess = tao_helpers_funcACL_Cache::retrieveModule($module);
			
			$actions = array();
			foreach (tao_helpers_funcACL_Model::getActions($module) as $action) {
				$uri = explode('#', $action->getUri());
				list($type, $extId, $modId, $actId) = explode('_', $uri[1]);
				
				$actions[$actId] = array('uri' => $action->getUri(),
										 'has-access' => false);
				
				if (isset($moduleAccess['actions'][$action->getUri()])){
					$grantedRoles = $moduleAccess['actions'][$action->getUri()];
					if (true === in_array($role->getUri(), $grantedRoles)){
						$actions[$actId]['has-access'] = true;
					}
				}
			}
			
			ksort($actions);
			echo json_encode($actions);	
		}
	}

	public function removeExtensionAccess() {
		if (!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		else{
			$role = $this->getRequestParameter('role');
			$uri = $this->getRequestParameter('uri');
			$extensionService = tao_models_classes_funcACL_ExtensionAccessService::singleton();
			$extensionService->remove($role, $uri);
			echo json_encode(array('uri' => $uri));	
		}
	}

	public function addExtensionAccess() {
		if (!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		else{
			$role = $this->getRequestParameter('role');
			$uri = $this->getRequestParameter('uri');
			$extensionService = tao_models_classes_funcACL_ExtensionAccessService::singleton();
			$extensionService->add($role, $uri);
			echo json_encode(array('uri' => $uri));
		}
	}

	public function removeModuleAccess() {
		if (!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		else{
			$role = $this->getRequestParameter('role');
			$uri = $this->getRequestParameter('uri');
			$moduleService = tao_models_classes_funcACL_ModuleAccessService::singleton();
			$moduleService->remove($role, $uri);
			echo json_encode(array('uri' => $uri));	
		}
	}

	public function addModuleAccess() {
		if (!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		else{
			$role = $this->getRequestParameter('role');
			$uri = $this->getRequestParameter('uri');
			$moduleService = tao_models_classes_funcACL_ModuleAccessService::singleton();
			$moduleService->add($role, $uri);
			echo json_encode(array('uri' => $uri));	
		}
	}

	public function removeActionAccess() {
		if (!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		else{
			$role = $this->getRequestParameter('role');
			$uri = $this->getRequestParameter('uri');
			$actionService = tao_models_classes_funcACL_ActionAccessService::singleton();
			$actionService->remove($role, $uri);
			echo json_encode(array('uri' => $uri));	
		}
	}

	public function addActionAccess() {
		if (!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		else{
			$role = $this->getRequestParameter('role');
			$uri = $this->getRequestParameter('uri');
			$actionService = tao_models_classes_funcACL_ActionAccessService::singleton();
			$actionService->add($role, $uri);
			echo json_encode(array('uri' => $uri));	
		}
	}

	public function moduleToActionAccess() {
		if (!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		else{
			$role = $this->getRequestParameter('role');
			$uri = $this->getRequestParameter('uri');
			$actionService = tao_models_classes_funcACL_ActionAccessService::singleton();
			$actionService->moduleToActionAccess($role, $uri);
			echo json_encode(array('uri' => $uri));	
		}
	}

	public function moduleToActionsAccess() {
		if (!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		else{
			$role = $this->getRequestParameter('role');
			$uri = $this->getRequestParameter('uri');
			$actionService = tao_models_classes_funcACL_ActionAccessService::singleton();
			$actionService->moduleToActionsAccess($role, $uri);
			echo json_encode(array('uri' => $uri));	
		}
	}

	public function actionsToModuleAccess() {
		if (!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		else{
			$role = $this->getRequestParameter('role');
			$uri = $this->getRequestParameter('uri');
			$moduleService = tao_models_classes_funcACL_ModuleAccessService::singleton();
			$moduleService->actionsToModuleAccess($role, $uri);
			echo json_encode(array('uri' => $uri));	
		}
	}

	public function getRoles() {
		if (!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		else{
			$useruri = tao_helpers_Uri::decode($this->getRequestParameter('useruri'));
			$roleService = tao_models_classes_funcACL_RoleService::singleton();
			$roles = $roleService->getRoles($useruri);
			echo json_encode($roles);
		}
	}

	public function attachRole() {
		if (!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		else{
			$roleuri = tao_helpers_Uri::decode($this->getRequestParameter('roleuri'));
			$useruri = tao_helpers_Uri::decode($this->getRequestParameter('useruri'));
			$roleService = tao_models_classes_funcACL_RoleService::singleton();
			$roleService->attachUser($useruri, $roleuri);
			echo json_encode(array('success' => true, 'id' => tao_helpers_Uri::encode($roleuri)));
		}
	}

	public function unattachRole() {
		if (!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		else{
			$roleuri = tao_helpers_Uri::decode($this->getRequestParameter('roleuri'));
			$useruri = tao_helpers_Uri::decode($this->getRequestParameter('useruri'));
			$roleService = tao_models_classes_funcACL_RoleService::singleton();
			$roleService->unattachUser($useruri, $roleuri);
			echo json_encode(array('success' => true, 'id' => tao_helpers_Uri::encode($roleuri)));	
		}
	}
}
?>