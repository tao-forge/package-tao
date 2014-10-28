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
 * Copyright (c) 2013 Open Assessment Technologies S.A.
 * 
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoOutcomeUi
 */

namespace oat\taoOutcomeUi\model;

use \Exception;
use \common_Exception;
use \common_Logger;
use \common_cache_FileCache;
use \common_exception_Error;
use \core_kernel_classes_Class;
use \core_kernel_classes_DbWrapper;
use \core_kernel_classes_Property;
use \core_kernel_classes_Resource;
use oat\taoOutcomeRds\model\RdsResultStorage;
use \taoResultServer_models_classes_Variable;
use \tao_helpers_Date;
use \tao_models_classes_ClassService;
use oat\taoOutcomeUi\helper\Datatypes;

class ResultsService extends tao_models_classes_ClassService {

    /**
     * a local cache (string)$callId=> (core_kernel_classes_Resource) $itemResult
     * 
     * @var core_kernel_classes_Resource
     */
    private $cacheItemResult = array(); 
    
    /**
     * a local cache (string)identifier=> (core_kernel_classes_Resource) $deliveryResult
     * @var core_kernel_classes_Resource
     */
    private $cacheDeliveryResult = array(); 
    
    private $implementation = null;
    
    /**
     * (non-PHPdoc)
     * @see tao_models_classes_ClassService::getRootClass()
     */
    public function getRootClass() {
        return new core_kernel_classes_Class(TAO_DELIVERY_RESULT);
    }

    public function setImplementation($implementationClass){
        if(class_exists($implementationClass)){
            $this->implementation = new $implementationClass;
        }
    }

    public function getImplementation(){
        if($this->implementation == null){
            $this->implementation = new RdsResultStorage();
        }
        return $this->implementation;
    }

