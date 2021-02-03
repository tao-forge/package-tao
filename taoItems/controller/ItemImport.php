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
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               2012-2018 (update and modification) Open Assessment Technologies SA;
 */

namespace oat\taoItems\controller;

use oat\taoItems\model\CsvImporter;
use oat\taoItems\model\ItemsService;
use oat\taoItems\model\ItemModel;
use oat\tao\controller\Import;
use tao_models_classes_import_CsvImporter;

/**
 * This controller provide the actions to import items
 *
 * @author  CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package taoItems
 *
 */
class ItemImport extends Import
{
    /**
     * overwrite the parent index to add the requiresRight for Items only
     *
     * @requiresRight id WRITE
     * @see           oat\tao\controller\Import::index()
     */
    public function index()
    {
        parent::index();
    }

    protected function getAvailableImportHandlers()
    {
        $returnValue = parent::getAvailableImportHandlers();

        foreach (array_keys($returnValue) as $key) {
            if ($returnValue[$key] instanceof \tao_models_classes_import_CsvImporter) {
                $importer = new CsvImporter();
                $returnValue[$key] = $importer;
            }
        }

        $itemModelClass = $this->getClass(ItemModel::CLASS_URI_MODELS);
        foreach ($itemModelClass->getInstances() as $model) {
            $impl = ItemsService::singleton()->getItemModelImplementation($model);
            if (in_array('tao_models_classes_import_ImportProvider', class_implements($impl))) {
                foreach ($impl->getImportHandlers() as $handler) {
                    array_unshift($returnValue, $handler);
                }
            }
        }

        return $returnValue;
    }
}
