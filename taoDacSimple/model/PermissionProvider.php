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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

namespace oat\taoDacSimple\model;

use oat\generis\model\data\permission\PermissionInterface;
use oat\generis\model\GenerisRdf;
use oat\tao\model\TaoOntology;
use oat\taoDacSimple\model\DataBaseAccess;
use oat\oatbox\user\User;
use core_kernel_classes_Class;
use oat\oatbox\service\ConfigurableService;

/**
 * Simple permissible Permission model
 * 
 * does not require privileges
 * does not grant privileges
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 */
class PermissionProvider extends ConfigurableService implements PermissionInterface
{
    
    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\PermissionInterface::getPermissions()
     */
    public function getPermissions(User $user, array $resourceIds) {

        if (in_array(TaoOntology::PROPERTY_INSTANCE_ROLE_SYSADMIN, $user->getRoles())) {
            $permissions = array();
            foreach ($resourceIds as $id) {
                $permissions[$id] = $this->getSupportedRights();
            }
            return $permissions;
        }

        $dbAccess = $this->getServiceManager()->get(DataBaseAccess::SERVICE_ID);
        $userIds = $user->getRoles();
        $userIds[] = $user->getIdentifier();
        return $dbAccess->getPermissions($userIds, $resourceIds);
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\PermissionInterface::onResourceCreated()
     */
    public function onResourceCreated(\core_kernel_classes_Resource $resource)
    {
        $dbAccess = $this->getServiceManager()->get(DataBaseAccess::SERVICE_ID);
        // verify resource is created
        $permissions = $dbAccess->getResourcePermissions($resource->getUri());
        if (empty($permissions)) {
            // treat resources as classes without parent classes
            $class = new \core_kernel_classes_Class($resource);
            foreach (array_merge($resource->getTypes(), $class->getParentClasses()) as $parent) {
                foreach (AdminService::getUsersPermissions($parent->getUri()) as $userUri => $rights) {
                    $dbAccess->addPermissions($userUri, $resource->getUri(), $rights);
                }
            }
        }
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\permission\PermissionInterface::getSupportedRights()
     */
    public function getSupportedRights() {
        return array('GRANT', 'WRITE', 'READ');
    }
    
    
    /**
     * Returns an associativ array with permission ids as keys
     * and labels as values
     * 
     * @return array
     */
    public static function getRightLabels() {
        return array(
            'GRANT' => __('grant'),
            'WRITE' => __('write'),
            'READ' => __('read')
        );
    }
    
    public static function getSupportedRootClasses() {
        return array(
            new core_kernel_classes_Class(TaoOntology::OBJECT_CLASS_URI),
            new core_kernel_classes_Class(GenerisRdf::CLASS_GENERIS_USER),
            new core_kernel_classes_Class(GenerisRdf::CLASS_ROLE)
        );
    }
}