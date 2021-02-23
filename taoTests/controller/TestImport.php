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
 *
 */

namespace oat\taoTests\controller;

use \core_kernel_classes_Class;
use oat\taoTests\models\TestsService;
use oat\taoTests\models\TestsService as TestService;
use oat\tao\controller\Import;

/**
 * This controller provide the actions to import items
 *
 * @author  CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package taoItems
 *
 */
class TestImport extends Import
{
    /**
     * overwrite the parent index to add the requiresRight for Tests
     *
     * @requiresRight id WRITE
     * @see           oat\tao\controller\Import::index()
     */
    public function index()
    {
        parent::index();
    }

    protected function getTestService(): TestsService
    {
        return $this->getServiceLocator()->get(TestsService::class);
    }

    protected function getAvailableImportHandlers()
    {
        $returnValue = parent::getAvailableImportHandlers();

        $testModelClass = new core_kernel_classes_Class(TestService::CLASS_TEST_MODEL);
        foreach ($testModelClass->getInstances() as $model) {
            $impl = $this->getTestService()->getTestModelImplementation($model);
            if (in_array('oat\\tao\\model\\import\\ImportProvider', class_implements($impl))) {
                foreach ($impl->getImportHandlers() as $handler) {
                    array_unshift($returnValue, $handler);
                }
            }
        }

        return $returnValue;
    }
}
