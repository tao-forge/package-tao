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

namespace oat\taoTaskQueue\model;

use common_report_Report as Report;
use oat\generis\model\OntologyAwareTrait;
use oat\oatbox\service\ConfigurableService;
use oat\oatbox\log\LoggerAwareTrait;
use oat\oatbox\task\Task;
use oat\taoTaskQueue\model\Task\CallbackTask;
use oat\taoTaskQueue\model\Task\CallbackTaskInterface;
use oat\taoTaskQueue\model\Task\TaskInterface;

/**
 * Class QueueDispatcher
 *
 * @author Gyula Szucs <gyula@taotesting.com>
 */
class QueueDispatcher extends ConfigurableService implements QueueDispatcherInterface
{
    use LoggerAwareTrait;
    use OntologyAwareTrait;

    /**
     * @var TaskLogInterface
     */
    private $taskLog;

    /**
     * QueueDispatcher constructor.
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        parent::__construct($options);

        $this->assertQueues();

        $this->assertTasks();

        if (!$this->hasOption(self::OPTION_TASK_LOG) || empty($this->getOption(self::OPTION_TASK_LOG))) {
            throw new \InvalidArgumentException("Task Log service needs to be set.");
        }
    }

    /**
     * @inheritdoc
     */
    public function __toPhpCode()
    {
        foreach ($this->getQueues() as $queue) {
            $this->propagateServices($queue);
        }

        return parent::__toPhpCode();
    }

    /**
     * @param TaskInterface $task
     * @return QueueInterface
     */
    protected function getQueueForTask(TaskInterface $task)
    {
        $action = $task instanceof CallbackTaskInterface && is_object($task->getCallable()) ? $task->getCallable() : $task;

        // getting queue name using the implemented getter function
        if ($action instanceof QueueAssociableInterface && ($queueName = $action->getQueueName($task->getParameters()))) {
            return $this->getQueue($queueName);
        }

        // getting the queue name based on the linked tasks configuration
        $className = get_class($action);
        if (array_key_exists($className, $this->getLinkedTasks())) {
            $queueName = $this->getLinkedTasks()[$className];

            return $this->getQueue($queueName);
        }

        // if we still don't have a queue, let's use the default one
        return $this->getDefaultQueue();
    }

    /**
     * @inheritdoc
     */
    public function getQueueNames()
    {
        return array_map(function(QueueInterface $queue) {
            return $queue->getName();
        }, $this->getQueues());
    }

    /**
     * @inheritdoc
     * @throws \LogicException
     */
    public function addQueue(QueueInterface $queue)
    {
        if ($this->hasQueue($queue->getName())) {
            throw new \LogicException('Queue "'. $queue .'" is already registered.');
        }

        $queues = $this->getQueues();
        $queues[] = $queue;

        $this->setOption(self::OPTION_QUEUES, $queues);
    }

    /**
     * @inheritdoc
     */
    public function hasQueue($queueName)
    {
        return in_array($queueName, $this->getQueueNames());
    }

    /**
     * @inheritdoc
     */
    public function getQueue($queueName)
    {
        $foundQueue = array_filter($this->getQueues(), function(QueueInterface $queue) use ($queueName){
            return $queue->getName() === $queueName;
        });

        if (count($foundQueue) === 1) {
            /** @var Queue $queue */
            $queue = reset($foundQueue);
            return $this->propagateServices($queue);
        }

        throw new \InvalidArgumentException('Queue "'. $queueName .'" does not exist.');
    }

    /**
     * @return QueueInterface[]
     */
    public function getQueues()
    {
        return (array) $this->getOption(self::OPTION_QUEUES);
    }

    /**
     * @inheritdoc
     */
    public function linkTaskToQueue($taskName, $queueName)
    {
        if (is_object($taskName)) {
            $taskName = get_class($taskName);
        }

        if (!$this->hasQueue($queueName)) {
            throw new \LogicException('Task "'. $taskName .'" cannot be added to "'. $queueName .'". Queue is not registered.');
        }

        $tasks = $this->getLinkedTasks();
        $tasks[] = (string) $taskName;

        $this->setOption(self::OPTION_TASK_TO_QUEUE_ASSOCIATIONS, $tasks);
    }

    /**
     * @inheritdoc
     */
    public function getLinkedTasks()
    {
        return (array) $this->getOption(self::OPTION_TASK_TO_QUEUE_ASSOCIATIONS);
    }

