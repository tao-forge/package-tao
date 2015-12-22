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

use oat\taoGroups\models\GroupsService;
use oat\oatbox\user\User;
use oat\taoFrontOffice\model\interfaces\DeliveryExecution;
use oat\oatbox\service\ServiceManager;
use oat\oatbox\service\ConfigurableService;

/**
 * Service to manage the execution of deliveries
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoDelivery
 */
class taoDelivery_models_classes_DeliveryServerService extends ConfigurableService
{
    const CONFIG_ID = 'taoDelivery/deliveryServer';

    public static function singleton()
    {
        return ServiceManager::getServiceManager()->get(self::CONFIG_ID);
    }

    /**
     * Get resumable (active) deliveries.
     * @param User $user User instance. If not given then all deliveries will be returned regardless of user URI.
     * @return type
     */
    public function getResumableDeliveries($user = null)
    {
        $deliveryExecutionService = taoDelivery_models_classes_execution_ServiceProxy::singleton();
        if ($user === null) {
            $executionClass = new core_kernel_classes_Class(CLASS_DELVIERYEXECUTION);
            $resources = $executionClass->searchInstances(array(
                PROPERTY_DELVIERYEXECUTION_STATUS => array(DeliveryExecution::STATE_ACTIVE, DeliveryExecution::STATE_PAUSED)
            ), array(
                'like' => false
            ));
            $started = array_map(function ($resource) use($deliveryExecutionService) {
                return $deliveryExecutionService->getDeliveryExecution($resource);
            }, $resources);
        } else {
            $started = array_merge(
                $deliveryExecutionService->getActiveDeliveryExecutions($user->getIdentifier()),
                $deliveryExecutionService->getPausedDeliveryExecutions($user->getIdentifier())
            );
        }
        
        $resumable = array();
        foreach ($started as $deliveryExecution) {
            $delivery = $deliveryExecution->getDelivery();
            if ($delivery->exists()) {
                $resumable[] = $deliveryExecution;
            }
        }
        return $resumable;
    }

    /**
     * Check if delivery configured for guest access
     *
     * @param core_kernel_classes_Resource $delivery
     * @return bool
     * @throws common_exception_InvalidArgumentType
     */
    public function hasDeliveryGuestAccess(core_kernel_classes_Resource $delivery )
    {
        $returnValue = false;

        $properties = $delivery->getPropertiesValues(array(
            new core_kernel_classes_Property(TAO_DELIVERY_ACCESS_SETTINGS_PROP),
        ));
        $propAccessSettings = current($properties[TAO_DELIVERY_ACCESS_SETTINGS_PROP]);
        $accessSetting = (!(is_object($propAccessSettings)) or ($propAccessSettings=="")) ? null : $propAccessSettings->getUri();

        if( !is_null($accessSetting) ){
            $returnValue = ($accessSetting === DELIVERY_GUEST_ACCESS);
        }

        return $returnValue;
    }

    public function getDeliverySettings(core_kernel_classes_Resource $delivery){
        $deliveryProps = $delivery->getPropertiesValues(array(
            new core_kernel_classes_Property(TAO_DELIVERY_MAXEXEC_PROP),
            new core_kernel_classes_Property(TAO_DELIVERY_START_PROP),
            new core_kernel_classes_Property(TAO_DELIVERY_END_PROP),
        ));

        $propMaxExec = current($deliveryProps[TAO_DELIVERY_MAXEXEC_PROP]);
        $propStartExec = current($deliveryProps[TAO_DELIVERY_START_PROP]);
        $propEndExec = current($deliveryProps[TAO_DELIVERY_END_PROP]);

        $settings[TAO_DELIVERY_MAXEXEC_PROP] = (!(is_object($propMaxExec)) or ($propMaxExec=="")) ? 0 : $propMaxExec->literal;
        $settings[TAO_DELIVERY_START_PROP] = (!(is_object($propStartExec)) or ($propStartExec=="")) ? null : $propStartExec->literal;
        $settings[TAO_DELIVERY_END_PROP] = (!(is_object($propEndExec)) or ($propEndExec=="")) ? null : $propEndExec->literal;
        $settings[CLASS_COMPILEDDELIVERY] = $delivery;

        return $settings;
    }

