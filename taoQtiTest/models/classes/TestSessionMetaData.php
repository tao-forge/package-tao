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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\taoQtiTest\models;

use DateTime;
use qtism\common\datatypes\Duration;
use qtism\data\AssessmentItemRef;
use qtism\data\ExtendedAssessmentItemRef;
use qtism\runtime\tests\AssessmentTestSession;
use qtism\common\enums\Cardinality;
use Context;
use taoResultServer_models_classes_TraceVariable;

/**
 * Class manages test session metadata such as section or test exit codes and other.
 * 
 * Data will be stored as trace variable {@link \taoResultServer_models_classes_TraceVariable}.
 * 
 * Section level data stored as test variable {@link \taoResultServer_models_classes_ResultServerStateFull::storeTestVariable()}
 * prefixed with session identifier e.g. <i>SECTION_EXIT_CODE</i> will be stored as <i>SECTION_EXIT_CODE_assessmentSection-1</i>
 * 
 * 
 * Usage example:
 * <pre>
 * $sessionMetaData = new TestSessionMetaData($session);
 * $metaData = array(
 *   //Test level metadata
 *   'TEST' => array( 
 *      'TEST_EXIT_CODE' => TestSessionMetaData::TEST_CODE_COMPLETE,
 *   ),
 *   //Section level metadata
 *   'SECTION' => array(
 *      'SECTION_EXIT_CODE' => TestSessionMetaData::SECTION_CODE_COMPLETED_NORMALLY,
 *   ),
 *   //Item level metadata
 *   'ITEM' => array( //save item level metadata
 *      'ITEM_META_DATA' => 'value',
 *   ),
 * )
 * $sessionMetaData->save($metaData);
 * </pre>
 * 
 * @author Aleh Hutnikau <hutnikau@1pt.com>
 *
 */
class TestSessionMetaData
{
    const SECTION_CODE_COMPLETED_NORMALLY = 700;
    const SECTION_CODE_QUIT = 701;
    const SECTION_CODE_COMPLETE_TIMEOUT = 703;
    const SECTION_CODE_TIMEOUT = 704;
    const SECTION_CODE_FORCE_QUIT = 705;
    const SECTION_CODE_IN_PROGRESS = 706;
    const SECTION_CODE_ERROR = 300;
    
    const TEST_CODE_COMPLETE = 'C';
    const TEST_CODE_TERMINATED = 'T';
    const TEST_CODE_INCOMPLETE = 'IC';
    const TEST_CODE_INCOMPLETE_QUIT = 'IQ';
    const TEST_CODE_INACTIVE = 'IA';
    const TEST_CODE_DISAGREED_WITH_NDA = 'DA';

    /**
     * Test session instance
     * @var AssessmentTestSession 
     */
    private $session;

    /**
     * Constructor.
     * @param \taoQtiTest_helpers_TestSession $session Test session instance.
     */
    public function __construct(\taoQtiTest_helpers_TestSession $session) {
       $this->session = $session;
    }
        
    /**
     * Save session metadata.
     * 
     * @param array $metaData Meta data array to be saved.
     * Example:
     * array(
     *   'TEST' => array('TEST_EXIT_CODE' => 'IC'),
     *   'SECTION' => array('SECTION_EXIT_CODE' => 701),
     * )
     */
    public function save(array $metaData)
    {
        $testUri = $this->session->getTest()->getUri();
        $resultServer = \taoResultServer_models_classes_ResultServerStateFull::singleton();

        foreach ($metaData as $type => $data) {
            foreach ($data as $key => $value) {
                $metaVariable = $this->getVariable($key, $value);

                if (strcasecmp($type, 'ITEM') === 0) {
                    $itemUri = \taoQtiTest_helpers_TestRunnerUtils::getCurrentItemUri($this->session);
                    $itemRef = $this->session->getCurrentAssessmentItemRef();
                    $occurence = $this->session->getCurrentAssessmentItemRefOccurence();
                    $sessionId = $this->session->getSessionId();
                    
                    $transmissionId = "${sessionId}.${itemRef}.${occurence}";
                    $resultServer->storeItemVariable($testUri, $itemUri, $metaVariable, $transmissionId);
                } elseif (strcasecmp($type, 'TEST') === 0) {
                    $resultServer->storeTestVariable($testUri, $metaVariable, $this->session->getSessionId());
                } elseif (strcasecmp($type, 'SECTION') === 0) {
                    //suffix section variables with _{SECTION_IDENTIFIER}
                    $assessmentSectionId = $this->session->getCurrentAssessmentSection()->getIdentifier();
                    $metaVariable->setIdentifier($key . '_' . $assessmentSectionId);
                    $resultServer->storeTestVariable($testUri, $metaVariable, $this->session->getSessionId());
                }
            }
        }
    }
    
