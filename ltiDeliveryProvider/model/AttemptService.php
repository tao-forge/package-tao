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
 * Copyright (c) 2018 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\ltiDeliveryProvider\model;

use oat\oatbox\user\User;
use oat\taoDelivery\model\execution\ServiceProxy;
use oat\taoLti\models\classes\TaoLtiSession;

/**
 * Service to count the attempts to pass the test.
 *
 * @access public
 * @author Aleh Hutnikau, <hutnikau@1pt.com>
 * @package taoDelivery
 */
class AttemptService extends \oat\taoDelivery\model\AttemptService
{

    /**
     * @inheritdoc
     */
    public function getAttempts($delivery, User $user)
    {
        $currentSession = \common_session_SessionManager::getSession();
        if ($currentSession instanceof TaoLtiSession) {
            return $this->getServiceLocator()->get(ServiceProxy::SERVICE_ID)
                ->getUserExecutions(new \core_kernel_classes_Resource($delivery), $user->getIdentifier());
        } else {
            return parent::getAttempts($delivery, $user);
        }
    }
}
