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
 * Copyright (c) 2019 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\taoReview\models;

use common_Exception;
use oat\generis\model\OntologyAwareTrait;
use oat\taoDeliveryRdf\model\DeliveryContainerService;
use oat\taoOutcomeUi\helper\ResponseVariableFormatter;
use oat\taoOutcomeUi\model\ResultsService;
use oat\taoOutcomeUi\model\Wrapper\ResultServiceWrapper;
use oat\taoProctoring\model\execution\DeliveryExecutionManagerService;
use oat\taoQtiTest\models\runner\QtiRunnerService;
use oat\taoQtiTest\models\runner\QtiRunnerServiceContext;
use oat\taoResultServer\models\classes\ResultServerService;
use qtism\data\AssessmentSection;
use qtism\data\AssessmentSectionRef;
use qtism\data\TestPart;
use taoQtiTest_helpers_Utils;
use taoResultServer_models_classes_OutcomeVariable;
use taoResultServer_models_classes_ResponseVariable;

class QtiRunnerInitDataBuilder
{
    use OntologyAwareTrait;

    /** @var DeliveryContainerService */
    private $deliveryContainerService;

    /** @var QtiRunnerService */
    private $qtiRunnerService;

    /** @var DeliveryExecutionManagerService */
    private $deliveryExecutionService;

    /** @var ResultsService */
    private $resultService;

    /** @var ResultServerService */
    private $resultServerService;

    private $itemsData = [];

    public function __construct(
        DeliveryContainerService $deliveryContainerService,
        QtiRunnerService $qtiRunnerService,
        DeliveryExecutionManagerService $deliveryExecutionService,
        ResultServiceWrapper $resultService,
        ResultServerService $resultServerService
    ) {
        $this->deliveryContainerService = $deliveryContainerService;
        $this->qtiRunnerService = $qtiRunnerService;
        $this->deliveryExecutionService = $deliveryExecutionService;
        $this->resultService = $resultService->getService();
        $this->resultServerService = $resultServerService;
    }

    /**
     * @param string $deliveryExecutionId
     *
     * @return array
     * @throws common_Exception
     */
    public function build($deliveryExecutionId)
    {
        $serviceContext = $this->getServiceContext($deliveryExecutionId);

        $itemsData = $this->getItemsData($deliveryExecutionId);

        $testMap = $this->getTestMap($serviceContext, $itemsData);

        $firstItem = array_shift($this->itemsData);

        $init = [
            'testMap' => $testMap,
            'testContext' => array_merge($this->qtiRunnerService->getTestContext($serviceContext), [
                'itemIdentifier' => $firstItem['itemId'],
                'itemPosition' => 0
            ]),
            'testData' => $this->qtiRunnerService->getTestData($serviceContext),
            'testResponses' => $itemsData,
            'success' => true,
        ];

        return $init;
    }

    protected function getItemsData(string $deliveryExecutionId)
    {
        $deliveryExecution = $this->deliveryExecutionService->getDeliveryExecutionById($deliveryExecutionId);
        $delivery = $deliveryExecution->getDelivery();

        $filterSubmission = ResultsService::VARIABLES_FILTER_LAST_SUBMITTED;
        $filterTypes = [
            taoResultServer_models_classes_ResponseVariable::class,
            taoResultServer_models_classes_OutcomeVariable::class
        ];

        $implementation = $this->resultServerService->getResultStorage($delivery->getUri());
        $this->resultService->setImplementation($implementation);

        $variables = $this->getResultVariables($deliveryExecution->getIdentifier(), $filterSubmission, $filterTypes);

        $returnValue = [];

        foreach ($variables as $variable) {

            $state = json_decode($variable['state'], true);
            $stateKeys = array_keys($state);

            $scores = array_filter($variable[taoResultServer_models_classes_ResponseVariable::class], function ($key) use ($stateKeys) {
                return in_array($key, $stateKeys, true);
            }, ARRAY_FILTER_USE_KEY);

            array_walk($scores, function (&$value) {
                $value = $value['isCorrect'] === 'correct';
            });

            $returnValue[$variable['internalIdentifier']] = [
                'state' => $state,
                'score' => $scores
            ];
        }

        return $returnValue;
    }

