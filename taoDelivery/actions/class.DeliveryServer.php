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
		$this->service = tao_models_classes_ServiceFactory::get('taoDelivery_models_classes_DeliveryServerService');
	}
		
	/**
     * Instanciate a process instance from a process definition
	 *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @param processDefinitionUri
     * @return void
     */
	public function processAuthoring($processDefinitionUri)
	{
		
		$userService = tao_models_classes_ServiceFactory::get('taoDelivery_models_classes_UserService');
		$subject = $userService->getCurrentUser();
		
		$processDefinitionUri = urldecode($processDefinitionUri);
		$delivery = taoDelivery_models_classes_DeliveryAuthoringService::getDeliveryFromProcess(new core_kernel_classes_Resource($processDefinitionUri), true);
		if(is_null($delivery)){
			throw new Exception("no delivery found for the selected process definition");
		}

		$wsdlContract = $this->service->getResultServer($delivery);
		if(empty($wsdlContract)){
			throw new Exception("no wsdl contract found for the current delivery");
		}

		ini_set('max_execution_time', 200);

		$processExecutionFactory = new wfEngine_models_classes_ProcessExecutionFactory();
			
		$processExecutionFactory->name = $delivery->getLabel();
		if(strlen(trim($processExecutionFactory->name)) == 0){
			$deliveryService = tao_models_classes_ServiceFactory::get('taoDelivery_models_classes_DeliveryService');
			$processExecutionFactory->name = "Execution ".count($deliveryService->getHistory($delivery))." of ".$delivery->getLabel();
		}
		$processExecutionFactory->comment = 'Created in delivery server on' . date(DATE_ISO8601);
			
		$processExecutionFactory->execution = $processDefinitionUri;
			
		$var_delivery = new core_kernel_classes_Resource(INSTANCE_PROCESSVARIABLE_DELIVERY);
		if($var_delivery->hasType(new core_kernel_classes_Class(CLASS_PROCESSVARIABLES))){
			$processExecutionFactory->variables = array($var_delivery->uriResource => $delivery->uriResource);//no need to encode here, will be donce in Service::getUrlCall
		}else{
			throw new Exception('the required process variable "delivery" is missing in delivery server, reinstalling tao is required');
		}

		$newProcessExecution = $processExecutionFactory->create();


		$newProcessExecution->feed();


		$processUri = urlencode($newProcessExecution->uri);



		//add history of delivery execution in the delivery ontology
		$this->service->addHistory($delivery, $subject, $newProcessExecution->resource);

		$param = array( 'processUri' => urlencode($processUri));
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

		$login = $_SESSION['taoqual.userId'];
		$this->setData('login',$login);
		
		//init required services
		$activityExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityExecutionService');
		$userService = tao_models_classes_ServiceFactory::get('taoDelivery_models_classes_UserService');

		//get current user:
		$subject = $userService->getCurrentUser();
                
		//init variable that save data to be used in the view
		$processViewData 	= array();
		$uiLanguages		= tao_helpers_I18n::getAvailableLangs();
		$this->setData('uiLanguages',$uiLanguages);
		
		//get the definition of delivery available for the subject:
		$visibleProcess = $this->service->getDeliveries($subject,false);
		
		$processes = array();
		$processes = $this->service->getStartedProcessExecutions($subject);
                
		foreach ($processes as $proc){
			
			$label 	= $proc->resource->getLabel();
			$uri 	= $proc->uri;
			$status = $proc->status;
			$persid	= "-";
			$res = $proc->process->resource;
                        
			if($res !=null && $res instanceof core_kernel_classes_Resource){
                                
				$defUri = $res->uriResource;
				$type 	= $proc->process->resource->getLabel();
					
				if(in_array($defUri, $visibleProcess)){
					
					$currentActivities = array();
					
					// Bypass ACL Check if possible...
					if ($status == 'Finished') {
						$processViewData[] = array(
							'type' 			=> $type,
							'label' 		=> $label,
							'uri' 			=> $uri,
							'persid'		=> $persid,
							'activities'	=> array(array('label' => '', 'uri' => '', 'may_participate' => false, 'finished' => true, 'allowed'=> true)),
							'status'		=> $status
						);
						
						continue;
					}
					
					$isAllowed = false;
					foreach ($proc->currentActivity as $currentActivity){
						$activity = $currentActivity;
						
						$isAllowed = $activityExecutionService->checkAcl($activity->resource, $subject, $proc->resource);
						
						$currentActivities[] = array(
							'label'				=> $currentActivity->resource->getLabel(),
							'uri' 				=> $currentActivity->uri,
							'may_participate'	=> (!$proc->isFinished() && $isAllowed),
							'finished'			=> $proc->isFinished(),
							'allowed'			=> $isAllowed
						);

					}
					
					//ondelivery server, display only user's delivery (finished and paused): ($proc->currentActivity is empty or checkACL returns "false")
					if(!$isAllowed){
						continue;
					}
						
					$processViewData[] = array(
						'type' 			=> $type,
						'label' 		=> $label,
						'uri' 			=> $uri,
						'persid'		=> $persid,
						'activities'	=> $currentActivities,
						'status'		=> $status
					);
				}
			}

		}
		$processClass = new core_kernel_classes_Class(CLASS_PROCESS);
		
		//get deliveries for the current user (set in groups extension)
		$availableProcessDefinitions = $this->service->getDeliveries($subject);

		//filter process that can be initialized by the current user (2nd check...)
		$processExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessExecutionService');
		$authorizedProcessDefinitions = array();
		foreach($availableProcessDefinitions as $processDefinition){
			if($processExecutionService->checkAcl($processDefinition, $subject)){
				$authorizedProcessDefinitions[] = $processDefinition;
			}
		}
		
		$this->setData('availableProcessDefinition',$authorizedProcessDefinitions);
		$this->setData('processViewData',$processViewData);
		$this->setView('deliveryIndex.tpl');
	}
}
?>