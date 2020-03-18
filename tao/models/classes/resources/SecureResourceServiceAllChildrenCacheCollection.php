<?php

declare(strict_types=1);

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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\tao\model\resources;

use common_exception_Error;
use core_kernel_classes_Resource;

class SecureResourceServiceAllChildrenCacheCollection
{
    /** @var core_kernel_classes_Resource[] */
    private $resources;

    public function __construct(array $resources)
    {
        $this->resources = array_keys($resources);
    }

    /**
     * @return array
     * @throws common_exception_Error
     */
    public function getInstances(): array
    {
        $result = [];
        if ($this->resources) {
            foreach ($this->resources as $resource) {
                $result[] = new core_kernel_classes_Resource($resource);
            }
        }

        return $result;
    }
}
