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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA;
 */

namespace oat\taoQtiTestPreviewer\models;

use common_exception_Error;
use core_kernel_users_GenerisUser as GenerisUser;
use oat\generis\model\OntologyAwareTrait;
use oat\oatbox\service\ConfigurableService;
use oat\taoResultServer\models\classes\implementation\ResultServerService;

class GenerisUserService extends ConfigurableService
{
    use OntologyAwareTrait;

    /**
     * @param $deliveryUri
     * @param $resultId
     * @return GenerisUser
     *
     * @throws common_exception_Error
     */
    public function getGenerisUser($deliveryUri, $resultId)
    {
        /** @var ResultServerService $resultServerService */
        $resultServerService = $this->getServiceLocator()->get(ResultServerService::SERVICE_ID);

        $resultStorage = $resultServerService->getResultStorage($deliveryUri);

        $testTakerResult = $resultStorage->getTestTaker($resultId);

        $testTakerResource = $this->getResource($testTakerResult);

        return new GenerisUser($testTakerResource);
    }
}
