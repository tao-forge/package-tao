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
 * Copyright (c) 2020 (original work) (update and modification) Open Assessment Technologies SA (under the project TAO-PRODUCT)
 *
 */

namespace oat\tao\model\oauth\lockout;

use IMSGlobal\LTI\OAuth\OAuthRequest;
use oat\oatbox\service\ConfigurableService;
use oat\oatbox\service\exception\InvalidService;
use oat\oatbox\service\exception\InvalidServiceManagerException;

/**
 * Basic implementation of Lockout service, that allows all requests
 * @package oat\tao\model\oauth\lockout
 */
class NoLockout extends ConfigurableService implements LockoutInterface
{
    public const OPTION_LOCKOUT_STORAGE = 'storage';

    public const OPTION_THRESHOLD = 'threshold';


    /**
     * @param OAuthRequest $request
     */
    public function logFailedAttempt(OAuthRequest $request)
    {

    }

    /**
     * @param OAuthRequest $request
     *
     * @return bool
     */
    public function isAllowed(OAuthRequest $request)
    {
        return true;
    }

    /**
     * @return mixed
     * @throws InvalidService
     * @throws InvalidServiceManagerException
     */
    protected function getLockoutStorage()
    {
        return $this->getSubService(self::OPTION_LOCKOUT_STORAGE);
    }
}
