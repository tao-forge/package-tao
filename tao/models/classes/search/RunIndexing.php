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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *
 *
 */
namespace oat\tao\model\search;

use oat\oatbox\Configurable;
use oat\oatbox\service\ConfigurableService;
use oat\tao\model\search\SearchService;
/**
 * Command to reindex all resources
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 */
class RunIndexing
    extends ConfigurableService
{
    /**
     * 
     * @param unknown $params
     */
    public function __invoke($params) {
        SearchService::runIndexing();
        return new \common_report_Report(\common_report_Report::TYPE_SUCCESS, __('Indexing has been run'));
    }

}