    public function isDeliveryExecutionAllowed(core_kernel_classes_Resource $delivery, User $user){

        $userUri = $user->getIdentifier();
        if (is_null($delivery)) {
            common_Logger::w("Attempt to start the compiled delivery ".$delivery->getUri(). " related to no delivery");
            return false;
        }
        
        //first check the user is assigned
        $serviceManager = ServiceManager::getServiceManager(); 
        if(!$serviceManager->get('taoDelivery/assignment')->isUserAssigned($delivery, $user)){
            common_Logger::w("User ".$userUri." attempts to start the compiled delivery ".$delivery->getUri(). " he was not assigned to.");
            return false;
        }
        
        $settings = $this->getDeliverySettings($delivery);

        //check Tokens
        $usedTokens = count(taoDelivery_models_classes_execution_ServiceProxy::singleton()->getUserExecutions($delivery, $userUri));
        
        if (($settings[TAO_DELIVERY_MAXEXEC_PROP] !=0 ) and ($usedTokens >= $settings[TAO_DELIVERY_MAXEXEC_PROP])) {
            common_Logger::d("Attempt to start the compiled delivery ".$delivery->getUri(). "without tokens");
            return false;
        }

        //check time
        $startDate  =    date_create('@'.$settings[TAO_DELIVERY_START_PROP]);
        $endDate    =    date_create('@'.$settings[TAO_DELIVERY_END_PROP]);
        if (!$this->areWeInRange($startDate, $endDate)) {
            common_Logger::d("Attempt to start the compiled delivery ".$delivery->getUri(). " at the wrong date");
            return false;
        }
        
        return true;
    }
    
    /**
     * Check if the date are in range
     * @param type $startDate
     * @param type $endDate
     * @return boolean true if in range
     */
    private function areWeInRange($startDate, $endDate){
        return (empty($startDate) || date_create() >= $startDate)
            && (empty($endDate) || date_create() <= $endDate);
    }
    
    /**
     * initalize the resultserver for a given execution
     * @param core_kernel_classes_resource processExecution
     */
    public function initResultServer($compiledDelivery, $executionIdentifier){

        //starts or resume a taoResultServerStateFull session for results submission

        //retrieve the result server definition
        $resultServer = $compiledDelivery->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_RESULTSERVER_PROP));
        //callOptions are required in the case of a LTI basic storage

        taoResultServer_models_classes_ResultServerStateFull::singleton()->initResultServer($resultServer->getUri());

        //a unique identifier for data collected through this delivery execution
        //in the case of LTI, we should use the sourceId


        taoResultServer_models_classes_ResultServerStateFull::singleton()->spawnResult($executionIdentifier, $executionIdentifier);
         common_Logger::i("Spawning/resuming result identifier related to process execution ".$executionIdentifier);
        //set up the related test taker
        //a unique identifier for the test taker
        taoResultServer_models_classes_ResultServerStateFull::singleton()->storeRelatedTestTaker(common_session_SessionManager::getSession()->getUserUri());

         //a unique identifier for the delivery
        taoResultServer_models_classes_ResultServerStateFull::singleton()->storeRelatedDelivery($compiledDelivery->getUri());
    }

    public function getAssembliesByGroup(core_kernel_classes_Resource $group) {
        $returnValue = array();
        foreach ($group->getPropertyValues(new core_kernel_classes_Property(PROPERTY_GROUP_DELVIERY)) as $groupUri) {
            $returnValue[] = new core_kernel_classes_Resource($groupUri);
        }
        return $returnValue;
    }
}