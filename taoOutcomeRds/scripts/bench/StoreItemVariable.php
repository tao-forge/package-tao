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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\taoOutcomeRds\scripts\bench;

use oat\oatbox\extension\AbstractAction;

class StoreItemVariable extends AbstractStoreItemVariable
{
    protected function storeItemVariableSet($deliveryResultIdentifier, $testIdentifier, $itemIdentifier, array $variables, $callIdItem, array &$time)
    {
        foreach ($variables as $variable) {
            $startTime = microtime(true);
            $this->storage->storeItemVariable($deliveryResultIdentifier, $testIdentifier, $itemIdentifier, $variable, $callIdItem);
            $endTime = microtime(true);
            
            $time[] = ($endTime - $startTime);
        }
    }
    
    protected function getBenchmarkMethodName()
    {
        return 'RdsResultStorage::storeItemVariable';
    }
}
