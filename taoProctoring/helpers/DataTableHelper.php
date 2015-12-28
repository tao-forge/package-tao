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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA ;
 *
 */

namespace oat\taoProctoring\helpers;

use oat\oatbox\user\User;

/**
 * Provides common data helper for datatable component.
 */
class DataTableHelper
{
    /**
     * The default number of rows displayed in a page
     */
    const DEFAULT_ROWS = 25;

    /**
     * The index of the default page
     */
    const DEFAULT_PAGE = 1;

    /**
     * The index of the option providing the number of rows per page
     */
    const OPTION_ROWS = 'rows';

    /**
     * The index of the option providing the page number
     */
    const OPTION_PAGE = 'page';

    /**
     * Paginates a collection to render a subset in a table
     * @param array $collection - The full amount of lines to paginate
     * @param array [$options] - Allow to setup the page. These options are supported:
     * - self::OPTION_ROWS : The number of rows per page
     * - self::OPTION_PAGE : The index of the page to get
     * @param function [$dataRenderer] - An optional callback function provided to format the paginated data
     * @return array
     */
    public static function paginate($collection, $options = array(), $dataRenderer = null)
    {
        $optRows = abs(intval(isset($options[self::OPTION_ROWS]) ? $options[self::OPTION_ROWS] : self::DEFAULT_ROWS));
        $optPage = abs(intval(isset($options[self::OPTION_PAGE]) ? $options[self::OPTION_PAGE] : self::DEFAULT_PAGE));

        $amount = count($collection);
        $rows = max(1, $optRows);
        $total = ceil($amount / $rows);
        $page = max(1, min($optPage, $total));
        $offset = ($page - 1) * $rows;

        $result = array(
            'offset' => $offset,
            'amount' => $amount,
            'total' => $total,
            'page' => $page,
            'rows' => $rows
        );

        if (is_callable($dataRenderer)) {
            $result['data'] = $dataRenderer(array_slice($collection, $offset, $rows));
        } else {
            $result['data'] = array_slice($collection, $offset, $rows);
        }
        $result['length'] = count($result['data']);

        return $result;
    }
}