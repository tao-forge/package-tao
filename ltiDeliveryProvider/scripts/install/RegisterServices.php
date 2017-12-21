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
 * Copyright (c) 2016-2017  (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\ltiDeliveryProvider\scripts\install;

use oat\ltiDeliveryProvider\model\LtiOutcomeService;
use oat\ltiDeliveryProvider\model\ResultAliasService;
use oat\oatbox\extension\InstallAction;

/**
 * Class RegisterServices
 * @package oat\ltiDeliveryProvider\scripts\install
 * @author Aleh Hutnikau, <hutnikau@1pt.com>
 */
class RegisterServices extends InstallAction
{
    /**
     * @param $params
     * @return \common_report_Report
     * @throws \common_exception_Error
     */
    public function __invoke($params)
    {
        $this->getServiceManager()->register(ResultAliasService::SERVICE_ID, new ResultAliasService());
        $this->getServiceManager()->register(LtiOutcomeService::SERVICE_ID, new LtiOutcomeService());

        return new \common_report_Report(\common_report_Report::TYPE_SUCCESS, __('Registered and created ResultAliasService / LtiOutcomeService services'));
    }
}
