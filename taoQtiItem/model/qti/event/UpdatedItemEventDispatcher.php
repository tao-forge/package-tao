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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

declare(strict_types=1);

namespace oat\taoQtiItem\model\qti\event;

use core_kernel_classes_Resource;
use oat\oatbox\event\EventManager;
use oat\oatbox\service\ConfigurableService;
use oat\taoItems\model\event\ItemUpdatedEvent;
use oat\taoQtiItem\model\qti\Item;
use oat\taoQtiItem\model\qti\parser\ElementReferencesExtractor;

class UpdatedItemEventDispatcher extends ConfigurableService
{
    public function dispatch(Item $qtiItem, core_kernel_classes_Resource $rdfItem): void
    {
        $elementReferences = $this->getElementReferencesExtractor()->extractAll($qtiItem);

        $this->getEventManager()
            ->trigger(new ItemUpdatedEvent($rdfItem->getUri(), $elementReferences->extractReferences()));
    }

    private function getEventManager(): EventManager
    {
        return $this->getServiceLocator()->get(EventManager::SERVICE_ID);
    }

    private function getElementReferencesExtractor(): ElementReferencesExtractor
    {
        return $this->getServiceLocator()->get(ElementReferencesExtractor::class);
    }

}
