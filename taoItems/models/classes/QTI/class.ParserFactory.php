<?php

error_reporting(E_ALL);

/**
 * The ParserFactory provides some methods to build the QTI_Data objects from an
 * element.
 * SimpleXML is used as source to build the model.
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:00000000000023E9-includes begin
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:00000000000023E9-includes end

/* user defined constants */
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:00000000000023E9-constants begin
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:00000000000023E9-constants end

/**
 * The ParserFactory provides some methods to build the QTI_Data objects from an
 * element.
 * SimpleXML is used as source to build the model.
 *
 * @access public
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI
 */
class taoItems_models_classes_QTI_ParserFactory
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Build a QTI_Item from a SimpleXMLElement (the root tag of this element is
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  SimpleXMLElement data
     * @return taoItems_models_classes_QTI_Item
     * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10010
     */
    public static function buildItem( SimpleXMLElement $data)
    {
        $returnValue = null;

        // section 127-0-1-1--12a4f8d3:12a37dedffb:-8000:000000000000248E begin
        
        //check on the root tag
	    if($data->getName() != 'assessmentItem'){
	       	throw new taoItems_models_classes_QTI_ParsingException("incorrect item root tag");
	    }
	       
	    //get the item id
	    $itemId = null;
       	if(isset($data['identifier'])){
			$itemId = (string)$data['identifier'];//might be an issue if the identifier given is no good, e.g. twice the same value...
       	}
       
       	//retrieve the item attributes
       	$options = array();
       	foreach($data->attributes() as $key => $value){
       		$options[$key] = (string)$value;
       	}
       	unset($options['identifier']);
       	
       	//create the item instance
       	$myItem = new taoItems_models_classes_QTI_Item($itemId, $options);
       	
       	//get the stylesheets
		$styleSheets = array();
       	$styleSheetNodes = $data->xpath("*[name(.) = 'stylesheet']");
       	foreach($styleSheetNodes as $styleSheetNode){
       		$styleSheets[] = array(
       			'href' 	=> (string)$styleSheetNode['href'],		//mandaory field
       			'title' => (isset($styleSheetNode['title'])) ? (string)$styleSheetNode['title'] : '', 
       			'media'	=> (isset($styleSheetNode['media'])) ? (string)$styleSheetNode['media'] : 'screen',
       			'type'	=> (isset($styleSheetNode['type']))  ? (string)$styleSheetNode['type'] : 'text/css',
       		);
       	}
       	$myItem->setStylesheets($styleSheets);
       
     	//parse the xml to find the interaction nodes
        $interactionNodes = $data->xpath("//*[contains(name(.), 'Interaction')]");
        foreach($interactionNodes as $interactionNode){
        	//build an interaction instance by found node
        	$interaction = self::buildInteraction($interactionNode);
        	if(!is_null($interaction)){
       			$myItem->addInteraction($interaction);
        	}
        }
        
        //extract the item structure to separate the structural/style content to the item content 
        $itemBodyNodes = $data->xpath("*[name(.) = 'itemBody']/*");
        
        $itemData = '';
        foreach($itemBodyNodes as $itemBodyNode){	//the node should be alone
        	$itemData .= $itemBodyNode->asXml();
        }
        if(!empty($itemData)){
	        foreach($myItem->getInteractions() as $interaction){
	        	//map the interactions by an identified tag: {interaction.serial} 
	        	$tag = $interaction->getType().'Interaction';
	        	$pattern = "/<{$tag}\b[^>]*>(.*?)<\/{$tag}>|(<{$tag}\b[^>]*\/>)/is";
	        	$itemData = preg_replace($pattern, "{{$interaction->getSerial()}}", $itemData, 1);
	        }
	        $myItem->setData($itemData);
        }
        
        //extract thee responses
        $responseNodes = $data->xpath("*[name(.) = 'responseDeclaration']");
        foreach($responseNodes as $responseNode){
        	$response = self::buildResponse($responseNode);
        	if(!is_null($response)){
        		foreach($myItem->getInteractions() as $interaction){
        			if($interaction->getOption('responseIdentifier') == $response->getIdentifier()){
        				$interaction->setResponse($response);
        				break;
        			}
        		}
        	}
        }
        
        //extract outcome variables
        $outcomes = array();
        $outComeNodes = $data->xpath("*[name(.) = 'outcomeDeclaration']");
        foreach($outComeNodes as $outComeNode){
        	$outcome = self::buildOutcome($outComeNode);
        	if(!is_null($outcome)){
        		$outcomes[] = $outcome;
        	}
        }
        if(count($outcomes) > 0){
        	$myItem->setOutcomes($outcomes);
        }
        
        //extract the response processing
        $rpNodes = $data->xpath("*[name(.) = 'responseProcessing']");
        foreach($rpNodes as $rpNode){		//the node should be alone
        	$rProcessing = self::buildResponseProcessing($rpNode);
        	if(!is_null($rProcessing)){
        		$myItem->setResponseProcessing($rProcessing);
        	}
        }
        
        $returnValue = $myItem;
        
        // section 127-0-1-1--12a4f8d3:12a37dedffb:-8000:000000000000248E end

        return $returnValue;
    }

    /**
     * Build a QTI_Interaction from a SimpleXMLElement (the root tag of this
     * is an 'interaction' node)
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  SimpleXMLElement data
     * @return taoItems_models_classes_QTI_Interaction
     * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10247
     */
    public static function buildInteraction( SimpleXMLElement $data)
    {
        $returnValue = null;

        // section 127-0-1-1--12a4f8d3:12a37dedffb:-8000:0000000000002491 begin
        
        $options = array();
       	foreach($data->attributes() as $key => $value){
       		$options[$key] = (string)$value;
       	}
       	try{
       		$type = str_replace('Interaction', '', $data->getName());
       		$myInteraction = new taoItems_models_classes_QTI_Interaction($type, null, $options);
       	
       		switch($type){
       			
       			case 'match':
       				$matchSetNodes = $data->xpath("//*[name(.) = 'simpleMatchSet']");
       				foreach($matchSetNodes as $matchSetNode){
       					$choiceNodes = $matchSetNode->xpath("*[name(.) = 'simpleAssociableChoice']");
       					$choices = array();
	       				foreach($choiceNodes as $choiceNode){
				        	$choice = self::buildChoice($choiceNode);
				        	if(!is_null($choice)){
				        		$myInteraction->addChoice($choice);
				        		$choices[] = $choice;
				        	}
	       				}
       					if(count($choices) > 0){
       						$group = new taoItems_models_classes_QTI_Group();
       						$group->setType($matchSetNode->getName());
       						$group->setChoices($choices);
       						$myInteraction->addGroup($group);
       					}
       				}
       				break;
       				
       			case 'gapMatch':
       				$choiceNodes = $data->xpath("//*[name(.)='gapText']");
       				$choices = array();
       				foreach($choiceNodes as $choiceNode){
			        	$choice = self::buildChoice($choiceNode);
			        	if(!is_null($choice)){
			       			$myInteraction->addChoice($choice);
			       			$choices[] = $choice;
			        	}
       				}
       				$gapNodes = $data->xpath("//*[name(.)='gap']");
       				foreach($gapNodes as $gapNode){
       					$group = new taoItems_models_classes_QTI_Group((string)$gapNode['identifier']);
       					$group->setType($gapNode->getName());
       					$group->setChoices($choices);
       					if(isset($gapNode['matchGroup'])){
       						$group->setOption('matchGroup', (string)$gapNode['matchGroup']);
       					}
       					$myInteraction->addGroup($group);
       				}
       				break;
       				
       			default :
       				$exp= "*[contains(name(.),'Choice')] | //*[(name(.)='hottext')]";
       				$choiceNodes = $data->xpath($exp);
       				foreach($choiceNodes as $choiceNode){
			        	$choice = self::buildChoice($choiceNode);
			        	if(!is_null($choice)){
			       			$myInteraction->addChoice($choice);
			        	}
       				}
       				break;
       		}
       		
	       	//extract the interaction structure to separate the structural/style content to the interaction content 
	        $interactionNodes = $data->children();
	        
	        $interactionData = '';
	        foreach($interactionNodes as $interactionNode){
	        	$interactionData .= $interactionNode->asXml();
	        }
	        if(!empty($interactionData)){
	        	
				switch($type){
					
       				case 'match':
       					foreach($myInteraction->getGroups() as $group){
       						//map the group by a identified tag: {group-serial}
       						$tag = $group->getType();
				        	$pattern = "/(<{$tag}\b[^>]*>(.*?)<\/{$tag}>)|(<{$tag}\b[^>]*\/>)/is";
				        	$interactionData = preg_replace($pattern, "{{$group->getSerial()}}", $interactionData, 1);
       					}
						
       					break;
       					
       				case 'gapMatch':
						foreach($myInteraction->getGroups() as $group){
       						//map the group by a identified tag: {group-serial}
       						$tag = $group->getType();
				        	$pattern = "/(<{$tag}\b[^>]*>(.*?)<\/{$tag}>)|(<{$tag}\b[^>]*\/>)/is";
				        	$interactionData = preg_replace($pattern, "{{$group->getSerial()}}", $interactionData, 1);
       					}
       					
       				default:
			        	foreach($myInteraction->getChoices() as $choice){
				        	//map the choices by a identified tag: {choice-serial}
				        	$tag = $choice->getType();
				        	$pattern = "/(<{$tag}\b[^>]*>(.*?)<\/{$tag}>)|(<{$tag}\b[^>]*\/>)/is";
				        	$interactionData = preg_replace($pattern, "{{$choice->getSerial()}}", $interactionData, 1);
				        }
				        break;
		        
	       		}
	       		$promptNodes = $data->xpath("//*[name(.) = 'prompt']");
	       		foreach($promptNodes as $promptNode){
	       			$myInteraction->setPrompt((string)$promptNode);
	       			$pattern = "/(<prompt\b[^>]*>(.*?)<\/prompt>)|(<prompt\b[^>]*\/>)/is";
	       			$interactionData = preg_replace($pattern, "", $interactionData);
	       		}
	       		
	        	$myInteraction->setData($interactionData);
	        }
       		
       		$returnValue = $myInteraction;
       	}
       	catch(InvalidArgumentException $iae){
       		throw new taoItems_models_classes_QTI_ParsingException($iae);
       	}
        
        // section 127-0-1-1--12a4f8d3:12a37dedffb:-8000:0000000000002491 end

        return $returnValue;
    }

    /**
     * Build a QTI_Choice from a SimpleXMLElement (the root tag of this element
     * an 'choice' node)
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  SimpleXMLElement data
     * @return taoItems_models_classes_QTI_Choice
     * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10254
     */
    public static function buildChoice( SimpleXMLElement $data)
    {
        $returnValue = null;

        // section 127-0-1-1--12a4f8d3:12a37dedffb:-8000:0000000000002494 begin
        
        $options = array();
       	foreach($data->attributes() as $key => $value){
       		$options[$key] = (string)$value;
       	}
       	unset($options['identifier']);
       	
       	if(!isset($data['identifier'])){
			throw new taoItems_models_classes_QTI_ParsingException("No identifier found for the choice {$data->getName()}");
       	}
       	
       	$myChoice = new taoItems_models_classes_QTI_Choice((string)$data['identifier'], $options);
       	$myChoice->setType($data->getName());
       	if(count($data->children()) > 0){
       		$myChoice->setData((string)$data . $data->children()->asXML());
       	}
       	else{
       		$myChoice->setData((string)$data);
       	}
       	$returnValue = $myChoice;
        
        // section 127-0-1-1--12a4f8d3:12a37dedffb:-8000:0000000000002494 end

        return $returnValue;
    }

    /**
     * Short description of method buildResponse
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  SimpleXMLElement data
     * @return taoItems_models_classes_QTI_Response
     * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10074
     */
    public static function buildResponse( SimpleXMLElement $data)
    {
        $returnValue = null;

        // section 127-0-1-1--12a4f8d3:12a37dedffb:-8000:0000000000002497 begin
        
	    $options = array();
       	foreach($data->attributes() as $key => $value){
       		$options[$key] = (string)$value;
       	}
       	unset($options['identifier']);
       	
       	if(!isset($data['identifier'])){
			throw new taoItems_models_classes_QTI_ParsingException("No identifier found for {$data->getName()}");
       	}
       	
       	$myResponse = new taoItems_models_classes_QTI_Response((string)$data['identifier'], $options);
       	$myResponse->setType($data->getName());
       	
       	//set the correct responses
       	$correctResponseNodes = $data->xpath("*[name(.) = 'correctResponse']");
       	$responses = array();
       	foreach($correctResponseNodes as $correctResponseNode){
       		foreach($correctResponseNode->value as $value){
       			$responses[] = (string)$value;
       		}
       		break;
       	}
       	$myResponse->setCorrectResponses($responses);
       	
       	//set the mapping if defined
       	$mappingNodes = $data->xpath("*[name(.) = 'mapping']");
       	foreach($mappingNodes as $mappingNode){
       		
       		if(isset($mappingNode['defaultValue'])){
       			$myResponse->setMappingDefaultValue((string)$mappingNode['defaultValue']);
       		}
       		$mappingOptions = array();
	       	foreach($mappingNode->attributes() as $key => $value){
	       		if($key != 'defaultValue'){
	       			$mappingOptions[$key] = (string)$value;
	       		}
	       	}
	       	$myResponse->setOption('mapping', $mappingOptions);
       		
       		$mapping = array();
       		foreach($mappingNode->mapEntry as $mapEntry){
       			$mapping[(string)$mapEntry['mapKey']] = (string)$mapEntry['mappedValue'];
       		}
       		$myResponse->setMapping($mapping);
       		
       		break;
       	}
       	
       	
       	$returnValue = $myResponse;
        
        // section 127-0-1-1--12a4f8d3:12a37dedffb:-8000:0000000000002497 end

        return $returnValue;
    }

    /**
     * Short description of method buildOutcome
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  SimpleXMLElement data
     * @return taoItems_models_classes_QTI_Outcome
     */
    public static function buildOutcome( SimpleXMLElement $data)
    {
        $returnValue = null;

        // section 127-0-1-1--12a4f8d3:12a37dedffb:-8000:000000000000249A begin
        
    	$options = array();
       	foreach($data->attributes() as $key => $value){
       		$options[$key] = (string)$value;
       	}
       	unset($options['identifier']);
       	
       	if(!isset($data['identifier'])){
			throw new taoItems_models_classes_QTI_ParsingException("No identifier found for an {$data->getName()}");
       	}
       	
       	$outCome = new taoItems_models_classes_QTI_Outcome((string)$data['identifier'], $options);
        if(isset($outcome->defaultValue)){
        	$outCome->setDefaultValue((string)$outcome->defaultValue->value);
        }
        
        $returnValue = $outCome;
       	
        // section 127-0-1-1--12a4f8d3:12a37dedffb:-8000:000000000000249A end

        return $returnValue;
    }

    /**
     * Short description of method buildResponseProcessing
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  SimpleXMLElement data
     * @return taoItems_models_classes_QTI_response_ResponseProcessing
     */
    public static function buildResponseProcessing( SimpleXMLElement $data)
    {
        $returnValue = null;

        // section 127-0-1-1-74726297:12ae6749c02:-8000:0000000000002585 begin
        
        if(isset($data['template'])){
        	//template processing
        	$returnValue = new taoItems_models_classes_QTI_response_Template((string)$data['template']);
        }
        else{
			//custom rule processing
			$returnValue = self::buildCustomResponseProcessing($data);
        }    
        
        // section 127-0-1-1-74726297:12ae6749c02:-8000:0000000000002585 end

        return $returnValue;
    }

    /**
     * Short description of method buildCustomResponseProcessing
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  SimpleXMLElement data
     * @return taoItems_models_classes_QTI_response_ResponseProcessing
     */
    public static function buildCustomResponseProcessing( SimpleXMLElement $data)
    {
        $returnValue = null;

        // section 127-0-1-1-21b9a9c1:12c0d84cd90:-8000:0000000000002A6D begin
        
        // Parse to find the different response rules
        $responseRules = array ();
        
        // Check if response conditions have been defined
        $responseConditionNodes = $data->xpath("//*[name(.) = 'responseCondition']");
        
        foreach($responseConditionNodes as $responseConditionNode) {
            $responseIf = null;
            $responseElseIf = array ();
            $responseElse = array ();
            
            $responseCondition = taoItems_models_classes_QTI_response_ExpressionFactory::create ($responseConditionNode);
            
            // RESPONSE IF
            list ($responseIfNode) = $responseConditionNode->xpath("//*[name(.) = 'responseIf']"); // Only one responseIf is allowed
            if (!empty($responseIfNode)) {
                $responseIf = self::buildConditionalExpression($responseIfNode);
            } else {
                throw new taoItems_models_classes_QTI_ParsingException("responseIf is required in responseCondition");
            }
            $responseCondition->setResponseIf ($responseIf);
            
            // RESPONSE ELSE IF
            $responseElseIfNodes = $responseConditionNode->xpath("//*[name(.) = 'responseElseIf']");
            foreach ($responseElseIfNodes as $responseElseIfNode) {
                $responseElseIf[] = self::buildConditionalExpression($responseElseIfNode);
            }
            $responseCondition->setResponseElseIf ($responseElseIf);
            
            // RESPONSE ELSE
            list($responseElseNode) = $responseConditionNode->xpath("//*[name(.) = 'responseElse']");
            if (!empty($responseElseNode)) {
                foreach ($responseElseNode->children() as $node) {
                    $responseElse[] = self::buildExpression ($node);
                }
                $responseCondition->setResponseElse ($responseElse);
            }
            
            $responseRules[] = $responseCondition;   
        } 
     
        $returnValue = new taoItems_models_classes_QTI_response_CustomRule($responseRules);
     
        // section 127-0-1-1-21b9a9c1:12c0d84cd90:-8000:0000000000002A6D end

        return $returnValue;
    }

    /**
     * Enables you to build the QTI_Resources from a manifest xml data node
     * Content Packaging 1.1)
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  SimpleXMLElement source
     * @return array
     * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_intgv2p0.html#section10003
     */
    public static function getResourcesFromManifest( SimpleXMLElement $source)
    {
        $returnValue = array();

        // section 127-0-1-1-5c65d02d:12bc97f5116:-8000:00000000000026FB begin
        
    	//check of the root tag
    	if($source->getName() != 'manifest'){
	       	throw new Exception("incorrect manifest root tag");
	    }
	    
	    $resourceNodes = $source->xpath("//*[name(.)='resource']");
	    foreach($resourceNodes as $resourceNode){
	    	$type = (string)$resourceNode['type'];
	    	if(taoItems_models_classes_QTI_Resource::isAllowed($type)){
	    		
	    		$id = (string)$resourceNode['identifier'];
	    		(isset($resourceNode['href'])) ? $href = (string)$resourceNode['href'] : $href = '';
	    		
	    		$auxFiles = array();
	    		$xmlFiles = array();
	    		foreach($resourceNode->file as $fileNode){
	    			$fileHref = (string)$fileNode['href'];
	    			if(preg_match("/\.xml$/", $fileHref)){
		    			if(empty($href)){
		    				$xmlFiles[] = $fileHref;
		    			}
	    			}
	    			else{
	    				$auxFiles[] = $fileHref;
	    			}
	    		}
	    		
	    		if(count($xmlFiles) == 1 && empty($href)){
	    			$href = $xmlFiles[0];
	    		}
	    		$resource = new taoItems_models_classes_QTI_Resource($id, $href);
	    		$resource->setAuxiliaryFiles($auxFiles);
	    		
	    		$returnValue[] = $resource;
	    	}
	    }
        
        // section 127-0-1-1-5c65d02d:12bc97f5116:-8000:00000000000026FB end

        return (array) $returnValue;
    }

    /**
     * Short description of method buildConditionalExpression
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  SimpleXMLElement data
     * @return taoItems_models_classes_QTI_response_ConditionalExpression
     */
    public static function buildConditionalExpression( SimpleXMLElement $data)
    {
        $returnValue = null;

        // section 127-0-1-1-554f2bd6:12c176484b7:-8000:0000000000002B31 begin
        
        // A conditional expression part consists of an expression which must have an effective baseType of boolean and single cardinality
        // It also contains a set of sub-rules. If the expression is true then the sub-rules are processed, otherwise they are 
        // skipped (including if the expression is NULL) and the following responseElseIf or responseElse parts (if any) are considered instead.

        $returnValue = new taoItems_models_classes_QTI_response_ConditionalExpression ();
        $actions = array ();
        
        // The first subExpression has to be the condition (single cardinality and boolean type)
        list($conditionNode) = $data->xpath ('*[1]');
        $condition = self::buildExpression ($conditionNode);
        //echo '<pre>';print_r ($condition);echo '</pre>';
        $returnValue->setCondition ($condition);

        // The rest of subExpression have to be computed if the condition is filled
        // These subExpression are responseRule (ResponseCondition, SetOutcomeValue, exitResponse). This code is yet writen, extract the function and avoid doublon
        for ($i=2; $i<=count($data); $i++) {
            list($actionNode) = $data->xpath ('*['.$i.']');
            $actions[]= self::buildExpression ($actionNode);
        }
        $returnValue->setActions ($actions);
        
        // section 127-0-1-1-554f2bd6:12c176484b7:-8000:0000000000002B31 end

        return $returnValue;
    }

    /**
     * Short description of method buildExpression
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  SimpleXMLElement data
     * @return taoItems_models_classes_QTI_response_Expression
     */
    public static function buildExpression( SimpleXMLElement $data)
    {
        $returnValue = null;

        // section 127-0-1-1-554f2bd6:12c176484b7:-8000:0000000000002B34 begin
        
        // The factory will create the right expression for us
        $expression = taoItems_models_classes_QTI_response_ExpressionFactory::create ($data);
        $subExpressions = Array();
        
        // All sub-expressions of an expression are embedded by this expression
        foreach ($data->children() as $subExpressionNode) {
            $subExpressions[] = self::buildExpression ($subExpressionNode);
        }
        $expression->setSubExpressions ($subExpressions);        
        
        // If the expression has a value
        $expressionValue = (string) trim ($data);
        if ($expressionValue != ''){
            $expression->setValue ($expressionValue);
        }
        
        $returnValue = $expression;
        
        // section 127-0-1-1-554f2bd6:12c176484b7:-8000:0000000000002B34 end

        return $returnValue;
    }

} /* end of class taoItems_models_classes_QTI_ParserFactory */

?>