    protected function getTestMap(QtiRunnerServiceContext $context, array $itemsStates)
    {
        $testDefinition = taoQtiTest_helpers_Utils::getTestDefinition($context->getTestCompilationUri());

        $map = [
            'scope' => 'test',
            'parts' => [],
            'stats' => [],
            'jumps' => [],
        ];

        $position = 0;
        foreach ($testDefinition->getTestParts() as $testPart) {
            /** @var TestPart $testPart */
            $sections = [];
            foreach ($testPart->getAssessmentSections() as $section) {
                /** @var AssessmentSection $section */

                $items = [];
                foreach ($section->getSectionParts() as $item) {
                    /** @var AssessmentSectionRef $item */
                    $itemData = $this->qtiRunnerService->getItemData($context, $item->getHref());

                    $itemId = $item->getIdentifier();
                    $score = $itemsStates[$itemId]['score'] ?: [];

                    $items[$itemId] = [
                        'id' => $itemId,
                        'label' => $itemData['data']['attributes']['label'],
                        'position' => $position,
                        'categories' => [],
                        'informational' => false,
                        'skipped' => false,
                        'score' => array_sum($score),
                        'maxScore' => count($score)
                    ];

                    $this->fillItemsData($itemId, $item->getHref(), $itemData['data']);
                    $position++;
                }

                $sectionId = $section->getIdentifier();
                $sections[$sectionId] = [
                    'id' => $sectionId,
                    'label' => $section->getTitle(),
                    'items' => $items,
                    'stats' => []
                ];
            }

            $testPartId = $testPart->getIdentifier();
            $map['parts'][$testPartId] = [
                'id' => $testPartId,
                'label' => $testPart->getIdentifier(),
                'sections' => $sections,
                'stats' => []
            ];
        }

        return $map;
    }

    private function fillItemsData($itemId, $itemRef, $itemData)
    {
        $this->itemsData[] = [
            'itemId' => $itemId,
            'itemRef' => $itemRef,
            'itemData' => $itemData,
        ];
    }

    protected function getResultVariables($resultId, $filterSubmission, $filterTypes = array())
    {
        $variables = $this->resultService->getStructuredVariables($resultId, $filterSubmission, array_merge($filterTypes, [\taoResultServer_models_classes_ResponseVariable::class]));
        $displayedVariables = $this->resultService->filterStructuredVariables($variables, $filterTypes);

        $responses = ResponseVariableFormatter::formatStructuredVariablesToItemState($variables);
        $excludedVariables = array_flip(['numAttempts', 'duration']);

        foreach ($displayedVariables as &$item) {
            if (!isset($item['uri'])) {
                continue;
            }
            $itemUri = $item['uri'];
            $item['state'] = isset($responses[$itemUri][$item['attempt']])
                ? json_encode(array_diff_key($responses[$itemUri][$item['attempt']], $excludedVariables))
                : null;
        }

        return $displayedVariables;
    }

    /**
     * @param string $deliveryExecutionId
     * @return QtiRunnerServiceContext
     * @throws common_Exception
     */
    private function getServiceContext($deliveryExecutionId): QtiRunnerServiceContext
    {
        $deliveryExecution = $this->deliveryExecutionService->getDeliveryExecutionById($deliveryExecutionId);

        $compilation = $this->deliveryContainerService->getTestCompilation($deliveryExecution);
        $testId = $this->deliveryContainerService->getTestDefinition($deliveryExecution);

        return $this->qtiRunnerService->getServiceContext($testId, $compilation, $deliveryExecutionId);
    }
}
