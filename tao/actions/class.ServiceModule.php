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

/**
 * V2 of the service controller
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package tao
 * @subpackage action
 *
 */
class tao_actions_ServiceModule extends tao_actions_CommonModule {

    protected function getServiceCallId() {
        if (!$this->hasRequestParameter('serviceCallId')) {
        	throw new common_exception_Error('No serviceCallId on service call');
        }
        return $this->getRequestParameter('serviceCallId');
    }
    
    protected function getState() {
        $serviceService = tao_models_classes_service_state_Service::singleton();
        $userUri = common_session_SessionManager::getSession()->getUserUri();
        return is_null($userUri) ? null : $serviceService->get($userUri, $this->getServiceCallId());
    }
    
    protected function setState($state) {
        $serviceService = tao_models_classes_service_state_Service::singleton();
        $userUri = common_session_SessionManager::getSession()->getUserUri();
        return is_null($userUri) ? false : $serviceService->set($userUri, $this->getServiceCallId(), $state);
    }
    
    public function submitState() {
        $success = $this->setState($_POST['state']);
        echo json_encode(array(
            'success' => $success
        ));
    }
    
}