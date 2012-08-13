<?php

error_reporting(E_ALL);

/**
 * The default implementation of the result server,
 * to be used when no explicit external result server
 * is defined.
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoDelivery
 * @subpackage actions
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include taoDelivery_actions_ResultDelivery
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('taoDelivery/actions/class.ResultDelivery.php');

/**
 * include taoDelivery_models_classes_ResultServerInterface
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('taoDelivery/models/classes/interface.ResultServerInterface.php');

/* user defined includes */
// section 127-0-1-1-6a6ca908:135cdb14af0:-8000:0000000000003837-includes begin
// section 127-0-1-1-6a6ca908:135cdb14af0:-8000:0000000000003837-includes end

/* user defined constants */
// section 127-0-1-1-6a6ca908:135cdb14af0:-8000:0000000000003837-constants begin
// section 127-0-1-1-6a6ca908:135cdb14af0:-8000:0000000000003837-constants end

/**
 * The default implementation of the result server,
 * to be used when no explicit external result server
 * is defined.
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoDelivery
 * @subpackage actions
 */
class taoDelivery_actions_InternalResultServer
    extends taoDelivery_actions_ResultDelivery
        implements taoDelivery_models_classes_ResultServerInterface
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute DELIVERYRESULT_SESSION_SERIAL
     *
     * @access private
     * @var string
     */
    const DELIVERYRESULT_SESSION_SERIAL = 'resultserver_dr';

    // --- OPERATIONS ---

    /**
     * Save the data that is pushed to the server
     * this can be either answers, scores or variables
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function save()
    {
        // section 127-0-1-1-6a6ca908:135cdb14af0:-8000:000000000000383B begin
        $saved = false;
		
        
        // save Answers
    	if($this->hasRequestParameter('taoVars') && is_array($this->getRequestParameter('taoVars'))){
    		$executionEnvironment = $this->getExecutionEnvironment();
			$resultNS = $executionEnvironment['localNamespace'];
		
			//here we save the TAO variables
			$taoVars = array();
			$variableService = wfEngine_models_classes_VariableService::singleton();
			foreach($this->getRequestParameter('taoVars') as $key => $encoded){
				list ($namespace, $suffix) = explode('#', $key, 2);
				switch ($suffix) {
					case 'ENDORSMENT':
						$variableService->save(array('PREV_ENDORSMENT' => $encoded));
						break;
					case ANSWERED_VALUES_ID:
						foreach (json_decode($encoded, true) as $varIdentifier => $varValue) {
							$this->resultService->setAnsweredValue(
								$this->getCurrentDeliveryResult(),
								$this->getCurrentActivityExecution(),
								$varIdentifier,
								$varValue
							);
						}
						break;
						
					default:
						common_Logger::w('No treatment of '.$suffix);
					break;
				}
			}
		}

        //save scores
        //save variables
        parent::save();
        // section 127-0-1-1-6a6ca908:135cdb14af0:-8000:000000000000383B end
    }

    /**
     * trace the events generated by the delivery
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function traceEvents()
    {
        // section 127-0-1-1-6a6ca908:135cdb14af0:-8000:000000000000383D begin
        parent::traceEvents();
        // section 127-0-1-1-6a6ca908:135cdb14af0:-8000:000000000000383D end
    }

    /**
     * evaluate the user's answers on the server
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function evaluate()
    {
        // section 127-0-1-1-6a6ca908:135cdb14af0:-8000:000000000000383F begin
        parent::evaluate();
        // section 127-0-1-1-6a6ca908:135cdb14af0:-8000:000000000000383F end
    }

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function __construct()
    {
        // section 127-0-1-1-6a6ca908:135cdb14af0:-8000:0000000000003844 begin
        parent::__construct();
        
        $this->resultService = taoResults_models_classes_ResultsService::singleton();
        
        // this test is worthless, since we can not progress if we don't have the executionEvironement in our Session
        if(!$this->hasRequestParameter('token')
        	|| !$this->authenticate($this->getRequestParameter('token'))) {
        		throw new taoDelivery_models_classes_SubjectException('Invalid Token'); 
		}
        // section 127-0-1-1-6a6ca908:135cdb14af0:-8000:0000000000003844 end
    }

    /**
     * Short description of method getCurrentActivityExecution
     *
     * @access private
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return core_kernel_classes_Resource
     */
    private function getCurrentActivityExecution()
    {
        $returnValue = null;

        // section 127-0-1-1-6a6ca908:135cdb14af0:-8000:000000000000384D begin
        
        // cost of current implementation: 1 query
        
        // since we are on the same server we can load the environment imediately from session
        $environment = $this->getExecutionEnvironment();
        
        $classProcessInstance = new core_kernel_classes_Resource($environment[CLASS_PROCESS_EXECUTIONS]['uri']);
        
        $returnValue = $classProcessInstance->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_PROCESSINSTANCES_CURRENTACTIVITYEXECUTIONS));
        // section 127-0-1-1-6a6ca908:135cdb14af0:-8000:000000000000384D end

        return $returnValue;
    }

    /**
     * Short description of method getCurrentDeliveryResult
     *
     * @access private
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return core_kernel_classes_Resource
     */
    private function getCurrentDeliveryResult()
    {
        $returnValue = null;

        // section 127-0-1-1-6a6ca908:135cdb14af0:-8000:000000000000384F begin
	    $environment = $this->getExecutionEnvironment();
        $classProcessInstance = new core_kernel_classes_Resource($environment[CLASS_PROCESS_EXECUTIONS]['uri']);
		
        if (tao_models_classes_cache_SessionCache::singleton()->contains(self::DELIVERYRESULT_SESSION_SERIAL)) {
        	$data = tao_models_classes_cache_SessionCache::singleton()->get(self::DELIVERYRESULT_SESSION_SERIAL);
        	if (isset($data['process']) && $data['process'] == $classProcessInstance->getUri()) {
	        	$returnValue = new core_kernel_classes_Resource($data['dr']);
        	} else {
				common_Logger::i('recovered Delivery Result does not match ProcessExecution');
        	}
        }
        if (is_null($returnValue)) {
        	// cost of current implementation: EXPENSIV SEARCH
	        $localNS = core_kernel_classes_Session::singleton()->getNameSpace();
	        $drClass = new core_kernel_classes_Class(TAO_DELIVERY_RESULT);
	        
	        $result = $drClass->searchInstances(array(
	        	PROPERTY_RESULT_OF_PROCESS	=> $classProcessInstance->getUri()
	        ));
	        
	        if (count($result) > 1) {
	        	throw new common_exception_Error('More then 1 deliveryResult for process '.$classProcessInstance);
	        } elseif (count($result) == 1) {
	        	$returnValue = array_shift($result);
				common_Logger::d('found Delivery Result after search for '.$classProcessInstance);
	        } else {
				// create Instance
				// since we are on the same server we can load the environment imediately from session
	        	$environment = $this->getExecutionEnvironment();
				$subject = new core_kernel_classes_Resource($environment[TAO_SUBJECT_CLASS]['uri']);
				$delivery = new core_kernel_classes_Resource($environment[TAO_DELIVERY_CLASS]['uri']);
				
				$label = $delivery->getLabel().' '.$subject->getLabel();
				$returnValue = $drClass->createInstanceWithProperties(array(
					RDFS_LABEL					=> $label,
					PROPERTY_RESULT_OF_PROCESS	=> $classProcessInstance,
					PROPERTY_RESULT_OF_DELIVERY => $delivery,
					PROPERTY_RESULT_OF_SUBJECT	=> $subject,
				));
				common_Logger::d('spawned Delivery Result for '.$classProcessInstance);
	        }
	        $data = array('process' => $classProcessInstance->getUri(), 'dr' => $returnValue->getUri());
	        tao_models_classes_cache_SessionCache::singleton()->put($data, self::DELIVERYRESULT_SESSION_SERIAL);
        }
    	
        // section 127-0-1-1-6a6ca908:135cdb14af0:-8000:000000000000384F end

        return $returnValue;
    }

} /* end of class taoDelivery_actions_InternalResultServer */

?>