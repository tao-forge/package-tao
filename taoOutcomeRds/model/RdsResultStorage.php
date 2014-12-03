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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\taoOutcomeRds\model;

use oat\taoResultServer\models\classes\ResultManagement;
use \common_Logger;
use \core_kernel_classes_Resource;
use \core_kernel_classes_Property;
use \tao_helpers_Date;
use qtism\common\datatypes\Float;
use qtism\common\enums\BaseType;
use qtism\common\enums\Cardinality;
use qtism\runtime\common\OutcomeVariable;

/**
 * Implements tao results storage using the configured persistency "taoOutcomeRds"
 *
 */
class RdsResultStorage extends \tao_models_classes_GenerisService
    implements \taoResultServer_models_classes_WritableResultStorage, \taoResultServer_models_classes_ReadableResultStorage, ResultManagement
{
    /**
     * Constantes for the database creation and data access
     *
     */
    const RESULTS_TABLENAME = "results_storage";
    const RESULTS_TABLE_ID = 'result_id';
    const TEST_TAKER_COLUMN = 'test_taker';
    const DELIVERY_COLUMN = 'delivery';


    const VARIABLES_TABLENAME = "variables_storage";
    const VARIABLES_TABLE_ID = "variable_id";
    const CALL_ID_ITEM_COLUMN = "call_id_item";
    const CALL_ID_TEST_COLUMN = "call_id_test";
    const TEST_COLUMN = "test";
    const ITEM_COLUMN = "item";
    const VARIABLE_IDENTIFIER = "identifier";
    const VARIABLE_CLASS = "class";
    const VARIABLES_FK_COLUMN = "results_result_id";
    const VARIABLES_FK_NAME = "fk_variables_results";

    const RESULT_KEY_VALUE_TABLE_NAME = "results_kv_storage";
    const KEY_COLUMN = "result_key";
    const VALUE_COLUMN = "result_value";
    const RESULTSKV_FK_COLUMN = "variables_variable_id";
    const RESULTSKV_FK_NAME = "fk_resultsKv_variables";


    /**
     * SQL persistence to use
     *
     * @var common_persistence_SqlPersistence
     */
    private $persistence;

    public function __construct()
    {
        parent::__construct();
        $this->persistence = $this->getPersistence();
    }

    private function getPersistence()
    {
        return \common_persistence_Manager::getPersistence('default');
    }

    /**
     * Store in the table all value corresponding to a key
     * @param \taoResultServer_models_classes_Variable $variable
     */
    private function storeKeysValues($variableId, \taoResultServer_models_classes_Variable $variable)
    {
        $basetype = $variable->getBaseType();
        foreach (array_keys((array)$variable) as $key) {
            $getter = 'get' . ucfirst($key);
            $value = null;
            if (method_exists($variable, $getter)) {
                $value = $variable->$getter();
                if ($key == 'value' || $key == 'candidateResponse') {
                    $value = base64_encode($value);
                }
            }
            if ($key == 'epoch' && !$variable->isSetEpoch()) {
                $value = microtime();
            }
            $this->persistence->insert(
                self::RESULT_KEY_VALUE_TABLE_NAME,
                array(
                    self::RESULTSKV_FK_COLUMN => $variableId,
                    self::KEY_COLUMN => $key,
                    self::VALUE_COLUMN => $value
                )
            );
        }
    }

    public function spawnResult()
    {
        \common_Logger::w('Unsupported function');
    }

    /**
     * Store the test variable in table and its value in key/value storage
     *
     * @param type $deliveryResultIdentifier
     *            lis_result_sourcedid
     * @param type $test
     *            ignored
     * @param \taoResultServer_models_classes_Variable $testVariable
     * @param type $callIdTest
     *            ignored
     */
    public function storeTestVariable(
        $deliveryResultIdentifier,
        $test,
        \taoResultServer_models_classes_Variable $testVariable,
        $callIdTest
    ) {
        $sql = 'SELECT COUNT(*) FROM ' . self::VARIABLES_TABLENAME .
            ' WHERE ' . self::VARIABLES_FK_COLUMN . ' = ? AND ' . self::TEST_COLUMN . ' = ?
            AND ' . self::VARIABLE_IDENTIFIER . ' = ?';
        $params = array($deliveryResultIdentifier, $test, $testVariable->getIdentifier());

        // if there is already a record for this item we update it
        if ($this->persistence->query($sql, $params)->fetchColumn() > 0) {
            $sqlUpdate = 'UPDATE ' . self::VARIABLES_TABLENAME . ' SET ' . self::CALL_ID_TEST_COLUMN . ' = ?
            WHERE ' . self::VARIABLES_FK_COLUMN . ' = ? AND ' . self::TEST_COLUMN . ' = ? AND ' . self::VARIABLE_IDENTIFIER . ' = ?';
            $paramsUpdate = array($callIdTest, $deliveryResultIdentifier, $test, $testVariable->getIdentifier());
            $this->persistence->exec($sqlUpdate, $paramsUpdate);
        } else {
            $variableClass = get_class($testVariable);

            $this->persistence->insert(
                self::VARIABLES_TABLENAME,
                array(
                    self::VARIABLES_FK_COLUMN => $deliveryResultIdentifier,
                    self::TEST_COLUMN => $test,
                    self::CALL_ID_TEST_COLUMN => $callIdTest,
                    self::VARIABLE_CLASS => $variableClass,
                    self::VARIABLE_IDENTIFIER => $testVariable->getIdentifier()
                )
            );

            $variableId = $this->persistence->lastInsertId();
            $this->storeKeysValues($variableId, $testVariable);
        }
    }

    /**
     * Store the item in table and its value in key/value storage
     * @param $deliveryResultIdentifier
     * @param $test
     * @param $item
     * @param \taoResultServer_models_classes_Variable $itemVariable
     * @param $callIdItem
     */
    public function storeItemVariable(
        $deliveryResultIdentifier,
        $test,
        $item,
        \taoResultServer_models_classes_Variable $itemVariable,
        $callIdItem
    ) {
        //store value in all case

        $variableClass = get_class($itemVariable);

        $this->persistence->insert(
            self::VARIABLES_TABLENAME,
            array(
                self::VARIABLES_FK_COLUMN => $deliveryResultIdentifier,
                self::TEST_COLUMN => $test,
                self::ITEM_COLUMN => $item,
                self::CALL_ID_ITEM_COLUMN => $callIdItem,
                self::VARIABLE_CLASS => $variableClass,
                self::VARIABLE_IDENTIFIER => $itemVariable->getIdentifier()
            )
        );

        $variableId = $this->persistence->lastInsertId();

        $this->storeKeysValues($variableId, $itemVariable);


    }

    /*
     * retrieve specific parameters from the resultserver to configure the storage
     */
    public function configure(core_kernel_classes_Resource $resultserver, $callOptions = array())
    {
        \common_Logger::w('configure : ' . implode(" ", $callOptions));
    }

    /**
     * Store test-taker doing the test
     * @param $deliveryResultIdentifier
     * @param $testTakerIdentifier
     */
    public function storeRelatedTestTaker($deliveryResultIdentifier, $testTakerIdentifier)
    {
        $sql = 'SELECT COUNT(*) FROM ' . self::RESULTS_TABLENAME .
            ' WHERE ' . self::RESULTS_TABLE_ID . ' = ?';
        $params = array($deliveryResultIdentifier);
        if ($this->persistence->query($sql, $params)->fetchColumn() == 0) {
            $this->persistence->insert(
                self::RESULTS_TABLENAME,
                array(
                    self::TEST_TAKER_COLUMN => $testTakerIdentifier,
                    self::RESULTS_TABLE_ID => $deliveryResultIdentifier
                )
            );
        } else {
            $sqlUpdate = 'UPDATE ' . self::RESULTS_TABLENAME . ' SET ' . self::TEST_TAKER_COLUMN . ' = ? WHERE ' . self::RESULTS_TABLE_ID . ' = ?';
            $paramsUpdate = array($testTakerIdentifier, $deliveryResultIdentifier);
            $this->persistence->exec($sqlUpdate, $paramsUpdate);
        }
    }

    /**
     * Store Delivery corresponding to the current test
     * @param $deliveryResultIdentifier
     * @param $deliveryIdentifier
     */
    public function storeRelatedDelivery($deliveryResultIdentifier, $deliveryIdentifier)
    {
        $sql = 'SELECT COUNT(*) FROM ' . self::RESULTS_TABLENAME .
            ' WHERE ' . self::RESULTS_TABLE_ID . ' = ?';
        $params = array($deliveryResultIdentifier);
        if ($this->persistence->query($sql, $params)->fetchColumn() == 0) {
            $this->persistence->insert(
                self::RESULTS_TABLENAME,
                array(self::DELIVERY_COLUMN => $deliveryIdentifier, self::RESULTS_TABLE_ID => $deliveryResultIdentifier)
            );
        } else {
            $sqlUpdate = 'UPDATE ' . self::RESULTS_TABLENAME . ' SET ' . self::DELIVERY_COLUMN . ' = ? WHERE ' . self::RESULTS_TABLE_ID . ' = ?';
            $paramsUpdate = array($deliveryIdentifier, $deliveryResultIdentifier);
            $this->persistence->exec($sqlUpdate, $paramsUpdate);
        }
    }

    /**
     * @param callId an item execution identifier
     * @return array keys as variableIdentifier , values is an array of observations ,
     * each observation is an object with deliveryResultIdentifier, test, taoResultServer_models_classes_Variable variable, callIdTest
     * Array
     * (
     * [LtiOutcome] => Array
     * (
     * [0] => stdClass Object
     * (
     * [deliveryResultIdentifier] => con-777:::rlid-777:::777777
     * [test] => http://tao26/tao26.rdf#i1402389674744647
     * [item] => http://tao26/tao26.rdf#i1402334674744890
     * [variable] => taoResultServer_models_classes_OutcomeVariable Object
     * (
     * [normalMaximum] =>
     * [normalMinimum] =>
     * [value] => MC41
     * [identifier] => LtiOutcome
     * [cardinality] => single
     * [baseType] => float
     * [epoch] => 0.10037600 1402390997
     * )
     * [callIdTest] => http://tao26/tao26.rdf#i14023907995907103
     * [callIdItem] => http://tao26/tao26.rdf#i14023907995907110
     * )
     *
     * )
     *
     * )
     */
    public function getVariables($callId)
    {
        $sql = 'SELECT * FROM ' . self::VARIABLES_TABLENAME . ', ' . self::RESULT_KEY_VALUE_TABLE_NAME . '
        WHERE (' . self::CALL_ID_ITEM_COLUMN . ' = ? OR ' . self::CALL_ID_TEST_COLUMN . ' = ?) AND ' . self::VARIABLES_TABLE_ID . ' = ' . self::RESULTSKV_FK_COLUMN;
        $params = array($callId, $callId);
        $variables = $this->persistence->query($sql, $params)->fetchAll(\PDO::FETCH_ASSOC);

        $returnValue = array();

        // for each variable we construct the array
        $lastVariable = array();


        foreach ($variables as $variable) {

            if (empty($lastVariable)) {
                $lastVariable = $variable;
                if (class_exists($lastVariable[self::VARIABLE_CLASS])) {
                    $resultVariable = new $lastVariable[self::VARIABLE_CLASS]();
                } else {
                    $resultVariable = new \taoResultServer_models_classes_OutcomeVariable();
                }
            }

            // store variable from 0 to n-1
            if ($lastVariable[self::VARIABLES_TABLE_ID] != $variable[self::VARIABLES_TABLE_ID]) {
                $object = new \stdClass();
                $object->uri = $lastVariable[self::VARIABLES_TABLE_ID];
                $object->class = $lastVariable[self::VARIABLE_CLASS];
                $object->deliveryResultIdentifier = $lastVariable[self::VARIABLES_FK_COLUMN];
                $object->callIdItem = $lastVariable[self::CALL_ID_ITEM_COLUMN];
                $object->callIdTest = $lastVariable[self::CALL_ID_TEST_COLUMN];
                $object->test = $lastVariable[self::TEST_COLUMN];
                $object->item = $lastVariable[self::ITEM_COLUMN];
                $object->variable = clone $resultVariable;
                $returnValue[$lastVariable[self::VARIABLES_TABLE_ID]][] = $object;
                $lastVariable = $variable;
                if (class_exists($lastVariable[self::VARIABLE_CLASS])) {
                    $resultVariable = new $lastVariable[self::VARIABLE_CLASS]();
                } else {
                    $resultVariable = new \taoResultServer_models_classes_OutcomeVariable();
                }
            }

            $setter = 'set' . ucfirst($variable[self::KEY_COLUMN]);
            $value = $variable[self::VALUE_COLUMN];

            if (method_exists($resultVariable, $setter) && !is_null($value)) {
                if ($variable[self::KEY_COLUMN] == 'value' || $variable[self::KEY_COLUMN] == 'candidateResponse') {
                    $value = base64_decode($value);
                }

                $resultVariable->$setter($value);
            }

        }
        if (count($variables) > 0) {
            // store the variable n
            $object = new \stdClass();
            $object->uri = $lastVariable[self::VARIABLES_TABLE_ID];
            $object->class = $lastVariable[self::VARIABLE_CLASS];
            $object->deliveryResultIdentifier = $lastVariable[self::VARIABLES_FK_COLUMN];
            $object->callIdItem = $lastVariable[self::CALL_ID_ITEM_COLUMN];
            $object->callIdTest = $lastVariable[self::CALL_ID_TEST_COLUMN];
            $object->test = $lastVariable[self::TEST_COLUMN];
            $object->item = $lastVariable[self::ITEM_COLUMN];
            $object->variable = clone $resultVariable;
            $returnValue[$lastVariable[self::VARIABLES_TABLE_ID]][] = $object;
        }

        return $returnValue;
    }

    /**
     * Get a variable from callId and Variable identifier
     * @param $callId
     * @param $variableIdentifier
     * @return array
     */
    public function getVariable($callId, $variableIdentifier)
    {
        $sql = 'SELECT * FROM ' . self::VARIABLES_TABLENAME . ', ' . self::RESULT_KEY_VALUE_TABLE_NAME . '
        WHERE (' . self::CALL_ID_ITEM_COLUMN . ' = ? OR ' . self::CALL_ID_TEST_COLUMN . ' = ?)
        AND ' . self::VARIABLES_TABLE_ID . ' = ' . self::RESULTSKV_FK_COLUMN . ' AND ' . self::VARIABLE_IDENTIFIER . ' = ?';

        $params = array($callId, $callId, $variableIdentifier);
        $variables = $this->persistence->query($sql, $params)->fetchAll(\PDO::FETCH_ASSOC);

        $returnValue = array();

        // for each variable we construct the array
        $lastVariable = array();
        foreach ($variables as $variable) {
            if (empty($lastVariable)) {
                $lastVariable = $variable;
                if (class_exists($lastVariable[self::VARIABLE_CLASS])) {
                    $resultVariable = new $lastVariable[self::VARIABLE_CLASS]();
                } else {
                    $resultVariable = new \taoResultServer_models_classes_OutcomeVariable();
                }
            }
            if ($lastVariable[self::VARIABLES_TABLE_ID] != $variable[self::VARIABLES_TABLE_ID]) {
                $object = new \stdClass();
                $object->uri = $lastVariable[self::VARIABLES_TABLE_ID];
                $object->class = $lastVariable[self::VARIABLE_CLASS];
                $object->deliveryResultIdentifier = $lastVariable[self::VARIABLES_FK_COLUMN];
                $object->callIdItem = $lastVariable[self::CALL_ID_ITEM_COLUMN];
                $object->callIdTest = $lastVariable[self::CALL_ID_TEST_COLUMN];
                $object->test = $lastVariable[self::TEST_COLUMN];
                $object->item = $lastVariable[self::ITEM_COLUMN];
                $object->variable = clone $resultVariable;
                $returnValue[$lastVariable[self::VARIABLES_TABLE_ID]][] = $object;

                $lastVariable = $variable;
                if (class_exists($lastVariable[self::VARIABLE_CLASS])) {
                    $resultVariable = new $lastVariable[self::VARIABLE_CLASS]();
                } else {
                    $resultVariable = new \taoResultServer_models_classes_OutcomeVariable();
                }
            }

            $setter = 'set' . ucfirst($variable[self::KEY_COLUMN]);
            $value = $variable[self::VALUE_COLUMN];
            if (method_exists($resultVariable, $setter) && !is_null($value)) {
                if ($variable[self::KEY_COLUMN] == 'value' || $variable[self::KEY_COLUMN] == 'candidateResponse') {
                    $value = base64_decode($value);
                }

                $resultVariable->$setter($value);
            }

        }

        // store the variable n
        if (count($variables) > 0) {
            $object = new \stdClass();
            $object->uri = $lastVariable[self::VARIABLES_TABLE_ID];
            $object->class = $lastVariable[self::VARIABLE_CLASS];
            $object->deliveryResultIdentifier = $lastVariable[self::VARIABLES_FK_COLUMN];
            $object->callIdItem = $lastVariable[self::CALL_ID_ITEM_COLUMN];
            $object->callIdTest = $lastVariable[self::CALL_ID_TEST_COLUMN];
            $object->test = $lastVariable[self::TEST_COLUMN];
            $object->item = $lastVariable[self::ITEM_COLUMN];
            $object->variable = clone $resultVariable;
            $returnValue[$lastVariable[self::VARIABLES_TABLE_ID]][] = $object;
        }
        return $returnValue;

    }

    public function getVariableProperty($variableId, $property)
    {
        $sql = 'SELECT ' . self::VALUE_COLUMN . ' FROM ' . self::RESULT_KEY_VALUE_TABLE_NAME . '
        WHERE ' . self::RESULTSKV_FK_COLUMN . ' = ? AND ' . self::KEY_COLUMN . ' = ?';
        $params = array($variableId, $property);
        return $this->persistence->query($sql, $params)->fetchColumn();

    }

    /**
     * get test-taker corresponding to a result
     * @param $deliveryResultIdentifier
     * @return mixed
     */
    public function getTestTaker($deliveryResultIdentifier)
    {
        $sql = 'SELECT ' . self::TEST_TAKER_COLUMN . ' FROM ' . self::RESULTS_TABLENAME . ' WHERE ' . self::RESULTS_TABLE_ID . ' = ?';
        $params = array($deliveryResultIdentifier);
        return $this->persistence->query($sql, $params)->fetchColumn();
    }

    /**
     * get delivery corresponding to a result
     * @param $deliveryResultIdentifier
     * @return mixed
     */
    public function getDelivery($deliveryResultIdentifier)
    {
        $sql = 'SELECT ' . self::DELIVERY_COLUMN . ' FROM ' . self::RESULTS_TABLENAME . ' WHERE ' . self::RESULTS_TABLE_ID . ' = ?';
        $params = array($deliveryResultIdentifier);
        return $this->persistence->query($sql, $params)->fetchColumn();
    }

    /**
     * @return array the list of item executions ids (across all results)
     * o(n) do not use real time (postprocessing)
     */
    public function getAllCallIds()
    {
        $returnValue = array();
        $sql = 'SELECT DISTINCT(' . self::CALL_ID_ITEM_COLUMN . '), ' . self::CALL_ID_TEST_COLUMN . ', ' . self::VARIABLES_FK_COLUMN . ' FROM ' . self::VARIABLES_TABLENAME;
        foreach ($this->persistence->query($sql)->fetchAll(\PDO::FETCH_ASSOC) as $value) {
            $returnValue[] = ($value[self::CALL_ID_ITEM_COLUMN] != "") ? $value[self::CALL_ID_ITEM_COLUMN] : $value[self::CALL_ID_TEST_COLUMN];
        }

        return $returnValue;
    }

    /**
     * @param $deliveryResultIdentifier
     * @return array the list of item executions ids related to a delivery result
     */
    public function getRelatedItemCallIds($deliveryResultIdentifier)
    {
        $returnValue = array();
        $sql = 'SELECT DISTINCT(' . self::CALL_ID_ITEM_COLUMN . ') FROM ' . self::VARIABLES_TABLENAME . '
        WHERE ' . self::VARIABLES_FK_COLUMN . ' = ? AND ' . self::CALL_ID_ITEM_COLUMN . ' <> \'\'';
        $params = array($deliveryResultIdentifier);
        foreach ($this->persistence->query($sql, $params)->fetchAll(\PDO::FETCH_ASSOC) as $value) {
            $returnValue[] = $value[self::CALL_ID_ITEM_COLUMN];
        }

        return $returnValue;
    }

    /**
     * (non-PHPdoc)
     * @see taoResultServer_models_classes_ReadableResultStorage::getAllTestTakerIds()
     */
    public function getAllTestTakerIds()
    {
        $returnValue = array();
        $sql = 'SELECT ' . self::RESULTS_TABLE_ID . ', ' . self::TEST_TAKER_COLUMN . ' FROM ' . self::RESULTS_TABLENAME;
        foreach ($this->persistence->query($sql)->fetchAll(\PDO::FETCH_ASSOC) as $value) {
            $returnValue[] = array(
                "deliveryResultIdentifier" => $value[self::RESULTS_TABLE_ID],
                "testTakerIdentifier" => $value[self::TEST_TAKER_COLUMN]
            );
        }
        return $returnValue;
    }

    /**
     * (non-PHPdoc)
     * @see taoResultServer_models_classes_ReadableResultStorage::getAllDeliveryIds()
     */
    public function getAllDeliveryIds()
    {
        $returnValue = array();
        $sql = 'SELECT ' . self::RESULTS_TABLE_ID . ', ' . self::DELIVERY_COLUMN . ' FROM ' . self::RESULTS_TABLENAME;
        foreach ($this->persistence->query($sql)->fetchAll(\PDO::FETCH_ASSOC) as $value) {
            $returnValue[] = array(
                "deliveryResultIdentifier" => $value[self::RESULTS_TABLE_ID],
                "deliveryIdentifier" => $value[self::DELIVERY_COLUMN]
            );
        }
        return $returnValue;
    }

    /**
     * order orderdir, offset, limit
     */
    public function getResultByColumn($columns, $filter, $options = array())
    {
        $returnValue = array();
        $sql = 'SELECT * FROM ' . self::RESULTS_TABLENAME;
        $params = array();


        if (count($columns) > 0) {
            $sql .= ' WHERE ';
        }

        if (in_array(PROPERTY_RESULT_OF_DELIVERY, $columns)) {
            $inQuery = implode(',', array_fill(0, count($filter[PROPERTY_RESULT_OF_DELIVERY]), '?'));
            $sql .= self::DELIVERY_COLUMN . ' IN (' . $inQuery . ')';
            $params = array_merge($params, $filter[PROPERTY_RESULT_OF_DELIVERY]);
        }

        if (count($columns) > 1) {
            $sql .= ' AND ';
        }

        if (in_array(PROPERTY_RESULT_OF_SUBJECT, $columns)) {
            $inQuery = implode(',', array_fill(0, count($filter[PROPERTY_RESULT_OF_SUBJECT]), '?'));
            $sql .= self::TEST_TAKER_COLUMN . ' IN (' . $inQuery . ')';
            $params = array_merge($params, $filter[PROPERTY_RESULT_OF_SUBJECT]);
        }

        if(isset($options['order'])){
            $sql .= ' ORDER BY ?';
            $params[] = $options['order'];
            if(isset($options['oderdir'])){
                $sql .= ' ?';
                $params[] = $options['orderdir'];
            }
        }
        if(isset($options['offset']) || isset($options['limit'])){
            $offset = (isset($options['offset']))?$options['offset']:0;
            $limit = (isset($options['limit']))?$options['limit']:1000;
            $sql .= ' LIMIT ?,?';
            $params = array_merge($params, array($offset, $limit));
        }

        foreach ($this->persistence->query($sql, $params)->fetchAll(\PDO::FETCH_ASSOC) as $value) {
            $returnValue[] = array(
                "deliveryResultIdentifier" => $value[self::RESULTS_TABLE_ID],
                "testTakerIdentifier" => $value[self::TEST_TAKER_COLUMN],
                "deliveryIdentifier" => $value[self::DELIVERY_COLUMN]
            );
        }
        return $returnValue;

    }

    public function countResultByFilter($columns, $filter){
        $returnValue = array();
        $sql = 'SELECT COUNT(*) FROM ' . self::RESULTS_TABLENAME;
        $params = array();


        if (count($columns) > 0) {
            $sql .= ' WHERE ';
        }

        if (in_array(PROPERTY_RESULT_OF_DELIVERY, $columns)) {
            $inQuery = implode(',', array_fill(0, count($filter[PROPERTY_RESULT_OF_DELIVERY]), '?'));
            $sql .= self::DELIVERY_COLUMN . ' IN (' . $inQuery . ')';
            $params = array_merge($params, $filter[PROPERTY_RESULT_OF_DELIVERY]);
        }

        if (count($columns) > 1) {
            $sql .= ' AND ';
        }

        if (in_array(PROPERTY_RESULT_OF_SUBJECT, $columns)) {
            $inQuery = implode(',', array_fill(0, count($filter[PROPERTY_RESULT_OF_SUBJECT]), '?'));
            $sql .= self::TEST_TAKER_COLUMN . ' IN (' . $inQuery . ')';
            $params = array_merge($params, $filter[PROPERTY_RESULT_OF_SUBJECT]);
        }

        return $this->persistence->query($sql, $params)->fetchColumn();
    }


    /**
     * Remove the result and all the related variables
     * @param $deliveryResultIdentifier
     * @return bool
     */
    public function deleteResult($deliveryResultIdentifier)
    {
        // get all the variables related to the result
        $sql = 'SELECT ' . self::VARIABLES_TABLE_ID . ' FROM ' . self::VARIABLES_TABLENAME . '
        WHERE ' . self::VARIABLES_FK_COLUMN . ' = ?';
        $variables = $this->persistence->query($sql, array($deliveryResultIdentifier))->fetchAll(\PDO::FETCH_ASSOC);

        // delete key/value for each variable
        foreach ($variables as $variable) {
            $sql = 'DELETE FROM ' . self::RESULT_KEY_VALUE_TABLE_NAME . '
            WHERE ' . self::RESULTSKV_FK_COLUMN . ' = ?';

            if ($this->persistence->exec($sql, array($variable[self::VARIABLES_TABLE_ID])) === false) {
                return false;
            }
        }

        // remove variables
        $sql = 'DELETE FROM ' . self::VARIABLES_TABLENAME . '
            WHERE ' . self::VARIABLES_FK_COLUMN . ' = ?';

        if ($this->persistence->exec($sql, array($deliveryResultIdentifier)) === false) {
            return false;
        }

        // remove results
        $sql = 'DELETE FROM ' . self::RESULTS_TABLENAME . '
            WHERE ' . self::RESULTS_TABLE_ID . ' = ?';

        if ($this->persistence->exec($sql, array($deliveryResultIdentifier)) === false) {
            return false;
        }

        return true;
    }

    public function getItemFromItemResult($itemResult) {
        $items = $this->getVariables($itemResult);
        $tmp = array_shift($items);
        return new core_kernel_classes_Resource($tmp[0]->item);
    }

    public function getDeliveryResultVariables($deliveryResultIdentifier) {
        $sql = 'SELECT * FROM ' . self::VARIABLES_TABLENAME . ', ' . self::RESULT_KEY_VALUE_TABLE_NAME . 
        ' WHERE ' . self::CALL_ID_TEST_COLUMN . ' = ? AND ' . self::VARIABLES_TABLE_ID . ' = ' . self::RESULTSKV_FK_COLUMN;
        $params = array($deliveryResultIdentifier);
        $variables = $this->persistence->query($sql, $params)->fetchAll(\PDO::FETCH_ASSOC);

        $variableData = array();
        $returnValue  = array();

        foreach ($variables as $variable) {
            $variableData[$variable[self::VARIABLES_TABLE_ID]][$variable[self::KEY_COLUMN]] = $variable[self::VALUE_COLUMN];
        }

        foreach ($variableData as $variable) {
            $returnValue[] = new OutcomeVariable(
                $variable['identifier'],
                Cardinality::getConstantByName($variable['cardinality']),
                BaseType::getConstantByName($variable['baseType']),
                new Float((float) base64_decode($variable['value']))
            );
        }

        return $returnValue;

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
     * returns the enum class type
     * 
     * @param string $className
     * @throws common_exception_Error
     */
    private function getVariableType($className) {
        switch ($className) {
            case "taoResultServer_models_classes_OutcomeVariable":
                return CLASS_OUTCOME_VARIABLE;
            case "taoResultServer_models_classes_ResponseVariable":
                return CLASS_RESPONSE_VARIABLE;
            case "taoResultServer_models_classes_TraceVariable":
                return CLASS_TRACE_VARIABLE;
            default:
                throw new \common_exception_Error("The variable class is not supported");
        }
    }

    public function getDeliveryItemVariables($deliveryResultIdentifier, $filter) {
        $undefinedStr = __('unknown'); //some data may have not been submitted

        $itemResults = $this->getRelatedItemCallIds($deliveryResultIdentifier);

        $variablesByItem = array();
        $numberOfResponseVariables = 0;
        $numberOfCorrectResponseVariables = 0;
        $numberOfInCorrectResponseVariables = 0;
        $numberOfUnscoredResponseVariables = 0;
        $numberOfOutcomeVariables = 0;

        foreach ($itemResults as $itemResult) {
            try {
                common_Logger::d("Retrieving related Item for itemResult " . $itemResult);
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
                    $modelProperty = $relatedItem->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY));
                    $itemModel = $modelProperty->getLabel();
                } catch (common_Exception $e) {
                    $itemModel = $undefinedStr;
                }

            } else {
                $itemIdentifier = $undefinedStr;
                $itemLabel = $undefinedStr;
                $itemModel = $undefinedStr;
            }

            $variables = array();

            foreach ($this->getVariables($itemResult) as $data) {

                $variable = $data[0]->variable;
                $variableType = $this->getVariableType(get_class($variable));
                $response = false;
                $outcome  = false;

                switch ($variableType) {
                    case CLASS_RESPONSE_VARIABLE:
                        ++$numberOfResponseVariables;

                        $response = $variable->getCorrectResponse();
                        if (empty($response)) {
                            ++$numberOfUnscoredResponseVariables;
                            $response = "unscored";
                        } else {
                            if($response >= 1){
                                ++$numberOfCorrectResponseVariables;
                                $response = "correct";
                            }
                            else{
                                ++$numberOfInCorrectResponseVariables;
                                $response = "incorrect";
                            }
                        }

                        break;
                    case CLASS_OUTCOME_VARIABLE:
                        ++$numberOfOutcomeVariables;
                        $outcome = array(base64_decode($variable->getValue()));
                        break;
                }

                $variables[$variableType][$variable->getIdentifier()][$variable->getEpoch()] = array(
                    'variable'  => $variable,
                    'outcome'   => $outcome,
                    'isCorrect' => $response,
                    'uri'       => $data[0]->uri
                );

            }

            $variablesByItem[$itemIdentifier] = array(
                'itemModel'  => $itemModel,
                'label'      => $itemLabel,
                'sortedVars' => $variables
            );

        }

        //sort by epoch and filter
        foreach ($variablesByItem as $itemIdentifier => $itemVariables) {

            foreach ($itemVariables['sortedVars'] as $type => $variables) {
                foreach ($variables as $variableIdentifier => $observation) {

                    uksort($variablesByItem[$itemIdentifier]['sortedVars'][$type][$variableIdentifier], "self::sortTimeStamps");

                    switch ($filter) {
                        case "lastSubmitted": {
                            $variablesByItem[$itemIdentifier]['sortedVars'][$type][$variableIdentifier] = array(array_pop($variablesByItem[$itemIdentifier]['sortedVars'][$type][$variableIdentifier]));
                            break;
                        }
                        case "firstSubmitted": {
                            $variablesByItem[$itemIdentifier]['sortedVars'][$type][$variableIdentifier] = array(array_shift($variablesByItem[$itemIdentifier]['sortedVars'][$type][$variableIdentifier]));
                            break;
                        }
                    }
                }
            }
        }

        return array(
            "nbResponses" => $numberOfResponseVariables,
            "nbCorrectResponses" => $numberOfCorrectResponseVariables,
            "nbIncorrectResponses" => $numberOfInCorrectResponseVariables,
            "nbUnscoredResponses" => $numberOfUnscoredResponseVariables,
            "data" => $variablesByItem
        );
    }

}