    /**
     * return all variable for taht deliveryResults (uri identifiers) 
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource deliveryResult
     * @param core_kernel_classes_Class to restrict to a specific class of variables
     * @param boolean flat a falt array is returned or a structured delvieryResult-ItemResult-Variable
     * @return array
     */
    public function getVariables(core_kernel_classes_Resource $deliveryResult, $variableClass = null, $flat = true) {
        $variables = array();
        //this service is slow due to the way the data model design  
        //if the delvieryResult related execution is finished, the data is stored in cache. 
        $serial = 'deliveryResultVariables';
        if ($variableClass != null) {
            $serial.=$variableClass->getUri();
        }
        if (common_cache_FileCache::singleton()->has($serial)) {
            $variables = common_cache_FileCache::singleton()->get($serial);
        } else {           
           foreach ($this->getItemResultsFromDeliveryResult($deliveryResult) as $itemResult) {
                $itemResultVariables = $this->getVariablesFromItemResult($itemResult, $variableClass);
                $itemResultUri = $itemResult;
                $variables[$itemResultUri] = $itemResultVariables;        
           }          
           //overhead for cache handling, the data is stored only when the underlying deliveryExecution is finished
           try {
                $executionIdentifier = $deliveryResult->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_IDENTIFIER));
                $status = $executionIdentifier->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_DELVIERYEXECUTION_STATUS));
                if ($status->getUri()== INSTANCE_DELIVERYEXEC_FINISHED ) {
                    common_cache_FileCache::singleton()->put($variables, $serial);
                }
           
           }catch (common_Exception $e) {
               common_Logger::i("List of variables of results of ".$deliveryResult->getUri()." could not be reliable cached due to an unfinished execution");
           }
           
           
        }
         if ($flat) {
                $returnValue = array();
                foreach ($variables as $itemResultVariables) {
                $returnValue = array_merge($itemResultVariables, $returnValue);
                }
            } else {
                $returnValue = $variables;
            }
        
        
        return (array) $returnValue;
    }

    /**
     * @param  Resource Itemresult
     * @return array
     */
    public function getVariablesFromItemResult($itemResult) {
        return $this->getImplementation()->getVariables($itemResult);
    }

    /**
     * Return the corresponding delivery 
     * @param core_kernel_classes_Resource $deliveryResult
     * @return core_kernel_classes_Resource delviery
     * @author Patrick Plichart, <patrick@taotesting.com>
     */
    public function getDelivery(core_kernel_classes_Resource $deliveryResult) {
        return new core_kernel_classes_Resource($this->getImplementation()->getDelivery($deliveryResult->getUri()));
    }

    /**
     * Returns all label of itemResults related to the delvieryResults
     * @param core_kernel_classes_Resource $deliveryResult
     * @return array core_kernel_classes_Resource
     * */
    public function getItemResultsFromDeliveryResult(core_kernel_classes_Resource $deliveryResult) {
        return $this->getImplementation()->getRelatedItemCallIds($deliveryResult->getUri());
    }

    /**
     * 
     * @param core_kernel_classes_Resource $variable
     * @return \common_Object
     */
    public function getItemResultFromVariable(core_kernel_classes_Resource $variable) {
        $relatedItemResult = new core_kernel_classes_Property(PROPERTY_RELATED_ITEM_RESULT);
        $itemResult = $variable->getUniquePropertyValue($relatedItemResult);
        return $itemResult;
    }

    /**
     * 
     * @param core_kernel_classes_Resource $itemResult
     * @return \common_Object
     */
    public function getItemFromItemResult($itemResult) {
        $items = $this->getImplementation()->getVariables($itemResult);
        $item = new core_kernel_classes_Resource(array_shift($items)[0]->item);
        return $item;
    }

    /**
     * 
     * @param core_kernel_classes_Resource $variable
     * @return \common_Object
     */
    public function getItemFromVariable(core_kernel_classes_Resource $variable) {
        return $this->getItemFromItemResult($this->getItemResultFromVariable($variable));
    }

    /**
     * 
     * @param unknown $variableUri 
     * @return \common_Object
     * 
     */
    public function getVariableCandidateResponse($variableUri) {
        return $this->getImplementation()->getVariableProperty($variableUri, 'candidateResponse');
    }

    /**
     * 
     * @param unknown $variableUri
     * @return \common_Object
     */
    public function getVariableBaseType($variableUri) {
        return $this->getImplementation()->getVariableProperty($variableUri, 'baseType');
    }

    /**
     *
     * @param core_kernel_classes_Resource $deliveryResult
     * @param string $filter 'lastSubmitted', 'firstSubmitted'
     * @return type
     */
    public function getItemVariableDataStatsFromDeliveryResult(core_kernel_classes_Resource $deliveryResult, $filter = null) {
        $itemResults = $this->getItemResultsFromDeliveryResult($deliveryResult);
        $numberOfResponseVariables = 0;
        $numberOfCorrectResponseVariables = 0;
        $numberOfInCorrectResponseVariables = 0;
        $numberOfUnscoredResponseVariables = 0;
        $numberOfOutcomeVariables = 0;
        $variablesData = $this->getItemVariableDataFromDeliveryResult($deliveryResult, $filter);
        foreach ($variablesData as $itemVariables) {
            foreach($itemVariables['sortedVars'] as $key => $value){
                if($key == CLASS_RESPONSE_VARIABLE){
                    foreach($value as $variable){
                        $numberOfResponseVariables++;
                        switch($variable[0]['isCorrect']){
                            case 'correct':
                                $numberOfCorrectResponseVariables++;
                                break;
                            case 'incorrect':
                                $numberOfInCorrectResponseVariables++;
                                break;
                            case 'unscored':
                                $numberOfUnscoredResponseVariables++;
                                break;
                            default:
                                common_Logger::w('The value '.$variable[0]['isCorrect'].' is not a valid value');
                                break;
                        }
                    }
                }
                else{
                    $numberOfOutcomeVariables++;
                }

            }
        }
        $stats = array(
            "nbResponses" => $numberOfResponseVariables,
            "nbCorrectResponses" => $numberOfCorrectResponseVariables,
            "nbIncorrectResponses" => $numberOfInCorrectResponseVariables,
            "nbUnscoredResponses" => $numberOfUnscoredResponseVariables,
            "data" => $variablesData
        );
        return $stats;
    }
    /**
     *  prepare a data set as an associative array, service intended to populate gui controller
     *  
     *  @param string $filter 'lastSubmitted', 'firstSubmitted'
     */
    public function getItemVariableDataFromDeliveryResult(core_kernel_classes_Resource $deliveryResult, $filter) {

        $undefinedStr = __('unknown'); //some data may have not been submitted           

        $itemResults = $this->getItemResultsFromDeliveryResult($deliveryResult);
        $variablesByItem = array();
        foreach ($itemResults as $itemResult) {
            try {
                common_Logger::d("Retrieving related Item for itemResult " . $itemResult . "");
                $relatedItem = $this->getItemFromItemResult($itemResult);
            } catch (common_Exception $e) {
                common_Logger::w("The itemResult " . $itemResult . " is not linked to a valid item. (deleted item ?)");
                $relatedItem = null;
            }
            if (get_class($relatedItem) == "core_kernel_classes_Literal") {
                $itemIdentifier = $relatedItem->__toString();
                $itemLabel = $relatedItem->__toString();
                $itemModel = $undefinedStr;
            } elseif (get_class($relatedItem) == "core_kernel_classes_Resource") {
                $itemIdentifier = $relatedItem->getUri();
                $itemLabel = $relatedItem->getLabel();

                try {
                    common_Logger::d("Retrieving related Item model for item " . $relatedItem->getUri() . "");
                    $itemModel = $relatedItem->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY));
                    $variablesByItem[$itemIdentifier]['itemModel'] = $itemModel->getLabel();
                } catch (common_Exception $e) { //a resource but unknown
                    $variablesByItem[$itemIdentifier]['itemModel'] = 'unknown';
                }
            } else {
                $itemIdentifier = $undefinedStr;
                $itemLabel = $undefinedStr;
                $variablesByItem[$itemIdentifier]['itemModel'] = $undefinedStr;
            }
            foreach ($this->getVariablesFromItemResult($itemResult) as $variable) {
                //retrieve the type of the variable
                $variableTemp = (array)$variable[0]->variable;
                $variableDescription = array();
                foreach($variableTemp as $key => $value){
                    switch($key){
                        case 'value':
                            $variableDescription[RDF_VALUE] = $value;
                            break;
                        case 'uri':
                            break;
                        case 'identifier':
                            $variableDescription[PROPERTY_IDENTIFIER] = $value;
                            break;
                        case 'epoch':
                            $variableDescription[PROPERTY_VARIABLE_EPOCH] = $value;
                            break;
                        default:
                            $variableDescription[RESULT_ONTOLOGY.'#'.$key] = $value;
                            break;
                    }
                }
                $type = get_class($variable[0]->variable);
                if ($type == "taoResultServer_models_classes_OutcomeVariable") {
                    $variableDescription[RDF_VALUE] = array(base64_decode($variableDescription[RDF_VALUE]));
                    $type = CLASS_OUTCOME_VARIABLE;
                }
                else{
                    $variableDescription[RDF_VALUE] = array(base64_decode($variableDescription[PROPERTY_RESPONSE_VARIABLE_CANDIDATERESPONSE]));
                    $type = CLASS_RESPONSE_VARIABLE;
                }
                $variableIdentifier = $variableDescription[PROPERTY_IDENTIFIER];
                $epoch = $variableDescription[PROPERTY_VARIABLE_EPOCH];
                $variableDescription["uri"] = $variable[0]->uri;
                $variableDescription["epoch"] = array(tao_helpers_Date::displayeDate(tao_helpers_Date::getTimeStamp($epoch), tao_helpers_Date::FORMAT_VERBOSE));

                if (isset($variableDescription[PROPERTY_RESPONSE_VARIABLE_CORRECTRESPONSE])) {
                    $correctResponse = $variableDescription[PROPERTY_RESPONSE_VARIABLE_CORRECTRESPONSE];
                }
                else{
                    $correctResponse = null;
                }
                if (!is_null($correctResponse)) {
                    if($correctResponse >= 1){
                        $variableDescription["isCorrect"] = "correct";
                    }
                    else{
                        $variableDescription["isCorrect"] = "incorrect";
                    }
                } else {
                    $variableDescription["isCorrect"] = "unscored";
                }

                $variablesByItem[$itemIdentifier]['sortedVars'][$type][$variableIdentifier][$epoch] = $variableDescription;
                $variablesByItem[$itemIdentifier]['label'] = $itemLabel;
            }
        }
        //sort by epoch and filter
        foreach ($variablesByItem as $itemIdentifier => $itemVariables) {

            foreach ($itemVariables['sortedVars'] as $variableType => $variables) {
                foreach ($variables as $variableIdentifier => $observation) {

                    uksort($variablesByItem[$itemIdentifier]['sortedVars'][$variableType][$variableIdentifier], "self::sortTimeStamps");

                    //print_r($observation);

                    switch ($filter) {
                        case "lastSubmitted": {
                                $variablesByItem[$itemIdentifier]['sortedVars'][$variableType][$variableIdentifier] = array(array_pop($variablesByItem[$itemIdentifier]['sortedVars'][$variableType][$variableIdentifier]));
                                break;
                            }
                        case "firstSubmitted": {
                                $variablesByItem[$itemIdentifier]['sortedVars'][$variableType][$variableIdentifier] = array(array_shift($variablesByItem[$itemIdentifier]['sortedVars'][$variableType][$variableIdentifier]));
                                break;
                            }
                    }
                }
            }
        }

        return $variablesByItem;
    }
    /**
     * 
     * @param unknown $a
     * @param unknown $b
     * @return number
     */
    public static function sortTimeStamps($a, $b) {
        list($usec, $sec) = explode(" ", $a);
        $floata = ((float) $usec + (float) $sec);
        list($usec, $sec) = explode(" ", $b);
        $floatb = ((float) $usec + (float) $sec);
        //common_Logger::i($a." ".$floata);
        //common_Logger::i($b. " ".$floatb);
        //the callback is expecting an int returned, for the case where the difference is of less than a second
        //intval(round(floatval($b) - floatval($a),1, PHP_ROUND_HALF_EVEN));
        if ((floatval($floata) - floatval($floatb)) > 0) {
            return 1;
        } elseif ((floatval($floata) - floatval($floatb)) < 0) {
            return -1;
        } else {
            return 0;
        }
    }

    /**
     * return all variables linked to the delviery result and that are not linked to a particular itemResult
     * 
     * @param core_kernel_classes_Resource $deliveryResult
     * @return multitype:multitype:string
     */
    public function getVariableDataFromDeliveryResult(core_kernel_classes_Resource $deliveryResult) {

        $variables = $this->getImplementation()->getVariables($deliveryResult->getUri());
        $variablesData = array();
        foreach($variables as $variable){
            if($variable[0]->callIdTest != ""){
                $variableDescription = array();
                foreach((array)$variable[0]->variable as $key => $value){
                    switch($key){
                        case 'value':
                            $variableDescription[RDF_VALUE] = base64_decode($value);
                            break;
                        case 'identifier':
                            $variableDescription[PROPERTY_IDENTIFIER] = $value;
                            break;
                        case 'epoch':
                            $variableDescription[PROPERTY_VARIABLE_EPOCH] = $value;
                            break;
                        default:
                            $variableDescription[RESULT_ONTOLOGY.'#'.$key] = $value;
                            break;
                    }
                }
                $variablesData[] = $variableDescription;
            }
        }
        return $variablesData;
    }

    /**
     * returns the test taker related to the delivery
     *
     * @author Patrick Plichart, <patrick.plichart@taotesting.com>
     */
    public function getTestTaker(core_kernel_classes_Resource $deliveryResult) {
        $testTaker = $this->getImplementation()->getTestTaker($deliveryResult->getUri());
        return new core_kernel_classes_Resource($testTaker);
    }

    /**
     * Short description of method deleteResult
     *
     */
    public function deleteResult(core_kernel_classes_Resource $result) {
        $returnValue = (bool) false;

        
        if (!is_null($result)) {
            $returnValue = $this->getImplementation()->deleteResult($result->getUri());

        }
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method deleteResultClass
     *
     */
    public function deleteResultClass(core_kernel_classes_Class $clazz) {
        $returnValue = (bool) false;

        if (!is_null($clazz)) {
            $returnValue = $clazz->delete();
        }
        return (bool) $returnValue;
    }


    /**
     * Retrieves all score variables pertaining to the deliveryResult
     *
     * @access public
     * @author Patrick Plichart, <patrick.plichart@taotesting.com>
     * @param  Resource deliveryResult
     * @return array
     */
    public function getScoreVariables(core_kernel_classes_Resource $deliveryResult) {
        return $this->getVariables($deliveryResult, new core_kernel_classes_Class(CLASS_OUTCOME_VARIABLE));
    }

    public function getVariableFile($variableUri) {
        //distinguish QTI file from other "file" base type
        $baseType = $this->getVariableBaseType($variableUri);
        
        // https://bugs.php.net/bug.php?id=52623 ; 
        // if the constant for max buffering, mysqlnd or similar driver
        // is being used without need to adapt buffer size as it is atutomatically adapted for all the data. 
        if (core_kernel_classes_DbWrapper::singleton()->getPlatForm()->getName() == 'mysql') {            
            if (defined("PDO::MYSQL_ATTR_MAX_BUFFER_SIZE")) {
                $maxBuffer = (is_int(ini_get('upload_max_filesize'))) ? (ini_get('upload_max_filesize')* 1.5) : 10485760 ;
                core_kernel_classes_DbWrapper::singleton()->getSchemaManager()->setAttribute(\PDO::MYSQL_ATTR_MAX_BUFFER_SIZE,$maxBuffer);
            }
        }
        
        
        switch ($baseType) {
            case "file": {
                    $value = base64_decode($this->getVariableCandidateResponse($variableUri));
                    common_Logger::i(var_export(strlen($value), true));
                    $decodedFile = Datatypes::decodeFile($value);
                    common_Logger::i("FileName:");
                    common_Logger::i(var_export($decodedFile["name"], true));
                    common_Logger::i("Mime Type:");
                    common_Logger::i(var_export($decodedFile["mime"], true));
                    $file = array(
                        "data" => $decodedFile["data"],
                        "mimetype" => "Content-type: " . $decodedFile["mime"],
                        "filename" => $decodedFile["name"]);
                    break;
                }
            default: { //legacy files
                    $file = array(
                        "data" => $this->getVariableCandidateResponse($variableUri),
                        "mimetype" => "Content-type: text/xml",
                        "filename" => "trace.xml");
                }
        }
        return $file;
    }

    /**
     * Retrieves information about the variable, including or not the related item $getItem (slower)
     * 
     * @access public
     * @author Patrick Plichart, <patrick.plichart@taotesting.com>
     * @param  Resource variable
     * @param  bool getItem retireve associated item reference
     * @return array simple associative
     */
    public function getVariableData(core_kernel_classes_Resource $variable, $getItem = false) {
        $returnValue = array();
        $baseTypes = $variable->getPropertyValues(new core_kernel_classes_Property(PROPERTY_VARIABLE_BASETYPE));
        $baseType = current($baseTypes);
        if ($baseType != "file") {
            $propValues = $variable->getPropertiesValues(array(
                PROPERTY_IDENTIFIER,
                PROPERTY_VARIABLE_EPOCH,
                RDF_VALUE,
                PROPERTY_VARIABLE_CARDINALITY,
                PROPERTY_VARIABLE_BASETYPE
            ));
            $returnValue["value"] = (string) base64_decode(current($propValues[RDF_VALUE]));
        } else {
            $propValues = $variable->getPropertiesValues(array(
                PROPERTY_IDENTIFIER,
                PROPERTY_VARIABLE_EPOCH,
                PROPERTY_VARIABLE_CARDINALITY,
                PROPERTY_VARIABLE_BASETYPE
            ));
            $returnValue["value"] = "";
        }
        $returnValue["identifier"] = current($propValues[PROPERTY_IDENTIFIER])->__toString();
        $class =  current($variable->getTypes());    
        $returnValue["type"]= $class;
        $returnValue["epoch"] = current($propValues[PROPERTY_VARIABLE_EPOCH])->__toString();
        if (count($propValues[PROPERTY_VARIABLE_CARDINALITY]) > 0) {
            $returnValue["cardinality"] = current($propValues[PROPERTY_VARIABLE_CARDINALITY])->__toString();
        }
        if (count($propValues[PROPERTY_VARIABLE_BASETYPE]) > 0) {
            $returnValue["basetype"] = current($propValues[PROPERTY_VARIABLE_BASETYPE])->__toString();
        }
        if ($getItem) {
            $returnValue["item"] = $this->getItemFromVariable($variable);
        }
        return (array) $returnValue;
    }
    

    /**
     * To be reviewed as it implies a dependency towards taoSubjects
     * @param core_kernel_classes_Resource $deliveryResult
     */
    public function getTestTakerData(core_kernel_classes_Resource $deliveryResult) {
        $testTaker = $this->gettestTaker($deliveryResult);
        if (get_class($testTaker) == 'core_kernel_classes_Literal') {
            return $testTaker;
        } else {
            $propValues = $testTaker->getPropertiesValues(array(
                RDFS_LABEL,
                PROPERTY_USER_LOGIN,
                PROPERTY_USER_FIRSTNAME,
                PROPERTY_USER_LASTNAME,
                PROPERTY_USER_MAIL,
            ));
        }
        return $propValues;
    }
}
