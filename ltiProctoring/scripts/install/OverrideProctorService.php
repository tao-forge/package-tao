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
 * Copyright (c) 2017  (original work) Open Assessment Technologies SA;
 *
 * @author Alexander Zagovorichev <zagovorichev@1pt.com>
 */

namespace oat\ltiProctoring\scripts\install;


use oat\ltiProctoring\model\delivery\ProctorService;
use oat\oatbox\extension\InstallAction;
use oat\taoProctoring\model\ProctorServiceRoute;

class OverrideProctorService extends InstallAction
{
    public function __invoke($params)
    {
        // to avoid configuration overwrite
        if (!$this->getServiceManager()->has(ProctorService::SERVICE_ID)
            || !is_a($this->getServiceManager()->get(ProctorService::SERVICE_ID), ProctorServiceRoute::class)
        ) {

            $this->getServiceManager()->register(ProctorService::SERVICE_ID, new ProctorServiceRoute());
        }
        $proctorService = $this->getServiceManager()->get(ProctorService::SERVICE_ID);
        $config = $proctorService->getOptions();
        if (!isset($config[ProctorServiceRoute::PROCTOR_SERVICE_ROUTES])) {
            $config[ProctorServiceRoute::PROCTOR_SERVICE_ROUTES] = [];
        }
        $config[ProctorServiceRoute::PROCTOR_SERVICE_ROUTES][] = ProctorService::class;
        $config[ProctorServiceRoute::PROCTOR_SERVICE_ROUTES] = array_unique($config[ProctorServiceRoute::PROCTOR_SERVICE_ROUTES]);
        $this->getServiceManager()->register(ProctorService::SERVICE_ID, new ProctorServiceRoute($config));
    }
}
