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
 * Copyright (c) 2013-2016 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 *
 */

namespace oat\taoQtiTest\models\export;

use \DOMDocument;
use \ZipArchive;
use \core_kernel_classes_Resource;
use oat\taoQtiTest\helpers\Utils;

/**
 * Export Handler for QTI tests.
 *
 * @access  public
 * @author  Joel Bout, <joel@taotesting.com>
 * @package taoQtiTest
 */
class TestExport22 extends TestExport
{
    /**
     * @return string
     */
    public function getLabel()
    {
        return __('QTI Test Package 2.2');
    }

    /**
     * @param core_kernel_classes_Resource $testResource
     * @param ZipArchive                   $zip
     * @param DOMDocument                  $manifest
     * @return oat\taoQtiTest\models\export\QtiTestExporter|oat\taoQtiTest\models\export\QtiTestExporter22
     */
    protected function createExporter(core_kernel_classes_Resource $testResource, ZipArchive $zip, DOMDocument $manifest)
    {
        return new QtiTestExporter22($testResource, $zip, $manifest);
    }

    /**
     * @return DOMDocument
     */
    protected function createManifest()
    {
        return Utils::emptyImsManifest('2.2');
    }

    /**
     * @param core_kernel_classes_Resource $resource
     * @return tao_helpers_form_Form
     */
    public function getExportForm(core_kernel_classes_Resource $resource)
    {
        $formData = $this->getFormData($resource);

        return (new QtiTest22ExportForm($formData))->getForm();
    }
}
