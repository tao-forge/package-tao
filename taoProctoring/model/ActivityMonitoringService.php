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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\taoProctoring\model;

use oat\generis\model\OntologyAwareTrait;
use oat\oatbox\service\ConfigurableService;
use oat\taoProctoring\model\execution\DeliveryExecution;
use oat\taoProctoring\model\monitorCache\DeliveryMonitoringService;
use oat\taoEventLog\model\requestLog\RequestLogStorage;
use oat\taoProctoring\model\monitorCache\implementation\MonitoringStorage;

/**
 * Service to manage and monitor assessment activity
 *
 * @author Aleh Hutnikau, <hutnikau@1pt.com>
 */
class ActivityMonitoringService extends ConfigurableService
{
    use OntologyAwareTrait;
    const SERVICE_ID = 'taoProctoring/ActivityMonitoringService';

    /** Threshold in seconds */
    const OPTION_ACTIVE_USER_THRESHOLD = 'active_user_threshold';

    /** Interval of refreshing assessment activity graph in seconds. 0 - no auto refresh */
    const OPTION_COMPLETED_ASSESSMENTS_AUTO_REFRESH = 'completed_assessments_auto_refresh';

    /** Interval of refreshing assessment activity data in seconds. 0 - no auto refresh */
    const OPTION_ASSESSMENT_ACTIVITY_AUTO_REFRESH = 'assessment_activity_auto_refresh';

    /** State of awaiting assessment */
    const STATE_AWAITING_ASSESSMENT = 'awaiting_assessments';

    /** State of authorized assessment */
    const STATE_AUTHORIZED_BUT_NOT_STARTED_ASSESSMENTS = 'authorized_but_not_started_assessments';

    /** State of paused assessment */
    const STATE_PAUSED_ASSESSMENTS = 'paused_assessments';

    /** State of in progress assessment */
    const STATE_IN_PROGRESS_ASSESSMENTS = 'in_progress_assessments';

    /** Active proctors field */
    const FIELD_ACTIVE_PROCTORS = 'active_proctors';

    /** Active Test Takers field */
    const FIELD_ACTIVE_TEST_TAKERS = 'active_test_takers';

    /** Total assessments field */
    const FIELD_TOTAL_ASSESSMENTS = 'total_assessments';

    /** Deliveries statistics field*/
    const FIELD_DELIVERIES_STATISTICS = 'deliveries_statistics';

    /** Retired deliveries field*/
    const FIELD_RETIRED_DELIVERIES = 'retired_deliveries';

    /** Total current assessment field */
    const FIELD_TOTAL_CURRENT_ASSESSMENTS = 'total_current_assessments';

    const LABEL_RETIRED_DELIVERIES = 'Retired Deliveries';


    /**
     * @var array list of all the statuses uris
     */
    protected $deliveryStatuses = [
        DeliveryExecution::STATE_AWAITING,
        DeliveryExecution::STATE_AUTHORIZED,
        DeliveryExecution::STATE_PAUSED,
        DeliveryExecution::STATE_ACTIVE,
        DeliveryExecution::STATE_TERMINATED,
        DeliveryExecution::STATE_CANCELED,
        DeliveryExecution::STATE_FINISHIED,
    ];

