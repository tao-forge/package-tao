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
namespace oat\taoDelivery\test\integration\model\execution;

use oat\tao\test\TaoPhpUnitTestRunner;
use oat\taoDelivery\model\execution\DeliveryExecution;
use oat\taoDelivery\model\execution\implementation\KeyValueService;
use oat\taoDelivery\model\execution\KVDeliveryExecution;
use oat\taoDelivery\model\execution\DeliveryExecutionInterface;
use oat\taoDelivery\model\execution\Service as ExecutionService;

class KeyValueServiceTest extends TaoPhpUnitTestRunner
{
    /** @var KeyValueService */
    private $service;

    public function setUp()
    {
        parent::setUp();

        $pmMock = $this->getKvMock('dummy');
        $serviceLocatorMock = $this->getServiceLocatorMock([
            \common_persistence_Manager::SERVICE_ID => $pmMock
        ]);
        $this->service = new KeyValueService([
            KeyValueService::OPTION_PERSISTENCE => 'dummy'
        ]);
        $this->service->setServiceLocator($serviceLocatorMock);
    }

    public function testSetState()
    {
        $this->assertInstanceOf(ExecutionService::class, $this->service);
        
        $assembly = new \core_kernel_classes_Resource('fake');
        $deWrapper = $this->service->spawnDeliveryExecution('DE label', $assembly, 'fakeUser', 'http://uri.com/fake#StartState');
        
        $this->assertInstanceOf(DeliveryExecution::class, $deWrapper);
        $deliveryExecution = $deWrapper->getImplementation();
        $this->assertInstanceOf(DeliveryExecutionInterface::class, $deliveryExecution);
        
        $success = $deliveryExecution->setState('http://uri.com/fake#State');
        $this->assertTrue($success);
        
        $state = $deliveryExecution->getState();
        $this->assertEquals('http://uri.com/fake#State', $state->getUri());
        
        $success = $deliveryExecution->setState('fakeState');
        $this->assertTrue($success);
        
        $state = $deliveryExecution->getState();
        $this->assertEquals('fakeState', $state->getUri());
        
        $success = $deliveryExecution->setState('fakeState');
        $this->assertFalse($success);
    }
    
    public function testFailedStartTime()
    {
        $execution = new KVDeliveryExecution($this->service, 'http://uri.com/fake#Execution');
        $this->setExpectedException(\common_exception_NotFound::class);
        $execution->getStartTime();
        
    }

    public function testGetDeliveryExecutionsByStatus()
    {
        $userId = 'fakeUser';
        $assembly = new \core_kernel_classes_Resource('fake');
        $de = $this->service->spawnDeliveryExecution('DE label', $assembly, $userId, 'http://uri.com/fake#StartState');
        $de2 = $this->service->spawnDeliveryExecution('DE label', $assembly, $userId, 'http://uri.com/fake#StartState');

        $kvde = $de->getImplementation();
        $this->service->updateDeliveryExecutionStatus($kvde, null, 'http://uri.com/fake#FinishState');
        $persistence = $this->service->getServiceLocator()->get(\common_persistence_Manager::SERVICE_ID)->getPersistenceById('dummy');

        $deInStartState = json_decode($persistence->get('kve_ue_fakeUserhttp://uri.com/fake#StartState'), true);
        $deInFinishState = json_decode($persistence->get('kve_ue_fakeUserhttp://uri.com/fake#FinishState'), true);

        /** $de in both start and finish state arrays, but itself is in start state*/
        $this->assertCount(2, $deInStartState);
        $this->assertCount(1, $deInFinishState);

        /** after call getDeliveryExecutionsByStatus it should be deleted from finish array, because both executions are in start state */
        $this->assertCount(2, $this->service->getDeliveryExecutionsByStatus($userId, 'http://uri.com/fake#StartState'));
        $this->assertCount(0, $this->service->getDeliveryExecutionsByStatus($userId, 'http://uri.com/fake#FinishState'));

        $kvde->setState('http://uri.com/fake#FinishState');

        $this->assertCount(1, $this->service->getDeliveryExecutionsByStatus($userId, 'http://uri.com/fake#StartState'));
        $this->assertCount(1, $this->service->getDeliveryExecutionsByStatus($userId, 'http://uri.com/fake#FinishState'));
    }

    public function testUpdateDeliveryExecutionStatus()
    {
        $userId = 'fakeUser';
        $assembly = new \core_kernel_classes_Resource('fake');
        $persistence = $this->service->getServiceLocator()->get(\common_persistence_Manager::SERVICE_ID)->getPersistenceById('dummy');
        $de = $this->service->spawnDeliveryExecution('DE label', $assembly, $userId, 'http://uri.com/fake#StartState');

        $deInStartState = json_decode($persistence->get('kve_ue_fakeUserhttp://uri.com/fake#StartState'), true);
        $this->assertCount(1, $deInStartState);

        $kvde = $de->getImplementation();
        $kvde->setState('http://uri.com/fake#FinishState');

        $deInStartState = json_decode($persistence->get('kve_ue_fakeUserhttp://uri.com/fake#StartState'), true);
        $deInFinishState = json_decode($persistence->get('kve_ue_fakeUserhttp://uri.com/fake#FinishState'), true);

        $this->assertCount(0, $deInStartState);
        $this->assertCount(1, $deInFinishState);
    }
}
