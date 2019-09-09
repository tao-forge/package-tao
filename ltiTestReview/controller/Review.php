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
 * Copyright (c) 2019 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\taoReview\controller;

use common_Exception;
use common_exception_Error;
use common_exception_NotFound;
use oat\generis\model\GenerisRdf;
use oat\generis\model\OntologyAwareTrait;
use oat\oatbox\service\exception\InvalidServiceManagerException;
use oat\taoLti\models\classes\LtiException;
use oat\taoLti\models\classes\LtiInvalidLaunchDataException;
use oat\taoLti\models\classes\LtiService;
use oat\taoLti\models\classes\LtiVariableMissingException;
use oat\taoLti\models\classes\TaoLtiSession;
use oat\taoProctoring\model\execution\DeliveryExecutionManagerService;
use oat\taoQtiTestPreviewer\models\ItemPreviewer;
use oat\taoResultServer\models\classes\ResultServerService;
use oat\taoReview\models\DeliveryExecutionFinderService;
use oat\taoReview\models\QtiRunnerInitDataBuilderFactory;
use tao_actions_SinglePageModule;

/**
 * Review controller class thar provides data for js-application
 * @package oat\taoReview\controller
 */
class Review extends tao_actions_SinglePageModule
{
    use OntologyAwareTrait;

    /** @var TaoLtiSession */
    private $ltiSession;

    public function __construct() {
        parent::__construct();

        $this->ltiSession = LtiService::singleton()->getLtiSession();
    }

    /**
     * @throws InvalidServiceManagerException
     * @throws LtiException
     * @throws LtiInvalidLaunchDataException
     * @throws LtiVariableMissingException
     * @throws common_exception_Error
     * @throws common_exception_NotFound
     */
    public function index(): void
    {
        $launchData = $this->ltiSession->getLaunchData();

        /** @var DeliveryExecutionFinderService $finder */
        $finder = $this->getServiceLocator()->get(DeliveryExecutionFinderService::SERVICE_ID);

        $execution = $finder->findDeliveryExecution($launchData);
        $delivery = $execution->getDelivery();

        $data = [
            'execution' => $execution->getUri(),
            'delivery'  => $delivery->getUri(),
            'show-score' => $finder->getShowScoreOption($launchData),
            'show-correct' => $finder->getShowCorrectOption($launchData)
        ];

        $this->composeView('delegated-view', $data, 'pages/index.tpl', 'tao');
    }

    /**
     * @throws common_Exception
     */
    public function init(): void
    {
        $this->validateCsrf();

        /** @var QtiRunnerInitDataBuilderFactory $dataBuilder */
        $dataBuilder = $this->getServiceLocator()->get(QtiRunnerInitDataBuilderFactory::SERVICE_ID);

        $params = $this->getPsrRequest()->getQueryParams();

        if (isset($params['serviceCallId'])) {

            /** @var DeliveryExecutionFinderService $finder */
            $finder = $this->getServiceLocator()->get(DeliveryExecutionFinderService::SERVICE_ID);

            $data = $dataBuilder->create()->build($params['serviceCallId'], $finder->getShowScoreOption($this->ltiSession->getLaunchData()));
        }

        $this->returnJson($data ?? []);
    }

    /**
     * Provides the definition data and the state for a particular item
     */
    public function getItem(): void
    {
        $this->validateCsrf();

        $params = $this->getPsrRequest()->getQueryParams();

        $deliveryExecutionId = $params['serviceCallId'];
        $itemDefinition = $params['itemUri'];

        /** @var DeliveryExecutionManagerService $deManagerService */
        $deManagerService = $this->getServiceLocator()->get(DeliveryExecutionManagerService::SERVICE_ID);
        $execution = $deManagerService->getDeliveryExecutionById($deliveryExecutionId);
        $delivery = $execution->getDelivery();

        $itemPreviewer = new ItemPreviewer();
        $itemPreviewer->setServiceLocator($this->getServiceLocator());

        $itemPreviewer
            ->setItemDefinition($itemDefinition)
            ->setUserLanguage($this->getUserLanguage($deliveryExecutionId, $delivery->getUri()))
            ->setDelivery($delivery);

        $itemData = $itemPreviewer->loadCompiledItemData();

        /** @var DeliveryExecutionFinderService $finder */
        $finder = $this->getServiceLocator()->get(DeliveryExecutionFinderService::SERVICE_ID);

        if (!empty($itemData['data']['responses'])
            && $finder->getShowCorrectOption($this->ltiSession->getLaunchData())
        ) {
            $itemData['data']['responses'] = array_merge_recursive(...[
                $itemData['data']['responses'],
                $itemPreviewer->loadCompiledItemVariables()
            ]);
        }

        $response['content'] = $itemData;
        $response['baseUrl'] = $itemPreviewer->getBaseUrl();
        $response['success'] = true;

        $this->returnJson($response);
    }

    /**
     * @param string $resultId
     * @param string $deliveryUri
     *
     * @return string
     * @throws common_exception_Error
     */
    protected function getUserLanguage($resultId, $deliveryUri)
    {
        /** @var ResultServerService $resultServerService */
        $resultServerService = $this->getServiceLocator()->get(ResultServerService::SERVICE_ID);
        /** @var \taoResultServer_models_classes_ReadableResultStorage $implementation */
        $implementation = $resultServerService->getResultStorage($deliveryUri);

        $testTaker = new \core_kernel_users_GenerisUser($this->getResource($implementation->getTestTaker($resultId)));
        $lang = $testTaker->getPropertyValues(GenerisRdf::PROPERTY_USER_DEFLG);

        return empty($lang) ? DEFAULT_LANG : (string)current($lang);
    }

}
