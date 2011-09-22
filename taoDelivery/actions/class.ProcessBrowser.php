<?php
error_reporting(E_ALL);

class taoDelivery_actions_ProcessBrowser extends wfEngine_actions_WfModule{
	
	protected $processExecution = null;
	protected $activityExecution = null;
	protected $processExecutionService = null;
	
	public function __construct(){
		
		parent::__construct();
		
	}
	
	protected function checkParameters(){
		
		$returnValue = true;
		
		$processExecutionUri = urldecode($this->getRequestParameter('processUri'));
		$activityExecutionUri = urldecode($this->getRequestParameter('activityExecutionUri'));
		if(empty($processExecutionUri)){
			Session::removeAttribute("processUri");
			$this->redirect(tao_helpers_Uri::url('index', 'DeliveryServer'));
			$returnValue = false;
		}else{
			$this->processExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessExecutionService');
			$this->processExecution = new core_kernel_classes_Resource($processExecutionUri);
			if(!empty($activityExecutionUri)){
				$activityExecution = new core_kernel_classes_Resource($activityExecutionUri);
				$currentActivityExecutions = $this->processExecutionService->getCurrentActivityExecutions($this->processExecution);
				//check if it is a current activity exec:
				if(array_key_exists($activityExecutionUri, $currentActivityExecutions)){
					$this->activityExecution = $activityExecution;
					$returnValue = true;
				}else{
					//if not redirect to the process browser and let it manage the situation:
					$parameters = array();
					$parameters['processUri'] = urlencode($this->processExecution->uriResource);
					$parameters['activityUri'] = '';
					if(ENABLE_HTTP_REDIRECT_PROCESS_BROWSER){
						$this->redirect(tao_helpers_Uri::url('index', 'ProcessBrowser', null, $parameters));
					}else{
						$this->index($parameters['processUri'], $parameters['activityUri']);
					}
					$returnValue = false;
				}
			}
		}
		
		return $returnValue;
	}
	
	public function index($processUri, $activityUri=''){
	
		Session::setAttribute("processUri", $processUri);//to be deleted
		$activityUri = urldecode($activityUri);
		$processUri = urldecode($processUri); // parameters clean-up.
		$this->setData('processUri', $processUri);
		$processExecution = new core_kernel_classes_Resource($processUri);
		
		//user data for browser view
		$userViewData = UsersHelper::buildCurrentUserForView(); 
		$this->setData('userViewData', $userViewData);
		$browserViewData = array(); // general data for browser view.
		
		//init services:
		$userService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_UserService');
		$processExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessExecutionService');
		$activityExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityExecutionService');
		$activityService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityService');
		$interactiveServiceService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_InteractiveServiceService');
		
		//get current user:
		$currentUser = $userService->getCurrentUser();
		if(is_null($currentUser)){
			throw new wfEngine_models_classes_ProcessExecutionException("No current user found!");
		}
		
		//get activity execution from currently available process definitions:
		
		//TODO: results really need to be cached!!
		$currentlyAvailableActivityDefinitions = $processExecutionService->getAvailableCurrentActivityDefinitions($processExecution, $currentUser, true);
		
		$activityExecution = null;
		if(count($currentlyAvailableActivityDefinitions) == 0){
			//no available current activity definition found: no permission or issue in process execution:
			$this->pause(urlencode($processExecution->uriResource));
			return;
		}else{
			if(!empty($activityUri)){
				foreach($currentlyAvailableActivityDefinitions as $availableActivity){
					if($availableActivity->uriResource == $activityUri){
						$activityExecution = $processExecutionService->initCurrentActivityExecution($processExecution, new core_kernel_classes_Resource($activityUri), $currentUser);
						break;
					}
				}
				if(is_null($activityExecution)){
					//invalid choice of activity definition:
//					$invalidActivity = new core_kernel_classes_Resource($activityUri);
//					throw new wfEngine_models_classes_ProcessExecutionException("invalid choice of activity definition in process browser {$invalidActivity->getLabel()} ({$invalidActivity->uriResource}). \n<br/> The link may be outdated.");
					$this->index(urlencode($processExecution->uriResource));
					return;
				}
			}else{
				if(count($currentlyAvailableActivityDefinitions) == 1){
					$activityExecution = $processExecutionService->initCurrentActivityExecution($processExecution, array_pop($currentlyAvailableActivityDefinitions), $currentUser);
					if(is_null($activityExecution)){
						throw new wfEngine_models_classes_ProcessExecutionException('cannot initiate the actiivty execution of the unique next activity definition');
					}
				}else{
					//count > 1:
					//parallel branch:
					$this->pause(urlencode($processExecution->uriResource));
					return;
				}
			}
		}
		
		if(!is_null($activityExecution)){
			
			$browserViewData['activityExecutionUri']= $activityExecution->uriResource;
			Session::setAttribute('activityExecutionUri', $activityExecution->uriResource);
			
			// process variables data.
//			$variablesViewData = array();
//			$variables = $process->getVariables();
//			foreach ($variables as $var){
//				$variablesViewData[$var->uri] = urlencode($var->value);	
//			}
//			$this->setData('variablesViewData',$variablesViewData);
			
			//get interactive services (call of services):
			$activityDefinition = $activityExecutionService->getExecutionOf($activityExecution);
			$interactiveServices = $activityService->getInteractiveServices($activityDefinition);
			$services = array();
			foreach($interactiveServices as $interactiveService){
				$services[] = array(
					'callUrl'	=> $interactiveServiceService->getCallUrl($interactiveService, $activityExecution),
					'style'		=> $interactiveServiceService->getStyle($interactiveService)
				);
			}
			$this->setData('services', $services);
			
			$processDefinition = $processExecutionService->getExecutionOf($processExecution);
			
			//set activity control:
			$controls = $activityService->getControls($activityDefinition);
			$browserViewData['controls'] = array(
				'backward' 	=> in_array(INSTANCE_CONTROL_BACKWARD, $controls),
				'forward'	=> in_array(INSTANCE_CONTROL_FORWARD, $controls)
			);
		
			// If paused, resume it:
			if ($processExecutionService->isFinished($processExecution)){
				$processExecutionService->resume($processExecution);
			}
			
			// Browser view main data.
			$browserViewData['processLabel'] 			= $processDefinition->getLabel();
			$browserViewData['processExecutionLabel']	= $processExecution->getLabel();
			$browserViewData['activityLabel'] 			= $activityDefinition->getLabel();
			$browserViewData['processUri']				= $processExecution->uriResource;
			$browserViewData['active_Resource']			="'".$activityDefinition->uriResource."'" ;
			$browserViewData['isInteractiveService'] 	= true;
			$this->setData('browserViewData', $browserViewData);
					
			$this->setData('activity', $activityDefinition);
		
		
			/* <DEBUG> :populate the debug widget */
			
			if(false){
				$this->setData('debugWidget', DEBUG_MODE);
				
				$servicesResources = array();
				foreach($services as $service){
					$servicesResources[] = array(
						'resource' => $service->resource,
						'input'		=> $service->input,
						'output'	=> $service->output
					);
				}

				$this->setData('debugData', array(
						'Activity' => $activity->resource,
						'ActivityExecution' => $activityExecution,
						'Current activities' => $tokenService->getCurrentActivities($process->resource),
						'Services' => $servicesResources,
						'VariableStack' => wfEngine_models_classes_VariableService::getAll()
				));
			}
			/* </DEBUG> */

			$this->setView('process_browser.tpl');
		}
	}

