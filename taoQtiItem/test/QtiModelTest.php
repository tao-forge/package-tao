<?php
/*
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
 *
 */

use oat\taoQtiItem\model\qti\Service;
use oat\taoQtiItem\model\qti\Item;
use oat\taoQtiItem\model\qti\interaction\ChoiceInteraction;
use oat\taoQtiItem\model\qti\ResponseDeclaration;
use oat\taoQtiItem\model\qti\response\Template;
use oat\taoQtiItem\model\qti\OutcomeDeclaration;
use oat\taoQtiItem\model\qti\feedback\ModalFeedback;
use oat\taoQtiItem\model\qti\response\SimpleFeedbackRule;
use oat\taoQtiItem\model\qti\Parser;
use oat\taoQtiItem\model\qti\interaction\MatchInteraction;

require_once dirname(__FILE__).'/../../tao/test/TaoPhpUnitTestRunner.php';
include_once dirname(__FILE__).'/../includes/raw_start.php';

/**
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoQTI
 
 */
class QtiModelTest extends TaoPhpUnitTestRunner
{

    protected $qtiService;

    /**
     * tests initialization
     * load qti service
     */
    public function setUp(){
        TaoPhpUnitTestRunner::initTest();
        $this->qtiService = Service::singleton();
    }

    public function testModel(){

        $myItem = new Item();
        $myItem->setAttribute('title', 'My Coolest Item');
        $myItem->getBody()->edit('sth');

        $myInteraction = new ChoiceInteraction();
        $myInteraction->getPrompt()->edit('Prompt you');
        $myChoice1 = $myInteraction->createChoice(array('fixed' => true), 'This is correct');
        $myChoice2 = $myInteraction->createChoice(array('fixed' => true), 'This is not correct');
        $this->assertIsA($myChoice2, 'oat\\taoQtiItem\\model\\qti\\choice\\SimpleChoice');
        $this->assertEquals(count($myInteraction->getChoices()), 2);
        $myChoice1->setContent('answer #1');
        $myChoice2->setContent('answer #2');

        $myInteraction->removeChoice($myChoice1);
        $this->assertEquals(count($myInteraction->getChoices()), 1);

        $myItem->addInteraction($myInteraction, "Adding my interaction here {$myInteraction->getPlaceholder()}. And not there.");
        $this->assertNotNull($myInteraction->getRelatedItem());
        $this->assertEquals($myInteraction->getRelatedItem()->getSerial(), $myItem->getSerial());

        $myResponse = new ResponseDeclaration();
        $myItem->addResponse($myResponse);
        $this->assertNotNull($myResponse->getRelatedItem());
        $this->assertEquals($myResponse->getRelatedItem()->getSerial(), $myItem->getSerial());

        $myItem->removeResponse($myResponse);
        $responses = $myItem->getResponses();
        $this->assertTrue(empty($responses));
    }

    public function testSimpleFeedback(){

        $response = new ResponseDeclaration(array('identifier' => 'RESPONSE'));
        $response->setHowMatch(Template::MAP_RESPONSE);
        $outcomeFeedback = new OutcomeDeclaration(array('identifier' => 'FEEDBACK'));
        $modalFeedback1 = new ModalFeedback(array('identifier' => 'feedbackOne'));
        $feebackRuleA = new SimpleFeedbackRule($outcomeFeedback, $modalFeedback1);
        $feebackRuleA->setCondition($response, 'gte', 2.1);
        $output1 = $feebackRuleA->toQTI();

        $modalFeedback2 = new ModalFeedback(array('identifier' => 'feedbackTwo'));
        $feebackRuleA->setFeedbackElse($modalFeedback2);
        $output2 = $feebackRuleA->toQTI();

        $doc = new DOMDocument();
        $doc->loadXML($output2);

        $data = simplexml_import_dom($doc);

        $subPatternFeedbackOperatorIf = '[name(./*[1]) = "responseIf" ] [count(./responseIf/*) = 2 ] [contains(name(./responseIf/*[1]/*[1]), "map")] [name(./responseIf/*[1]/*[2]) = "baseValue" ] [name(./responseIf/*[2]) = "setOutcomeValue" ] [name(./responseIf/setOutcomeValue/*[1]) = "baseValue" ]';
        $subPatternFeedbackElse = '[name(./*[2]) = "responseElseIf"] [count(./responseElseIf/*) = 1 ] [name(./responseElseIf/*[1]) = "setOutcomeValue"] [name(./responseElseIf/setOutcomeValue/*[1]) = "baseValue"]';
        $patternFeedbackOperator = '/responseCondition [count(./*) = 1 ]'.$subPatternFeedbackOperatorIf;
        $patternFeedbackOperatorWithElse = '/responseCondition [count(./*) = 2 ]'.$subPatternFeedbackOperatorIf.$subPatternFeedbackElse;
        $match = $data->xpath($patternFeedbackOperatorWithElse);

        $operator = '';
        $responseIdentifier = '';
        $value = '';
        foreach($data->responseIf->children() as $child){
            $operator = $child->getName();
            $map = null;
            foreach($child->children() as $granChild){
                $map = $granChild->getName();
                $responseIdentifier = (string) $granChild['identifier'];
                break;
            }
            $value = (string) $child->baseValue;
            break;
        }

        $feedbackOutcomeIdentifier = (string) $data->responseIf->setOutcomeValue['identifier'];
        $feedbackIdentifier = (string) $data->responseIf->setOutcomeValue->baseValue;
        $feedbackIdentifierElse = (string) $data->responseElse->setOutcomeValue->baseValue;

        $this->assertEquals($feedbackOutcomeIdentifier, 'FEEDBACK');
        $this->assertEquals($feedbackIdentifier, 'feedbackOne');
        $this->assertEquals($map, 'mapResponse');
        $this->assertEquals($responseIdentifier, 'RESPONSE');
        $this->assertEquals($operator, 'gte');
        $this->assertEquals($value, '2.1');
        $this->assertEquals($feedbackIdentifierElse, 'feedbackTwo');
    }

