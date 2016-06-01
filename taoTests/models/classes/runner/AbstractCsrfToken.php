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
 * Class AbstractCsrfToken
 * @package oat\taoTests\models\runner
 */
abstract class AbstractCsrfToken implements CsrfToken
{
    /**
     * The desired length of the CSRF token string
     */
    const TOKEN_LENGTH = 40;

    /**
     * Generates a security token
     * @return string
     * @throws \common_Exception
     */
    protected function generateToken()
    {
        try {
            return bin2hex(random_bytes(self::TOKEN_LENGTH / 2));
        } catch (\TypeError $e) {
            // This is okay, so long as `Error` is caught before `Exception`.
            throw new \common_Exception("An unexpected error has occurred while trying to generate a security token", 0, $e);
        } catch (\Error $e) {
            // This is required, if you do not need to do anything just rethrow.
            throw new \common_Exception("An unexpected error has occurred while trying to generate a security token", 0, $e);
        } catch (\Exception $e) {
            throw new \common_Exception("Could not generate a security token. Is our OS secure?", 0, $e);
        }
    }
}