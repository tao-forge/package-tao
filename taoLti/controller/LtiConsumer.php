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

declare(strict_types=1);

namespace oat\taoLti\controller;

use common_exception_Error;
use common_exception_MissingParameter;
use common_http_Request;
use common_session_SessionManager;
use oat\taoLti\models\classes\LtiUtils;
use oat\tao\controller\ServiceModule;
use oat\tao\model\oauth\Credentials;
use oat\tao\model\oauth\Service;

class LtiConsumer extends ServiceModule
{
    /**
     * Launches a oauth tool
     * @throws common_exception_Error
     * @throws common_exception_MissingParameter
     */
    public function call()
    {
        if (!$this->hasRequestParameter('ltiConsumerUri')) {
            throw new common_exception_MissingParameter('ltiConsumerUri', get_class($this));
        }
        if (!$this->hasRequestParameter('ltiLaunchUrl')) {
            throw new common_exception_MissingParameter('ltiLaunchUrl', get_class($this));
        }
        $ltiConsumer = new Credentials($this->getRequestParameter('ltiConsumerUri'));
        $launchUrl =  $this->getRequestParameter('ltiLaunchUrl');
        
        $serviceCallId = $this->getServiceCallId() . '_c';
        
        $session = common_session_SessionManager::getSession();
        
        $roles = [];
        foreach ($session->getUserRoles() as $role) {
            foreach (LtiUtils::mapTaoRole2LTIRoles($role) as $ltiRole) {
                $roles[] = $ltiRole;
            }
        }
        
        $ltiData = [
            'lti_message_type' => 'basic-lti-launch-request',
            'lti_version' => 'LTI-1p0',
            
            'resource_link_id' => rand(0, 9999999),
            'resource_link_title' => 'Launch Title',
            'resource_link_label' => 'Launch label',
            
            'context_id' => $serviceCallId,
            'context_title' => 'Launch Title',
            'context_label' => 'Launch label',
            
            'user_id' => $session->getUserUri(),
            'roles' => implode(',', $roles),
            'lis_person_name_full' => $session->getUserLabel(),
            
            'tool_consumer_info_product_family_code' => PRODUCT_NAME,
            'tool_consumer_info_version' => TAO_VERSION
        ];
        
        // @todo add:
        /*
        user_id:
        roles:

        lis_person_name_full:
        lis_person_name_family:
        lis_person_name_given:
        lis_person_contact_email_primary:
        lis_person_sourcedid:

        tool_consumer_info_product_family_code:
        tool_consumer_info_version:
        tool_consumer_instance_guid:
        tool_consumer_instance_description:
        */
        $request = new common_http_Request($launchUrl, common_http_Request::METHOD_POST, $ltiData);
        $service = new Service();
        $signedRequest = $service->sign($request, $ltiConsumer);
        
        $this->setData('launchUrl', $launchUrl);
        $this->setData('ltiData', $signedRequest->getParams());
        $this->setData('client_config_url', $this->getClientConfigUrl());
        $this->setView('ltiConsumer.tpl');
    }
}
