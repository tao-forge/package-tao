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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 *
 */

namespace oat\taoLti\models\classes;

use common_http_InvalidSignatureException;
use common_http_Request;
use oat\taoLti\models\classes\LtiMessages\LtiErrorMessage;
use oat\taoLti\models\classes\user\LtiUserService;
use tao_models_classes_oauth_Service;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Authentication adapter interface to be implemented by authentication methods
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoLti
 */
class LtiAuthAdapter
    implements \common_user_auth_Adapter, ServiceLocatorAwareInterface
{

    use ServiceLocatorAwareTrait;

    /**
     *
     * @var common_http_Request
     */
    private $request;

    /**
     * Creates an Authentication adapter from an OAuth Request
     *
     * @param common_http_Request $request
     */
    public function __construct(common_http_Request $request)
    {
        $this->request = $request;
    }

    /**
     * (non-PHPdoc)
     * @see common_user_auth_Adapter::authenticate()
     *
     * @return user\LtiUser
     * @throws LtiException
     * @throws LtiVariableMissingException
     * @throws \ResolverException
     * @throws \common_Exception
     * @throws \common_exception_Error
     * @throws \core_kernel_users_CacheException
     * @throws \core_kernel_users_Exception
     */
    public function authenticate()
    {
        $service = new tao_models_classes_oauth_Service();
        try {
            $service->validate($this->request);
            $ltiLaunchData = LtiLaunchData::fromRequest($this->request);
            /** @var LtiUserService $userService */
            $userService = $this->getServiceLocator()->get(LtiUserService::SERVICE_ID);
            return $userService->findOrSpawnUser($ltiLaunchData);
        } catch (common_http_InvalidSignatureException $e) {
            throw new LtiException('Invalid LTI signature', LtiErrorMessage::ERROR_UNAUTHORIZED);
        }
    }
}