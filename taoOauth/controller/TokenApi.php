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
 * Copyright (c) 2018 (original work) Open Assessment Technologies SA
 *
 */

namespace oat\taoOauth\controller;

use League\OAuth2\Client\Provider\AbstractProvider;
use oat\oatbox\log\LoggerAwareTrait;
use oat\oatbox\log\TaoLoggerAwareInterface;
use oat\tao\helpers\RestExceptionHandler;
use oat\taoOauth\model\OAuthClient;
use oat\taoOauth\model\provider\Provider;
use oat\taoOauth\model\token\provider\TokenProviderFactory;
use oat\taoOauth\model\token\TokenService;

class TokenApi extends \tao_actions_CommonModule implements TaoLoggerAwareInterface
{
    use LoggerAwareTrait;

    private $responseEncoding = "application/json";

    /**
     * Check response encoding requested
     *
     * tao_actions_RestModule constructor.
     */
    public function __construct()
    {
        if ($this->hasHeader("Accept")) {
            try {
                $this->responseEncoding = (\tao_helpers_Http::acceptHeader($this->getAcceptableMimeTypes(), $this->getHeader("Accept")));
            } catch (\common_exception_ClientException $e) {
                $this->returnFailure($e);
            }
        }

        header('Content-Type: '.$this->responseEncoding);
    }

    /**
     * Endpoint api to request an oauth token based on incoming parameters
     *
     * @throws \common_exception_NotImplemented
     */
    public function requestToken()
    {
        try {
            $parameters = $this->getParameters();
            /** @var AbstractProvider $provider */
            $provider = (new TokenProviderFactory($parameters))->build();
            $token = $this->getTokenService()->generateToken($provider);
            $this->returnJson($token);
        } catch (\Exception $e) {
            $this->logWarning($e->getMessage());
            $this->returnFailure($e);
        }
    }

    /**
     * Extract parameters from request. It must include a client id and secret.
     * An optional grant type params is allowed.
     *
     * @return array
     * @throws \common_exception_MissingParameter
     */
    protected function getParameters()
    {
        $parameters = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() != JSON_ERROR_NONE) {
            $parameters = [];
        }

        if (!$parameters) {
            $parameters = $this->getRequestParameters();
        }

        if (!isset($parameters[Provider::CLIENT_ID])) {
            throw new \common_exception_MissingParameter(Provider::CLIENT_ID, __CLASS__);
        }

        if (!isset($parameters[Provider::CLIENT_SECRET])) {
            throw new \common_exception_MissingParameter(Provider::CLIENT_SECRET, __CLASS__);
        }

        if (!isset($parameters[Provider::GRANT_TYPE])) {
            $grantType = OAuthClient::DEFAULT_GRANT_TYPE;
        } else {
            $grantType = $parameters[Provider::GRANT_TYPE];
        }

        return [
            Provider::CLIENT_ID => $parameters[Provider::CLIENT_ID],
            Provider::CLIENT_SECRET => $parameters[Provider::CLIENT_SECRET],
            Provider::GRANT_TYPE => $grantType
        ];
    }

    /**
     * Return http Accepted mimeTypes
     *
     * @return array
     */
    protected function getAcceptableMimeTypes()
    {
        return array("application/json", "text/xml", "application/xml", "application/rdf+xml");
    }

    /**
     * @return TokenService
     */
    protected function getTokenService()
    {
        return $this->getServiceLocator()->get(TokenService::SERVICE_ID);
    }

    /**
     * Return failed Rest response
     * Set header http by using handle()
     * If $withMessage is true:
     *     Send response with success, code, message & version of TAO
     *
     * @param \Exception $exception
     * @param $withMessage
     * @throws \common_exception_NotImplemented
     */
    protected function returnFailure(\Exception $exception, $withMessage=true)
    {
        $handler = new RestExceptionHandler();
        $handler->sendHeader($exception);

        $data = array();
        if ($withMessage) {
            $data['success']	=  false;
            $data['errorCode']	=  $exception->getCode();
            $data['errorMsg']	=  $this->getErrorMessage($exception);
            $data['version']	= TAO_VERSION;
        }

        echo $this->encode($data);
        exit(0);
    }

    /**
     * Return success Rest response
     * Send response with success, data & version of TAO
     *
     * @param array $rawData
     * @param bool $withMessage
     * @throws \common_exception_NotImplemented
     */
    protected function returnSuccess($rawData = array(), $withMessage=true)
    {
        $data = array();
        if ($withMessage) {
            $data['success'] = true;
            $data['data'] 	 = $rawData;
            $data['version'] = TAO_VERSION;
        } else {
            $data = $rawData;
        }

        echo $this->encode($data);
        exit(0);
    }

    /**
     * Generate safe message preventing exposing sensitive date in non develop mode
     * @param \Exception $exception
     * @return string
     */
    private function getErrorMessage(\Exception $exception)
    {
        $defaultMessage =  __('Unexpected error. Please contact administrator');
        if (DEBUG_MODE) {
            $defaultMessage = $exception->getMessage();
        }
        return ($exception instanceof \common_exception_UserReadableException) ? $exception->getUserMessage() :  $defaultMessage;
    }

    /**
     * Encode data regarding responseEncoding
     *
     * @param $data
     * @return string
     * @throws \common_exception_NotImplemented
     */
    protected function encode($data)
    {
        switch ($this->responseEncoding){
            case "application/rdf+xml":
                throw new \common_exception_NotImplemented();
                break;
            case "text/xml":
            case "application/xml":
                return \tao_helpers_Xml::from_array($data);
            case "application/json":
            default:
                return json_encode($data);
        }
    }
}