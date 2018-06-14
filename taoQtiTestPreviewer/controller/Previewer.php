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
 * Copyright (c) 2018 (original work) Open Assessment Technologies SA ;
 */

namespace oat\taoQtiTestPreviewer\controller;

use oat\taoQtiTestPreviewer\models\ItemPreviewer;
use oat\taoResultServer\models\classes\ResultServerService;
use tao_actions_ServiceModule as ServiceModule;
use taoQtiTest_helpers_TestRunnerUtils as TestRunnerUtils;

/**
 * Class taoQtiTest_actions_Runner
 *
 * Serves QTI implementation of the test runner
 */
class Previewer extends ServiceModule
{
    /**
     * taoQtiTest_actions_Runner constructor.
     */
    public function __construct()
    {
        // Prevent anything to be cached by the client.
        TestRunnerUtils::noHttpClientCache();
    }

    /**
     * Gets an error response object
     * @param Exception [$e] Optional exception from which extract the error context
     * @param array $prevResponse Response before catch
     * @return array
     */
    protected function getErrorResponse($e = null)
    {
        $response = [
            'success' => false,
            'type' => 'error',
        ];

        if ($e) {
            if ($e instanceof \Exception) {
                $response['type'] = 'exception';
                $response['code'] = $e->getCode();
            }

            if ($e instanceof \common_exception_UserReadableException) {
                $response['message'] = $e->getUserMessage();
            } else {
                $response['message'] = __('An error occurred!');
            }

            switch (true) {
                case $e instanceof \tao_models_classes_FileNotFoundException:
                    $response['type'] = 'FileNotFound';
                    $response['message'] = __('File not found');
                    break;

                case $e instanceof \common_exception_Unauthorized:
                    $response['code'] = 403;
                    break;
            }
        }

        return $response;
    }

    /**
     * Gets an HTTP response code
     * @param Exception [$e] Optional exception from which extract the error context
     * @return int
     */
    protected function getErrorCode($e = null)
    {
        $code = 200;
        if ($e) {
            $code = 500;

            switch (true) {
                case $e instanceof \common_exception_NotImplemented:
                case $e instanceof \common_exception_NoImplementation:
                case $e instanceof \common_exception_Unauthorized:
                    $code = 403;
                    break;

                case $e instanceof \tao_models_classes_FileNotFoundException:
                    $code = 404;
                    break;
            }
        }
        return $code;
    }

    /**
     * @param string $resultId
     * @param string $deliveryUri
     * @return string
     * @throws \common_exception_Error
     */
    protected function getUserLanguage($resultId, $deliveryUri)
    {
        /** @var ResultServerService $resultServerService */
        $resultServerService = $this->getServiceLocator()->get(ResultServerService::SERVICE_ID);
        /** @var \taoResultServer_models_classes_ReadableResultStorage $implementation */
        $implementation = $resultServerService->getResultStorage($deliveryUri);

        $testTaker = new \core_kernel_users_GenerisUser(new \core_kernel_classes_Resource($implementation->getTestTaker($resultId)));
        $lang = $testTaker->getPropertyValues(\oat\generis\model\GenerisRdf::PROPERTY_USER_DEFLG);

        return empty($lang) ? DEFAULT_LANG : (string) current($lang);
    }

    /**
     * Initializes the delivery session
     */
    public function init()
    {
        $code = 200;

        try {

            $serviceCallId = $this->getRequestParameter('serviceCallId');

            $response = [
                'success' => $serviceCallId == 'previewer',
                'itemIdentifier' => null,
                'itemData' => null
            ];

        } catch (\Exception $e) {
            $response = $this->getErrorResponse($e);
            $code = $this->getErrorCode($e);
        }

        $this->returnJson($response, $code);
    }

    /**
     * Provides the definition data and the state for a particular item
     */
    public function getItem()
    {
        $code = 200;

        try {
            $itemUri = $this->getRequestParameter('itemUri');
            $resultId = $this->getRequestParameter('resultId');

            $response = [
                'baseUrl' => '',
                'content' => [],
            ];

            // previewing a result
            if ($resultId) {
                if (!$this->hasRequestParameter('itemDefinition')) {
                    throw new \common_exception_MissingParameter('itemDefinition', $this->getRequestURI());
                }

                if (!$this->hasRequestParameter('deliveryUri')) {
                    throw new \common_exception_MissingParameter('deliveryUri', $this->getRequestURI());
                }

                $itemDefinition = $this->getRequestParameter('itemDefinition');
                $delivery = new \core_kernel_classes_Resource($this->getRequestParameter('deliveryUri'));

                $itemPreviewer = new ItemPreviewer();
                $itemPreviewer->setServiceLocator($this->getServiceLocator());

                $response['content'] = $itemPreviewer->setItemDefinition($itemDefinition)
                    ->setUserLanguage($this->getUserLanguage($resultId, $delivery->getUri()))
                    ->setDelivery($delivery)
                    ->loadCompiledItemData();

                $response['baseUrl'] = $itemPreviewer->getBaseUrl();

            } else if ($itemUri) {
                // Load RESOURCE item data
                // TODO
            } else {
                throw new \common_exception_BadRequest('Either itemUri or resultId needs to be provided.');
            }

            $response['success'] = true;
        } catch (\Exception $e) {
            $response = $this->getErrorResponse($e);
            $code = $this->getErrorCode($e);
        }

        $this->returnJson($response, $code);
    }

    /**
     * Stores the state object and the response set of a particular item
     */
    public function submitItem()
    {
        $code = 200;

        try {

            $displayFeedback = false;

            // @TODO implement the scoring
            $response = [
                'success' => true,
                'displayFeedbacks' => $displayFeedback,
                'itemSession' => [
                    'SCORE' => [
                        'base' => [
                            'float' => 0
                        ]
                    ]
                ]
            ];

            if ($displayFeedback == true) {
                $response['feedbacks'] = [];
                $response['itemSession'] = [];
            }


        } catch (\Exception $e) {
            $response = $this->getErrorResponse($e);
            $code = $this->getErrorCode($e);
        }

        $this->returnJson($response, $code);
    }
}
