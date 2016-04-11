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
namespace oat\taoProctoring\model;

use core_kernel_classes_Resource as Resource;
use core_kernel_classes_Class;
use core_kernel_classes_Property as Property;
use oat\taoProctoring\model\implementation\DeliveryService;
use tao_models_classes_ClassService;
use oat\oatbox\user\User;

/**
 * Service to manage eligible deliveries
 */
class EligibilityService extends tao_models_classes_ClassService
{
    const CLASS_URI = 'http://www.tao.lu/Ontologies/TAOProctor.rdf#DeliveryEligibility';

    const PROPERTY_TESTCENTER_URI = 'http://www.tao.lu/Ontologies/TAOProctor.rdf#EligibileTestCenter';

    const PROPERTY_TESTTAKER_URI = 'http://www.tao.lu/Ontologies/TAOProctor.rdf#EligibileTestTaker';

    const PROPERTY_DELIVERY_URI = 'http://www.tao.lu/Ontologies/TAOProctor.rdf#EligibileDelivery';

    /**
     * return the test center top level class
     *
     * @access public
     * @return core_kernel_classes_Class
     */
    public function getRootClass()
    {
        return new core_kernel_classes_Class(self::CLASS_URI);
    }
    
    /**
     * Establishes a new eligibility
     * 
     * @param Resource $testCenter
     * @param Resource $delivery
     * @return boolean
     */
    public function createEligibility(Resource $testCenter, Resource $delivery) {
        if (!is_null($this->getEligibility($testCenter, $delivery))) {
            // already exists, don't recreate
            return false;
        }
        $eligibilty = $this->getRootClass()->createInstanceWithProperties(array(
            self::PROPERTY_TESTCENTER_URI => $testCenter,
            self::PROPERTY_DELIVERY_URI => $delivery
        ));
        return true;
    }
    
    /**
     * Get deliveries eligible at a testcenter
     * 
     * @param Resource $testCenter
     * @return Resource[]
     */
    public function getEligibleDeliveries(Resource $testCenter) {
        $eligibles = $this->getRootClass()->searchInstances(array(
            self::PROPERTY_TESTCENTER_URI => $testCenter
        ), array('recursive' => false, 'like' => false));
        $deliveries = array();
        foreach ($eligibles as $eligible) {
            $delivery = $eligible->getOnePropertyValue(new Property(self::PROPERTY_DELIVERY_URI));
            if ($delivery->exists()) {
                $deliveries[] = $delivery;
            }
        }
        return $deliveries;
    }
    
    /**
     * Removes an eligibility
     * 
     * @param Resource $testCenter
     * @param Resource $delivery
     * @throws IneligibileException
     * @return boolean
     */
    public function removeEligibility(Resource $testCenter, Resource $delivery) {
        $eligibility = $this->getEligibility($testCenter, $delivery);
        if (is_null($eligibility)) {
            throw new IneligibileException('Delivery '.$delivery->getUri().' ineligible to test center '.$testCenter->getUri());
        }
        $deletion = $eligibility->delete();
        if($deletion){
            //unassign all test taker for this delivery in this test center
            $deliveryService = $this->getServiceManager()->get(DeliveryService::CONFIG_ID);
            $deliveryService->removeAvailability($delivery->getUri(), $testCenter->getUri());
        }
        return $deletion;
    }
    
    /**
     * Return ids of test-takers that are eligble in the specified context
     * 
     * @param Resource $testCenter
     * @param Resource $delivery
     * @return string[] identifiers of the test-takers
     */
    public function getEligibleTestTakers(Resource $testCenter, Resource $delivery) {
        $eligible = array();
        $eligibility = $this->getEligibility($testCenter, $delivery);
        if (!is_null($eligibility)) {
            foreach ($eligibility->getPropertyValues(new Property(self::PROPERTY_TESTTAKER_URI)) as $testTaker) {
                $eligible[] = $testTaker instanceof Resource ? $testTaker->getUri() : (string)$testTaker;
            }
        }
        return $eligible;
    }
    
    /**
     * Allow test-taker to be eligible for this testcenter/delivery context
     *
     * @param Resource $testCenter
     * @param Resource $delivery
     * @param string[] $testTakerIds
     * @throws IneligibileException
     * @return boolean
     */
    public function setEligibleTestTakers(Resource $testCenter, Resource $delivery, $testTakerIds) {
        $eligibility = $this->getEligibility($testCenter, $delivery);
        if (is_null($eligibility)) {
            throw new IneligibileException('Delivery '.$delivery->getUri().' ineligible to test center '.$testCenter->getUri());
        }
        return $eligibility->editPropertyValues(new Property(self::PROPERTY_TESTTAKER_URI), $testTakerIds);
    }
    
    /**
     * Returns the eligibility representing the link, or null if not found
     *  
     * @param Resource $testCenter
     * @param Resource $delivery
     * @throws \common_exception_InconsistentData
     * @return Resource eligibility resource
     */
    protected function getEligibility(Resource $testCenter, Resource $delivery) {
        $eligibles = $this->getRootClass()->searchInstances(array(
            self::PROPERTY_TESTCENTER_URI => $testCenter,
            self::PROPERTY_DELIVERY_URI => $delivery
        ), array('recursive' => false, 'like' => false));
        if (count($eligibles) == 0) {
            return null;
        }
        if (count($eligibles) > 1) {
            throw new \common_exception_InconsistentData('Multiple eligibilities for testcenter '.$testCenter->getUri().' and delivery '.$delivery->getUri());
        }
        return reset($eligibles);
    }

    /**
     * @param \core_kernel_classes_Resource $delivery
     * @param User $user
     * @return bool
     */
    public function isDeliveryEligible(\core_kernel_classes_Resource $delivery, User $user)
    {
        $result = false;
        $class = new \core_kernel_classes_Class(EligibilityService::CLASS_URI);
        $eligibilities = $class->searchInstances([
            EligibilityService::PROPERTY_TESTTAKER_URI => $user->getIdentifier(),
            EligibilityService::PROPERTY_DELIVERY_URI => $delivery->getUri(),
        ]);

        foreach ($eligibilities as $eligibility) {
            /* @var \core_kernel_classes_Resource $eligibility*/
            $testCenter = $eligibility->getOnePropertyValue(new \core_kernel_classes_Property(EligibilityService::PROPERTY_TESTCENTER_URI));
            if ($testCenter instanceof \core_kernel_classes_Resource && $testCenter->exists()) {
                $result = true;
                break;
            }
        }

        return $result;
    }

}
