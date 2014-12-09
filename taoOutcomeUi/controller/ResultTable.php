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
 * Copyright (c) 2009-2012 (original work) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *
 *
 */


namespace oat\taoOutcomeUi\controller;

use \common_Exception;
use \core_kernel_classes_Class;
use \core_kernel_classes_Property;
use \core_kernel_classes_Resource;
use \tao_actions_Table;
use \tao_models_classes_table_Column;
use \tao_models_classes_table_PropertyColumn;
use oat\taoOutcomeUi\model\ResultsService;
use oat\taoOutcomeUi\model\table\GradeColumn;
use oat\taoOutcomeUi\model\table\ResponseColumn;
use oat\taoOutcomeUi\model\table\VariableColumn;
use oat\taoOutcomeRds\model\RdsResultStorage;

/**
 * should be entirelyrefactored
 * Results Controller provide actions performed from url resolution
 *
 * @author Joel Bout <joel@taotesting.com>
 * @author Patrick Plichart <patrick@taotesting.com>
 * @package taoOutcomeUi
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 *
 */
class ResultTable extends tao_actions_Table {

    /**
     * constructor: initialize the service and the default data
     * @return Results
     */
    protected $service;

    public function __construct() {

        parent::__construct();
        $this->service = ResultsService::singleton();
    }

    /**
     * get the main class
     * @return \core_kernel_classes_Classes
     */
    public function index() {
    	$filter = $this->getRequestParameter('filter');
    	$implementation = $this->getRequestParameter('implementation');
		$this->setData('filter', $filter);
		$this->setData('implementation', $implementation);
		$this->setView('resultTable.tpl');
    }

    /**
     * Relies on two optionnal parameters,
     * - filters (facet based query) ($this->hasRequestParameter('filter'))
     * - the list of columns currently selected on the frontend side ($this->hasRequestParameter('columns'))
     * @return void - a csv string is being sent out by parent class -> data method into the buffer
     */
    public function getCsvFile(){
        $rows = array();

        $filter =  $this->hasRequestParameter('filter') ? $this->getRequestParameter('filter') : array();
       	$filterData =  $this->hasRequestParameter('filterData')? $this->getRequestParameter('filterData') : array();
    	$columns = $this->hasRequestParameter('columns') ? $this->getColumns('columns') : array();
    	
    	//The list of delivery Results matching the current selection filters
        $results = array();
        foreach($this->service->getImplementation()->getResultByColumn(array_keys($filterData), $filterData) as $result){
            $results[] = new core_kernel_classes_Resource($result['deliveryResultIdentifier']);
        }
        $dpmap = array();
        foreach ($columns as $column) {
                $dataprovider = $column->getDataProvider();
                $found = false;
                foreach ($dpmap as $k => $dp) {
                        if ($dp['instance'] == $dataprovider) {
                                $found = true;
                                $dpmap[$k]['columns'][] = $column;
                        }
                }
                if (!$found) {
                        $dpmap[] = array(
                                'instance'	=> $dataprovider,
                                'columns'	=> array(
                                        $column
                                )
                        );
                }
        }

        foreach ($dpmap as $arr) {
            $arr['instance']->prepare($results, $arr['columns']);
        }
        
        foreach($results as $result) {
            $cellData = array();
            foreach ($columns as $column) {
                if (count($column->getDataProvider()->cache) > 0) {
                    $cellData[]=self::filterCellData($column->getDataProvider()->getValue($result, $column), $filter);
                } else {
                    $cellData[]=self::filterCellData($this->service->getTestTaker($result)->getLabel(), $filter);
                }
            }
            $rows[] = array(
                    'id' => $result->getUri(),
                    'cell' => $cellData
            );
        }

        $encodedData = $this->dataToCsv($columns, $rows,';','"');

        header('Set-Cookie: fileDownload=true'); //used by jquery file download to find out the download has been triggered ...
        setcookie("fileDownload","true", 0, "/");
        header("Content-type: text/csv");
        header('Content-Disposition: attachment; filename=Data.csv');
        echo $encodedData;
    }

    /**
     * Returns the default column selection that contains the Result of Subject property (This has been removed from the other commodity function adding grades and responses)
     */
    public function getResultOfSubjectColumn(){

		$testtaker = new tao_models_classes_table_PropertyColumn(new core_kernel_classes_Property(PROPERTY_RESULT_OF_SUBJECT));
		$arr[] = $testtaker->toArray();
        echo json_encode(array(
                'columns' => $arr,
                'first'   => true
        ));
    }

    /** 
     * Returns all columns with all responses pertaining to the current delivery results selection
     */
    public function getResponseColumns() {
	    $this->getVariableColumns(CLASS_RESPONSE_VARIABLE);
    }

