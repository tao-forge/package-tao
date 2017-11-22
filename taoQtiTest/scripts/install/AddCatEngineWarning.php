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
 */

namespace oat\taoQtiTest\scripts\install;

use common_report_Report as Report;
use oat\oatbox\extension\InstallAction;

class AddCatEngineWarning extends InstallAction
{
    public function __invoke($params)
    {
        $extension = $this->getServiceManager()->get(\common_ext_ExtensionsManager::SERVICE_ID)->getExtensionById('taoQtiTest');
        $config = $extension->getConfig('testRunner');
        $config['catEngineWarning'] = [
            'echoDelayUpdate' => 15,
            'echoPauseLimit' => 120,
            'echoExceptionName' => 'CatEngine'
        ];
        $extension->setConfig('testRunner', $config);
        return new Report(Report::TYPE_SUCCESS, 'Added catEngineWarning in testRunner config');
    }
}
