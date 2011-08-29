<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

class BuildTestEnvironmentTestCase extends UnitTestCase {
	
	/**
	 * CHANGE IT MANNUALLY to see step by step the output
	 * @var boolean
	 */
	const OUTPUT = false;
	
	/**
	 * @var wfEngine_models_classes_ActivityExecutionService the tested service
	 */
	protected $service = null;
	
	/**
	 * @var wfEngine_models_classes_UserService
	 */
	protected $userService = null;
	
	/**
	 * @var core_kernel_classes_Resource
	 */
	protected $currentUser = null;
	
	/*
	* Define the execution parameters
	*/
	protected $subjectNumber = 500;
	protected $totalNbActivities = 0;
	protected $executionNumber = 500;
	
	protected $myUserUri = '';
	protected $processDefinitionUri = 'http://localhost/middleware/taoDevQIS.rdf#i1299756668073087100';
	
	public function setUp(){
		
		TestRunner::initTest();
		
		error_reporting(E_ALL);
		
		if(is_null($this->userService)){
			$this->userService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_UserService');
		}
		
		if(!empty($this->myUserUri)){
			//try login with the given user.
			$myUser = new core_kernel_classes_Resource($this->myUserUri);
			if($myUser->hasType( new core_kernel_classes_Class(CLASS_ROLE_WORKFLOWUSERROLE))){
				$login = (string) $myUser->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_USER_LOGIN));
				$md5pass = (string) $myUser->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_USER_PASSWORD));
			}
		}
		
		
		$login = 'wfTester';
		$pass = 'test123';
		$userData = array(
			PROPERTY_USER_LOGIN		=> 	$login,
			PROPERTY_USER_PASSWORD	=>	md5($pass),
			PROPERTY_USER_DEFLG		=>	'EN'
		);
		
		$this->currentUser = $this->userService->getOneUser($login);
		if(is_null($this->currentUser)){
			$this->userService->saveUser($this->currentUser, $userData, new core_kernel_classes_Resource(CLASS_ROLE_WORKFLOWUSERROLE));
		}
		
		core_kernel_users_Service::logout();
		if($this->userService->loginUser($login, md5($pass))){
			$this->userService->connectCurrentUser();
			$this->currentUser = $this->userService->getCurrentUser();
		}
		
		$this->subjectsService = tao_models_classes_ServiceFactory::get('Subjects');
		$this->service = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_TokenService');
	}
	
	public function testCreateSubjects(){
		
		/*Crer n sujets*/
		
		//check parent class
		$this->assertTrue(defined('TAO_SUBJECT_CLASS'));
		$subjectClass = $this->subjectsService->getSubjectClass();
		$this->assertIsA($subjectClass, 'core_kernel_classes_Class');
		$this->assertEqual(TAO_SUBJECT_CLASS, $subjectClass->uriResource);

		//create a subclass
		$subsubjectClassLabel = 'test subject class';
		$subsubjectClass = $this->subjectsService->createSubClass($subjectClass, $subsubjectClassLabel);
		$this->assertIsA($subsubjectClass, 'core_kernel_classes_Class');
		$this->assertEqual($subsubjectClassLabel, $subsubjectClass->getLabel());
		$this->assertTrue($this->subjectsService->isSubjectClass($subsubjectClass));
		$i=1;
		$n = $this->subjectNumber;
		
		while ($i<=$n){

			//create an instance of the subject class
			$subjectInstanceLabel = 'test subject instance';
			$subjectInstance = $this->subjectsService->createInstance($subsubjectClass, $subjectInstanceLabel);
			$this->assertIsA($subjectInstance, 'core_kernel_classes_Resource');
			$this->assertEqual($subjectInstanceLabel, $subjectInstance->getLabel());

			$subjectInstance->setPropertyValue(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/generis.rdf#login'), "login{$i}");
			$subjectInstance->setPropertyValue(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/generis.rdf#password'), "pass{$i}");
			
			$i++;
		}

	}
	
	private function out($message, $ln = false){
		if(self::OUTPUT){
			if(PHP_SAPI == 'cli'){
				if($ln){
					echo "\n";
				}
				echo "$message\n";
			}
			else{
				if($ln){
					echo "<br />";
				}
				echo "$message<br />";
			}
		}
	}
	
	public function testVirtualSequencialProcess(){
		
		if(empty($this->totalNbActivities)){
			return;
		}
		
		try{

			$authoringService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessAuthoringService');
			$processExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessExecutionService');
			$activityExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityExecutionService');


			//create a new process def
			$processDefinitionClass = new core_kernel_classes_Class(CLASS_PROCESS);
			$processDefinition = $processDefinitionClass->createInstance('ProcessForUnitTest', 'Unit test');
			$this->assertIsA($processDefinition, 'core_kernel_classes_Resource');

			//define activities and connectors
			$activity = $authoringService->createActivity($processDefinition, 'activity1');
			$this->assertNotNull($activity);
			$authoringService->setFirstActivity($processDefinition, $activity);
			
			$cleanRes = array();
			
			$i = 2;
			$totalNbActivities = $this->totalNbActivities;
			while($i <= $totalNbActivities ){
				$connector = null;
				$connector = $authoringService->createConnector($activity);
				$authoringService->setConnectorType($connector, new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_SEQUENCE));
				$this->assertNotNull($connector);
				
				$activity = $authoringService->createSequenceActivity($connector, null, 'activity'.$i);
				$this->assertNotNull($activity);
				
				$cleanRes[]=$connector;
				$cleanRes[]=$activity;
				
				$i++;
			}

			//run the process
			$executionCount=0;
			while($executionCount<$this->executionNumber){
			
				$factory = new ProcessExecutionFactory();
				$factory->name = 'Test Process Execution';
				
				$factory->execution = $processDefinition->uriResource;
				$factory->ownerUri = SYS_USER_LOGIN;

				//init 1st activity
				$proc = $factory->create();

				$this->out(__METHOD__, true);
				
				
				$i = 1;
				while($i <= $totalNbActivities ){
				
					$activity = $proc->currentActivity[0];

					$this->out("<strong>".$activity->label."</strong>", true);
					$this->assertTrue($activity->label == 'activity'.$i);

					$currentTokens = $this->service->getCurrents($proc->resource);

					$this->assertIsA($currentTokens, 'array');
					foreach($currentTokens as $currentToken){
						$this->out("Current : ". $currentToken->getLabel());
					}
					
					//init execution
					$this->assertTrue($processExecutionService->initCurrentExecution($proc->resource, $activity->resource, $this->currentUser));

					$activityExecuction = $activityExecutionService->getExecution($activity->resource, $this->currentUser, $proc->resource);
					$this->assertNotNull($activityExecuction);
					$this->out("Execution: ".$activityExecuction->getLabel());

					$token = $this->service->getCurrent($activityExecuction);
					$this->assertNotNull($token);
					$this->out("Token: ".$token->getLabel()." ".$token->uriResource);

					$tokenActivity = $token->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_TOKEN_ACTIVITY));
					$this->assertNotNull($tokenActivity);
					$this->out("Token Activity: ".$tokenActivity->getLabel());

					$tokenActivityExe = $token->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_TOKEN_ACTIVITYEXECUTION));
					$this->assertNotNull($tokenActivityExe);
					$this->out("Token ActivityExecution: ".$tokenActivityExe->getLabel());

					$tokenUser = $token->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_TOKEN_CURRENTUSER));
					$this->assertNotNull($tokenUser);
					$this->out("Token User: ".$tokenUser->getLabel());

					//transition to 2nd activity
					$proc->performTransition($activityExecuction->uriResource);

					$currentTokens = $this->service->getCurrents($proc->resource);
					$this->assertIsA($currentTokens, 'array');


					$this->assertFalse($proc->isPaused());

					$i++;
				}
				
				
				$this->assertTrue($proc->isFinished());

				$currentTokens = $this->service->getCurrents($proc->resource);
				foreach($currentTokens as $currentToken){
					$this->assertTrue($this->service->delete($currentToken));
				}

				$proc->resource->delete();
				$executionCount++;
			}
			
			//execution completed so delete the process def now:
			$processDefinition->delete();
			
			foreach ($cleanRes as $resource){
				$resource->delete();
			}
			
			if(!is_null($this->currentUser)){
				
				core_kernel_users_Service::logout();
				$this->userService->removeUser($this->currentUser);
			}
			
		}catch(common_Exception $ce){
				$this->fail($ce);
		}
		
	}
	
	public function testExecuteDeliveryProcess(){
		if(!empty($this->processDefinitionUri) && !empty($this->executionNumber)){
		
			$deliveryProcess = new core_kernel_classes_Resource($this->processDefinitionUri);
			if($deliveryProcess->hasType(new core_kernel_classes_Class(CLASS_PROCESS))){
				$activityNumber = 0;
				$processAuthoringService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessAuthoringService');
				$activityNumber = count($processAuthoringService->getActivitiesByProcess($deliveryProcess));
				
				if($activityNumber){
					//set as a subject
					$roleSubjectClass = new core_kernel_classes_Class(CLASS_ROLE_SUBJECT);
					$this->currentUser->removeType($roleSubjectClass);
					$this->currentUser->setType($roleSubjectClass);
					
					$authoringService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessAuthoringService');
					$processExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessExecutionService');
					$activityExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityExecutionService');
			
					// $executionCount=0;
					// var_dump($this);exit;
					for($executionCount=0;$executionCount<$this->executionNumber;$executionCount++){
					
						$factory = null;
						$proc = null;
						
						$factory = new ProcessExecutionFactory();
						$factory->name = 'Test Process Execution of '.$deliveryProcess->getLabel();
						
						$factory->execution = $deliveryProcess->uriResource;
						$factory->ownerUri = SYS_USER_LOGIN;

						//init 1st activity
						$proc = $factory->create();

						$this->out(__METHOD__, true);
						
						
						for($i=0; $i<$activityNumber; $i++){
							
							$activity = $proc->currentActivity[0];

							$this->out("<strong>".$activity->label."</strong>", true);

							$currentTokens = $this->service->getCurrents($proc->resource);
							
							$this->assertIsA($currentTokens, 'array');
							foreach($currentTokens as $currentToken){
								$this->out("Current : ". $currentToken->getLabel());
							}
							
							//init execution
							$this->assertTrue($processExecutionService->initCurrentExecution($proc->resource, $activity->resource, $this->currentUser));
							
							
							$activityExecuction = $activityExecutionService->getExecution($activity->resource, $this->currentUser, $proc->resource);
							$this->assertNotNull($activityExecuction);
							$this->out("Execution: ".$activityExecuction->getLabel());

							$token = $this->service->getCurrent($activityExecuction);
							$this->assertNotNull($token);
							$this->out("Token: ".$token->getLabel()." ".$token->uriResource);

							$tokenActivity = $token->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_TOKEN_ACTIVITY));
							$this->assertNotNull($tokenActivity);
							$this->out("Token Activity: ".$tokenActivity->getLabel());

							$tokenActivityExe = $token->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_TOKEN_ACTIVITYEXECUTION));
							$this->assertNotNull($tokenActivityExe);
							$this->out("Token ActivityExecution: ".$tokenActivityExe->getLabel());

							$tokenUser = $token->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_TOKEN_CURRENTUSER));
							$this->assertNotNull($tokenUser);
							$this->out("Token User: ".$tokenUser->getLabel());

							//transition to 2nd activity
							$proc->performTransition($activityExecuction->uriResource);

							$currentTokens = $this->service->getCurrents($proc->resource);
							$this->assertIsA($currentTokens, 'array');

							$this->assertFalse($proc->isPaused());
							
						}
						
						$this->assertTrue($proc->isFinished());

						$currentTokens = $this->service->getCurrents($proc->resource);
						foreach($currentTokens as $currentToken){
							$this->assertTrue($this->service->delete($currentToken));
						}
						
						//do not delete here:
						// $executionCount++;
						
					}
				}
				
			}
		}
		
	}
}
?>