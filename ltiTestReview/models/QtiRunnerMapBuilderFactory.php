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
 * Copyright (c) 2019 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\taoReview\models;


use oat\oatbox\service\ConfigurableService;
use oat\taoQtiTest\models\cat\CatService;
use oat\taoQtiTest\models\ExtendedStateService;
use oat\taoQtiTest\models\runner\config\QtiRunnerConfig;

class QtiRunnerMapBuilderFactory extends ConfigurableService
{
    const SERVICE_ID = 'taoReview/QtiRunnerMapBuilderFactory';

    /**
     * @return QtiRunnerMapBuilder
     */
    public function create()
    {
        $locator = $this->getServiceLocator();

        return new QtiRunnerMapBuilder(
            $locator->get(QtiRunnerConfig::SERVICE_ID),
            $locator->get(ExtendedStateService::SERVICE_ID),
            $locator->get(CatService::SERVICE_ID)
        );
    }
}
