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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\taoQtiTest\models\export\metadata;

use oat\generis\model\OntologyAwareTrait;
use oat\oatbox\service\ConfigurableService;
use oat\taoQtiItem\model\flyExporter\simpleExporter\ItemExporter;
use oat\taoQtiItem\model\flyExporter\simpleExporter\SimpleExporter;

/**
 *
 * Class TestExporter
 */
class TestExporter extends ConfigurableService implements TestMetadataExporter
{
    use OntologyAwareTrait;

    /**
     * Main action to launch export
     *
     * @param string $uri
     * @return mixed
     */
    public function export($uri)
    {
        /** @var ItemExporter $itemExporter */
        $itemExporter = $this->getServiceManager()->get(SimpleExporter::SERVICE_ID);
        return $itemExporter->export(
            \taoQtiTest_models_classes_QtiTestService::singleton()->getItems($this->getResource($uri)),
            true
        );
    }
}