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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA ;
 */

declare(strict_types=1);

namespace oat\taoQtiTestPreviewer\controller;

use InvalidArgumentException;
use Throwable;
use common_exception_UserReadableException;
use oat\taoQtiTestPreviewer\models\testConfiguration\service\TestPreviewerConfigurationService;
use oat\taoQtiTestPreviewer\models\test\TestPreviewRequest;
use oat\taoQtiTestPreviewer\models\test\service\TestPreviewer as TestPreviewerService;
use oat\tao\controller\ServiceModule;
use oat\tao\model\http\HttpJsonResponseTrait;

class TestPreviewer extends ServiceModule
{
    use HttpJsonResponseTrait;

    public function init()
    {
        try {
            $requestParams = $this->getPsrRequest()->getQueryParams();

            if (empty($requestParams['testUri'])) {
                throw  new InvalidArgumentException('Required `testUri` param is missing ');
            }

            $response = $this->getTestPreviewerService()
                ->createPreview(new TestPreviewRequest($requestParams['testUri']));

            $this->setNoCacheHeaders();

            $this->setSuccessJsonResponse(
                [
                    'success' => true,
                    'testData' => [],
                    'testContext' => [],
                    'testMap' => $response->getMap()->getMap(),
                ]
            );
        } catch (Throwable $exception) {
            $message = $exception instanceof common_exception_UserReadableException
                ? $exception->getUserMessage()
                : $exception->getMessage();

            $this->setErrorJsonResponse($message);
        }
    }

    public function configuration(): void
    {
        try {
            $this->setNoCacheHeaders();

            $this->setSuccessJsonResponse(
                $this->getTestPreviewerConfigurationService()->getConfiguration()
            );
        } catch (Throwable $exception) {
            $message = $exception instanceof common_exception_UserReadableException
                ? $exception->getUserMessage()
                : $exception->getMessage();

            $this->setErrorJsonResponse($message);
        }
    }

    private function setNoCacheHeaders(): void
    {
        $this->getResponseFormatter()
            ->addHeader('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->addHeader('Pragma', 'no-cache')
            ->addHeader('Expires', '0');
    }

    private function getTestPreviewerConfigurationService(): TestPreviewerConfigurationService
    {
        return $this->getServiceLocator()->get(TestPreviewerConfigurationService::class);
    }

    private function getTestPreviewerService(): TestPreviewerService
    {
        return $this->getServiceLocator()->get(TestPreviewerService::class);
    }
}
