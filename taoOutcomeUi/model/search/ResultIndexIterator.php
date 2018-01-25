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
 * Copyright (c) 2018 (original work) Open Assessment Technologies SA;
 */
namespace oat\taoOutcomeUi\model\search;

use oat\oatbox\service\ServiceManager;
use oat\tao\model\search\index\IndexDocument;
use oat\tao\model\search\SearchTokenGenerator;
use oat\taoDelivery\model\execution\ServiceProxy;
use oat\taoDelivery\model\execution\DeliveryExecution;
use oat\taoResultServer\models\classes\ResultServerService;
use oat\taoResultServer\models\classes\ResultService;

class ResultIndexIterator implements \Iterator
{
    const CACHE_SIZE = 100;

    private $resourceIterator;

    /** @var ResultServerService  */
    private $resultService;

    /**
     * Id of the current instance
     *
     * @var int
     */
    private $currentInstance = 0;

    /**
     * List of resource uris currently being iterated over
     *
     * @var array
     */
    private $instanceCache = null;

    /**
     * Indicater whenever the end of  the current cache is also the end of the current class
     *
     * @var boolean
     */
    private $endOfResource = false;

    private $tokenGenerator = null;

    /**
     * Whenever we already moved the pointer, used to prevent unnecessary rewinds
     *
     * @var boolean
     */
    private $unmoved = true;

    /**
     * Constructor of the iterator expecting a class or classes as argument
     *
     * @param mixed $classes array/instance of class(es) to iterate over
     */
    public function __construct($classes) {
        $this->resourceIterator = new \core_kernel_classes_ResourceIterator($classes);
        $this->tokenGenerator = new SearchTokenGenerator();
        /** @var ResultServerService $resultService */
        $this->resultService = ServiceManager::getServiceManager()->get(ResultServerService::SERVICE_ID);

        $this->ensureNotEmpty();
    }

    /**
     * (non-PHPdoc)
     * @see Iterator::rewind()
     */
    function rewind() {
        if (!$this->unmoved) {
            $this->resourceIterator->rewind();
            $this->ensureNotEmpty();
            $this->unmoved = true;
        }
    }

    /**
     * (non-PHPdoc)
     * @see Iterator::current()
     */
    function current() {
        $deliveryExecution = ServiceProxy::singleton()->getDeliveryExecution($this->instanceCache[$this->currentInstance]);
        return $this->createDocument($deliveryExecution);
    }

    /**
     * (non-PHPdoc)
     * @see Iterator::key()
     */
    function key() {
        return $this->resourceIterator->key().'#'.$this->currentInstance;
    }

    /**
     * (non-PHPdoc)
     * @see Iterator::next()
     */
    function next() {
        $this->unmoved = false;
        if ($this->valid()) {
            $this->currentInstance++;
            if (!isset($this->instanceCache[$this->currentInstance])) {
                // try to load next block (unless we know it's empty)
                $remainingInstances = !$this->endOfResource && $this->load($this->resourceIterator->current(), $this->currentInstance);

                // endOfClass or failed loading
                if (!$remainingInstances) {
                    $this->resourceIterator->next();
                    $this->ensureNotEmpty();
                }
            }
        }
    }

    /**
     * While there are remaining classes there are instances to load
     *
     * (non-PHPdoc)
     * @see Iterator::valid()
     */
    function valid() {
        return $this->resourceIterator->valid();
    }

    // Helpers

    /**
     * Ensure the class iterator is pointin to a non empty class
     * Loads the first resource block to test this
     */
    protected function ensureNotEmpty() {
        $this->currentInstance = 0;
        while ($this->resourceIterator->valid() && !$this->load($this->resourceIterator->current(), 0)) {
            $this->resourceIterator->next();
        }
    }

    /**
     * @param \core_kernel_classes_Resource $delivery
     * @param $offset
     * @return bool
     * @throws \common_exception_Error
     */
    protected function load(\core_kernel_classes_Resource $delivery, $offset) {

        $options = array(
            'recursive' => true,
            'offset' => $offset,
            'limit' => self::CACHE_SIZE
        );

        $resultsImplementation = $this->resultService->getResultStorage($delivery->getUri());
        $this->instanceCache = array();
        $results = $resultsImplementation->getResultByDelivery([$delivery->getUri()], $options);
        foreach($results as $result){
            $id = isset($result['deliveryResultIdentifier']) ? $result['deliveryResultIdentifier'] : null;
            if ($id) {
                $this->instanceCache[$offset] = $id;
                $offset++;
            }
        }

        $this->endOfResource = count($results) < self::CACHE_SIZE;

        return count($results) > 0;
    }

    /**
     * @param DeliveryExecution $execution
     * @return IndexDocument
     * @throws \common_exception_NotFound
     */
    protected function createDocument(DeliveryExecution $execution) {
        $body = [
            'label' => $execution->getLabel(),
            'delivery' => $execution->getDelivery()->getUri()
        ];
        $document = new IndexDocument(
            $execution->getIdentifier(),
            $execution->getIdentifier(),
            ResultService::DELIVERY_RESULT_CLASS_URI,
            $body
        );
        return $document;
    }
}