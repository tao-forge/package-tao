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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA ;
 */
declare(strict_types=1);

namespace oat\taoQtiTest\scripts\install;

use common_report_Report;
use oat\oatbox\filesystem\FileSystemService;
use oat\tao\helpers\FileHelperService;
use oat\taoQtiTest\helpers\QtiPackageExporter;
use taoQtiTest_models_classes_export_TestExport22;
use oat\oatbox\extension\InstallAction;

class RegisterQtiPackageExporter extends InstallAction
{
    public function __invoke($params)
    {
        $serviceManager = $this->getServiceManager();
        $qtiPackageExporter = new QtiPackageExporter(
            new taoQtiTest_models_classes_export_TestExport22(),
            $serviceManager->get(FileSystemService::SERVICE_ID),
            $serviceManager->get(FileHelperService::class)
        );
        $serviceManager->register(QtiPackageExporter::SERVICE_ID, $qtiPackageExporter);

        return new common_report_Report(
            common_report_Report::TYPE_SUCCESS,
            'QtiPackageExporter successfully registered.'
        );
    }
}