	public function back($processUri, $activityExecutionUri){
		
		if(!$this->checkParameters()){
			return;
		}
		
		$processExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessExecutionService');
		$processExecution = new core_kernel_classes_Resource(urldecode($processUri));
		$activityExecution = new core_kernel_classes_Resource(urldecode($activityExecutionUri));
		
		$previousActivityDefinitions = $processExecutionService->performBackwardTransition($processExecution, $activityExecution);
		if($processExecutionService->isPaused($processExecution)){
			$this->pause($processExecution->uriResource);
		}else{
			$parameters = array();
			$parameters['processUri'] = urlencode($processExecution->uriResource);
			if(count($previousActivityDefinitions) == 1){
				$parameters['activityUri'] = urlencode(array_pop($previousActivityDefinitions)->uriResource);
			}else{
				$parameters['activityUri'] = '';
			}
			
			if(ENABLE_HTTP_REDIRECT_PROCESS_BROWSER){
				$this->redirect(tao_helpers_Uri::url('index', 'ProcessBrowser', null, $parameters));
			}else{
				$this->index($parameters['processUri'], $parameters['activityUri']);
			}
			
		}
	}

	public function next($processUri, $activityExecutionUri){
		
		if(!$this->checkParameters()){
			return;
		}
		
		$processExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessExecutionService');
		
		$processExecution = new core_kernel_classes_Resource(urldecode($processUri));
		$activityExecution = new core_kernel_classes_Resource(urldecode($activityExecutionUri));
		
		$nextActivityDefinitions = $processExecutionService->performTransition($processExecution, $activityExecution);
		
		if($processExecutionService->isFinished($processExecution)){
			$this->redirect(tao_helpers_Uri::url('index', 'DeliveryServer'));
		}
		elseif($processExecutionService->isPaused($processExecution)){
			$this->pause($processExecution->uriResource);
		}
		else{
			//if $nextActivityDefinitions count = 1, pass it to the url:
			$parameters = array();
			$parameters['processUri'] = urlencode($processExecution->uriResource);
			if(count($nextActivityDefinitions) == 1){
				$parameters['activityUri'] = urlencode(array_pop($nextActivityDefinitions)->uriResource);
			}else{
				$parameters['activityUri'] = '';
			}
			
			if(ENABLE_HTTP_REDIRECT_PROCESS_BROWSER){
				$this->redirect(tao_helpers_Uri::url('index', 'ProcessBrowser', null, $parameters));
			}else{
				$this->index($parameters['processUri'], $parameters['activityUri']);
			}
		}
	}

	public function pause($processUri){
		
		$processExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessExecutionService');
		
		$processExecution = new core_kernel_classes_Resource(urldecode($processUri));
		if(!$processExecutionService->isPaused($processExecution)){
			$processExecutionService->pause($processExecution);
		}
		
		Session::removeAttribute("processUri");
		$_SESSION["processUri"]= null;
		$this->redirect(tao_helpers_Uri::url('index', 'DeliveryServer'));
		
	}

}
?>
