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
 * Copyright (c) 2018 (original work) Open Assessment Technologies SA ;
 *
 */

/**
 * @author Jean-Sébastien Conan <jean-sebastien@taotesting.com>
 */

namespace oat\taoClientDiagnostic\model\schoolName;

use oat\oatbox\service\ConfigurableService;

class SchoolNameProvider extends ConfigurableService implements SchoolNameService
{
    /**
     * Gets a school name from an ID and a PIN
     * @param string $id
     * @param string $pin
     * @return string
     * @throws \common_exception_NoImplementation
     */
    public function getSchoolName($id, $pin)
    {
        throw new \common_exception_NoImplementation('You must implement a data provider to get the school name');
    }

}