    public function testSimpleFeedbackCorrect(){

        $outcomeFeedback = new OutcomeDeclaration(array('identifier' => 'FEEDBACK'));
        $response2 = new ResponseDeclaration(array('identifier' => 'RESPONSE2'));
        $response2->setHowMatch(Template::MATCH_CORRECT);
        $modalFeedback3 = new ModalFeedback(array('identifier' => 'feedbackThree'));
        $feebackRuleB = new SimpleFeedbackRule($outcomeFeedback, $modalFeedback3);
        $feebackRuleB->setCondition($response2, 'correct');
        $output3 = $feebackRuleB->toQTI();

        $doc = new DOMDocument();
        $doc->loadXML($output3);

        $data = simplexml_import_dom($doc);
        $patternFeedbackCorrect = '/responseCondition [count(./*) = 1 ] [name(./*[1]) = "responseIf" ] [count(./responseIf/*) = 2 ] [name(./responseIf/*[1]) = "match" ] [name(./responseIf/*[1]/*[1]) = "variable" ] [name(./responseIf/*[1]/*[2]) = "correct" ] [name(./responseIf/*[2]) = "setOutcomeValue" ] [name(./responseIf/setOutcomeValue/*[1]) = "baseValue" ]';
        $match = $data->xpath($patternFeedbackCorrect);

        $responseIdentifier = (string) $data->responseIf->match->variable['identifier'];
        $feedbackOutcomeIdentifier = (string) $data->responseIf->setOutcomeValue['identifier'];
        $feedbackIdentifier = (string) $data->responseIf->setOutcomeValue->baseValue;

        $this->assertEquals($responseIdentifier, 'RESPONSE2');
        $this->assertEquals($feedbackOutcomeIdentifier, 'FEEDBACK');
        $this->assertEquals($feedbackIdentifier, 'feedbackThree');

    }

    /**
     * test the building of item from all the samples
     */
    public function testSamples(){

        //check if samples are loaded
        foreach(glob(dirname(__FILE__).'/samples/xml/qtiv2p1/*.xml') as $file){

            $qtiParser = new Parser($file);

            $item = $qtiParser->load();
            $this->assertTrue($qtiParser->isValid());
            $this->assertNotNull($item);
            $this->assertIsA($item, 'oat\\taoQtiItem\\model\\qti\\Item');

            foreach($item->getInteractions() as $interaction){
                $this->assertIsA($interaction, 'oat\\taoQtiItem\\model\\qti\\interaction\\Interaction');
                if($interaction instanceof MatchInteraction){
                    foreach($interaction->getChoices(0) as $choice){
                        $this->assertIsA($choice, 'oat\\taoQtiItem\\model\\qti\\choice\\Choice');
                    }
                    foreach($interaction->getChoices(1) as $choice){
                        $this->assertIsA($choice, 'oat\\taoQtiItem\\model\\qti\\choice\\Choice');
                    }
                }else{
                    foreach($interaction->getChoices() as $choice){
                        $this->assertIsA($choice, 'oat\\taoQtiItem\\model\\qti\\choice\\Choice');
                    }
                }
            }
        }
    }

    /**
     * Generate sample json files
     */
    public function _testToJson(){
        
        $jsons = array();
        $outputDir = dirname(__FILE__).'/samples/json/';
        $files = array_merge(
            glob(dirname(__FILE__).'/samples/xml/qtiv2p1/*.xml'), 
            glob(dirname(__FILE__).'/samples/xml/qtiv2p1/rubricBlock/*.xml'),
            glob(dirname(__FILE__).'/samples/xml/qtiv2p1/pci/*.xml')
        );
        
        foreach($files as $file){

            $qtiParser = new Parser($file);
            $item = $qtiParser->load();
            $dataForDelivery = $item->getDataForDelivery();
            
            $data = array('full' => $item->toArray(), 'core' => $dataForDelivery['core'], 'variable' => $dataForDelivery['variable']);
            $jsons[$item->getIdentifier()] = $data;
        }
        
        file_put_contents($outputDir.'ALL.json', tao_helpers_Javascript::buildObject($jsons, true));
    }

}