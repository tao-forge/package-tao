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
 *
 */

namespace oat\taoProctoring\model\monitorCache\implementation;

use oat\taoProctoring\model\monitorCache\DeliveryMonitoringData as DeliveryMonitoringDataInterface;
use oat\taoProctoring\model\monitorCache\DeliveryMonitoringService as DeliveryMonitoringServiceInterface;

/**
 * Class MonitorCacheService
 *
 * RDS Implementation of DeliveryMonitoringService without using autoincrement fields and PDO::lastInsertId function.
 *
 * @package oat\taoProctoring\model
 * @author Aleh Hutnikau <hutnikau@1pt.com>
 */
class MonitorCacheService extends DeliveryMonitoringService
{

    const COLUMN_ID = DeliveryMonitoringServiceInterface::DELIVERY_EXECUTION_ID;

    /**
     * @inheritdoc
     */
    public function find(array $criteria = [], array $options = [], $together = false)
    {
        $result = [];
        $defaultOptions = [
            'order' => self::COLUMN_ID." ASC",
            'offset' => 0,
            'asArray' => false
        ];
        $options = array_merge($defaultOptions, $options);

        $whereClause = 'WHERE ';
        $parameters = [];

        $selectClause = "SELECT DISTINCT t.* ";
        $fromClause = "FROM " . self::TABLE_NAME . " t ";
        $whereClause .= $this->prepareCondition($criteria, $parameters, $selectClause);

        $sql = $selectClause . $fromClause . PHP_EOL .
            //"LEFT JOIN " . self::KV_TABLE_NAME . " kv_t ON kv_t." . self::KV_COLUMN_PARENT_ID . " = t." . self::COLUMN_DELIVERY_EXECUTION_ID . PHP_EOL .
            implode(PHP_EOL, $this->joins) . PHP_EOL .
            $whereClause . PHP_EOL .
            "ORDER BY " . $options['order'];

        if (isset($options['limit']))  {
            $sql = $this->getPersistence()->getPlatForm()->limitStatement($sql, $options['limit'], $options['offset']);
        }

        $stmt = $this->getPersistence()->query($sql, $parameters);

        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if ($together) {
            foreach ($data as &$row) {
                $row = array_merge($row, $this->getKvData($row[self::COLUMN_DELIVERY_EXECUTION_ID]));
            }
        }

        if ($options['asArray']) {
            $result = $data;
        } else {
            foreach($data as $row) {
                $deliveryExecution = \taoDelivery_models_classes_execution_ServiceProxy::singleton()->getDeliveryExecution($row[self::COLUMN_DELIVERY_EXECUTION_ID]);
                $monitoringData = new DeliveryMonitoringData($deliveryExecution, false);
                $result[] = $monitoringData;
            }
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    protected function create(DeliveryMonitoringDataInterface $deliveryMonitoring)
    {
        $data = $deliveryMonitoring->get();

        $primaryTableData = $this->extractPrimaryData($data);

        $result = $this->getPersistence()->insert(self::TABLE_NAME, $primaryTableData) === 1;

        if ($result) {
            $this->saveKvData($deliveryMonitoring);
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    protected function saveKvData(DeliveryMonitoringDataInterface $deliveryMonitoring)
    {
        $data = $deliveryMonitoring->get();
        $isNewRecord = $this->isNewRecord($deliveryMonitoring);

        if (!$isNewRecord) {
            $this->deleteKvData($deliveryMonitoring);
            $id = $data[self::COLUMN_DELIVERY_EXECUTION_ID];
            $kvTableData = $this->extractKvData($data);
            foreach($kvTableData as $kvDataKey => $kvDataValue) {
                $this->getPersistence()->insert(
                    self::KV_TABLE_NAME,
                    array(
                        self::KV_COLUMN_PARENT_ID => $id,
                        self::KV_COLUMN_KEY => $kvDataKey,
                        self::KV_COLUMN_VALUE => $kvDataValue,
                    )
                );
            }
        }
    }

    /**
     * @param $condition
     * @param $parameters
     * @param $selectClause
     * @return string
     */
    protected function prepareCondition($condition, &$parameters, &$selectClause)
    {
        $whereClause = '';

        if (is_array($condition) && count($condition) === 1 && is_array(current($condition))) {
            $condition = current($condition);
        }

        if (is_string($condition) && in_array(mb_strtoupper($condition), ['OR', 'AND'])) {
            $whereClause .= " $condition ";
        } else if (is_array($condition) && count($condition) > 1) {
            $whereClause .=  '(';
            $previousCondition = null;
            foreach ($condition as $subCondition) {
                if (is_array($subCondition) && is_array($previousCondition)) {
                    $whereClause .= 'AND';
                }
                $whereClause .=  $this->prepareCondition($subCondition, $parameters, $selectClause);
                $previousCondition = $subCondition;
            }
            $whereClause .=  ')';
        } else if (is_array($condition) && count($condition) === 1) {
            $primaryColumns = $this->getPrimaryColumns();
            $key = array_keys($condition)[0];
            $value = $condition[$key];

            if ($value === null) {
                $op = 'IS NULL';
            } else if (preg_match('/^(?:\s*(<>|<=|>=|<|>|=|LIKE|NOT\sLIKE))?(.*)$/', $value, $matches)) {
                $value = $matches[2];
                $op = $matches[1] ? $matches[1] : "=";
                $op .= ' ?';
            }

            if (in_array($key, $primaryColumns)) {
                $whereClause .= " t.$key $op ";
            } else {
                $joinNum = count($this->joins);
                $whereClause .= " (kv_t_$joinNum.monitoring_key = ? AND kv_t_$joinNum.monitoring_value $op) ";

                $this->joins[] = "LEFT JOIN " . self::KV_TABLE_NAME . " kv_t_$joinNum ON kv_t_$joinNum." . self::KV_COLUMN_PARENT_ID . " = t." . self::COLUMN_DELIVERY_EXECUTION_ID;
                $parameters[] = trim($key);
            }

            if ($value !== null) {
                $parameters[] = trim($value);
            }
        }
        return $whereClause;
    }

    /**
     * @inheritdoc
     */
    protected function deleteKvData(DeliveryMonitoringDataInterface $deliveryMonitoring)
    {
        $result = false;
        $data = $deliveryMonitoring->get();
        $isNewRecord = $this->isNewRecord($deliveryMonitoring);

        if (!$isNewRecord) {
            $sql = 'DELETE FROM ' . self::KV_TABLE_NAME . '
                    WHERE ' . self::KV_COLUMN_PARENT_ID . '=?';
            $this->getPersistence()->exec($sql, [$data[self::COLUMN_DELIVERY_EXECUTION_ID]]);
            $result = true;
        }

        return $result;
    }
}