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

namespace oat\taoProctoring\controller;

use \common_session_SessionManager as SessionManager;
use \core_kernel_classes_Resource;

/**
 *
 * @author Open Assessment Technologies SA
 * @package taoProctoring
 * @license GPL-2.0
 *
 */
class Proctoring extends \tao_actions_CommonModule
{
    protected $currentTestCenter = null;
    protected $currentDelivery   = null;

    private function _getDummyDelivery(){
        
        $fakeUri = 'my_local_ns#i9999999999999999';
        $delivery = new core_kernel_classes_Resource($fakeUri);
        if(!$delivery->exists()){
            $objectClass = new \core_kernel_classes_Class(TAO_OBJECT_CLASS);
            $delivery = $objectClass->createInstance('Dummy Delivery', 'temporarly generated delivery', $fakeUri);
        }
        return $delivery;
    }

    private function _getDummyTestCenter(){

        $fakeUri = 'my_local_ns#i111111111111111';
        $testCenter = new core_kernel_classes_Resource($fakeUri);
        if(!$testCenter->exists()){
            $objectClass = new \core_kernel_classes_Class(TAO_OBJECT_CLASS);
            $testCenter = $objectClass->createInstance('Dummy Test Center', 'temporarly generated test center', $fakeUri);
        }
        return $testCenter;
    }

    protected function getCurrentTestCenter()
    {
        if (is_null($this->currentTestCenter)) {
            if($this->hasRequestParameter('testCenter')){

                //@todo remove me
                return $this->_getDummyTestCenter();

                //get test center resource from its uri
                $testCenterUri           = $this->getRequestParameter('testCenter');
                $this->currentTestCenter = new core_kernel_classes_Resource($testCenterUri);
            }else{
                //@todo use a better exception
                throw new \common_Exception('no current test center');
            }
            
        }
        return $this->currentTestCenter;
    }

    protected function getCurrentDelivery()
    {
        if (is_null($this->currentDelivery)) {
            if($this->hasRequestParameter('delivery')){

                //@todo remove me
                return $this->_getDummyDelivery();

                //get test center resource from its uri
                $deliveryUri           = $this->getRequestParameter('delivery');
                $this->currentDelivery = new core_kernel_classes_Resource($deliveryUri);
            }else{
                //@todo use a better exception
                throw new \common_Exception('no current delivery');
            }
        }
        return $this->currentDelivery;
    }

    protected function composeView($cssClass, $data = array(), $breadcrumbs = array())
    {
        $data['userLabel']   = SessionManager::getSession()->getUserLabel();
        $data['breadcrumbs'] = $breadcrumbs;

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $this->returnJson($data);
        } else {
            $this->defaultData();
            $this->setData('clientConfigUrl', $this->getClientConfigUrl());
            $this->setData('cls', $cssClass);
            $this->setData('data', $data);
            $this->setData('content-template', 'index.tpl');
            $this->setView('layout.tpl');
        }
    }

    /**
     * Gets a list of available Test Centers for the current proctor
     *
     * @return array
     */
    protected function getTestCenters()
    {
        $user = SessionManager::getSession()->getUser();
        //get allowed test centers based on current proctor...

        $entries = array();

        $entries[] = array(
            'id' => 'locam_ns#i1000000001',
            'url' => _url('testCenter', 'TestCenter', null, array('testCenter' => 'locam_ns#i1000000001')),
            'label' => 'Room A',
            'text' => __('Go to')
        );
        $entries[] = array(
            'id' => 'locam_ns#i1000000002',
            'url' => _url('testCenter', 'TestCenter', null, array('testCenter' => 'locam_ns#i1000000002')),
            'label' => 'Room B',
            'text' => __('Go to')
        );
        $entries[] = array(
            'id' => 'locam_ns#i1000000003',
            'url' => _url('testCenter', 'TestCenter', null, array('testCenter' => 'locam_ns#i1000000003')),
            'label' => 'Room C',
            'text' => __('Go to')
        );

        return $entries;
    }

    /**
     * Gets the list of available deliveries for the selected test center
     *
     * @return array
     */
    protected function getDeliveries()
    {

        $testCenter = $this->getCurrentTestCenter();

        $entries = array();

        $entries[] = array(
            'id' => 'locam_ns#i2000000001',
            'url' => _url('monitoring', 'Delivery', null, array('delivery' => 'locam_ns#i2000000001', 'testCenter' => $testCenter->getUri())),
            'label' => 'Test A',
            'text' => __('Monitor')
        );
        $entries[] = array(
            'id' => 'locam_ns#i2000000002',
            'url' => _url('monitoring', 'Delivery', null, array('delivery' => 'locam_ns#i2000000002', 'testCenter' => $testCenter->getUri())),
            'label' => 'Test B',
            'text' => __('Monitor')
        );
        $entries[] = array(
            'id' => 'locam_ns#i2000000003',
            'url' => _url('monitoring', 'Delivery', null, array('delivery' => 'locam_ns#i2000000003', 'testCenter' => $testCenter->getUri())),
            'label' => 'Test C',
            'text' => __('Monitor')
        );

        return $entries;
    }

    public function getBreadcrumb()
    {
        
    }
}