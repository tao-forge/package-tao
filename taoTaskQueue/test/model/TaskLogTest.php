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

namespace oat\taoTaskQueue\test\model;

use oat\taoTaskQueue\model\Entity\TaskLogEntity;
use oat\taoTaskQueue\model\Entity\TasksLogsStats;
use oat\taoTaskQueue\model\Task\AbstractTask;
use oat\taoTaskQueue\model\TaskLog;
use oat\taoTaskQueue\model\TaskLogBroker\TaskLogBrokerInterface;
use oat\taoTaskQueue\model\TaskLogBroker\TaskLogCollection;

class TaskLogTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testTaskLogServiceShouldThrowExceptionWhenTaskLogBrokerOptionIsNotSet()
    {
        new TaskLog([]);
    }

    public function testGetBrokerInstantiatingTheTaskLogBrokerAndReturningItWithTheRequiredInterface()
    {
        $logBrokerMock = $this->getMockBuilder(TaskLogBrokerInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['setServiceLocator'])
            ->getMockForAbstractClass();

        $logBrokerMock->expects($this->once())
            ->method('setServiceLocator');

        $taskLogMock = $this->getMockBuilder(TaskLog::class)
            ->disableOriginalConstructor()
            ->setMethods(['getOption', 'getServiceLocator'])
            ->getMock();

        $taskLogMock->expects($this->once())
            ->method('getOption')
            ->willReturn($logBrokerMock);

        $taskLogMock->expects($this->once())
            ->method('getServiceLocator');

        $brokerCaller = function () {
            return $this->getBroker();
        };

        $bound = $brokerCaller->bindTo($taskLogMock, $taskLogMock);

        $this->assertInstanceOf(TaskLogBrokerInterface::class, $bound());
    }

    public function testAddWhenWrongStatusIsSuppliedThenErrorMessageShouldBeLogged()
    {
        $taskMock = $this->getMockForAbstractClass(AbstractTask::class, [], "", false);

        $taskLogMock = $this->getMockBuilder(TaskLog::class)
            ->disableOriginalConstructor()
            ->setMethods(['logError'])
            ->getMock();

        $taskLogMock->expects($this->atLeastOnce())
            ->method('logError');

        $taskLogMock->add($taskMock, 'fake_status');
    }

    public function testAddWhenStatusIsOkayThenTaskShouldBeAddedByBroker()
    {
        $taskMock = $this->getMockForAbstractClass(AbstractTask::class, [], "", false);

        $logBrokerMock = $this->getMockForAbstractClass(TaskLogBrokerInterface::class);

        $logBrokerMock->expects($this->once())
            ->method('add');

        $taskLogMock = $this->getMockBuilder(TaskLog::class)
            ->disableOriginalConstructor()
            ->setMethods(['getBroker'])
            ->getMock();

        $taskLogMock->expects($this->once())
            ->method('getBroker')
            ->willReturn($logBrokerMock);;

        $taskLogMock->add($taskMock, 'enqueued');
    }

    public function testSetStatusWhenNewAndPrevStatusIsOkayThenStatusShouldBeUpdatedByBroker()
    {
        $logBrokerMock = $this->getMockForAbstractClass(TaskLogBrokerInterface::class);

        $logBrokerMock->expects($this->once())
            ->method('updateStatus');

        $taskLogMock = $this->getMockBuilder(TaskLog::class)
            ->disableOriginalConstructor()
            ->setMethods(['getBroker', 'validateStatus'])
            ->getMock();

        $taskLogMock->expects($this->once())
            ->method('getBroker')
            ->willReturn($logBrokerMock);

        $taskLogMock->expects($this->exactly(2))
            ->method('validateStatus');

        $taskLogMock->setStatus('fakeId', 'dequeued', 'running');
    }

    public function testGetStatusWhenTaskExistItReturnsItsStatus()
    {
        $expectedStatus = 'dequeued';

        $logBrokerMock = $this->getMockForAbstractClass(TaskLogBrokerInterface::class);

        $logBrokerMock->expects($this->once())
            ->method('getStatus')
            ->willReturn($expectedStatus);

        $taskLogMock = $this->getMockBuilder(TaskLog::class)
            ->disableOriginalConstructor()
            ->setMethods(['getBroker', 'validateStatus'])
            ->getMock();

        $taskLogMock->expects($this->once())
            ->method('getBroker')
            ->willReturn($logBrokerMock);

        $this->assertEquals($expectedStatus, $taskLogMock->getStatus('existingTaskId'));
    }

    public function testFindAvailableByUser()
    {
        $model = $this->getTaskLogMock();
        $this->assertInstanceOf(TaskLogCollection::class, $model->findAvailableByUser('userId'));
    }

    public function testGetByIdAndUser()
    {
        $model = $this->getTaskLogMock();
        $this->assertInstanceOf(TaskLogEntity::class, $model->getByIdAndUser('taskId', 'userId'));
    }

    /**
     * @expectedException  \common_exception_NotFound
     */
    public function testGetByIdAndUserNotFound()
    {
        $model = $this->getTaskLogMock(true);
        $this->assertInstanceOf(TaskLogEntity::class, $model->getByIdAndUser('some task id not found', 'userId'));
    }

    public function testGetStats()
    {
        $model = $this->getTaskLogMock();
        $this->assertInstanceOf(TasksLogsStats::class, $model->getStats('userId'));
    }

    public function testArchive()
    {
        $model = $this->getTaskLogMock();
        $this->assertTrue($model->archive($model->getByIdAndUser('taskId', 'userId')));
    }

    /**
     * @expectedException  \common_exception_NotFound
     */
    public function testArchiveTaskNotFound()
    {
        $model = $this->getTaskLogMock(true);
        $this->assertTrue($model->archive($model->getByIdAndUser('taskId', 'userId')));
    }

    /**
     * @expectedException  \Exception
     */
    public function testArchiveNotPossibleTaskIsRunning()
    {
        $model = $this->getTaskLogMock(false, false, true);

        $this->assertTrue($model->archive($model->getByIdAndUser('taskId', 'userId')));
    }

    protected function getTaskLogMock($notFound = false, $shouldArchive = true, $taskRunning = false)
    {
        $taskLogMock = $this->getMockBuilder(TaskLog::class)->disableOriginalConstructor()->getMock();
        $collectionMock = $this->getMockBuilder(TaskLogCollection::class)->disableOriginalConstructor()->getMock();
        $entity = $this->getMockBuilder(TaskLogEntity::class)->disableOriginalConstructor()->getMock();
        $statsMock = $this->getMockBuilder(TasksLogsStats::class)->disableOriginalConstructor()->getMock();

        $taskLogMock
            ->method('findAvailableByUser')
            ->willReturn($collectionMock);
        $taskLogMock
            ->method('getStats')
            ->willReturn($statsMock);
        if ($taskRunning) {
            $taskLogMock
                ->method('getByIdAndUser')
                ->willThrowException(new \Exception());
        } else {
            $taskLogMock
                ->method('archive')
                ->willReturn($shouldArchive);
        }
        if ($notFound) {
            $taskLogMock
                ->method('getByIdAndUser')
                ->willThrowException(new \common_exception_NotFound());
        } else {
            $taskLogMock
                ->method('getByIdAndUser')
                ->willReturn($entity);
        }

        return $taskLogMock;
    }
}