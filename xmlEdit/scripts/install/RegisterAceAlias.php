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
 * Copyright (c) 2015-2019 (original work) Open Assessment Technologies SA ;
 *
 */
namespace oat\xmlEdit\scripts\install;

use common_report_Report as Report;
use oat\oatbox\extension\InstallAction;
use oat\tao\model\ClientLibRegistry;

class RegisterAceAlias extends InstallAction
{
    public function __invoke($params)
    {
        ClientLibRegistry::getRegistry()->register('ace', ROOT_URL.'xmlEdit/views/js/lib/ace-1.4.5/');

        // report the result
        return new Report(Report::TYPE_SUCCESS, 'Ace library registered!');
    }
}