    /** 
     * Returns all columns with all grades pertaining to the current delivery results selection
     */
     public function getGradeColumns() {
        $this->getVariableColumns(CLASS_OUTCOME_VARIABLE);
    }

     /**
     * Retrieve the different variables columns pertainign to the current selection of results
     * Implementation note : it nalyses all the data collected to identify the different response variables submitted by the items in the context of activities
     */
    protected function getVariableColumns($variableClassUri) {

		$columns = array();
		$filter = $this->getFilterState('filter');

        if($this->hasRequestParameter('implementation')){
            if (class_exists(urldecode($this->getRequestParameter('implementation')))) {
                $this->service->setImplementation(urldecode($this->getRequestParameter('implementation')));
            }
        }
		//The list of delivery Results matching the current selection filters
        $results = $this->service->getImplementation()->getResultByColumn(array_keys($filter), $filter);

		//retrieveing all individual response variables referring to the  selected delivery results
		$selectedVariables = array ();
		foreach ($results as $result){
            $variables = $this->service->getVariables(new core_kernel_classes_Resource($result['deliveryResultIdentifier']), new core_kernel_classes_Class($variableClassUri) );
            $selectedVariables = array_merge($selectedVariables, $variables);
		}
		//retrieving The list of the variables identifiers per activities defintions as observed
		$variableTypes = array();
		foreach ($selectedVariables as $variable) {
            if(!is_null($variable[0]->item) && (get_class($variable[0]->variable) == 'taoResultServer_models_classes_OutcomeVariable' && $variableClassUri == CLASS_OUTCOME_VARIABLE)
            || (get_class($variable[0]->variable) == 'taoResultServer_models_classes_ResponseVariable' && $variableClassUri == CLASS_RESPONSE_VARIABLE)){
                //variableIdentifier
                $variableIdentifier = $variable[0]->variable->identifier;
                $item = new core_kernel_classes_Resource($variable[0]->item);
                if (get_class($item) == "core_kernel_classes_Resource") {
                $contextIdentifierLabel = $item->getLabel();
                $contextIdentifier = $item->getUri(); // use the callId/itemResult identifier
                }
                else {
                    $contextIdentifierLabel = $item->__toString();
                $contextIdentifier = $item->__toString();
                }
                $variableTypes[$contextIdentifier.$variableIdentifier] = array("contextLabel" => $contextIdentifierLabel, "contextId" => $contextIdentifier, "variableIdentifier" => $variableIdentifier);
            }
        }
		foreach ($variableTypes as $variable){

		    switch ($variableClassUri){
                case CLASS_RESPONSE_VARIABLE:{ $columns[] = new ResponseColumn($variable["contextId"], $variable["contextLabel"], $variable["variableIdentifier"]);break;}
                case CLASS_OUTCOME_VARIABLE: { $columns[] = new GradeColumn($variable["contextId"], $variable["contextLabel"], $variable["variableIdentifier"]);break;}
                default:{$columns[] = new ResponseColumn($variable["contextId"], $variable["contextLabel"], $variable["variableIdentifier"]);}
			}
		}
		$arr = array();
		foreach ($columns as $column) {
			$arr[] = $column->toArray();
		}
    	echo json_encode(array(
    		'columns' => $arr
    	));
    }

    /**
     * @return string A csv file with the data table
     * @param columns an array of column objects including the property information and as it is used in the tao class.Table.php context
     */
    private function dataToCsv($columns, $rows, $delimiter, $enclosure){
       //opens a temporary stream rather than producing a file and get benefit of csv php helpers
        $handle = fopen('php://temp', 'r+');
        //print_r($this->columnsToFlatArray($columns));
       fputcsv($handle, $this->columnsToFlatArray($columns), $delimiter, $enclosure);
       foreach ($rows as $line) {
           $seralizedData = array();
           foreach ($line["cell"] as $cellData){

             if (!is_array($cellData)) {
                 $seralizedData[] = $cellData;
             } else {
                 $seralizedData[] = array_pop($cellData);
             }
               //$seralizedData[] = $this->cellDataToString($cellData);
           }
           fputcsv($handle, $seralizedData, $delimiter, $enclosure);
       }
       rewind($handle);
       //read the content of the csv
       $encodedData = "";
       while (!feof($handle)) {
           $encodedData .= fread($handle, 8192);
       }
       fclose($handle);
       return $encodedData;
    }

    /**
     * Returns a flat array with the list of column labels.
     * @param columns an array of column object including the property information and that is used within tao class.Table context
     */
    private function columnsToFlatArray($columns){
        $flatColumnsArray = array();
        foreach ($columns as $column){
            $flatColumnsArray[] = $column->label;
        }
        return $flatColumnsArray;
    }


