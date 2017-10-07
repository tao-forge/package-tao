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

namespace oat\taoTaskQueue\model\QueueBroker;

use oat\taoTaskQueue\model\Task\TaskInterface;
use Psr\Log\LoggerAwareInterface;

/**
 * Interface QueueBrokerInterface
 *
 * @author Gyula Szucs <gyula@taotesting.com>
 */
interface QueueBrokerInterface extends \Countable, LoggerAwareInterface
{
    const SERVICE_ID = 'taoTaskQueue/taskQueueBroker';

    // Maximum amount of tasks that can be received when polling the queue; Default is 1.
    const OPTION_NUMBER_OF_TASKS_TO_RECEIVE = 'number_of_tasks_to_receive';

    /**
     * Set queue name
     *
     * @param string $name
     * @return QueueBrokerInterface
     */
    public function setQueueName($name);

    /**
     * Creates the queue.
     *
     * @return mixed
     */
    public function createQueue();

    /**
     * Pushes a task into the queue.
     *
     * @param TaskInterface $task
     * @return bool
     */
    public function push(TaskInterface $task);

    /**
     * Pops a task from the queue. Returns null if there is no more task.
     *
     * @return null|TaskInterface
     */
    public function pop();

    /**
     * If the driver supports it, this will be called when a task has been consumed.
     *
     * @param TaskInterface $task
     */
    public function delete(TaskInterface $task);

    /**
     * The amount of tasks that can be received in one pop.
     *
     * @return int
     */
    public function getNumberOfTasksToReceive();
}