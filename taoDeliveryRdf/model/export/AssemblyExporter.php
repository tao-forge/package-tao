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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\taoDeliveryRdf\model\export;

use core_kernel_classes_Class;
use core_kernel_classes_Resource;
use oat\oatbox\service\ServiceManager;
use oat\taoDeliveryRdf\view\form\export\ExportForm;
use oat\tao\model\export\ExportHandler;

/**
 * tao delivery assembly exporter
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoDelivery

 */
class AssemblyExporter implements ExportHandler
{

    /**
     * (non-PHPdoc)
     * @see oat\tao\model\export\ExportHandler::getLabel()
     */
    public function getLabel()
    {
        return __('Assembly');
    }
    
    /**
     * (non-PHPdoc)
     * @see oat\tao\model\export\ExportHandler::getExportForm()
     */
    public function getExportForm(core_kernel_classes_Resource $resource)
    {
        if ($resource instanceof core_kernel_classes_Class) {
            $formData = ['class' => $resource];
        } else {
            $formData = ['instance' => $resource];
        }
        $form = new ExportForm($formData);
        return $form->getForm();
    }
    
    /**
     * (non-PHPdoc)
     * @see oat\tao\model\export\ExportHandler::export()
     */
    public function export($formValues, $destination)
    {
        if (!isset($formValues['exportInstance']) || empty($formValues['exportInstance'])) {
            throw new \common_Exception('No instance selected');
        }
        
        $delivery = new core_kernel_classes_Resource($formValues['exportInstance']);
        $path = ServiceManager::getServiceManager()->get(AssemblyExporterService::class)->exportCompiledDelivery($delivery);

        return $path;
    }
}
