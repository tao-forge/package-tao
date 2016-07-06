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
 * Copyright (c) 2016  (original work) Open Assessment Technologies SA;
 * 
 * @author Alexander Zagovorichev <zagovorichev@1pt.com>
 */

namespace oat\taoEventLog\scripts\install;

use common_exception_Error;
use common_ext_action_InstallAction;
use common_report_Report;
use oat\oatbox\action\Action;
use oat\tao\model\event\LoginFailedEvent;
use oat\tao\model\event\LoginSucceedEvent;
use oat\taoEventLog\model\LoggerService;
use oat\taoEventLog\model\StorageInterface;

/**
 * Class RegisterLoggerService
 * @package oat\taoEventLog\scripts\install
 */
class RegisterLoggerService extends common_ext_action_InstallAction implements Action
{
    /**
     * @param $params
     * @return common_report_Report
     * @throws common_exception_Error
     */
    public function __invoke($params)
    {
        $this->registerService(LoggerService::SERVICE_ID, new LoggerService([
            LoggerService::OPTION_STORAGE => StorageInterface::SERVICE_ID,
            LoggerService::OPTION_ROTATION_PERIOD => 'P90D'
        ]));

        $this->registerEvent(LoginFailedEvent::class, [LoggerService::class, 'logEvent']);
        $this->registerEvent(LoginSucceedEvent::class, [LoggerService::class, 'logEvent']);

        return new common_report_Report(common_report_Report::TYPE_SUCCESS, __('Registered EventLog Logger Service'));
    }
}
