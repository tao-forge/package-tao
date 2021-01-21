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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA
 *
 */

namespace oat\taoQtiItem\scripts\install;

use oat\oatbox\extension\InstallAction;
use oat\tao\model\ClientLibRegistry;
use oat\tao\model\asset\AssetService;

/**
 * Register former portable shared libraries to make existing PCI implementation compatible
 */
class RegisterPortableContexts extends InstallAction
{
    public function __invoke($params)
    {
        //register location of portable libs to legacy share lib aliases for backward compatibility
        $assetService = $this->getServiceManager()->get(AssetService::SERVICE_ID);
        $basePath = $assetService->getJsBaseWww('taoQtiItem') . 'js/runtime';
        $clientLibRegistry = ClientLibRegistry::getRegistry();
        $clientLibRegistry->register('qtiCustomInteractionContext', $basePath . 'qtiCustomInteractionContext');
        $clientLibRegistry->register('qtiInfoControlContext', $basePath . 'qtiInfoControlContext');
    }
}
