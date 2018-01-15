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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\taoOauth\scripts\update;

use oat\taoOauth\model\token\storage\TokenStorage;
use oat\taoOauth\model\token\TokenService;

class Updater extends \common_ext_ExtensionUpdater
{
    public function update($initialVersion)
    {
        $this->skip('0.0.1', '0.0.5');

        if ($this->isVersion('0.0.5')) {

            $tokenService = new TokenService();
            $this->getServiceManager()->register(TokenService::SERVICE_ID, $tokenService);

            $tokenStorage = new TokenStorage(array(
                TokenStorage::OPTION_PERSISTENCE => 'default',
                TokenStorage::OPTION_CACHE => 'cache',
            ));
            $this->getServiceManager()->register(TokenStorage::SERVICE_ID, $tokenStorage);

            $this->setVersion('0.1.0');
        }
    }
}