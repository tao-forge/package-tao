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
 */

namespace oat\taoTaskQueue\test\model\TaskLogBroker;

use oat\oatbox\service\ServiceManager;
use oat\taoTaskQueue\model\TaskLogBroker\RdsTaskLogBroker;

/**
 * @backupGlobals disabled
 * @backupStaticAttributes disabled
 */
class RdsTaskLogBrokerTest extends \PHPUnit_Framework_TestCase
{
    public function testServiceShouldThrowExceptionWhenPersistenceOptionIsNotSet()
    {
        $this->expectException(\InvalidArgumentException::class);
        new RdsTaskLogBroker([]);
    }

    public function testGetPersistenceWhenInstantiatingANewOneThenItReturnsOneWithTheRequiredInterface()
    {
        $commonPersistenceSqlPersistenceMock = $this->createMock(\common_persistence_SqlPersistence::class);
        $commonPersistenceManagerMock = $this->createMock(\common_persistence_Manager::class);

        $commonPersistenceManagerMock->expects($this->once())
            ->method('getPersistenceById')
            ->willReturn($commonPersistenceSqlPersistenceMock);

        $serviceManagerMock = $this->getMockBuilder(ServiceManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['get'])
            ->getMock();

        $serviceManagerMock->expects($this->once())
            ->method('get')
            ->willReturn($commonPersistenceManagerMock);

        $rdsLogBrokerMock = $this->getMockBuilder(RdsTaskLogBroker::class)
            ->disableOriginalConstructor()
            ->setMethods(['getServiceManager'])
            ->getMock();

        $rdsLogBrokerMock->expects($this->once())
            ->method('getServiceManager')
            ->willReturn($serviceManagerMock);

        $persistenceCaller = function () {
            return $this->getPersistence();
        };

        // Bind the closure to $rdsLogBrokerMock's scope.
        // $bound is now a Closure, and calling it is like asking $rdsLogBrokerMock to call $this->getPersistence(); and return the results.
        $bound = $persistenceCaller->bindTo($rdsLogBrokerMock, $rdsLogBrokerMock);

        $this->assertInstanceOf(\common_persistence_SqlPersistence::class, $bound());
    }

    public function testGetTableNameWhenContainerNameIsSuppliedByOptionThenItShouldBeInTheTableName()
    {
        $prefix = 'tq';
        $containerName = 'example_container_name';

        $broker = new RdsTaskLogBroker([
            'persistence' => 'fake',
            'container_name' => $containerName
        ]);

        $tableNameCaller = function () {
            return $this->getTableName();
        };

        $bound = $tableNameCaller->bindTo($broker, $broker);

        $this->assertEquals($prefix .'_'. $containerName, $bound());
    }

    public function testGetTableNameWhenContainerNameIsNotSuppliedByOptionThenTableNameShouldHaveADefaultValue()
    {
        $prefix = 'tq';
        $defaultName = 'task_log';

        $broker = new RdsTaskLogBroker([
            'persistence' => 'fake'
        ]);

        $tableNameCaller = function () {
            return $this->getTableName();
        };

        $bound = $tableNameCaller->bindTo($broker, $broker);

        $this->assertEquals($prefix .'_'. $defaultName, $bound());
    }
}