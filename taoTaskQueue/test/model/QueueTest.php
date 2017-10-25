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

use oat\taoTaskQueue\model\Task\AbstractTask;
use oat\taoTaskQueue\model\QueueBroker\QueueBrokerInterface;
use oat\taoTaskQueue\model\Queue;
use oat\taoTaskQueue\model\TaskLogInterface;

/**
 * @backupGlobals disabled
 * @backupStaticAttributes disabled
 */
class QueueTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();

        require_once __DIR__ .'/../../../tao/includes/raw_start.php';
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectExceptionMessage  Queue name needs to be set.
     */
    public function testWhenQueueNameIsEmptyThenThrowException()
    {
        new Queue('');
    }


    public function testGetNameShouldReturnTheValueOfQueueName()
    {
        $brokerMock = $this->getMockForAbstractClass(QueueBrokerInterface::class);

        $queue = new Queue('fakeQueue', $brokerMock);
        $this->assertEquals('fakeQueue', $queue->getName());
    }

    public function testGetWeightShouldReturnTheValueOfQueueWeight()
    {
        $brokerMock = $this->getMockForAbstractClass(QueueBrokerInterface::class);

        $queue = new Queue('fakeQueue', $brokerMock, 23);
        $this->assertEquals(23, $queue->getWeight());
    }

    /**
     * @dataProvider provideEnqueueOptions
     */
    public function testEnqueueWhenTaskPushedOrNot($isEnqueued, $expected)
    {
        $taskMock = $this->getMockForAbstractClass(AbstractTask::class, [], "", false);

        $queueBrokerMock = $this->getMockForAbstractClass(QueueBrokerInterface::class);

        $queueBrokerMock->expects($this->once())
            ->method('push')
            ->willReturn($isEnqueued);

        $taskLogMock = $this->getMockForAbstractClass(TaskLogInterface::class);

        $queueMock = $this->getMockBuilder(Queue::class)
            ->disableOriginalConstructor()
            ->setMethods(['getBroker', 'getTaskLog'])
            ->getMock();

        $queueMock->expects($this->once())
            ->method('getBroker')
            ->willReturn($queueBrokerMock);

        if ($isEnqueued) {
            $taskLogMock->expects($this->once())
                ->method('add');

            $queueMock->expects($this->once())
                ->method('getTaskLog')
                ->willReturn($taskLogMock);
        }

        $this->assertEquals($expected, $queueMock->enqueue($taskMock));
    }

    public function provideEnqueueOptions()
    {
        return [
            'ShouldBeSuccessful' => [true, true],
            'ShouldBeFailed' => [false, false],
        ];
    }

    /**
     * @dataProvider provideDequeueOptions
     */
    public function testDequeueWhenTaskPoppedOrNot($dequeuedElem, $expected)
    {
        $queueBrokerMock = $this->getMockForAbstractClass(QueueBrokerInterface::class);

        $queueBrokerMock->expects($this->once())
            ->method('pop')
            ->willReturn($dequeuedElem);

        $taskLogMock = $this->getMockForAbstractClass(TaskLogInterface::class);

        $queueMock = $this->getMockBuilder(Queue::class)
            ->disableOriginalConstructor()
            ->setMethods(['getBroker', 'getTaskLog'])
            ->getMock();

        $queueMock->expects($this->once())
            ->method('getBroker')
            ->willReturn($queueBrokerMock);

        if ($dequeuedElem) {
            $taskLogMock->expects($this->once())
                ->method('setStatus');

            $queueMock->expects($this->once())
                ->method('getTaskLog')
                ->willReturn($taskLogMock);
        }

        $this->assertEquals($expected, $queueMock->dequeue());
    }

    public function provideDequeueOptions()
    {
        $taskMock = $this->getMockForAbstractClass(AbstractTask::class, [], "", false);

        return [
            'ShouldBeSuccessful' => [$taskMock, $taskMock],
            'ShouldBeFailed' => [null, null],
        ];
    }

    public function testAcknowledgeShouldCallDeleteOnBroker()
    {
        $taskMock = $this->getMockForAbstractClass(AbstractTask::class, [], "", false);

        $queueBrokerMock = $this->getMockForAbstractClass(QueueBrokerInterface::class);

        $queueBrokerMock->expects($this->once())
            ->method('delete');

        $queueMock = $this->getMockBuilder(Queue::class)
            ->disableOriginalConstructor()
            ->setMethods(['getBroker'])
            ->getMock();

        $queueMock->expects($this->once())
            ->method('getBroker')
            ->willReturn($queueBrokerMock);

        $queueMock->acknowledge($taskMock);
    }

    public function testCountShouldCallCountOnBroker()
    {
        $taskMock = $this->getMockForAbstractClass(AbstractTask::class, [], "", false);

        $queueBrokerMock = $this->getMockForAbstractClass(QueueBrokerInterface::class);

        $queueBrokerMock->expects($this->once())
            ->method('count');

        $queueMock = $this->getMockBuilder(Queue::class)
            ->disableOriginalConstructor()
            ->setMethods(['getBroker'])
            ->getMock();

        $queueMock->expects($this->once())
            ->method('getBroker')
            ->willReturn($queueBrokerMock);

        $queueMock->count($taskMock);
    }

    public function testInitializeShouldCallCreateQueueOnBroker()
    {
        $taskMock = $this->getMockForAbstractClass(AbstractTask::class, [], "", false);

        $queueBrokerMock = $this->getMockForAbstractClass(QueueBrokerInterface::class);

        $queueBrokerMock->expects($this->once())
            ->method('createQueue');

        $queueMock = $this->getMockBuilder(Queue::class)
            ->disableOriginalConstructor()
            ->setMethods(['getBroker'])
            ->getMock();

        $queueMock->expects($this->once())
            ->method('getBroker')
            ->willReturn($queueBrokerMock);

        $queueMock->initialize($taskMock);
    }
}