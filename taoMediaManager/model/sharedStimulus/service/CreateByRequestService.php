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

namespace oat\taoMediaManager\model\sharedStimulus\service;

use oat\oatbox\service\ConfigurableService;
use oat\taoMediaManager\model\sharedStimulus\CreateCommand;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;
use function GuzzleHttp\Psr7\stream_for;

class CreateByRequestService extends ConfigurableService
{
    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $sharedStimulus = $this->getCreateService()
                ->create($this->createCommand($this->getParsedBody($request)));

            return $this->populateResponse(
                $response,
                200,
                [
                    'success' => true,
                    'data' => $sharedStimulus->jsonSerialize()
                ]
            );
        } catch (Throwable $exception) {
            return $this->populateResponse(
                $response,
                400,
                [
                    'success' => false,
                    'code' => $exception->getCode(),
                    'message' => $exception->getMessage()
                ]
            );
        }
    }

    private function createCommand(array $parsedBody): CreateCommand
    {
        return new CreateCommand(
            $parsedBody['classUri'] ?? '',
            $parsedBody['name'] ?? null,
            $parsedBody['languageUri'] ?? null
        );
    }

    private function getParsedBody(ServerRequestInterface $request): array
    {
        return json_decode((string)$request->getBody(), true);
    }

    private function populateResponse(ResponseInterface $response, int $statusCode, array $payload): ResponseInterface
    {
        return $response->withStatus($statusCode)
            ->withBody(stream_for(json_encode($payload)));
    }

    private function getCreateService(): CreateService
    {
        return $this->getServiceLocator()->get(CreateService::class);
    }
}