    /**
     * Register callbacks for all item sessions
     */
    public function registerItemCallbacks() 
    {
        $sessionItems = $this->session->getItemSubset();
        foreach($sessionItems as $assesmentItem) {
            $itemSessions = $this->session->getAssessmentItemSessions($assesmentItem->getIdentifier());
            if (!empty($itemSessions)) {
                foreach ($itemSessions as $itemSession) {
                    $itemSession->registerCallback(
                        'suspend',
                        function ($item, $testSessionMetaData) {
                            $time = microtime(true);
                            /** @var $testSessionMetaData TestSessionMetaData */
                            $session  = $testSessionMetaData->getTestSession();
                            $section  = $session->getCurrentAssessmentSection();

                            $testPart = $session->getCurrentTestPart();
                            $test     = $session->getAssessmentTest();

                            $testMaxTimeAllowed     = $test->hasTimeLimits() ? $test->getTimeLimits()->getMaxTime() : null;
                            $testPartMaxTimeAllowed = $testPart->hasTimeLimits() ? $testPart->getTimeLimits()->getMaxTime() : null;
                            $sectionMaxTimeAllowed  = $section->hasTimeLimits() ? $section->getTimeLimits()->getMaxTime() : null;
                            $itemMaxTimeAllowed     = $item->hasTimeLimits() ? $item->getTimeLimits()->getMaxTime() : null;

                            $timeLimits = array_filter(array(
                                $testMaxTimeAllowed,
                                $testPartMaxTimeAllowed,
                                $sectionMaxTimeAllowed,
                                $itemMaxTimeAllowed
                            ));

                            usort($timeLimits, function ($a, $b) {
                                /** @var $a Duration */
                                if ($a->equals($b)) {
                                    return 0;
                                };

                                return $a->longerThanOrEquals($b) ? 1 : - 1;

                            });

                            $startTimeSection = $testSessionMetaData->getStartSectionTime();//start time of first item in section

                            if (isset( $timeLimits[count($timeLimits) - 1] )) {
                                $maxAllowedTime = $timeLimits[count($timeLimits) - 1];//actually current limit for answering

                                $latestPossibleSectionTime = $startTimeSection->add(new DateInterval('PT' . $maxAllowedTime->getSeconds(true) . 'S'));

                                if ($time > $latestPossibleSectionTime->getTimestamp()) {
                                    $currentItemRef = $this->getTestSession()->getCurrentAssessmentItemRef();
                                    if ($itemMaxTimeAllowed) {
                                        $time = $testSessionMetaData->getItemStartTime($currentItemRef)->add(new DateInterval('PT' . $itemMaxTimeAllowed->getSeconds(true) . 'S'))->getTimestamp();
                                    } else {
                                        $time = $latestPossibleSectionTime->getTimestamp();
                                    }
                                }
                            }

                            $testSessionMetaData->save(
                                array('ITEM' => array('ITEM_END_TIME_SERVER' => $time))
                            );
                        },
                        array($this)
                    );
                    $itemSession->registerCallback(
                        'interact',
                        function ($item, $testSessionMetaData) {
                            $testSessionMetaData->save(
                                array('ITEM' => array('ITEM_START_TIME_SERVER' => microtime(true)))
                            );
                        },
                        array($this)
                    );
                    $itemSession->registerCallback(
                        'beginAttempt',
                        function ($item, $testSessionMetaData) {
                            $testSessionMetaData->save(
                                array('ITEM' => array('ITEM_START_TIME_SERVER' => microtime(true)))
                            );
                        },
                        array($this)
                    );
                }
            }
        }
    }
    
