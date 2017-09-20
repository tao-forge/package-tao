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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
namespace oat\taoDelivery\controller;


use common_exception_NotFound;
use common_exception_Unauthorized;
use common_Logger;
use common_exception_Error;
use common_session_SessionManager;
use oat\tao\model\mvc\DefaultUrlService;
use oat\taoDelivery\helper\Delivery as DeliveryHelper;
use oat\taoDelivery\model\AssignmentService;
use oat\taoDelivery\model\authorization\AuthorizationService;
use oat\taoDelivery\model\authorization\AuthorizationProvider;
use oat\taoDelivery\model\execution\DeliveryExecution;
use oat\taoDelivery\model\execution\DeliveryServerService;
use oat\taoDelivery\model\execution\Service;
use oat\taoDelivery\model\execution\ServiceProxy;
use oat\taoDelivery\models\classes\ReturnUrlService;
use oat\taoDelivery\model\authorization\UnAuthorizedException;
use oat\tao\helpers\Template;
use oat\taoDelivery\model\execution\StateServiceInterface;
use oat\taoDelivery\models\classes\theme\DeliveryThemeDetailsProvider;

/**
 * DeliveryServer Controller
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoDelivery
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class DeliveryServer extends \tao_actions_CommonModule
{
    /**
     * @var Service
     */
    private $executionService;

    /**
	 * constructor: initialize the service and the default data
	 * @return DeliveryServer
	 */
	public function __construct()
	{
		$this->service = $this->getServiceManager()->get(DeliveryServerService::SERVICE_ID);
		$this->executionService = ServiceProxy::singleton();
	}
	
	/**
	 * @return DeliveryExecution
	 */
	protected function getCurrentDeliveryExecution() {
	    $id = \tao_helpers_Uri::decode($this->getRequestParameter('deliveryExecution'));
	    return $this->executionService->getDeliveryExecution($id);
	}

    /**
     * Set a view with the list of process instances (both started or finished) and available process definitions
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param processDefinitionUri
     * @return void
     * @throws \common_exception_Error
     */
	public function index(){

		$user = common_session_SessionManager::getSession()->getUser();
		
		/**
		 * Retrieve resumable deliveries (via delivery execution)
		 */
		$resumableData = array();
		foreach ($this->service->getResumableDeliveries($user) as $de) {
		    $resumableData[] = DeliveryHelper::buildFromDeliveryExecution($de);
		}
		$this->setData('resumableDeliveries', $resumableData);
		
		$assignmentService= $this->getServiceManager()->get(AssignmentService::SERVICE_ID);
		
		$deliveryData = array();
		foreach ($assignmentService->getAssignments($user) as $delivery)
		{
		    $deliveryData[] = DeliveryHelper::buildFromAssembly($delivery, $user);
		}
		$this->setData('availableDeliveries', $deliveryData);
                
        /**
         * Header & footer info
         */
        $this->setData('showControls', $this->showControls());
        $this->setData('userLabel', common_session_SessionManager::getSession()->getUserLabel());

        // Require JS config
        $this->setData('client_config_url', $this->getClientConfigUrl());
        $this->setData('client_timeout', $this->getClientTimeout());

        $loaderRenderer = new \Renderer(Template::getTemplate('DeliveryServer/blocks/loader.tpl', 'taoDelivery'));
        $loaderRenderer->setData('client_config_url', $this->getClientConfigUrl());
        $loaderParams = [];
        if ($this->getRequest()->hasParameter('warning') && !empty($this->getRequest()->getParameter('warning'))) {
            $loaderParams['message'] = [
                'level' => 'danger',
                'content' => $this->getRequest()->getParameter('warning'),
                'timeout' => -1
            ];
        }
        $loaderRenderer->setData('parameters', $loaderParams);
        
        /* @var $urlRouteService DefaultUrlService */
        $urlRouteService = $this->getServiceManager()->get(DefaultUrlService::SERVICE_ID);
        $this->setData('logout', $urlRouteService->getUrl('logoutDelivery' , []));
        
        /**
         * Layout template + real template inclusion
         */
        $this->setData('additional-header', $loaderRenderer);
        $this->setData('content-template', 'DeliveryServer/index.tpl');
        $this->setData('content-extension', 'taoDelivery');
        $this->setView('DeliveryServer/layout.tpl', 'taoDelivery');
    }

    /**
     * Init a delivery execution from the current delivery.
     *
     * @throws common_exception_Unauthorized
     * @return DeliveryExecution the selected execution
     * @throws \common_exception_Error
     */
    protected function _initDeliveryExecution() {
        $compiledDelivery  = new \core_kernel_classes_Resource(\tao_helpers_Uri::decode($this->getRequestParameter('uri')));
        $user              = common_session_SessionManager::getSession()->getUser();

        $assignmentService = $this->getServiceManager()->get(AssignmentService::SERVICE_ID);

        $this->verifyDeliveryStartAuthorized($compiledDelivery->getUri());

        //check if the assignment allows the user to start the delivery and the authorization provider
        if (!$assignmentService->isDeliveryExecutionAllowed($compiledDelivery->getUri(), $user) ) {
            throw new common_exception_Unauthorized();
        }
        $stateService = $this->getServiceManager()->get(StateServiceInterface::SERVICE_ID);
        /** @var DeliveryExecution $deliveryExecution */
        $deliveryExecution = $stateService->createDeliveryExecution($compiledDelivery->getUri(), $user, $compiledDelivery->getLabel());


        return $deliveryExecution;
    }


    /**
     * Init the selected delivery execution and forward to the execution screen
     */
    public function initDeliveryExecution() {
        try {
            $deliveryExecution = $this->_initDeliveryExecution();
            //if authorized we can move to this URL.
            $this->redirect(_url('runDeliveryExecution', null, null, array('deliveryExecution' => $deliveryExecution->getIdentifier())));
        } catch (UnAuthorizedException $e) {
            return $this->redirect($e->getErrorPage());
        } catch (common_exception_Unauthorized $e) {
            return $this->returnJson([
                'success' => false,
                'message' => __('You are no longer allowed to take this test')
            ], 403);
        }
    }

    /**
     * Displays the execution screen
     * 
     * @throws common_exception_Error
     */
	public function runDeliveryExecution() {
	    $deliveryExecution = $this->getCurrentDeliveryExecution();

        // Sets the deliveryId to session.

        $this->setSessionAttribute(
            DeliveryThemeDetailsProvider::getDeliveryIdSessionKey($deliveryExecution->getIdentifier()),
            $deliveryExecution->getDelivery()->getUri()
        );

        try {
            $this->verifyDeliveryExecutionAuthorized($deliveryExecution);
        } catch (UnAuthorizedException $e) {
            return $this->redirect($e->getErrorPage());
        }

	    $userUri = common_session_SessionManager::getSession()->getUserUri();
	    if ($deliveryExecution->getUserIdentifier() != $userUri) {
	        throw new common_exception_Error('User '.$userUri.' is not the owner of the execution '.$deliveryExecution->getIdentifier());
	    }
	    
	    $delivery = $deliveryExecution->getDelivery();
	    $this->initResultServer($delivery, $deliveryExecution->getIdentifier());

        $deliveryExecutionStateService = $this->getServiceManager()->get(StateServiceInterface::SERVICE_ID);
        $deliveryExecutionStateService->run($deliveryExecution);

        /**
         * Use particular delivery container
         */
        $container = $this->service->getDeliveryContainer($deliveryExecution);

	    // Require JS config
        $container->setData('client_config_url', $this->getClientConfigUrl());
        $container->setData('client_timeout', $this->getClientTimeout());
        // Delivery params
        $container->setData('returnUrl', $this->getReturnUrl());
        $container->setData('finishUrl', $this->getfinishDeliveryExecutionUrl());
        
        $this->setData('additional-header', $container->getContainerHeader());
        $this->setData('container-body', $container->getContainerBody());
        
		
		/**
		 * Delivery header & footer info
		 */
	    $this->setData('userLabel', common_session_SessionManager::getSession()->getUserLabel());
	    $this->setData('showControls', $this->showControls());
        $this->setData('returnUrl', $this->getReturnUrl());

        /* @var $urlRouteService DefaultUrlService */
        $urlRouteService = $this->getServiceManager()->get(DefaultUrlService::SERVICE_ID);
        $this->setData('logout', $urlRouteService->getUrl('logoutDelivery' , []));

        /**
         * Layout template + real template inclusion
         */
        $this->setData('content-template', 'DeliveryServer/runDeliveryExecution.tpl');
        $this->setData('content-extension', 'taoDelivery');
        $this->setView('DeliveryServer/layout.tpl', 'taoDelivery');
	}

    /**
     * Finish the delivery execution
     * @throws \common_exception_Error
     */
	public function finishDeliveryExecution() {
	    $deliveryExecution = $this->getCurrentDeliveryExecution();
	    if ($deliveryExecution->getUserIdentifier() == common_session_SessionManager::getSession()->getUserUri()) {
            $stateService = $this->getServiceManager()->get(StateServiceInterface::SERVICE_ID);
	        $success = $stateService->finish($deliveryExecution);
	    } else {
	        common_Logger::w('Non owner '.common_session_SessionManager::getSession()->getUserUri().' tried to finish deliveryExecution '.$deliveryExecution->getIdentifier());
	        $success = false;
	    }
	    echo json_encode(array(
	        'success'      => $success,
	    	'destination'  => $this->getReturnUrl()
	    ));
	}
	
	/**
	 * Initialize the result server using the delivery configuration and for this results session submission
     *
     * @param $compiledDelivery
     * @param $executionIdentifier
     */
    protected function initResultServer($compiledDelivery, $executionIdentifier)
    {
        $resultServerCallOverride =  $this->hasRequestParameter('resultServerCallOverride') ? $this->getRequestParameter('resultServerCallOverride') : false;
        if (!($resultServerCallOverride)) {
            $this->service->initResultServer($compiledDelivery, $executionIdentifier);
        }
    }
    
    /**
     * Defines if the top and bottom action menu should be displayed or not
     * 
     * @return boolean
     */
	protected function showControls()
	{
	    return true;
	}

    /**
     * Defines the returning URL in the top-right corner action menu
     *
     * @return string
     * @throws common_exception_NotFound
     */
	protected function getReturnUrl()
	{
	    if($this->getServiceManager()->has(ReturnUrlService::SERVICE_ID)){
            $deliveryExecution = $this->getCurrentDeliveryExecution();
	        return $this->getServiceManager()->get(ReturnUrlService::SERVICE_ID)->getReturnUrl($deliveryExecution->getIdentifier());
        }
	    return _url('index', 'DeliveryServer', 'taoDelivery');
	}
    
    /**
     * Defines the URL of the finish delivery execution action
     * 
     * @return string
     */
    protected function getfinishDeliveryExecutionUrl()
    {
        return _url('finishDeliveryExecution');
    }


    /**
     * Gives you the authorization provider for the given execution.
     *
     * @return AuthorizationProvider
     */
    protected function getAuthorizationProvider()
    {
        $authService = $this->getServiceManager()->get(AuthorizationService::SERVICE_ID);
        return $authService->getAuthorizationProvider();
    }

    /**
     * Verify if the start of the delivery is allowed.
     * Throws an exception if not
     *
     * @param string $deliveryId
     * @throws UnAuthorizedException
     * @throws \common_exception_Error
     * @throws \common_exception_Unauthorized
     */
    protected function verifyDeliveryStartAuthorized($deliveryId)
    {
        $user = common_session_SessionManager::getSession()->getUser();
        $this->getAuthorizationProvider()->verifyStartAuthorization($deliveryId, $user);
    }

    /**
     * Check wether the delivery execution is authorized to run
     * Throws an exception if not
     *
     * @param DeliveryExecution $deliveryExecution
     * @return boolean
     * @throws \common_exception_Unauthorized
     * @throws \common_exception_Error
     * @throws UnAuthorizedException
     */
    protected function verifyDeliveryExecutionAuthorized(DeliveryExecution $deliveryExecution)
    {
        $user = common_session_SessionManager::getSession()->getUser();
        $this->getAuthorizationProvider()->verifyResumeAuthorization($deliveryExecution, $user);
    }

    public function logout()
    {
        common_session_SessionManager::endSession();
        /* @var $urlRouteService \oat\tao\model\mvc\DefaultUrlService */
        $urlRouteService = $this->getServiceManager()->get(\oat\tao\model\mvc\DefaultUrlService::SERVICE_ID);
        $this->redirect($urlRouteService->getRedirectUrl('logoutDelivery'));
        
    }
}
