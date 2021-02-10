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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *
 */

namespace oat\tao\model;

use \common_Logger;
use \common_exception_UserReadableException;
use \common_log_SeverityLevel;
use Request;

/**
 * Short description of class oat\tao\model\UserException
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao

 */
class AccessDeniedException extends UserException implements common_exception_UserReadableException, common_log_SeverityLevel
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---
    /**
     * @access private
     * @var Request
     */
    private $request;

    // --- OPERATIONS ---

    /**
     * An exception if a user is not authorised to execute a controller action
     *
     * @param string $userUri
     * @param string $action
     * @param string $module
     * @param string $ext
     */
    public function __construct($userUri, $action, $module, $ext)
    {
        $this->request = new Request();
        parent::__construct('Access to ' . $ext . '::' . $module . '::' . $action . ' denied for user \'' . $userUri . '\'');
    }
    
    /**
     * @return Request
     */
    public function getDeniedRequest()
    {
        return $this->request;
    }
    
    public function getSeverity()
    {
        return common_Logger::INFO_LEVEL;
    }
    
    public function getUserMessage()
    {
        return __('Access denied. Please renew your authentication!');
    }
}
