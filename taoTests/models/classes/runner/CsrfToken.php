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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA ;
 */
/**
 * @author Jean-Sébastien Conan <jean-sebastien.conan@vesperiagroup.com>
 */

namespace oat\taoTests\models\runner;

/**
 * Interface CsrfToken
 *
 * Provides the API to handle CSRF tokens
 *
 * @package oat\taoQtiTest\models\runner
 */
interface CsrfToken
{
    /**
     * Generates and returns the CSRF token
     * @return string
     */
    public function getCsrfToken();

    /**
     * Validates a given token with the current CSRF token
     * @param string $token The given token to validate
     * @param int $lifetime A max life time for the current token, default to infinite
     * @return bool
     */
    public function checkCsrfToken($token, $lifetime = 0);

    /**
     * Revokes the current CSRF token
     * @return void
     */
    public function revokeCsrfToken();
}
