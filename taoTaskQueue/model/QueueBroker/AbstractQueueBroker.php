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

use oat\oatbox\service\ConfigurableService;
use oat\oatbox\action\ActionService;
use oat\oatbox\action\ResolutionException;
use oat\oatbox\log\LoggerAwareTrait;
use oat\taoTaskQueue\model\Task\CallbackTaskInterface;
use oat\taoTaskQueue\model\QueueInterface;
use oat\taoTaskQueue\model\Task\TaskFactory;
use oat\taoTaskQueue\model\Task\TaskInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Class AbstractQueueBroker
 *
 * @author Gyula Szucs <gyula@taotesting.com>
 */
abstract class AbstractQueueBroker extends ConfigurableService implements QueueBrokerInterface
{
    use LoggerAwareTrait;

    private $queueName;
    private $preFetchedQueue;

    /**
     * AbstractMessageBroker constructor.
     *
     * @param array  $options
     */
    public function __construct($options = []) {
        parent::__construct($options);

        $this->preFetchedQueue = new \SplQueue();
    }

    /**
     * Do the specific pop mechanism related to the given broker.
     * Tasks need to be added to the internal pre-fetched queue.
     *
     * @return void
     */
    abstract protected function doPop();

    /**
     * Internal mechanism of deleting a message, specific for the given broker
     *
     * @param string $id
     * @param array $logContext
     * @return void
     */
    abstract protected function doDelete($id, array $logContext = []);

    /**
     * @return null|TaskInterface
     */
    public function pop()
    {
        // if there is item in the pre-fetched queue, let's return that
        if ($message = $this->popPreFetchedMessage()) {
            return $message;
        }

        $this->doPop();

        return $this->popPreFetchedMessage();
    }

    /**
     * Pop a task from the internal queue.
     *
     * @return TaskInterface|null
     */
    private function popPreFetchedMessage()
    {
        if ($this->preFetchedQueue->count()) {
            return $this->preFetchedQueue->dequeue();
        }

        return null;
    }

    /**
     * Add a task to the internal queue.
     *
     * @param TaskInterface $task
     */
    protected function pushPreFetchedMessage(TaskInterface $task)
    {
        $this->preFetchedQueue->enqueue($task);
    }

    /**
     * Unserialize the given task JSON.
     *
     * If the json is not valid, it deletes the task straight away without processing it.
     *
     * @param string $taskJSON
     * @param string $idForDeletion An identification of the given task
     * @param array  $logContext
     * @return null|TaskInterface
     */
    protected function unserializeTask($taskJSON, $idForDeletion, array $logContext = [])
    {
        try {
            $basicData = json_decode($taskJSON, true);
            $this->assertValidJson($basicData);

            $task = TaskFactory::build($basicData);

            if ($task instanceof CallbackTaskInterface && is_string($task->getCallable())) {
                $this->handleCallbackTask($task, $logContext);
            }

            return $task;

        } catch (\Exception $e) {

            $this->doDelete($idForDeletion, $logContext);

            return null;
        }
    }

    /**
     * @param $basicData
     * @throws \Exception
     */
    protected function assertValidJson($basicData)
    {
        if ( ($basicData !== null
            && json_last_error() === JSON_ERROR_NONE
            && isset($basicData[TaskInterface::JSON_TASK_CLASS_NAME_KEY])) === false
        ) {
            throw new \Exception();
        }
    }

    /**
     * @param TaskInterface $task
     * @param array $logContext
     * @throws \Exception
     */
    protected function handleCallbackTask($task, $logContext)
    {
        try {
            $callable = $this->getActionResolver()->resolve($task->getCallable());

            if ($callable instanceof ServiceLocatorAwareInterface) {
                $callable->setServiceLocator($this->getServiceLocator());
            }

            $task->setCallable($callable);
        } catch (ResolutionException $e) {

            $this->logError('Callable/Action class ' . $task->getCallable() . ' does not exist', $logContext);

            throw new \Exception;
        }
    }

    /**
     * @return ActionService|ConfigurableService
     */
    protected function getActionResolver()
    {
        return $this->getServiceManager()->get(ActionService::SERVICE_ID);
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setQueueName($name)
    {
        $this->queueName = $name;

        return $this;
    }

    /**
     * @return string
     */
    protected function getQueueName()
    {
        return $this->queueName;
    }

    /**
     * @return string
     */
    protected function getQueueNameWithPrefix()
    {
        return sprintf("%s_%s", QueueInterface::QUEUE_PREFIX, $this->getQueueName());
    }

    /**
     * @inheritdoc
     */
    public function getNumberOfTasksToReceive()
    {
        if($this->hasOption(self::OPTION_NUMBER_OF_TASKS_TO_RECEIVE)) {
            return abs((int) $this->getOption(self::OPTION_NUMBER_OF_TASKS_TO_RECEIVE));
        }

        return 1;
    }
}