    /**
     * Get current test session meta data array
     *
     * @return array test session meta data.
     */
    public function getData()
    {
        $request = Context::getInstance()->getRequest();
        $data = $request->hasParameter('metaData') ? $request->getParameter('metaData') : array();
        $action = Context::getInstance()->getActionName();
        $route = $this->getTestSession()->getRoute();

        if (in_array($action, array('index'))) {
            $data['TEST']['TAO_VERSION'] = TAO_VERSION;
        }

        if (in_array($action, array('moveForward', 'skip'))) {
            if (!isset($data['SECTION']['SECTION_EXIT_CODE'])) {
                $currentSection = $this->getTestSession()->getCurrentAssessmentSection();
                $timeOut = \taoQtiTest_helpers_TestRunnerUtils::isTimeout($this->getTestSession());
                $lastInSection = $route->isLast() ||
                    ($route->getNext()->getAssessmentSection()->getIdentifier() !== $currentSection->getIdentifier());

                if ($lastInSection && $timeOut) {
                    $data['SECTION']['SECTION_EXIT_CODE'] = self::SECTION_CODE_COMPLETE_TIMEOUT;
                } elseif ($timeOut) {
                    $data['SECTION']['SECTION_EXIT_CODE'] = self::SECTION_CODE_TIMEOUT;
                } elseif ($lastInSection) {
                    $data['SECTION']['SECTION_EXIT_CODE'] = self::SECTION_CODE_COMPLETED_NORMALLY;
                }
            }

            if ($route->isLast()) {
                $data['TEST']['TEST_EXIT_CODE'] = self::TEST_CODE_COMPLETE;
            }
        }

        return $data;
    }

    /**
     * Get trace variable instance to save.
     *
     * @param string $identifier
     * @param string $value
     * @return taoResultServer_models_classes_TraceVariable variable instance to save.
     */
    private function getVariable($identifier, $value)
    {
        $metaVariable = new taoResultServer_models_classes_TraceVariable();
        $metaVariable->setIdentifier($identifier);
        $metaVariable->setBaseType('string');
        $metaVariable->setCardinality(Cardinality::getNameByConstant(Cardinality::SINGLE));
        $metaVariable->setTrace($value);

        return $metaVariable;
    }

    /**
     * Get test session instance
     * @return AssessmentTestSession|\taoQtiTest_helpers_TestSession
     */
    public function getTestSession()
    {
        return $this->session;
    }

    /**
     * Retrieve information about passed items
     * @return DateTime
     * @throws \common_exception_Error
     */
    public function getStartSectionTime()
    {
        $itemResults        = array();
        $assessmentItemsRef = $this->getTestSession()->getCurrentAssessmentSection()->getComponentsByClassName('assessmentItemRef');

        /** @var ExtendedAssessmentItemRef $itemRef */
        foreach ($assessmentItemsRef as $itemRef) {
            $itemResults[] = $this->getItemStartTime($itemRef);
        }
        $sectionStart = min(array_filter($itemResults));

        return $sectionStart;
    }

    /**
     * @param AssessmentItemRef $itemRef
     *
     * @return DateTime
     */
    public function getItemStartTime($itemRef)
    {
        $itemResults   = array();
        $itemStartTime = null;

        $ssid         = $this->getTestSession()->getSessionId();
        $resultServer = \taoResultServer_models_classes_ResultServerStateFull::singleton();
        $collection   = $resultServer->getVariables("{$ssid}.{$itemRef->getIdentifier()}.{$this->getTestSession()->getCurrentAssessmentItemRefOccurence()}");

        foreach ($collection as $vars) {
            foreach ($vars as $var) {
                if ($var->variable instanceof taoResultServer_models_classes_TraceVariable && $var->variable->getIdentifier() === 'ITEM_START_TIME_SERVER') {
                    $itemResults[] = $var->variable->getValue();
                }
            }
        }

        $itemResults = array_map(function ($ts) {
            $itemStart = (new DateTime('now', new DateTimeZone('UTC')));
            $itemStart->setTimestamp($ts);

            return $itemStart;
        }, $itemResults);

        if ( ! empty( $itemResults )) {
            $itemStartTime = min($itemResults);
        }

        return $itemStartTime;
    }
}