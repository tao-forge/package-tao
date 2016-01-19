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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\taoProctoring\model\implementation;

use oat\oatbox\service\ConfigurableService;
use oat\taoProctoring\model\TestSessionService as TestSessionServiceInterface;
use oat\taoDelivery\models\classes\execution\DeliveryExecution;
use oat\taoDelivery\model\AssignmentService;
use qtism\runtime\storage\binary\BinaryAssessmentTestSeeker;
use oat\taoQtiTest\models\TestSessionMetaData;
use qtism\runtime\tests\AssessmentTestSession;

/**
 * Interface TestSessionService
 * @package oat\taoProctoring\model
 * @author Aleh Hutnikau <hutnikau@1pt.com>
 */
class TestSessionService extends ConfigurableService implements TestSessionServiceInterface
{
    /** @var array cache to store session instances */
    private $cache = [];

    /**
     * Gets the test session for a particular deliveryExecution
     *
     * @param DeliveryExecution $deliveryExecution
     * @return \qtism\runtime\tests\AssessmentTestSession
     * @throws \common_exception_Error
     * @throws \common_exception_MissingParameter
     */
    public function getTestSession(DeliveryExecution $deliveryExecution)
    {
        if (!isset($this->cache[$deliveryExecution->getIdentifier()])) {
            $resultServer = \taoResultServer_models_classes_ResultServerStateFull::singleton();

            $compiledDelivery = $deliveryExecution->getDelivery();
            $inputParameters = $this->getRuntimeInputParameters($deliveryExecution);

            $testDefinition = \taoQtiTest_helpers_Utils::getTestDefinition($inputParameters['QtiTestCompilation']);
            $testResource = new \core_kernel_classes_Resource($inputParameters['QtiTestDefinition']);

            $sessionManager = new \taoQtiTest_helpers_SessionManager($resultServer, $testResource);

            $qtiStorage = new \taoQtiTest_helpers_TestSessionStorage(
                $sessionManager,
                new BinaryAssessmentTestSeeker($testDefinition), $deliveryExecution->getUserIdentifier()
            );

            $sessionId = $deliveryExecution->getIdentifier();

            if ($qtiStorage->exists($sessionId)) {
                $session = $qtiStorage->retrieve($testDefinition, $sessionId);

                $resultServerUri = $compiledDelivery->getOnePropertyValue(new \core_kernel_classes_Property(TAO_DELIVERY_RESULTSERVER_PROP));
                $resultServerObject = new \taoResultServer_models_classes_ResultServer($resultServerUri, array());
                $resultServer->setValue('resultServerUri', $resultServerUri->getUri());
                $resultServer->setValue('resultServerObject', array($resultServerUri->getUri() => $resultServerObject));
                $resultServer->setValue('resultServer_deliveryResultIdentifier', $deliveryExecution->getIdentifier());
            } else {
                $session = null;
            }

            $this->cache[$deliveryExecution->getIdentifier()] = [
                'session' => $session,
                'storage' => $qtiStorage
            ];
        }

        return $this->cache[$deliveryExecution->getIdentifier()]['session'];
    }

    /**
     *
     * @param DeliveryExecution $deliveryExecution
     * @return array
     * Example:
     * <pre>
     * array(
     *   'QtiTestCompilation' => 'http://sample/first.rdf#i14369768868163155-|http://sample/first.rdf#i1436976886612156+',
     *   'QtiTestDefinition' => 'http://sample/first.rdf#i14369752345581135'
     * )
     * </pre>
     */
    public function getRuntimeInputParameters(DeliveryExecution $deliveryExecution)
    {
        $compiledDelivery = $deliveryExecution->getDelivery();
        $runtime = $this->getServiceManager()->get(AssignmentService::CONFIG_ID)->getRuntime($compiledDelivery->getUri());
        $inputParameters = \tao_models_classes_service_ServiceCallHelper::getInputValues($runtime, array());

        return $inputParameters;
    }

    /**
     * Sets a test variable with name automatic suffix
     * @param AssessmentTestSession $session
     * @param string $name
     * @param mixe $value
     */
    public function setTestVariable(AssessmentTestSession $session, $name, $value)
    {
        $testSessionMetaData = new TestSessionMetaData($session);
        $testSessionMetaData->save(array(
            'TEST' => array(
                $this->nameTestVariable($session, $name) => $this->encodeTestVariable($value)
            )
        ));
    }

    /**
     * @param AssessmentTestSession $session
     */
    public function persist(AssessmentTestSession $session)
    {
        $sessionId = $session->getSessionId();
        $storage = $this->cache[$sessionId]['storage'];
        $storage->persist($session);
    }

    /**
     * Build a variable name based on the current position inside the test
     * @param AssessmentTestSession $session
     * @param string $name
     * @return string
     */
    private function nameTestVariable(AssessmentTestSession $session, $name)
    {
        $varName = array($name);
        if ($session) {
            $varName[] = $session->getCurrentAssessmentItemRef();
            $varName[] = $session->getCurrentAssessmentItemRefOccurence();
            $varName[] = time();
        }
        return implode('.', $varName);
    }

    /**
     * Encodes a test variable
     * @param mixed $value
     * @return string
     */
    private function encodeTestVariable($value)
    {
        return json_encode(array(
            'timestamp' => microtime(),
            'details' => $value
        ));
    }
}