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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2002-2008 (update and modification) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *
 */

declare(strict_types=1);

namespace oat\taoTestTaker\actions;

use oat\taoTestTaker\models\CsvImporter;
use oat\taoTestTaker\models\RdfImporter;
use oat\tao\controller\Import as Import_2;
use oat\tao\model\import\CsvImporter as CsvImporter_2;
use oat\tao\model\import\RdfImporter as RdfImporter_2;

/**
 * Extends the common Import class to exchange the generic
 * CsvImporter with a subject specific one
 *
 * @author  Bertrand Chevrier, <taosupport@tudor.lu>
 */
class Import extends Import_2
{
    /**
     * @inheritDoc
     */
    public function getAvailableImportHandlers()
    {
        $returnValue = parent::getAvailableImportHandlers();

        foreach (array_keys($returnValue) as $key) {
            switch (get_class($returnValue[$key])) {
                case CsvImporter_2::class:
                    $importer = new CsvImporter();
                    $importer->setValidators($this->getValidators());
                    $returnValue[$key] = $importer;

                    break;
                case RdfImporter_2::class:
                    $importer = new RdfImporter();
                    $returnValue[$key] = $importer;

                    break;
            }
        }

        return $returnValue;
    }
}
