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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */
namespace oat\tao\model\accessControl\data;

use oat\tao\model\accessControl\AccessControl;
use oat\tao\model\accessControl\DataAccessControl;

/**
 * Proxy for the Acl Implementation
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 
 */
class AclProxy implements AccessControl, DataAccessControl
{
    const CONFIG_KEY_IMPLEMENTATION = 'DataAccessControl';
    
    const FALLBACK_IMPLEMENTATION_CLASS = 'oat\tao\model\accessControl\data\implementation\FreeAccess';
    
    /**
     * @var DataAccessControl
     */
    private static $implementation;

    /**
     * @return DataAccessControl
     */
    protected static function getImplementation() {
        if (is_null(self::$implementation)) {
            $implClass = common_ext_ExtensionsManager::singleton()->getExtensionById('tao')->getConfig(self::CONFIG_KEY_IMPLEMENTATION);
            if (empty($implClass) || !class_exists($implClass)) {
                common_Logger::e('No implementation found for Access Control, locking down the server');
                $implClass = self::FALLBACK_IMPLEMENTATION_CLASS;
            }
            self::$implementation = new $implClass();
        }
        return self::$implementation;
    }
    
    /**
     * Change the implementation of the access control permanently
     * 
     * @param DataAccessControl $implementation
     */
    public static function setImplementation(DataAccessControl $implementation) {
        self::$implementation = $implementation;
        common_ext_ExtensionsManager::singleton()->getExtensionById('tao')->setConfig(self::CONFIG_KEY_IMPLEMENTATION, get_class($implementation));
    }    
    
    /**
     * Returns whenever or not a user has access to a specified link
     *
     * @param string $action
     * @param string $controller
     * @param string $extension
     * @param array $parameters
     * @return boolean
     */
    public function hasAccess($user, $action, $parameters) {
        $required = array();
        foreach ($this->getRequiredPrivileges($action) as $paramName => $privileges) {
            if (isset($parameters[$paramName])) {
                $required[$parameters[$paramName]] = $privileges;
            } else {
                throw new \Exception('Missing parameter');
            }
        }
        if (!empty($required)) {
            $privileges = $this->getPrivileges($user, array_keys($required));
        }
        
        foreach ($required as $id => $reqPriv) {
            $missing = array_diff($privileges[$id], $reqPriv);
            if (!empty($missing)) {
                common_Logger::d('Missing '.implode(',', $missing).' for resource '.$id);
                return false;
            }
        }
        return true;
    }
    
    public function getRequiredPrivileges($action) {
        return self::getImplementation()->getRequiredPrivileges($action);
    }    
    
    public function getPrivileges($user, $resourceId) {
        return self::getImplementation()->getPrivileges($user, $resourceId);
    }
}