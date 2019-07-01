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

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use oat\oatbox\service\ConfigurableService;
use oat\taoResultServer\models\classes\ResultDeliveryExecutionDelete;
use oat\taoResultServer\models\classes\ResultManagement;
use taoResultServer_models_classes_Variable as Variable;

/**
 * Implements tao results storage using the configured persistency "taoOutcomeRds"
 */
class RdsResultStorage extends ConfigurableService
    implements \taoResultServer_models_classes_WritableResultStorage, \taoResultServer_models_classes_ReadableResultStorage, ResultManagement
{
    use ResultDeliveryExecutionDelete;

    const SERVICE_ID = 'taoOutcomeRds/RdsResultStorage';

    /**
     * Constants for the database creation and data access
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
    const VARIABLE_VALUE = "value";
    const VARIABLE_IDENTIFIER = "identifier";

    const CALL_ID_ITEM_INDEX = "idx_variables_storage_call_id_item";
    const CALL_ID_TEST_INDEX = "idx_variables_storage_call_id_test";

    /** @deprecated */
    const VARIABLE_CLASS = "class";
    const VARIABLES_FK_COLUMN = "results_result_id";
    const VARIABLES_FK_NAME = "fk_variables_results";

    /** @deprecated */
    const RESULT_KEY_VALUE_TABLE_NAME = "results_kv_storage";
    /** @deprecated */
    const KEY_COLUMN = "result_key";
    /** @deprecated */
    const VALUE_COLUMN = "result_value";
    /** @deprecated */
    const RESULTSKV_FK_COLUMN = "variables_variable_id";
    /** @deprecated */
    const RESULTSKV_FK_NAME = "fk_resultsKv_variables";

    /** result storage persistence identifier */
    const OPTION_PERSISTENCE = 'persistence';

    // Fields for results retrieval.
    const FIELD_DELIVERY_RESULT = 'deliveryResultIdentifier';
    const FIELD_TEST_TAKER = 'testTakerIdentifier';
    const FIELD_DELIVERY = 'deliveryIdentifier';

    public function storeTestVariable($deliveryResultIdentifier, $test, Variable $testVariable, $callIdTest)
    {
        $this->storeTestVariables($deliveryResultIdentifier, $test, [$testVariable], $callIdTest);
    }

    /**
     * @inheritdoc
     * Stores the test variables in table and their values in key/value storage.
     */
    public function storeTestVariables($deliveryResultIdentifier, $test, array $testVariables, $callIdTest)
    {
        $dataToInsert = [];

        foreach ($testVariables as $testVariable) {
            $dataToInsert[] = $this->prepareTestVariableData(
                $deliveryResultIdentifier,
                $test,
                $testVariable,
                $callIdTest
            );
        };

        $this->getPersistence()->insertMultiple(self::VARIABLES_TABLENAME, $dataToInsert);
    }

    /**
     * @inheritdoc
     * Stores the item in table and its value in key/value storage.
     */
    public function storeItemVariable($deliveryResultIdentifier, $test, $item, Variable $itemVariable, $callIdItem)
    {
        $this->storeItemVariables($deliveryResultIdentifier, $test, $item, [$itemVariable], $callIdItem);
    }

    /**
     * Stores the item variables in table and their values in key/value storage.
     * @param string $deliveryResultIdentifier
     * @param string $test
     * @param string $item
     * @param array $itemVariables
     * @param string $callIdItem
     */
    public function storeItemVariables($deliveryResultIdentifier, $test, $item, array $itemVariables, $callIdItem)
    {
        $dataToInsert = [];

        foreach ($itemVariables as $itemVariable) {
            $dataToInsert[] = $this->prepareItemVariableData(
                $deliveryResultIdentifier,
                $test,
                $item,
                $itemVariable,
                $callIdItem
            );
        }

        $this->getPersistence()->insertMultiple(self::VARIABLES_TABLENAME, $dataToInsert);
    }

    public function storeRelatedTestTaker($deliveryResultIdentifier, $testTakerIdentifier)
    {
        $this->storeRelatedData($deliveryResultIdentifier, self::TEST_TAKER_COLUMN, $testTakerIdentifier);
    }

    public function storeRelatedDelivery($deliveryResultIdentifier, $deliveryIdentifier)
    {
        $this->storeRelatedData($deliveryResultIdentifier, self::DELIVERY_COLUMN, $deliveryIdentifier);
    }

    /**
     * Store Delivery corresponding to the current test
     * @param string $deliveryResultIdentifier
     * @param string $relatedField
     * @param string $relatedIdentifier
     */
    public function storeRelatedData($deliveryResultIdentifier, $relatedField, $relatedIdentifier)
    {
        $qb = $this->getQueryBuilder()
            ->select('COUNT(*)')
            ->from(self::RESULTS_TABLENAME)
            ->andWhere(self::RESULTS_TABLE_ID .' = :id')
            ->setParameter('id', $deliveryResultIdentifier);
        if ((int) $qb->execute()->fetchColumn() === 0) {
            $this->getPersistence()->insert(
                self::RESULTS_TABLENAME,
                [
                    self::RESULTS_TABLE_ID => $deliveryResultIdentifier,
                    $relatedField => $relatedIdentifier,
                ]
            );
        } else {
            $sqlUpdate = 'UPDATE ' . self::RESULTS_TABLENAME . ' SET ' . $relatedField . ' = ? WHERE ' . self::RESULTS_TABLE_ID . ' = ?';
            $paramsUpdate = array($relatedIdentifier, $deliveryResultIdentifier);
            $this->getPersistence()->exec($sqlUpdate, $paramsUpdate);
        }
    }

    public function getVariables($callId)
    {
        if (!is_array($callId)) {
            $callId = [$callId];
        }

        $qb = $this->getQueryBuilder()
            ->select('*')
            ->from(self::VARIABLES_TABLENAME)
            ->andWhere(self::CALL_ID_ITEM_COLUMN .' IN (:ids) OR ' . self::CALL_ID_TEST_COLUMN .' IN (:ids)')
            ->orderBy(self::VARIABLES_TABLE_ID)
            ->setParameter('ids', $callId, Connection::PARAM_STR_ARRAY);

        $returnValue = [];
        foreach ($qb->execute()->fetchAll() as $variable) {
            $returnValue[$variable[self::VARIABLES_TABLE_ID]][] = $this->getResultRow($variable);
        }

        return $returnValue;
    }

    public function getDeliveryVariables($deliveryResultIdentifier)
    {
        if (!is_array($deliveryResultIdentifier)) {
            $deliveryResultIdentifier = [$deliveryResultIdentifier];
        }

        $qb = $this->getQueryBuilder()
            ->select('*')
            ->from(self::VARIABLES_TABLENAME)
            ->andWhere(self::VARIABLES_FK_COLUMN .' IN (:ids)')
            ->orderBy(self::VARIABLES_TABLE_ID)
            ->setParameter('ids', $deliveryResultIdentifier, Connection::PARAM_STR_ARRAY);

        $returnValue = [];
        foreach ($qb->execute()->fetchAll() as $variable) {
            $returnValue[$variable[self::VARIABLES_TABLE_ID]][] = $this->getResultRow($variable);
        }

        return $returnValue;
    }

    public function getVariable($callId, $variableIdentifier)
    {
        $qb = $this->getQueryBuilder()
            ->select('*')
            ->from(self::VARIABLES_TABLENAME)
            ->andWhere(self::CALL_ID_ITEM_COLUMN .' = :callId OR ' . self::CALL_ID_TEST_COLUMN . ' = :callId')
            ->andWhere(self::VARIABLE_IDENTIFIER . ' = :variableId')
            ->setParameter('callId', $callId)
            ->setParameter('variableId', $variableIdentifier);

        $returnValue = [];
        foreach ($qb->execute()->fetchAll() as $variable) {
            $returnValue[$variable[self::VARIABLES_TABLE_ID]] = $this->getResultRow($variable);
        }

        return $returnValue;
    }

    public function getVariableProperty($variableId, $property)
    {
        $qb = $this->getQueryBuilder()
            ->select(self::VARIABLE_VALUE)
            ->from(self::VARIABLES_TABLENAME)
            ->andWhere(self::VARIABLES_TABLE_ID .' = :variableId')
            ->setParameter('variableId', $variableId);

        $variableValue = $qb->execute()->fetchColumn();
        $variableValue = $this->unserializeVariableValue($variableValue);
        $getter = 'get' . ucfirst($property);
        if(is_callable([$variableValue, $getter])) {
            return $variableValue->$getter();
        }

        return null;
    }

    public function getTestTaker($deliveryResultIdentifier)
    {
        return $this->getRelatedData($deliveryResultIdentifier, self::TEST_TAKER_COLUMN);
    }

    public function getDelivery($deliveryResultIdentifier)
    {
        return $this->getRelatedData($deliveryResultIdentifier, self::DELIVERY_COLUMN);
    }

    /**
     * Retrieves data related to a result.
     *
     * @param string $deliveryResultIdentifier
     * @param string $field
     * @return mixed
     */
    public function getRelatedData($deliveryResultIdentifier, $field)
    {
        $qb = $this->getQueryBuilder()
            ->select($field)
            ->from(self::RESULTS_TABLENAME)
            ->andWhere(self::RESULTS_TABLE_ID .' = :id')
            ->setParameter('id', $deliveryResultIdentifier);

        return $qb->execute()->fetchColumn();
    }

    /**
     * @inheritdoc
     * o(n) do not use real time (postprocessing)
     */
    public function getAllCallIds()
    {
        $qb = $this->getQueryBuilder()
            ->select('DISTINCT(' . self::CALL_ID_ITEM_COLUMN . '), ' . self::CALL_ID_TEST_COLUMN . ', ' . self::VARIABLES_FK_COLUMN)
            ->from(self::VARIABLES_TABLENAME);

        $returnValue = [];
        foreach ($qb->execute()->fetchAll() as $value) {
            $returnValue[] = ($value[self::CALL_ID_ITEM_COLUMN] != "") ? $value[self::CALL_ID_ITEM_COLUMN] : $value[self::CALL_ID_TEST_COLUMN];
        }

        return $returnValue;
    }

    public function getRelatedItemCallIds($deliveryResultIdentifier)
    {
        return $this->getRelatedCallIds($deliveryResultIdentifier, self::CALL_ID_ITEM_COLUMN);
    }

    public function getRelatedTestCallIds($deliveryResultIdentifier)
    {
        return $this->getRelatedCallIds($deliveryResultIdentifier, self::CALL_ID_TEST_COLUMN);
    }

    public function getRelatedCallIds($deliveryResultIdentifier, $field)
    {
        $qb = $this->getQueryBuilder()
            ->select('DISTINCT(' . $field . ')')
            ->from(self::VARIABLES_TABLENAME)
            ->andWhere(self::VARIABLES_FK_COLUMN . ' = :id AND ' . $field . ' <> :field')
            ->setParameter('id', $deliveryResultIdentifier)
            ->setParameter('field', '');

        $returnValue = [];
        foreach ($qb->execute()->fetchAll() as $value) {
            if(isset($value[$field])){
                $returnValue[] = $value[$field];
            }
        }

        return $returnValue;
    }

    public function getAllTestTakerIds()
    {
        return $this->getAllIds(self::FIELD_TEST_TAKER, self::TEST_TAKER_COLUMN);
    }

    public function getAllDeliveryIds()
    {
        return $this->getAllIds(self::FIELD_DELIVERY, self::DELIVERY_COLUMN);
    }

    public function getAllIds($fieldName, $field)
    {
        $qb = $this->getQueryBuilder()
            ->select(self::RESULTS_TABLE_ID . ', ' . $field)
            ->from(self::RESULTS_TABLENAME);

        $returnValue = [];
        foreach ($qb->execute()->fetchAll() as $value) {
            $returnValue[] = [
                self::FIELD_DELIVERY_RESULT => $value[self::RESULTS_TABLE_ID],
                $fieldName => $value[$field],
            ];
        }

        return $returnValue;
    }

    public function getResultByDelivery($delivery, $options = array())
    {
        if (!is_array($delivery)) {
            $delivery = [$delivery];
        }
        $qb = $this->getQueryBuilder()
            ->select('*')
            ->from(self::RESULTS_TABLENAME)
            ->orderBy($this->getOrderField($options), $this->getOrderDirection($options))
            ->setMaxResults(isset($options['limit']) ? $options['limit'] : 1000)
            ->setFirstResult(isset($options['offset']) ? $options['offset'] : 0);

        if (count($delivery) > 0) {
            $qb
                ->andWhere(self::DELIVERY_COLUMN .' IN (:delivery)')
                ->setParameter(':delivery', $delivery, Connection::PARAM_STR_ARRAY);
        }

        $returnValue = [];
        foreach ($qb->execute()->fetchAll() as $value) {
            $returnValue[] = [
                self::FIELD_DELIVERY_RESULT => $value[self::RESULTS_TABLE_ID],
                self::FIELD_TEST_TAKER => $value[self::TEST_TAKER_COLUMN],
                self::FIELD_DELIVERY => $value[self::DELIVERY_COLUMN],
            ];
        }

        return $returnValue;
    }

    /**
     * Generates and sanitize ORDER BY field.
     *
     * @param array $options
     *
     * @return string
     */
    protected function getOrderField(array $options)
    {
        $allowedOrderFields = [self::DELIVERY_COLUMN, self::TEST_TAKER_COLUMN, self::RESULTS_TABLE_ID];

        if (isset($options['order']) && in_array($options['order'], $allowedOrderFields)) {
            return $options['order'];
        }

        return self::RESULTS_TABLE_ID;
    }

    /**
     * Generates and sanitize ORDER BY direction.
     *
     * @param array $options
     *
     * @return string
     */
    protected function getOrderDirection(array $options)
    {
        $allowedOrderDirections = ['ASC', 'DESC'];

        if (isset($options['orderdir']) && in_array(strtoupper($options['orderdir']), $allowedOrderDirections)) {
            return $options['orderdir'];
        }

        return 'ASC';
    }

    public function countResultByDelivery($delivery)
    {
        if (!is_array($delivery)) {
            $delivery = [$delivery];
        }
        $qb = $this->getQueryBuilder()
            ->select('COUNT(*)')
            ->from(self::RESULTS_TABLENAME);

        if (count($delivery) > 0) {
            $qb
                ->andWhere(self::DELIVERY_COLUMN .' IN (:delivery)')
                ->setParameter('delivery', $delivery, Connection::PARAM_STR_ARRAY);
        }

        return $qb->execute()->fetchColumn();
    }

    public function deleteResult($deliveryResultIdentifier)
    {
        // remove variables
        $sql = 'DELETE FROM ' . self::VARIABLES_TABLENAME . '
            WHERE ' . self::VARIABLES_FK_COLUMN . ' = ?';

        if ($this->getPersistence()->exec($sql, array($deliveryResultIdentifier)) === false) {
            return false;
        }

        // remove results
        $sql = 'DELETE FROM ' . self::RESULTS_TABLENAME . '
            WHERE ' . self::RESULTS_TABLE_ID . ' = ?';

        if ($this->getPersistence()->exec($sql, array($deliveryResultIdentifier)) === false) {
            return false;
        }

        return true;
    }

    /**
     * Prepares data to be inserted in database.
     *
     * @param string   $deliveryResultIdentifier
     * @param string   $test
     * @param string   $item
     * @param Variable $variable
     * @param string   $callId
     *
     * @return array
     */
    protected function prepareItemVariableData($deliveryResultIdentifier, $test, $item, Variable $variable, $callId)
    {
        $variableData = $this->prepareVariableData($deliveryResultIdentifier, $test, $variable);
        $variableData[self::ITEM_COLUMN] = $item;
        $variableData[self::CALL_ID_ITEM_COLUMN] = $callId;

        return $variableData;
    }

    /**
     * Prepares data to be inserted in database.
     *
     * @param string   $deliveryResultIdentifier
     * @param string   $test
     * @param Variable $variable
     * @param string   $callId
     *
     * @return array
     */
    protected function prepareTestVariableData($deliveryResultIdentifier, $test, Variable $variable, $callId)
    {
        $variableData = $this->prepareVariableData($deliveryResultIdentifier, $test, $variable);
        $variableData[self::CALL_ID_TEST_COLUMN] = $callId;

        return $variableData;
    }

    /**
     * Prepares data to be inserted in database.
     *
     * @param string   $deliveryResultIdentifier
     * @param string   $test
     * @param Variable $variable
     *
     * @return array
     */
    protected function prepareVariableData($deliveryResultIdentifier, $test, Variable $variable)
    {
        // Ensures that variable has epoch.
        if (!$variable->isSetEpoch()) {
            $variable->setEpoch(microtime());
        }

        return [
            self::VARIABLES_FK_COLUMN => $deliveryResultIdentifier,
            self::TEST_COLUMN => $test,
            self::VARIABLE_IDENTIFIER => $variable->getIdentifier(),
            self::VARIABLE_VALUE => $this->serializeVariableValue($variable),
        ];
    }

    /**
     * Builds a variable from database row.
     *
     * @param array $variable
     * @return \stdClass
     */
    protected function getResultRow($variable)
    {
        $resultVariable = $this->unserializeVariableValue($variable[self::VARIABLE_VALUE]);
        $object = new \stdClass();
        $object->uri = $variable[self::VARIABLES_TABLE_ID];
        $object->class = get_class($resultVariable);
        $object->deliveryResultIdentifier = $variable[self::VARIABLES_FK_COLUMN];
        $object->callIdItem = $variable[self::CALL_ID_ITEM_COLUMN];
        $object->callIdTest = $variable[self::CALL_ID_TEST_COLUMN];
        $object->test = $variable[self::TEST_COLUMN];
        $object->item = $variable[self::ITEM_COLUMN];
        $object->variable = clone $resultVariable;

        return $object;
    }

    /**
     * @return QueryBuilder
     */
    private function getQueryBuilder()
    {
        return $this->getPersistence()->getPlatform()->getQueryBuilder();
    }

    /**
     * @return \common_persistence_SqlPersistence
     */
    public function getPersistence()
    {
        $persistenceId = $this->hasOption(self::OPTION_PERSISTENCE) ?
            $this->getOption(self::OPTION_PERSISTENCE) : 'default';
        return $this->getServiceLocator()->get(\common_persistence_Manager::SERVICE_ID)->getPersistenceById($persistenceId);
    }

    /**
     * @param $value
     * @return mixed
     */
    protected function unserializeVariableValue($value)
    {
        return unserialize($value);
    }

    /**
     * @param $value
     * @return string
     */
    protected function serializeVariableValue($value)
    {
        return serialize($value);
    }

    public function spawnResult()
    {
        \common_Logger::w('Unsupported function');
    }

    /*
     * retrieve specific parameters from the resultserver to configure the storage
     */
    public function configure($callOptions = array())
    {
        \common_Logger::d('configure  RdsResultStorage with options : ' . implode(" ", $callOptions));
    }

    /**
     *
     * @param mixed $a
     * @param mixed $b
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
}