    /**
     * Return the first queue as a default one.
     * Maybe, later we need other logic the determine the default queue.
     *
     * @return QueueInterface
     */
    public function getDefaultQueue()
    {
        return $this->hasOption(self::OPTION_DEFAULT_QUEUE) && $this->getOption(self::OPTION_DEFAULT_QUEUE)
            ? $this->getQueue($this->getOption(self::OPTION_DEFAULT_QUEUE))
            : $this->getFirstQueue();
    }

    /**
     * Return the first queue from the array.
     *
     * @return QueueInterface
     */
    protected function getFirstQueue()
    {
        $queues = $this->getQueues();

        /** @var Queue $queue */
        $queue = reset($queues);
        return $this->propagateServices($queue);
    }

    /**
     * Gets random queue based on weight.
     *
     * For example, an array like ['A'=>5, 'B'=>45, 'C'=>50] means that "A" has a 5% chance of being selected, "B" 45%, and "C" 50%.
     * The values are simply relative to each other. If one value weight was 2, and the other weight of 1,
     * the value with the weight of 2 has about a 66% chance of being selected.
     *
     * @return QueueInterface
     */
    public function getQueueByWeight()
    {
        $weights = array_map(function(QueueInterface $queue) {
            return $queue->getWeight();
        }, $this->getQueues());

        $rand = mt_rand(1, array_sum($weights));

        /** @var Queue $queue */
        foreach ($this->getQueues() as $queue) {
            $rand -= $queue->getWeight();
            if ($rand <= 0) {
                $this->logInfo('Queue "'. strtoupper($queue->getName()) .'" selected by weight.');
                return $this->propagateServices($queue);
            }
        }
    }

    /**
     * Initialize queue.
     *
     * @return void
     */
    public function initialize()
    {
        foreach ($this->getQueues() as $queue) {
            $this->propagateServices($queue)
                ->initialize();
        }
    }

    /**
     * @inheritdoc
     */
    public function createTask(callable $callable, array $parameters = [], $label = null)
    {
        $id = \common_Utils::getNewUri();
        $owner = \common_session_SessionManager::getSession()->getUser()->getIdentifier();

        $callbackTask = new CallbackTask($id, $owner);
        $callbackTask->setCallable($callable)
            ->setParameter($parameters);

        if ($this->enqueue($callbackTask, $label)) {
            $callbackTask->markAsEnqueued();
        }

        return $callbackTask;
    }

    /**
     * @param TaskInterface $task
     * @param null|string   $label
     * @return bool
     */
    public function enqueue(TaskInterface $task, $label = null)
    {
        $queue = $this->getQueueForTask($task);
        $isEnqueued = $queue->enqueue($task, $label);

        // if we need to run the task straightaway
        if ($isEnqueued && $queue->isSync()) {
            $this->runWorker($queue);
        }

        return $isEnqueued;
    }

    /**
     * @inheritdoc
     */
    public function dequeue($queueName = null)
    {
        if (!is_null($queueName)) {
            return $this->getQueue($queueName)->dequeue();
        }

        // if there is only one queue defined, let's use that
        if(count($this->getQueues()) === 1) {
            return $this->getFirstQueue()->dequeue();
        }

        // default option getting a queue by weights
        return $this->getQueueByWeight()->dequeue();
    }

    /**
     * @inheritdoc
     */
    public function acknowledge(TaskInterface $task)
    {
        $this->getQueueForTask($task)->acknowledge($task);
    }

    /**
     * Count of messages in all queues.
     *
     * @return int
     */
    public function count()
    {
        $counts = array_map(function(QueueInterface $queue) {
            return $this->propagateServices($queue)
                ->count();
        }, $this->getQueues());

        return array_sum($counts);
    }