    /**
     * ActivityMonitoringService constructor.
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        parent::__construct($options);

        $deliveryStatuses = [];
        foreach ($this->deliveryStatuses as $deliveryStatus) {
            $deliveryStatuses[] = new \core_kernel_classes_Resource($deliveryStatus);
        }
        $this->deliveryStatuses = $deliveryStatuses;
    }

    /**
     * Return comprehensive activity monitoring data.
     * @return array
     */
    public function getData()
    {
        $awaiting = $this->getNumberOfAssessments(DeliveryExecution::STATE_AWAITING);
        $authorized = $this->getNumberOfAssessments(DeliveryExecution::STATE_AUTHORIZED);
        $paused = $this->getNumberOfAssessments(DeliveryExecution::STATE_PAUSED);
        $active = $this->getNumberOfAssessments(DeliveryExecution::STATE_ACTIVE);
        $current = $awaiting + $authorized + $paused + $active;
        $assessments = [
            self::FIELD_ACTIVE_PROCTORS => $this->getNumberOfActiveUsers(ProctorService::ROLE_PROCTOR),
            self::FIELD_ACTIVE_TEST_TAKERS => $this->getNumberOfActiveUsers(INSTANCE_ROLE_DELIVERY),
            self::FIELD_TOTAL_ASSESSMENTS => $this->getNumberOfAssessments(),
            self::FIELD_TOTAL_CURRENT_ASSESSMENTS => $current,
            self::STATE_AWAITING_ASSESSMENT => $awaiting,
            self::STATE_AUTHORIZED_BUT_NOT_STARTED_ASSESSMENTS => $authorized,
            self::STATE_PAUSED_ASSESSMENTS => $paused,
            self::STATE_IN_PROGRESS_ASSESSMENTS => $active
        ];

        $deliveryStates = $this->getStatesByDelivery();
        $assessments[self::FIELD_DELIVERIES_STATISTICS] = $deliveryStates;
        return $assessments;
    }

    /**
     * Get array of DateTime objects build from $date (or current time if not given) $amount times back with given interval
     * Example:
     * $timeKeys = $service->getTimeKeys(new \DateInterval('PT1H'), new \DateTime('now'), 24);
     *
     *   array (
     *     0 =>
     *       DateTime::__set_state(array(
     *       'date' => '2017-04-24 08:00:00.000000',
     *       'timezone_type' => 1,
     *       'timezone' => '+00:00',
     *     )),
     *     1 =>
     *       DateTime::__set_state(array(
     *       'date' => '2017-04-24 07:00:00.000000',
     *       'timezone_type' => 1,
     *       'timezone' => '+00:00',
     *     )),
     *     2 =>
     *       DateTime::__set_state(array(
     *       'date' => '2017-04-24 06:00:00.000000',
     *       'timezone_type' => 1,
     *       'timezone' => '+00:00',
     *     )),
     *       ...
     *   )
     *
     * @param \DateInterval $interval
     * @param \DateTime|null $date
     * @param null $amount
     * @return \DateTime[]
     */
    public function getTimeKeys(\DateInterval $interval, \DateTime $date = null, $amount = null)
    {
        $timeKeys = [];
        if ($date === null) {
            $date = new \DateTime('now', new \DateTimeZone('UTC'));
        }

        if ($interval->format('%i') > 0) {
            $date->setTime($date->format('H'), $date->format('i')+1, 0);
            $amount = $amount === null ? 60 : $amount;
        }
        if ($interval->format('%h') > 0) {
            $date->setTime($date->format('H')+1, 0, 0);
            $amount = $amount === null ? 24 : $amount;
        }
        if ($interval->format('%d') > 0) {
            $date->setTime(0, 0, 0);
            $date->setDate($date->format('Y'), $date->format('m'), $date->format('d')+1);
            $amount = $amount === null ? cal_days_in_month(CAL_GREGORIAN, $date->format('m'), $date->format('Y')) : $amount;
        }
        if ($interval->format('%m') > 0) {
            $date->setTime(0, 0, 0);
            $date->setDate($date->format('Y'), $date->format('m')+1, 1);
            $amount = $amount === null ? 12 : $amount;
        }

        while ($amount > 0) {
            $timeKeys[] = new \DateTime($date->format(\DateTime::ISO8601), new \DateTimeZone('UTC'));
            $date->sub($interval);
            $amount--;
        }
        return $timeKeys;
    }

    /**
     * @param null|string $state
     * @return int
     */
    protected function getNumberOfAssessments($state = null)
    {
        $deliveryMonitoringService = $this->getServiceManager()->get(DeliveryMonitoringService::SERVICE_ID);
        if ($state === null) {
            return $deliveryMonitoringService->count();
        } else {
            return $deliveryMonitoringService->count([DeliveryMonitoringService::STATUS => $state]);
        }
    }