     protected  function getColumns($identifier) {
    	 if (!$this->hasRequestParameter($identifier)) {
    	 	throw new common_Exception('Missing parameter "'.$identifier.'" for getColumns()');
    	 }
    	 $columns = array();
    	 foreach ($this->getRequestParameter($identifier) as $array) {
    	 	$column = tao_models_classes_table_Column::buildColumnFromArray($array);
    	 	if (!is_null($column)) {
    	 		$columns[] = $column;
    	 	}
    	 }
    	 return $columns;
    }
    
    /**
     * Data provider for the table, returns json encoded data according to the parameter
     * @author Bertrand Chevrier, <taosupport@tudor.lu>,
     */
    public function data() {
        $filter =  $this->hasRequestParameter('filter') ? $this->getFilterState('filter') : array();
       	$filterData =  $this->getRequestParameter('filterData');

    	$columns = $this->hasRequestParameter('columns') ? $this->getColumns('columns') : array();
    	$page = $this->getRequestParameter('page');
        $limit = $this->getRequestParameter('rows');
        $sidx = $this->getRequestParameter('sidx');
        $sord = $this->getRequestParameter('sord');
        $start = $limit * $page - $limit;

        $options = array (
            'recursive'=>true, 
            'like' => false, 
            'offset' => $start, 
            'limit' => $limit, 
            'order' => $sidx, 
            'orderdir' => $sord  
        );
        $response = new \stdClass();
        if($this->hasRequestParameter('implementation')){
            if (class_exists(urldecode($this->getRequestParameter('implementation')))) {
                $this->service->setImplementation(urldecode($this->getRequestParameter('implementation')));
            }
        }

        $deliveryResults = $this->service->getImplementation()->getResultByColumn(array_keys($filter), $filter, $options);
        $counti = $this->service->getImplementation()->countResultByFilter(array_keys($filter), $filter);
        foreach($deliveryResults as $deliveryResult){
            $results[] = new core_kernel_classes_Resource($deliveryResult['deliveryResultIdentifier']);
        }

        $dpmap = array();
        foreach ($columns as $column) {
            $dataprovider = $column->getDataProvider();
            $found = false;
            foreach ($dpmap as $k => $dp) {
                if ($dp['instance'] == $dataprovider) {
                    $found = true;
                    $dpmap[$k]['columns'][] = $column;
                }
            }
            if (!$found) {
                $dpmap[] = array(
                    'instance'	=> $dataprovider,
                    'columns'	=> array(
                            $column
                    )
                );
            }
        }

        foreach ($dpmap as $arr) {
            $arr['instance']->prepare($results, $arr['columns']);
        }

        foreach($results as $result) {
            $data = array(
                'id' => $result->getUri()
            );
            foreach ($columns as $column) {
                $key = null;
                if($column instanceof tao_models_classes_table_PropertyColumn){
                    $key = $column->getProperty()->getUri(); 
                } else  if ($column instanceof VariableColumn) {
                    $key =  $column->getContextIdentifier() . '_' . $column->getIdentifier();
                }
                if(!is_null($key)){
                    if (count($column->getDataProvider()->cache) > 0) {
                        $data[$key] = self::filterCellData(
                            $column->getDataProvider()->getValue($result, $column),
                            $filterData
                        );
                    } else {
                        $data[$key] = self::filterCellData($this->service->getTestTaker($result)->getLabel(), $filterData);
                    }
                }
                else {
                    \common_Logger::w('KEY IS NULL');
                }
            }
            $response->data[] = $data;
        }

        $response->page = (int)$page;
        if ($limit!=0) {
            $response->total = ceil($counti / $limit);
        } else {
            $response->total = 1;
        }
        $response->records = count($results);

        $this->returnJSON($response);
    }

    private static function filterCellData($observationsList, $filterData){
        //if the cell content is not an array with multiple entries, do not filter

        if (!(is_array($observationsList))){
            return $observationsList;

        }
        //takes only the alst or the first observation
            if (
                ($filterData=="lastSubmitted" or $filterData=="firstSubmitted")
                and
                (is_array($observationsList))
            ){
            $returnValue = array();

            //sort by timestamp observation
           uksort($observationsList, "oat\\taoOutcomeUi\\model\\ResultsService::sortTimeStamps" );
           $filteredObservation = ($filterData=='lastSubmitted') ? array_pop($observationsList) : array_shift($observationsList);
            $returnValue[]= $filteredObservation[0];

            } else {
               $cellData = "";
               foreach ($observationsList as $observation) {
                   $cellData.= $observation[0].$observation[1].'
                       ';
               }
                $returnValue = $cellData;
            }
        return $returnValue;
    }
}
?>
