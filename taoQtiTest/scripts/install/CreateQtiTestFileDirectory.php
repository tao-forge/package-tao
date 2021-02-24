<?php
/*
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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *
 */

namespace oat\taoQtiTest\scripts\install;

use common_report_Report as Report;
use oat\oatbox\extension\AbstractAction;
use helpers_File;
use oat\oatbox\filesystem\FileSystemService;
use oat\taoQtiTest\models\QtiTestService;

/**
 * Creates the storage on the filesystem for QTI tests
 */
class CreateQtiTestFileDirectory extends AbstractAction
{
    public function __invoke($params)
    {
        $dataPath = FILES_PATH . 'taoQtiTest' . DIRECTORY_SEPARATOR . 'testData' . DIRECTORY_SEPARATOR;
        if (file_exists($dataPath)) {
            helpers_File::emptyDirectory($dataPath);
        }
        
        $fsService = $this->getServiceLocator()->get(FileSystemService::SERVICE_ID);
        $fsService->createFileSystem('taoQtiTest');
        $this->getServiceManager()->register(FileSystemService::SERVICE_ID, $fsService);
        
        $this->getServiceLocator()->get(QtiTestService::class)->setQtiTestFileSystem('taoQtiTest');
        
        return new Report(Report::TYPE_SUCCESS, 'RDS schema for RdsToolsStateStorage is now installed');
    }
    
}