    /**
     * @param null|string $role
     * @return int
     */
    protected function getNumberOfActiveUsers($role = null)
    {
        /** @var  RequestLogStorage $requestLogService */
        $requestLogService = $this->getServiceManager()->get(RequestLogStorage::SERVICE_ID);
        $now = microtime(true);
        $filter = [
            [RequestLogStorage::EVENT_TIME, 'between', $now - $this->getOption(self::OPTION_ACTIVE_USER_THRESHOLD), $now]
        ];
        if ($role !== null) {
            $filter[] = [RequestLogStorage::USER_ROLES, 'like', '%,' . $role . ',%'];
        }
        return $requestLogService->count($filter, ['group'=>RequestLogStorage::USER_ID]);
    }

    /**
     * Get list of all the deliveries and number of it's executions in each status
     * Result indexed by delivery Uri
     * @return array
     */
    protected function getStatesByDelivery()
    {
        $statusesArray = [];
        foreach ($this->deliveryStatuses as $deliveryStatus) {
            $statusesArray[$deliveryStatus->getUri()] = 0;
        }

        $retiredTitle = __('Retired Deliveries');

        $newResult = [];
        $sql = "select kvdm." . MonitoringStorage::KV_COLUMN_VALUE . " as delivery_id, dm." . MonitoringStorage::COLUMN_STATUS . " as status,
                count(dm." . MonitoringStorage::COLUMN_DELIVERY_EXECUTION_ID . ") as cnt
                    from " . MonitoringStorage::TABLE_NAME . " dm
                    left join " . MonitoringStorage::KV_TABLE_NAME . " kvdm
                     on dm." . MonitoringStorage::COLUMN_DELIVERY_EXECUTION_ID . "=kvdm." . MonitoringStorage::KV_COLUMN_PARENT_ID . " and kvdm." . MonitoringStorage::KV_COLUMN_KEY . "='delivery_id'
                    group by kvdm." . MonitoringStorage::KV_COLUMN_VALUE . ", dm." . MonitoringStorage::COLUMN_STATUS;
        $stmt = $this->getPersistence()->query($sql);
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($data as $row) {
            $delivery_id = $row['delivery_id'] ?: self::FIELD_RETIRED_DELIVERIES;
            if (!isset($newResult[$delivery_id])) {
                $newResult[$delivery_id] = $statusesArray;
                $delivery = $this->getResource($delivery_id);
                if (!$delivery->exists()) {
                    $newResult[$delivery_id]['label'] = $retiredTitle;
                } else {
                    $label = $delivery->getLabel();
                    $newResult[$delivery_id]['label'] = $label ?: $delivery_id;
                }
            }

            $newResult[$delivery_id][$row['status']] += $row['cnt'];
        }

        $list = [];
        foreach ($newResult as $key => $row) {
            if ($row['label'] != $retiredTitle) {
                $list[$key] = $row;
            } else {
                if (!isset($list[self::FIELD_RETIRED_DELIVERIES])) {
                    $list[self::FIELD_RETIRED_DELIVERIES] = $row;
                } else {
                    foreach ($row as $status => $val) {
                        if ($status == 'label') {
                            continue;
                        }
                        $list[self::FIELD_RETIRED_DELIVERIES][$status] = (int)$list[self::FIELD_RETIRED_DELIVERIES][$status] + (int)$val;
                    }
                }
            }
        }

        return $list;
    }

    protected function getPersistence()
    {
        $monitoringStorageService = $this->getServiceManager()->get(MonitoringStorage::SERVICE_ID);
        return $this->getServiceManager()
            ->get(\common_persistence_Manager::SERVICE_ID)
            ->getPersistenceById($monitoringStorageService->getOption(MonitoringStorage::OPTION_PERSISTENCE));
    }
}
