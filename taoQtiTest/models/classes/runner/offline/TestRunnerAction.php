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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA ;
 */

namespace oat\taoQtiTest\models\runner\offline;

use oat\taoQtiTest\models\runner\QtiRunnerClosedException;
use oat\taoQtiTest\models\runner\QtiRunnerMessageService;
use oat\taoQtiTest\models\runner\QtiRunnerPausedException;
use oat\taoQtiTest\models\runner\RunnerParamParserTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

abstract class TestRunnerAction implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;
    use RunnerParamParserTrait;

    protected $name;
    protected $timestamp;
    protected $start;

    protected $parameters;

    abstract public function process();

    public function __construct($name, $timestamp, array $parameters = [])
    {
        $this->name = $name;
        $this->timestamp = $timestamp;
        $this->parameters = $parameters;
    }

    public function hasRequestParameter($name)
    {
        return isset($this->parameters[$name]);
    }

    public function getRequestParameter($name)
    {
        return $this->hasRequestParameter($name) ? $this->parameters[$name] : false;
    }

    /**
     * @return mixed
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @param mixed $start
     */
    public function setStart($start)
    {
        $this->start = $start;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    public function getRequiredFields()
    {
        return ['testDefinition', 'testCompilation', 'serviceCallId'];
    }

    public function validate()
    {
        $requiredFields = array_unique($this->getRequiredFields());
        $isValid = ($requiredFields == array_unique(array_intersect($requiredFields, array_keys($this->parameters))));
        if (!$isValid) {
            throw new \common_exception_InconsistentData('Some parameters are missing. Required parameters are : ' . implode(', ', $requiredFields));
        }
    }

    /**
     * Gets an error response object
     *
     * @param Exception [$e] Optional exception from which extract the error context
     * @return array
     */
    protected function getErrorResponse($e = null) {
        $response = [
            'success' => false,
            'type' => 'error',
        ];

        if ($e) {
            if ($e instanceof \Exception) {
                $response['type'] = 'exception';
                $response['code'] = $e->getCode();
            }

            if ($e instanceof \common_exception_UserReadableException) {
                $response['message'] = $e->getUserMessage();
            } else {
                $response['message'] = __('An error occurred!');
            }

            switch (true) {
                case $e instanceof QtiRunnerClosedException:
                case $e instanceof QtiRunnerPausedException:
                    if ($this->serviceContext) {
                        $messageService = $this->getServiceLocator()->get(QtiRunnerMessageService::SERVICE_ID);
                        $response['message'] = $messageService->getStateMessage($this->getServiceContext()->getTestSession());
                    }
                    $response['type'] = 'TestState';
                    break;

                case $e instanceof \tao_models_classes_FileNotFoundException:
                    $response['type'] = 'FileNotFound';
                    $response['message'] = __('File not found');
                    break;

                case $e instanceof \common_exception_Unauthorized:
                    $response['code'] = 403;
                    break;
            }
        }

        return $response;
    }
}