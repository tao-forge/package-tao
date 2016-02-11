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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\taoClientDiagnostic\exception;

class InvalidCallException extends \Exception implements \common_exception_UserReadableException
{
    /**
     * Message for end user
     * @var string
     */
    private $userMessage = 'Internal server error';

    /**
     * InvalidCallException constructor.
     * Set user message with exception message
     * @param string $message
     * @param int $code
     * @param Exception $previous
     */
    public function __construct($message = '', $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        if (!empty($message)) {
            $this->userMessage = $message;
        }
    }

    /**
     * Return user compliant message
     * @return string
     */
    public function getUserMessage()
    {
        return __($this->userMessage);
    }
}