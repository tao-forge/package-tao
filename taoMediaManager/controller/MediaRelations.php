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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\taoMediaManager\controller;

use Psr\Http\Message\ResponseInterface;
use Throwable;
use oat\oatbox\log\LoggerAwareTrait;
use oat\taoMediaManager\model\relation\MediaRelationService;
use oat\taoMediaManager\model\relation\factory\QueryFactory;
use oat\tao\controller\CommonModule;
use oat\tao\model\http\HttpJsonResponseTrait;
use oat\tao\model\http\formatter\ResponseFormatter;
use oat\tao\model\http\response\JsonResponseInterface;

class MediaRelations extends CommonModule
{
    use LoggerAwareTrait;
    use HttpJsonResponseTrait;

    public function relations(): void
    {
        try {
            $query = $this->getQueryFactory()
                ->createFindAllQueryByRequest($this->getPsrRequest());

            $collection = $this->getMediaRelationService()
                ->getMediaRelations($query)
                ->jsonSerialize();

            $this->setSuccessJsonResponse($collection);
        } catch (Throwable $exception) {
            $this->logError(sprintf('Error getting media relation: %s, ', $exception->getMessage()));

            $this->setErrorJsonResponse($exception->getMessage(), $exception->getCode());
        }
    }

    private function formatResponse(JsonResponseInterface $jsonResponse, int $statusCode): ResponseInterface
    {
        return $this->getResponseFormatter()
            ->withJsonHeader()
            ->withStatusCode($statusCode)
            ->withBody($jsonResponse)
            ->format($this->getPsrResponse());
    }

    private function getResponseFormatter(): ResponseFormatter
    {
        return $this->getServiceLocator()->get(ResponseFormatter::class);
    }

    private function getMediaRelationService(): MediaRelationService
    {
        return $this->getServiceLocator()->get(MediaRelationService::class);
    }

    private function getQueryFactory(): QueryFactory
    {
        return $this->getServiceLocator()->get(QueryFactory::class);
    }
}
