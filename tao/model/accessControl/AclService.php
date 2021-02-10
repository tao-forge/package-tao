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
 */

namespace oat\tao\model\accessControl;

use oat\oatbox\service\ConfigurableService;
use ResolverException;
use common_session_SessionManager;
use oat\oatbox\user\User;
use oat\oatbox\session\SessionService;

/**
 * Proxy for the Acl Implementation
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 */
class AclService extends ConfigurableService
{
    public function hasAccess($action, $controller, $extension, $parameters = [])
    {
        try {
            $resolver  = ActionResolver::getByControllerName($controller, $extension);
            $className = $resolver->getController();
        } catch (ResolverException $e) {
            return false;
        }
        return AclProxy::hasAccess($this->getUser(), $className, $action, $parameters);
    }
    
    /**
     * Does not respect params
     *
     * @param string $url
     * @return boolean
     */
    public function hasAccessUrl($url)
    {
        try {
            $resolver  = new ActionResolver($url);
            return AclProxy::hasAccess($this->getUser(), $resolver->getController(), $resolver->getAction(), []);
        } catch (ResolverException $e) {
            return false;
        }
    }
    
    private function getUser() : User
    {
        return $this->getServiceLocator()->get(SessionService::class)->getCurrentUser();
    }
}
