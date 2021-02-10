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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\tao\model\service;

use \core_kernel_classes_Resource;
use oat\generis\model\OntologyAwareTrait;
use oat\generis\model\OntologyRdfs;
use oat\tao\model\WfEngineOntology;
use oat\tao\model\exceptions\UserErrorException;
use JsonSerializable;

/**
 * Represents a call of an interactive tao service
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 */
class ServiceCall implements JsonSerializable
{
    use OntologyAwareTrait;

    /**
     * @var core_kernel_classes_Resource
     */
    
    private $serviceDefinitionId = null;

    /**
     * Input Parameters used to call this service
     *
     * @var array
     */
    private $inParameters = [];
    
    /**
     * Variable parameter to which the outcome of the service is send
     *
     * @var VariableParameter
     */
    private $outParameter = null;
    
    /**
     * Instantiates a new service call
     *
     * @param core_kernel_classes_Resource $serviceDefinition
     */
    public function __construct($serviceDefinition)
    {
        $this->serviceDefinitionId = is_object($serviceDefinition)
           ? $serviceDefinition->getUri()
           : $serviceDefinition;
    }
    
    /**
     * Adds an input parameter
     *
     * @param Parameter $param
     */
    public function addInParameter(Parameter $param)
    {
        $this->inParameters[] = $param;
    }
    
    /**
     * Sets the output parameter, does not except constants
     *
     * @param VariableParameter $param
     */
    public function setOutParameter(VariableParameter $param)
    {
        $this->outParameter = $param;
    }
    
    /**
     * returns the definition of the called service
     *
     * @return core_kernel_classes_Resource
     */
    public function getServiceDefinitionId()
    {
        return $this->serviceDefinitionId;
    }
    
    /**
     * returns the call parameters
     *
     * @return array:
     */
    public function getInParameters()
    {
        return $this->inParameters;
    }

    /**
     * Gets the variables expected to be present to call this service
     *
     * @return array:
     */
    public function getRequiredVariables()
    {
        $variables = [];
        foreach ($this->inParameters as $param) {
            if ($param instanceof VariableParameter) {
                $variables[] = $param->getVariable();
            }
        }
        return $variables;
    }
    
    /**
     * Stores a service call in the ontology
     *
     * @return core_kernel_classes_Resource
     */
    public function toOntology()
    {
        $inResources = [];
        $outResources = is_null($this->outParameter)
           ? []
           : $this->outParameter->toOntology($this->getModel());
        foreach ($this->inParameters as $param) {
            $inResources[] = $param->toOntology($this->getModel());
        }
        $serviceCallClass = $this->getClass(WfEngineOntology::CLASS_URI_CALL_OF_SERVICES);
        $resource = $serviceCallClass->createInstanceWithProperties([
            OntologyRdfs::RDFS_LABEL => 'serviceCall',
            WfEngineOntology::PROPERTY_CALL_OF_SERVICES_SERVICE_DEFINITION    => $this->serviceDefinitionId,
            WfEngineOntology::PROPERTY_CALL_OF_SERVICES_ACTUAL_PARAMETER_IN    => $inResources,
            WfEngineOntology::PROPERTY_CALL_OF_SERVICES_ACTUAL_PARAMETER_OUT   => $outResources,
            WfEngineOntology::PROPERTY_CALL_OF_SERVICES_WIDTH                => '100',
            WfEngineOntology::PROPERTY_CALL_OF_SERVICES_HEIGHT               => '100'
        ]);
             
        return $resource;
    }
    
    /**
     * Builds a service call from it's serialized form
     *
     * @param core_kernel_classes_Resource $resource
     * @return ServiceCall
     */
    public static function fromResource(core_kernel_classes_Resource $resource)
    {
        $values = $resource->getPropertiesValues([
            WfEngineOntology::PROPERTY_CALL_OF_SERVICES_SERVICE_DEFINITION,
            WfEngineOntology::PROPERTY_CALL_OF_SERVICES_ACTUAL_PARAMETER_IN,
            WfEngineOntology::PROPERTY_CALL_OF_SERVICES_ACTUAL_PARAMETER_OUT
        ]);
        $serviceDefUri = current($values[WfEngineOntology::PROPERTY_CALL_OF_SERVICES_SERVICE_DEFINITION]);
        $serviceCall = new self($serviceDefUri);
        foreach ($values[WfEngineOntology::PROPERTY_CALL_OF_SERVICES_ACTUAL_PARAMETER_IN] as $inRes) {
            $param = Parameter::fromResource($inRes);
            $serviceCall->addInParameter($param);
        }
        if (!empty($values[WfEngineOntology::PROPERTY_CALL_OF_SERVICES_ACTUAL_PARAMETER_OUT])) {
            $param = Parameter::fromResource(current($values[WfEngineOntology::PROPERTY_CALL_OF_SERVICES_ACTUAL_PARAMETER_OUT]));
            $serviceCall->setOutParameter($param);
        }
        return $serviceCall;
    }
    
    /**
     * Serialize the current serivceCall object to a string
     *
     * @return string
     *
     * @deprecated Use json_encode($serviceCall) instead
     */
    public function serializeToString()
    {
        return json_encode($this);
    }

    /**
     * Unserialize the string to a serivceCall object
     *
     * @param string $string
     * @return ServiceCall
     * @throws \InvalidArgumentException
     */
    public static function fromString($string)
    {
        $data = json_decode($string, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException("Provided string is not a valid JSON.");
        }

        return self::fromJson($data);
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'service' => $this->serviceDefinitionId,
            'in' => $this->inParameters,
            'out' => $this->outParameter
        ];
    }

    /**
     * @param array $data
     * @return ServiceCall
     */
    public static function fromJson(array $data)
    {
        $call = new self($data['service']);
        if (!empty($data['out'])) {
            $call->setOutParameter(Parameter::fromJson($data['out']));
        }
        foreach ($data['in'] as $in) {
            $call->addInParameter(Parameter::fromJson($in));
        }
        return $call;
    }
}
