<?php
/**
 * DeliveryServer Controller
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoDelivery
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */

class taoDelivery_actions_DeliveryServer extends taoDelivery_actions_DeliveryServerModule{

	/**
	 * constructor: initialize the service and the default data
	 * @return DeliveryServer
	 */
	public function __construct(){

		parent::__construct();
		$this->service = taoDelivery_models_classes_DeliveryServerService::singleton();
	}
		
	/**
     * Instanciate a process instance from a process definition
	 *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @param processDefinitionUri
     * @return void
     */
	public function initDeliveryExecution($processDefinitionUri){
		
		$processDefinitionUri = urldecode($processDefinitionUri);
		$processDefinition = new core_kernel_classes_Resource($processDefinitionUri);
		
		$userService = taoDelivery_models_classes_UserService::singleton();
		$subject = $userService->getCurrentUser();
		
		$newProcessExecution = taoDelivery_models_classes_DeliveryService::singleton()->initDeliveryExecution($processDefinition, $subject);


		$param = array('processUri' => $newProcessExecution->getUri());
		$this->redirect(tao_helpers_Uri::url('index', 'ProcessBrowser', null, $param));
	}
	
	/**
     * Set a view with the list of process instances (both started or finished) and available process definitions
	 *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @param processDefinitionUri
     * @return void
     */
	public function index(){
		
		//should be removed
		if(isset($_SESSION['taoqual.userId'])){
			$login = $_SESSION['taoqual.userId'];
		}
		else{
			session_destroy();
			$this->redirect(tao_helpers_Uri::url('index', 'DeliveryServerAuthentification'));
		}
		$this->setData('login',$login);
		
		//init required services
		$activityExecutionService = wfEngine_models_classes_ActivityExecutionService::singleton();
		$processExecutionService = wfEngine_models_classes_ProcessExecutionService::singleton();
		$processDefinitionService = wfEngine_models_classes_ProcessDefinitionService::singleton();
		$userService = taoDelivery_models_classes_UserService::singleton();

		//get current user:
		$subject = $userService->getCurrentUser();
                
		//init variable that save data to be used in the view
		$processViewData 	= array();
		$uiLanguages		= tao_helpers_I18n::getAvailableLangs();
		$this->setData('uiLanguages', $uiLanguages);
		
		//get the definition of delivery available for the subject:
		$visibleProcess = $this->service->getDeliveries($subject,false);
		$processExecutions = $this->service->getStartedProcessExecutions($subject);
                
		foreach ($processExecutions as $processExecution){
			
			if(!is_null($processExecution) && $processExecution instanceof core_kernel_classes_Resource){
				
				$status = $processExecutionService->getStatus($processExecution);
				$processDefinition = $processExecutionService->getExecutionOf($processExecution);
				if(is_null($status) || !$status instanceof core_kernel_classes_Resource){
					continue;
				}
				
				if(in_array($processDefinition->uriResource, $visibleProcess)){
					
					$currentActivities = array();
					
					// Bypass ACL Check if possible...
					if ($status->uriResource == INSTANCE_PROCESSSTATUS_FINISHED) {
						$processViewData[] = array(
							'type' 			=> $processDefinition->getLabel(),
							'label' 		=> $processExecution->getLabel(),
							'uri' 			=> $processExecution->uriResource,
							'activities'	=> array(array('label' => '', 'uri' => '', 'may_participate' => false, 'finished' => true, 'allowed'=> true)),
							'status'		=> $status
						);
						continue;
						
					}else{
						
						$isAllowed = false;
						$currentActivityExecutions = $processExecutionService->getCurrentActivityExecutions($processExecution);
						foreach ($currentActivityExecutions as $uri => $currentActivityExecution){
							$isAllowed = $activityExecutionService->checkAcl($currentActivityExecution, $subject, $processExecution);
							$currentActivity = $activityExecutionService->getExecutionOf($currentActivityExecution);
							$currentActivities[] = array(
								'label'				=> $currentActivity->getLabel(),
								'uri' 				=> $uri,
								'may_participate'	=> ($status->uriResource != INSTANCE_PROCESSSTATUS_FINISHED && $isAllowed),
								'finished'			=> ($status->uriResource == INSTANCE_PROCESSSTATUS_FINISHED),
								'allowed'			=> $isAllowed
							);

						}

						//ondelivery server, display only user's delivery (finished and paused): ($processExecution->currentActivity is empty or checkACL returns "false")
						if(!$isAllowed){
							continue;
						}

						$processViewData[] = array(
							'type' 			=> $processDefinition->getLabel(),
							'label' 		=> $processExecution->getLabel(),
							'uri' 			=> $processExecution->uriResource,
							'activities'	=> $currentActivities,
							'status'		=> $status
						);
						
					}
				}
			}
		}
		
		//get deliveries for the current user (set in groups extension)
		$availableProcessDefinitions = $this->service->getDeliveries($subject);

		//filter process that can be initialized by the current user (2nd check...)
		$authorizedProcessDefinitions = array();
		foreach($availableProcessDefinitions as $processDefinition){
			if($processDefinitionService->checkAcl($processDefinition, $subject)){
				$authorizedProcessDefinitions[] = $processDefinition;
			}
		}
		
		$this->setData('availableProcessDefinition', $authorizedProcessDefinitions);
		$this->setData('processViewData', $processViewData);
		$this->setView('deliveryIndex.tpl');
	}
}
?>