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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 *
 */

namespace oat\taoLti\models\classes\user;

use oat\oatbox\service\ConfigurableService;
use oat\taoLti\models\classes\LtiLaunchData;

/**
 * Lti user service, allow us to find or spawn a lti user based on launch data
 *
 * @access public
 * @author Antoine Antoine, <joel@taotesting.com>
 * @package taoLti
 */
abstract class LtiUserService extends ConfigurableService
{
    const SERVICE_ID = 'taoLti/LtiUserService';

    /**
     * Returns the existing tao User that corresponds to
     * the LTI request or spawns it
     *
     * @param LtiLaunchData $launchData
     * @return LtiUser
     * @throws \common_Exception
     * @throws \common_exception_Error
     * @throws \core_kernel_users_CacheException
     * @throws \core_kernel_users_Exception
     * @throws \oat\taoLti\models\classes\LtiVariableMissingException
     */
    public function findOrSpawnUser(LtiLaunchData $launchData)
    {
        $taoUser = $this->findUser($launchData);
        if (is_null($taoUser)) {
            $taoUser = $this->spawnUser($launchData);
        }
        return $taoUser;
    }

    /**
     * Searches if this user was already created in TAO
     *
     * @param LtiLaunchData $ltiContext
     * @return LtiUser
     * @throws \common_Exception
     * @throws \common_exception_Error
     * @throws \core_kernel_users_CacheException
     * @throws \core_kernel_users_Exception
     * @throws \oat\taoLti\models\classes\LtiVariableMissingException
     */
    public function findUser(LtiLaunchData $ltiContext)
    {
        $ltiConsumer = $ltiContext->getLtiConsumer();
        $taoUserId = $this->getUserIdentifier($ltiContext->getUserID(), $ltiConsumer->getUri());
        if (is_null($taoUserId)) {
            return null;
        }

        $ltiUser = new LtiUser($ltiContext, $taoUserId);

        \common_Logger::t("LTI User '" . $ltiUser->getIdentifier() . "' found.");

        $this->updateUser($ltiUser, $ltiContext);

        return $ltiUser;
    }

    /**
     * @param LtiUser $user
     * @param LtiLaunchData $ltiContext
     * @return mixed
     */
    abstract protected function updateUser(LtiUser $user, LtiLaunchData $ltiContext);

    /**
     * Find the tao user identifier related to a lti user id and a consumer
     * @param string $ltiUserId
     * @param \core_kernel_classes_Resource $consumer
     * @return mixed
     */
    abstract public function getUserIdentifier($ltiUserId, $consumer);

    /**
     * Creates a new LTI User with the absolute minimum of required informations
     *
     * @param LtiLaunchData $ltiContext
     * @return LtiUser
     * @throws \common_Exception
     * @throws \common_exception_Error
     * @throws \core_kernel_users_CacheException
     * @throws \core_kernel_users_Exception
     * @throws \oat\taoLti\models\classes\LtiVariableMissingException
     */
    public function spawnUser(LtiLaunchData $ltiContext)
    {
        //@TODO create LtiUser after create and save in db.
        $userId = $ltiContext->getUserID();

        $ltiUser = new LtiUser($ltiContext, $userId);

        $this->updateUser($ltiUser, $ltiContext);

        return $ltiUser;
    }

    /**
     * Get the user information from the tao user identifier
     * @param string $taoUserId
     * @return array structure that represent the user
     * [
     * 'http://www.tao.lu/Ontologies/generis.rdf#userRoles' => ['firstRole', 'secondRole'],
     * 'http://www.tao.lu/Ontologies/generis.rdf#userUILg' => 'en-US',
     * 'http://www.tao.lu/Ontologies/generis.rdf#userFirstName' => 'firstname,
     * 'http://www.tao.lu/Ontologies/generis.rdf#userLastName' => 'lastname',
     * 'http://www.tao.lu/Ontologies/generis.rdf#userMail' => 'test@test.com',
     * 'http://www.w3.org/2000/01/rdf-schema#label' => 'label'
     * ]
     */
    abstract public function getUserDataFromId($taoUserId);
}
