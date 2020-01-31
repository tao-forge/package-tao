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
 * Copyright (c) 2019 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\tao\model\search\aggregator;

use oat\tao\model\search\ResultSet;
use oat\tao\model\search\Search;

interface UnionSearchInterface
{
    const SERVICE_ID = 'tao/UnionSearchService';

    /**
     *
     * Returns Search services which was appended for current realisation
     *
     * @return Search []
     */
    public function getInternalServices();

    /**
     * @param string $queryString
     * @param string $type
     * @param int $start
     * @param int $count
     * @param string $order
     * @param string $dir
     * @return ResultSet
     */
    public function query($queryString, $type, $start = 0, $count = 10, $order = 'id', $dir = 'DESC');
}
