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
 * 
 */

namespace oat\taoLti\models\classes\user;

use oat\oatbox\service\ConfigurableService;


/**
 * Authentication adapter interface to be implemented by authentication methodes
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoLti
 
 */
abstract class LtiUserService extends ConfigurableService
{
    const SERVICE_ID = 'taoLti/LtiUserService';

    /**
     * Returns the existing tao User that corresponds to
     * the LTI request or spawns it
     *
     * @param \taoLti_models_classes_LtiLaunchData $launchData
     * @throws \taoLti_models_classes_LtiException
     * @return LtiUser
     */
    public function findOrSpawnUser(\taoLti_models_classes_LtiLaunchData $launchData) {
        $taoUser = $this->findUser($launchData->getUserID(), \taoLti_models_classes_LtiService::singleton()->getLtiConsumerResource($launchData), $launchData);
        if (is_null($taoUser)) {
            $taoUser = $this->spawnUser($launchData);
        }
        return $taoUser;
    }

    /**
     * Searches if this user was already created in TAO
     *
     * @param string $userId
     * @param \core_kernel_classes_Resource $ltiConsumer
     * @throws \taoLti_models_classes_LtiException
     * @return LtiUser
     */
    abstract public function findUser($userId, $ltiConsumer);

    /**
     * Creates a new LTI User with the absolute minimum of required informations
     *
     * @param \taoLti_models_classes_LtiLaunchData $ltiContext
     * @return LtiUser
     */
    abstract public function spawnUser(\taoLti_models_classes_LtiLaunchData $ltiContext);


    protected function determineTaoRoles(\taoLti_models_classes_LtiLaunchData $ltiLaunchData) {
        $roles = array();
        if ($ltiLaunchData->hasVariable(\taoLti_models_classes_LtiLaunchData::ROLES)) {
            foreach ($ltiLaunchData->getUserRoles() as $role) {
                $taoRole = \taoLti_models_classes_LtiUtils::mapLTIRole2TaoRole($role);
                if (!is_null($taoRole)) {
                    $roles[] = $taoRole;
                    foreach (\core_kernel_users_Service::singleton()->getIncludedRoles(new \core_kernel_classes_Resource($taoRole)) as $includedRole) {
                        $roles[] = $includedRole->getUri();
                    }
                }
            }
            $roles = array_unique($roles);
        } else {
            return array(INSTANCE_ROLE_LTI_BASE);
        }
        return $roles;
    }


    protected function getRoles($taoRoles) {
        $roles = array();
        foreach ($taoRoles as $role){
            $roles[] = $role->getUri();

        }
        return $roles;
    }
}
