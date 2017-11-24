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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */
namespace oat\tao\scripts\install;

use oat\oatbox\extension\InstallAction;
use oat\tao\model\service\ContainerService;

/**
 * This post-installation script creates the ContainerService
 */
class SetContainerService extends InstallAction
{
    /**
     * Sets the ContainerService.
     *
     * @param $params
     *
     * @return \common_report_Report
     */
    public function __invoke($params)
    {
        $this->registerService(ContainerService::SERVICE_ID, new ContainerService());

        return new \common_report_Report(\common_report_Report::TYPE_SUCCESS, 'ContainerService registered');
    }
}