    /**
     * @inheritdoc
     */
    public function isSync()
    {
        foreach ($this->getQueues() as $queue) {
            if (!$queue->isSync()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return TaskLogInterface
     */
    protected function getTaskLog()
    {
        if (is_null($this->taskLog)) {
            $this->taskLog = $this->getServiceManager()->get($this->getOption(self::OPTION_TASK_LOG));
        }

        return $this->taskLog;
    }

    /**
     * Run worker on-the-fly for one round.
     *
     * @param QueueInterface $queue
     */
    protected function runWorker(QueueInterface $queue)
    {
        (new Worker($this, $this->getTaskLog(), false))
            ->setDedicatedQueue($queue, 1)
            ->run();
    }

    /**
     * @param QueueInterface $queue
     * @return QueueInterface
     */
    protected function propagateServices(QueueInterface $queue)
    {
        $this->getServiceManager()->propagate($queue);

        if ($queue instanceof TaskLogAwareInterface) {
            $queue->setTaskLog($this->getTaskLog());
        }

        return $queue;
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function assertQueues()
    {
        if (!$this->hasOption(self::OPTION_QUEUES) || empty($this->getOption(self::OPTION_QUEUES))) {
            throw new \InvalidArgumentException("Queues needs to be set.");
        }

        if (count($this->getQueues()) === 1) {
            return;
        }

        if (count($this->getQueues()) != count(array_unique($this->getQueues()))) {
            throw new \InvalidArgumentException('There are duplicated Queue names. Please check the values of "'. self::OPTION_QUEUES .'" in your queue dispatcher settings.');
        }
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function assertTasks()
    {
        if (empty($this->getLinkedTasks())) {
            return;
        }

        // check if every task is linked to a registered queue
        $notRegisteredQueues = array_diff(array_values($this->getLinkedTasks()), $this->getQueueNames());

        if (count($notRegisteredQueues)) {
            throw new \LogicException('Found not registered queue(s) linked to task(s): "'. implode('", "', $notRegisteredQueues) .'". Please check the values of "'. self::OPTION_TASK_TO_QUEUE_ASSOCIATIONS .'" in your queue dispatcher settings.');
        }
    }

    /**
     * Get resource from rdf storage which represents task in the task queue by linked resource
     * Returns null if there is no task linked to given resource
     *
     * It will be deprecated once we have the general GUI for displaying different info of a task for the user.
     *
     * @param \core_kernel_classes_Resource $resource
     * @return null|\core_kernel_classes_Resource
     */
    public function getTaskResource(\core_kernel_classes_Resource $resource)
    {
        $tasksRootClass = $this->getClass(Task::TASK_CLASS);
        $taskResources = $tasksRootClass->searchInstances([Task::PROPERTY_LINKED_RESOURCE => $resource->getUri()]);

        return empty($taskResources) ? null : current($taskResources);
    }

    /**
     * It will be deprecated once we have the general GUI for displaying different info of a task for the user.
     *
     * @param \core_kernel_classes_Resource $resource
     * @return Report
     */
    public function getReportByLinkedResource(\core_kernel_classes_Resource $resource)
    {
        $taskResource = $this->getTaskResource($resource);

        if ($taskResource !== null) {
            $report = $taskResource->getOnePropertyValue($this->getProperty(Task::PROPERTY_REPORT));

            if ($report) {
                $report = Report::jsonUnserialize($report->literal);
            } else {
                $status = $this->getTaskLog()->getStatus($taskResource->getUri());
                $msg = __('Task is in \'%s\' state', $status);

                $report = $status == TaskLogInterface::STATUS_FAILED
                    ? Report::createFailure($msg)
                    : Report::createInfo($msg);
            }
        } else {
            $report = Report::createFailure(__('Resource is not the task placeholder'));
        }

        return $report;
    }

    /**
     * Create task resource in the rdf storage and link placeholder resource to it.
     *
     * It will be deprecated once we have the general GUI for displaying different info of a task for the user.
     *
     * @param TaskInterface $task
     * @param \core_kernel_classes_Resource|null $resource - placeholder resource to be linked with task.
     * @return \core_kernel_classes_Resource
     */
    public function linkTask(TaskInterface $task, \core_kernel_classes_Resource $resource = null)
    {
        $taskResource = $this->getResource($task->getId());

        if (!$taskResource->exists()) {
            $tasksRootClass = $this->getClass(Task::TASK_CLASS);
            $taskResource = $tasksRootClass->createInstance('', '', $task->getId());
        }

        if ($resource !== null) {
            $taskResource->setPropertyValue($this->getProperty(Task::PROPERTY_LINKED_RESOURCE), $resource->getUri());
        }

        if ($this->isSync()) {
            $report = $this->getTaskLog()->getReport($task->getId());

            if (!empty($report)) {
                //serialize only two first report levels because sometimes serialized report is huge and it does not fit into `k_po` index of statements table.
                $serializableReport = new Report($report->getType(), $report->getMessage(), $report->getData());

                foreach ($report as $subReport) {
                    $serializableSubReport = new Report($subReport->getType(), $subReport->getMessage(), $subReport->getData());
                    $serializableReport->add($serializableSubReport);
                }

                $taskResource->setPropertyValue($this->getProperty(Task::PROPERTY_REPORT), json_encode($serializableReport));
            }
        }

        return $taskResource;
    }
}