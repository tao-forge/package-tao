<?php

/**
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
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

use qtism\data\storage\php\PhpDocument;
use qtism\runtime\tests\AssessmentItemSession;
use qtism\runtime\tests\AssessmentItemSessionState;
use qtism\runtime\tests\AssessmentTestSessionException;
use qtism\runtime\tests\AssessmentTestSessionFactory;
use qtism\runtime\tests\AssessmentTestSessionState;
use qtism\runtime\tests\AssessmentTestSession;
use qtism\runtime\tests\AssessmentTestPlace;
use qtism\data\AssessmentTest;
use qtism\runtime\common\State;
use qtism\runtime\common\ResponseVariable;
use qtism\common\enums\BaseType;
use qtism\common\enums\Cardinality;
use qtism\common\datatypes\String;
use qtism\runtime\tests\AssessmentItemSessionException;
use qtism\runtime\storage\common\AbstractStorage;
use qtism\data\SubmissionMode;
use qtism\data\NavigationMode;
use qtism\data\View;

/**
 * Runs a QTI Test.
 *
 * @author Joel Bout, <joel@taotesting.com>
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @package taoQtiTest
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class taoQtiTest_actions_TestRunner extends tao_actions_ServiceModule {

    const ERROR_UNKNOWN = 0;
    
    const ERROR_TESTPART_TIME_OVERFLOW = 1;
    
    const ERROR_TESTPART_TIME_UNDERFLOW = 2;
    
    /**
     * The current AssessmentTestSession object.
     * 
     * @var AssessmentTestSession
     */
    private $testSession = null;
    
    /**
     * The current AssessmentTest definition object.
     * 
     * @var AssessmentTest
     */
    private $testDefinition = null;
    
    /**
     * The TAO Resource describing the test to be run.
     * 
     * @var core_kernel_classes_Resource
     */
    private $testResource = null;
    
    /**
     * The current AbstractStorage object.
     * 
     * @var AbstractStorage
     */
    private $storage = null;
    
    /**
     * Whether an attempt has begun during the request.
     * 
     * @var boolean
     */
    private $attemptBegun = false;
    
    /**
     * The error that occured during the current request.
     * 
     */
    private $currentError = -1;
    
    /**
     * The compilation directory.
     * 
     * @var string
     */
    private $compilationDirectory;
    
    /**
     * Get the current assessment test session.
     * 
     * @return AssessmentTestSession An AssessmentTestSession object.
     */
    protected function getTestSession() {
        return $this->testSession;
    }
    
    /**
     * Set the current assessment test session.
     * 
     * @param AssessmentTestSession $testSession An AssessmentTestSession object.
     */
    protected function setTestSession(AssessmentTestSession $testSession) {
        $this->testSession = $testSession;
    }
    
    /**
     * Get the current test definition.
     * 
     * @return AssessmentTest An AssessmentTest object.
     */
    protected function getTestDefinition() {
        return $this->testDefinition;
    }
    
    /**
     * Set the current test defintion.
     * 
     * @param AssessmentTest $testDefinition An AssessmentTest object.
     */
    protected function setTestDefinition(AssessmentTest $testDefinition) {
        $this->testDefinition = $testDefinition;
    }
    
    /**
	 * Get the QtiSm AssessmentTestSession Storage Service.
	 * 
	 * @return AbstractStorage An AssessmentTestSession Storage Service.
	 */
	protected function getStorage() {
	    return $this->storage;    
	}
	
	/**
	 * Set the QtiSm AssessmentTestSession Storage Service.
	 * 
	 * @param AbstractStorage $storage An AssessmentTestSession Storage Service.
	 */
	protected function setStorage(AbstractStorage $storage) {
	    $this->storage = $storage;
	}
	
	/**
	 * Get the TAO Resource describing the test to be run.
	 * 
	 * @return core_kernel_classes_Resource A TAO Resource in database.
	 */
	protected function getTestResource() {
	    return $this->testResource;
	}
	
	/**
	 * Set the TAO Resource describing the test to be run.
	 * 
	 * @param core_kernel_classes_Resource $testResource A TAO Resource in database.
	 */
	protected function setTestResource(core_kernel_classes_Resource $testResource) {
	    $this->testResource = $testResource;
	}
	
	/**
	 * Set whether a new attempt begun during this request.
	 * 
	 * @param boolean $begun
	 */
	protected function setAttemptBegun($begun) {
	    $this->attemptBegun = true;
	}
	
	/**
	 * Whether a new attempt begun during this request.
	 * 
	 * @return boolean
	 */
	protected function hasAttemptBegun() {
	    return $this->attemptBegun;
	}
	
	/**
	 * Get the error that occured during the previous request.
	 * 
	 * @return integer
	 */
	protected function getPreviousError() {
	    return $this->getStorage()->getLastError();
	}
	
	/**
	 * Set the error that occured during the current request.
	 * 
	 * @param integer $error
	 */
	protected function setCurrentError($currentError) {
	    $this->currentError = $currentError;
	}
	
	/**
	 * Get the error that occured during the current request.
	 * 
	 * @return integer
	 */
	protected function getCurrentError() {
	    return $this->currentError;
	}
	
	/**
	 * Set the path to the directory where the test is compiled.
	 * 
	 * @param string $compilationDirectory An absolute path.
	 */
	protected function setCompilationDirectory($compilationDirectory) {
	    $this->compilationDirectory = $compilationDirectory;
	}
	
	/**
	 * Get the path to the directory where the test is compiled.
	 * 
	 * @return string
	 */
	protected function getCompilationDirectory() {
	    return $this->compilationDirectory;
	}
    
    protected function beginCandidateInteraction() {
        $testSession = $this->getTestSession();
        $itemSession = $testSession->getCurrentAssessmentItemSession();
        $itemSessionState = $itemSession->getState();
        
        $initial = $itemSessionState === AssessmentItemSessionState::INITIAL;
        $suspended = $itemSessionState === AssessmentItemSessionState::SUSPENDED;
        $remainingAttempts = $itemSession->getRemainingAttempts();
        $attemptable = $remainingAttempts === -1 || $remainingAttempts > 0;
        
        if ($initial === true || ($suspended === true && $attemptable === true)) {
            // Begin the very first attempt.
            common_Logger::i('New attempt begun.');
            $this->beginAttempt();
        }
        // Otherwise, the item is not attemptable bt the candidate.
    }
    
    protected function beforeAction() {
        // Controller initialization.
        $this->retrieveTestResource();
        $this->retrieveTestDefinition();
        $resultServer = taoResultServer_models_classes_ResultServerStateFull::singleton();
        $testSessionFactory = new taoQtiTest_helpers_TestSessionFactory($this->getTestDefinition(), $resultServer, $this->getTestResource());
        $this->setStorage(new taoQtiTest_helpers_TestSessionStorage($testSessionFactory));
        $this->retrieveTestSession();
        
        // From stackOverflow: http://stackoverflow.com/questions/49547/making-sure-a-web-page-is-not-cached-across-all-browsers
        // license is Creative Commons Attribution Share Alike (author Edward Wilde)
        header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
        header('Pragma: no-cache'); // HTTP 1.0.
        header('Expires: 0'); // Proxies.
    }
    
    protected function beforeMyAction() {
        
        // Do the required stuff
        // --- If the session has just been instantiated, begin the test session.
        $testSession = $this->getTestSession();
        
        if ($testSession->getState() === AssessmentTestSessionState::INITIAL) {
            // The test has just been instantiated.
            common_Logger::d("Beginning Assessment Test Session.");
            $testSession->beginTestSession();
        }
        
        if ($testSession->getState() === AssessmentTestSessionState::INTERACTING) {
            // Log current [itemId].[occurence].
            common_Logger::d("Current Route Item is '" . $testSession->getCurrentAssessmentItemRef()->getIdentifier() . "." . $testSession->getCurrentAssessmentItemRefOccurence() . "'");
            
            $itemSession = $testSession->getCurrentAssessmentItemSession();
            $itemSessionState = $itemSession->getState();
            
            if ($itemSessionState === AssessmentItemSessionState::INITIAL) {
                // Begin the very first attempt.
                $this->beginAttempt();
            }
        }  
    }
    
    protected function afterAction() {
        $sessionId = $this->getTestSession()->getSessionId();
        common_Logger::i("Persisting QTI Assessment Test Session '${sessionId}'...");
        $this->persistTestSession();
    }

    /**
     * Main action of the TestRunner module.
     * 
     */
	public function index() {
	    $this->beforeAction();
	    
	    $testSession = $this->getTestSession();
	    if ($testSession->getState() === AssessmentTestSessionState::INITIAL) {
            // The test has just been instantiated.
            $testSession->beginTestSession();
            common_Logger::i("Assessment Test Session begun.");
        }
	    
        if ($this->isTimeout() === false) {
            $this->beginCandidateInteraction();
        }
        
        $this->buildAssessmentTestContext();
        $this->setData('client_config_url', $this->getClientConfigUrl());
        $this->setView('test_runner.tpl');
        
        $this->afterAction();
	}
	
	/**
	 * Move forward in the Assessment Test Session flow.
	 *
	 */
	public function moveForward() {
        $this->beforeAction();
        $session = $this->getTestSession();
        
        try {
            $session->moveNext();
            
            if ($session->isRunning() === true && $this->isTimeout() === false) {
                $this->beginCandidateInteraction();
            }
        }
        catch (AssessmentTestSessionException $e) {
            $this->registerAssessmentTestSessionException($e);
        }

        $context = $this->buildAssessmentTestContext();
        echo json_encode($context);
        $this->afterAction();
	}
	
	/**
	 * Move backward in the Assessment Test Session flow.
	 *
	 */
	public function moveBackward() {
	    $this->beforeAction();
	    $session = $this->getTestSession();
	    
	    try {
	        $session->moveBack();
	        
	        if ($this->isTimeout() === false) {
	            $this->beginCandidateInteraction();
	        }
	    }
	    catch (AssessmentTestSessionException $e) {
	        $this->registerAssessmentTestSessionException($e);
	    }
	    
	    $context = $this->buildAssessmentTestContext();
	    echo json_encode($context);
	    $this->afterAction();
	}
	
	/**
	 * Skip the current item in the Assessment Test Session flow.
	 *
	 */
	public function skip() {
	    $this->beforeAction();
	    $session = $this->getTestSession();
	    
	    try {
	        $session->skip();
	        $session->moveNext();
	        
	        if ($session->isRunning() === true && $this->isTimeout() === false) {
	            $this->beginCandidateInteraction();
	        }
	    }
	    catch (AssessmentTestSessionException $e) {
	        $this->registerAssessmentTestSessionException($e);
	    }
	    
	    $context = $this->buildAssessmentTestContext();
	    echo json_encode($context);
	    $this->afterAction();
	}
	
	/**
	 * Action to call when a structural QTI component times out in linear mode.
	 *
	 */
	public function timeout() {
	    $this->beforeAction();
	    $session = $this->getTestSession();
	    
	    try {
            $session->checkTimeLimits(false, true, false);
        }
        catch (AssessmentTestSessionException $e) {
            $timedOut = $e->getCode();
        }
        
        if ($timedOut !== false) {
            //We are okay!
	        switch ($timedOut) {
	            case AssessmentTestSessionException::ASSESSMENT_TEST_DURATION_OVERFLOW:
	                $session->endTestSession();
	            break;

	            case AssessmentTestSessionException::TEST_PART_DURATION_OVERFLOW:
	                $session->moveNextTestPart();
	            break;

	            case AssessmentTestSessionException::ASSESSMENT_SECTION_DURATION_OVERFLOW:
	                $session->moveNextAssessmentSection();
	            break;

	            case AssessmentTestSessionException::ASSESSMENT_ITEM_DURATION_OVERFLOW:
	                $session->moveNextAssessmentItem();
	            break;
            }
            
            if ($session->isRunning() === true && $this->isTimeout() === false) {
                $this->beginCandidateInteraction();
            }
        }

        $context = $this->buildAssessmentTestContext();
        echo json_encode($context);
        $this->afterAction();
	}

	/**
	 * Action called when a QTI Item embedded in a QTI Test submit responses.
	 * 
	 */
	public function storeItemVariableSet() {
	    $this->beforeAction();
	    
	    // --- Deal with provided responses.
	    $responses = new State();
	    if ($this->hasRequestParameter('responseVariables')) {

	        // Transform the values from the client-side in a QtiSm form.
	        foreach ($this->getRequestParameter('responseVariables') as $id => $val) {
	            if (empty($val) === false) {
	                $filler = new taoQtiCommon_helpers_VariableFiller($this->getTestSession()->getCurrentAssessmentItemRef());
	                
	                try {
	                    $var = $filler->fill($id, $val);
	                    $responses->setVariable($var);
	                }
	                catch (OutOfRangeException $e) {
	                    // The value could not be transformed, ignore it.
	                    // format the logger message.
	                    common_Logger::d("Could not convert client-side value for variable '${id}'.");
	                }
	            }
	        }
	    }
	    
	    $currentItem = $this->getTestSession()->getCurrentAssessmentItemRef();
	    $currentOccurence = $this->getTestSession()->getCurrentAssessmentItemRefOccurence();
	    $displayFeedback = $this->getTestSession()->getCurrentSubmissionMode() !== SubmissionMode::SIMULTANEOUS;
	    $stateOutput = new taoQtiCommon_helpers_StateOutput();
	    
	    try {
	        common_Logger::i('Responses sent from the client-side. The Response Processing will take place.');
	        $this->getTestSession()->endAttempt($responses, true);
	         
	        // Return the item session state to the client side.
	        $itemSession = $this->getTestSession()->getAssessmentItemSessionStore()->getAssessmentItemSession($currentItem, $currentOccurence);
	         
	        foreach ($itemSession->getAllVariables() as $var) {
	            $stateOutput->addVariable($var);
	        }
	    }
	    catch (AssessmentItemSessionException $e) {
	        $this->registerAssessmentItemSessionException($e);
	    }
	    catch (AssessmentTestSessionException $e) {
	        $this->registerAssessmentTestSessionException($e);
	        
	        if ($e->getCode() === AssessmentTestSessionException::TEST_PART_DURATION_OVERFLOW) {
	            $this->getTestSession()->moveNext();
	        }
	    }
	    
	    echo json_encode(array('success' => true, 'displayFeedback' => $displayFeedback, 'itemSession' => $stateOutput->getOutput()));
	    
	    $this->afterAction();
	}
	
	/**
	 * Checks whether or not the current step is timeout.
	 *
	 * @return boolean
	 */
	protected function isTimeout() {
	    $session = $this->getTestSession();
	    try {
	        $session->checkTimeLimits(false, true, false);
	    }
	    catch (AssessmentTestSessionException $e) {
	         
	        return true;
	    }
	     
	    return false;
	}
	
	public function comment() {
	    $this->beforeAction();
	    
	    $resultServer = taoResultServer_models_classes_ResultServerStateFull::singleton();
	    $transmitter = new taoQtiCommon_helpers_ResultTransmitter($resultServer);
	    
	    // prepare transmission Id for result server.
	    $item = $this->getTestSession()->getCurrentAssessmentItemRef()->getIdentifier();
	    $occurence = $this->getTestSession()->getCurrentAssessmentItemRefOccurence();
	    $sessionId = $this->getServiceCallId();
	    $transmissionId = "${sessionId}.${item}.${occurence}";
	    
	    // retrieve comment's intrinsic value.
	    $comment = $this->getRequestParameter('comment');
	    
	    // build variable and send it.
	    $itemUri = $this->getCurrentItemUri();
	    $testUri = $this->getTestSession()->getTest()->getUri();
	    $variable = new ResponseVariable('comment', Cardinality::SINGLE, BaseType::STRING, new String($comment));
	    $transmitter->transmitItemVariable($variable, $transmissionId, $itemUri, $testUri);
	}
	
	/**
	 * Retrieve the Test Definition the test session is built
	 * from as an AssessmentTest object. This method
	 * also retrieves the compilation directory.
	 * 
	 * @return AssessmentTest The AssessmentTest object the current test session is built from.
	 */
	protected function retrieveTestDefinition() {
	    
	    $directories = array('private' => null, 'public' => null);
	    $directoryIds = explode('|', $this->getRequestParameter('QtiTestCompilation'));
	    
	    $directories['private'] = $this->getDirectory($directoryIds[0]);
	    $directories['public'] = $this->getDirectory($directoryIds[1]);
	    
	    $this->setCompilationDirectory($directories);
	    
	    $dirPath = $directories['private']->getPath();
	    $testFilePath = $dirPath .'compact-test.php';
	    
	    common_Logger::d("Loading QTI-PHP file at '${testFilePath}'.");
	    $doc = new PhpDocument();
	    $doc->load($testFilePath);
	    
	    $this->setTestDefinition($doc->getDocumentComponent());
	    
	    // Retrieve the compilation directory.
	    $pathinfo = pathinfo($testFilePath);
	}
	
	/**
	 * Retrieve the current test session as an AssessmentTestSession object from
	 * persistent storage.
	 * 
	 */
	protected function retrieveTestSession() {
	    $qtiStorage = $this->getStorage();
	    $sessionId = $this->getServiceCallId();
	    
	    if ($qtiStorage->exists($sessionId) === false) {
	        common_Logger::i("Instantiating QTI Assessment Test Session");
	        $this->setTestSession($qtiStorage->instantiate($sessionId));
	    }
	    else {
	        common_Logger::i("Retrieving QTI Assessment Test Session '${sessionId}'...");
	        $this->setTestSession($qtiStorage->retrieve($sessionId));
	    }
	}
	
	/**
	 * Retrieve the TAO Resource describing the test to be run.
	 * 
	 * @return core_kernel_classes_Resource A TAO Test Resource in database.
	 */
	protected function retrieveTestResource() {
	    $this->setTestResource(new core_kernel_classes_Resource($this->getRequestParameter('QtiTestDefinition')));
	}
	
	/**
	 * Persist the current assessment test session.
	 * 
	 * @throws RuntimeException If no assessment test session has started yet.
	 */
	protected function persistTestSession() {
	
	    common_Logger::d("Persisting Assessment Test Session.");
	    $this->getStorage()->persist($this->getTestSession());
	}
	
	/**
	 * Builds an associative array which describes the current AssessmentTestContext and set
	 * into the request data for a later use.
	 * 
	 * @return array The built AssessmentTestContext.
	 */
	protected function buildAssessmentTestContext() {
	    $session = $this->getTestSession();
	    $context = array();
	    
	    // The state of the test session.
	    $context['state'] = $session->getState();
	    
	    // Default values for the test session context.
	    $context['navigationMode'] = null;
	    $context['submissionMode'] = null;
	    $context['remainingAttempts'] = 0;
	    $context['isAdaptive'] = false;
	    
	    if ($session->getState() === AssessmentTestSessionState::INTERACTING) {
	        // The navigation mode.
	        $context['navigationMode'] = $session->getCurrentNavigationMode();
	         
	        // The submission mode.
	        $context['submissionMode'] = $session->getCurrentSubmissionMode();
	         
	        // The number of remaining attempts for the current item.
	        $context['remainingAttempts'] = $session->getCurrentRemainingAttempts();
	        
	        // Whether or not the current step is time out.
	        $context['isTimeout'] = $this->isTimeout();
	        
	        // The identifier of the current item.
	        $context['itemIdentifier'] = $session->getCurrentAssessmentItemRef()->getIdentifier();
	        
	        // The state of the current AssessmentTestSession.
	        $context['itemSessionState'] = $session->getCurrentAssessmentItemSession()->getState();
	             
	        // Whether the current item is adaptive.
	        $context['isAdaptive'] = $session->isCurrentAssessmentItemAdaptive();
	        
	        // Time constraints.
	        $context['timeConstraints'] = $this->timeConstraints();
	        
	        // The URLs to be called to move forward/backward in the Assessment Test Session or skip or comment.
	        $context['moveForwardUrl'] = $this->buildActionCallUrl('moveForward');
	        $context['moveBackwardUrl'] = $this->buildActionCallUrl('moveBackward');
	        $context['skipUrl'] = $this->buildActionCallUrl('skip');
	        $context['commentUrl'] = $this->buildActionCallUrl('comment');
	        $context['timeoutUrl'] = $this->buildActionCallUrl('timeout');
	        
	        // If the candidate is allowed to move backward e.g. first item of the test.
	        $context['canMoveBackward'] = $session->canMoveBackward();
	        
	        // The places in the test session where the candidate is allowed to jump to.
	        $context['jumps'] = $this->buildPossibleJumps();

	        // The code to be executed to build the ServiceApi object to be injected in the QTI Item frame.
	        $context['itemServiceApiCall'] = $this->buildServiceApi();
	        
	        // Rubric Blocks.
	        $rubrics = array();
	        
	        $compilationDirs = $this->getCompilationDirectory();
	        
	        // -- variables used in the included rubric block templates.
	        // base path (base URI to be used for resource inclusion).
	        $basePathVarName = TAOQTITEST_BASE_PATH_NAME;
	        $$basePathVarName = $compilationDirs['public']->getPublicAccessUrl();
	        
	        // state name (the variable to access to get the state of the assessmentTestSession).
	        $stateName = TAOQTITEST_RENDERING_STATE_NAME;
	        $$stateName = $session;
	        
	        // views name (the variable to be accessed for the visibility of rubric blocks).
	        $viewsName = TAOQTITEST_VIEWS_NAME;
	        $$viewsName = array(View::CANDIDATE);
	        
	        foreach ($session->getRoute()->current()->getRubricBlockRefs() as $rubric) {
	            ob_start();
	            include($compilationDirs['private']->getPath() . $rubric->getHref());
	            $rubrics[] = ob_get_clean();
	        }
	        
	        $context['rubrics'] = $rubrics;
	        
	        // Comment allowed?
	        $context['allowComment'] = $this->doesAllowComment();
	        
	        // Skipping allowed?
	        $context['allowSkipping'] = $this->doesAllowSkipping();
	    }
	    
	    $this->setData('assessmentTestContext', $context);
	    return $context;
	}
	
	/**
	 * Begin an attempt on the current item.
	 * 
	 */
	protected function beginAttempt() {
	    common_Logger::i("New attempt for item '" . $this->buildServiceCallId() .  "' begins.");
	    $this->getTestSession()->beginAttempt();
	    $this->setAttemptBegun(true);
	}
	
	/**
	 * Get the service call for the current item.
	 * 
	 * @return tao_models_classes_service_ServiceCall A ServiceCall object.
	 */
	protected function getItemServiceCall() {
	    $href = $this->getTestSession()->getCurrentAssessmentItemRef()->getHref();
	    
	    // retrive itemUri & itemPath. 
	    $parts = explode('|', $href);
	    
	    $definition =  new core_kernel_classes_Resource(INSTANCE_QTITEST_ITEMRUNNERSERVICE);
	    $serviceCall = new tao_models_classes_service_ServiceCall($definition);
	    
	    $uriResource = new core_kernel_classes_Resource(INSTANCE_FORMALPARAM_ITEMURI);
	    $uriParam = new tao_models_classes_service_ConstantParameter($uriResource, $parts[0]);
	    $serviceCall->addInParameter($uriParam);
	    
	    $pathResource = new core_kernel_classes_Resource(INSTANCE_FORMALPARAM_ITEMPATH);
	    $pathParam = new tao_models_classes_service_ConstantParameter($pathResource, $parts[1]);
	    $serviceCall->addInParameter($pathParam);
	    
	    $parentServiceCallIdResource = new core_kernel_classes_Resource(INSTANCE_FORMALPARAM_QTITESTITEMRUNNER_PARENTCALLID);
	    $parentServiceCallIdParam = new tao_models_classes_service_ConstantParameter($parentServiceCallIdResource, $this->getServiceCallId());
	    $serviceCall->addInParameter($parentServiceCallIdParam);
	    
	    $testDefinitionResource = new core_kernel_classes_Resource(INSTANCE_FORMALPARAM_QTITEST_TESTDEFINITION);
	    $testDefinitionParam = new tao_models_classes_service_ConstantParameter($testDefinitionResource, $this->getRequestParameter('QtiTestDefinition'));
	    $serviceCall->addInParameter($testDefinitionParam);
	    
	    $testCompilationResource = new core_kernel_classes_Resource(INSTANCE_FORMALPARAM_QTITEST_TESTCOMPILATION);
	    $testCompilationParam = new tao_models_classes_service_ConstantParameter($testCompilationResource, $this->getRequestParameter('QtiTestCompilation'));
	    $serviceCall->addInParameter($testCompilationParam);
	    
	    return $serviceCall;
	}
	
	protected function timeConstraints() {
	
	    $testSession = $this->getTestSession();
	    $constraints = array();
	
	    foreach ($testSession->getTimeConstraints() as $tc) {
	        // transmit to client only if there is a maximum remaining time.
	        if ($tc->getMaximumRemainingTime() !== false) {
	            $constraints[] = array(
                    'source' => $tc->getSource()->getIdentifier(),
                    'seconds' => $tc->getMaximumRemainingTime()->getSeconds(true)
	            );
	        }
	    }
	
	    return $constraints;
	}
	
	/**
	 * Build the service call id for the current item.
	 * 
	 * @return string A service call id composed of the identifier of the item and its occurence number in the route.
	 */
	protected function buildServiceCallId() {
	    $testSession = $this->getTestSession();
	    $sessionId = $this->getServiceCallId();
	    $itemId = $testSession->getCurrentAssessmentItemRef()->getIdentifier();
	    $occurence = $testSession->getCurrentAssessmentItemRefOccurence();
	    return "${sessionId}.${itemId}.${occurence}";
	}
	
	/**
	 * Build the serviceApi call for the current item and store
	 * it in the request parameters with key 'itemServiceApi'.
	 */
	protected function buildServiceApi() {
	    $serviceCall = $this->getItemServiceCall();
	    $serviceCallId = $this->buildServiceCallId();
	    $call = tao_helpers_ServiceJavascripts::getServiceApi($serviceCall, $serviceCallId);
	    $this->setData('itemServiceApi', $call);
	    return $call;
	}
	
	protected function buildCurrentItemSrc() {
	    $href = $this->getTestSession()->getCurrentAssessmentItemRef()->getHref();
	    $parts = explode('|', $href);
	     
	    return $this->buildItemSrc($parts[0], $parts[1]);
	}
	
	protected function getCurrentItemUri() {
	    $href = $this->getTestSession()->getCurrentAssessmentItemRef()->getHref();
	    $parts = explode('|', $href);
	
	    return $parts[0];
	}
	
	protected function doesAllowComment() {
	    $doesAllowComment = false;
	    
	    $routeItem = $this->getTestSession()->getRoute()->current();
	    $routeControl = $routeItem->getItemSessionControl();
	    
	    if (empty($routeControl) === false) {
	        $doesAllowComment = $routeControl->getItemSessionControl()->doesAllowComment();
	    }
	    
	    return $doesAllowComment;
	}
	
	protected function doesAllowSkipping() {
	    $doesAllowSkipping = false;
	    
	    $routeItem = $this->getTestSession()->getRoute()->current();
	    $routeControl = $routeItem->getItemSessionControl();
	    
	    if (empty($routeControl) === false) {
	        $doesAllowSkipping = $routeControl->getItemSessionControl()->doesAllowSkipping();
	    }
	    
	    return $doesAllowSkipping && $this->getTestSession()->getCurrentNavigationMode() === NavigationMode::LINEAR;
	}
	
	protected function buildItemSrc($itemUri, $itemPath) {
	    $src = BASE_URL . 'ItemRunner/index?';
	    $src .= 'itemUri=' . urlencode($itemUri);
	    $src.= '&itemPath=' . urlencode($itemPath);
	    $src.= '&QtiTestParentServiceCallId=' . urlencode($this->getServiceCallId());
	    $src.= '&QtiTestDefinition=' . urlencode($this->getRequestParameter('QtiTestDefinition'));
	    $src.= '&QtiTestCompilation=' . urlencode($this->getRequestParameter('QtiTestCompilation'));
	    $src.= '&standalone=true';
	    $src.= '&serviceCallId=' . $this->buildServiceCallId();
	    
	    return $src;
	}
	
	protected function buildActionCallUrl($action) {
	    $url = BASE_URL . "TestRunner/${action}";
	    $url.= '?QtiTestDefinition=' . urlencode($this->getRequestParameter('QtiTestDefinition'));
	    $url.= '&QtiTestCompilation=' . urlencode($this->getRequestParameter('QtiTestCompilation'));
	    $url.= '&standalone=' . urlencode($this->getRequestParameter('standalone'));
	    $url.= '&serviceCallId=' . urlencode($this->getRequestParameter('serviceCallId'));
	    return $url;
	}
	
	protected function buildPossibleJumps() {
	    $jumps = array();
	    
	    foreach ($this->getTestSession()->getPossibleJumps() as $jumpObject) {
	        $jump = array();
	        $jump['identifier'] = $jumpObject->getTarget()->getAssessmentItemRef()->getIdentifier();
	        $jump['position'] = $jumpObject->getPosition();
	        
	        $jumps[] = $jump;
	    }
	    
	    return $jumps;
	}
	
	protected function registerAssessmentItemSessionException(AssessmentItemSessionException $e) {
	    switch ($e->getCode()) {
	        case AssessmentItemSessionException::ATTEMPTS_OVERFLOW:
	            
	        break;
	        
	        case AssessmentItemSessionException::DURATION_OVERFLOW:
	            
	        break;
	        
	        case AssessmentItemSessionException::DURATION_UNDERFLOW:
	            
	        break;
	        
	        case AssessmentItemSessionException::INVALID_RESPONSE:
	            
	        break;
	        
	        case AssessmentItemSessionException::RUNTIME_ERROR:
	            
	        break;
	        
	        case AssessmentItemSessionException::SKIPPING_FORBIDDEN:
	            
	        break;
	        
	        case AssessmentItemSessionException::STATE_VIOLATION:
	            
	        break;
	        
	        case AssessmentItemSessionException::UNKNOWN:
	            
	        break;
	        
	        default:
	            
	        break;
	    }
	}
	
	protected function registerAssessmentTestSessionException(AssessmentTestSessionException $e) {
	    switch ($e->getCode()) {
	        case AssessmentTestSessionException::ASSESSMENT_SECTION_DURATION_OVERFLOW:
	            
	        break;
	        
	        case AssessmentTestSessionException::FORBIDDEN_JUMP:
	            
	        break;
	        
	        case AssessmentTestSessionException::LOGIC_ERROR:
	            
	        break;
	        
	        case AssessmentTestSessionException::NAVIGATION_MODE_VIOLATION:
	            
	        break;
	        
	        case AssessmentTestSessionException::OUTCOME_PROCESSING_ERROR:
	            
	        break;
	        
	        case AssessmentTestSessionException::RESPONSE_PROCESSING_ERROR:
	            
	        break;
	        
	        case AssessmentTestSessionException::RESULT_SUBMISSION_ERROR:
	            
	        break;
	        
	        case AssessmentTestSessionException::STATE_VIOLATION:
	            
	        break;
	        
	        case AssessmentTestSessionException::TEST_PART_DURATION_OVERFLOW:
	            $this->setCurrentError(self::ERROR_TESTPART_TIME_OVERFLOW);
	        break;
	        
	        case AssessmentTestSessionException::UNKNOWN:
	        default:
	            $this->setCurrentError(self::ERROR_UNKNOWN);
	        break;
	    }
	}
}
