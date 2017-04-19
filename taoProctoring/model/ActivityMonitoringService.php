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

use oat\oatbox\service\ConfigurableService;
use oat\taoProctoring\model\execution\DeliveryExecution;
use oat\taoProctoring\model\monitorCache\DeliveryMonitoringService;
use oat\taoEventLog\model\requestLog\RequestLogStorage;
use oat\taoDeliveryRdf\model\DeliveryAssemblyService;

/**
 * Service to manage and monitor assessment activity
 *
 * @author Aleh Hutnikau, <hutnikau@1pt.com>
 */
class ActivityMonitoringService extends ConfigurableService
{
    const SERVICE_ID = 'taoProctoring/ActivityMonitoringService';

    /** Threshold in seconds */
    const OPTION_ACTIVE_USER_THRESHOLD = 'active_user_threshold';

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
        return [
            'active_proctors' => $this->getNumberOfActiveUsers(ProctorService::ROLE_PROCTOR),
            'active_test_takers' => $this->getNumberOfActiveUsers(INSTANCE_ROLE_DELIVERY),
            'total_assessments' => $this->getNumberOfAssessments(),
            'awaiting_assessments' => $this->getNumberOfAssessments(DeliveryExecution::STATE_AWAITING),
            'authorized_but_not_started_assessments' => $this->getNumberOfAssessments(DeliveryExecution::STATE_AUTHORIZED),
            'paused_assessments' => $this->getNumberOfAssessments(DeliveryExecution::STATE_PAUSED),
            'in_progress_assessments' => $this->getNumberOfAssessments(DeliveryExecution::STATE_ACTIVE),
            'terminated_assessment' => $this->getNumberOfAssessments(DeliveryExecution::STATE_TERMINATED),
            'cancelled_assessments' => $this->getNumberOfAssessments(DeliveryExecution::STATE_CANCELED),
            'finished_assessments' => $this->getNumberOfAssessments(DeliveryExecution::STATE_FINISHIED),
            'deliveries_statistics' => $this->getStatesByDelivery(),
        ];
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
        $deliveryMonitoringService = $this->getServiceManager()->get(DeliveryMonitoringService::SERVICE_ID);
        $deliveries = DeliveryAssemblyService::singleton()->getAllAssemblies();
        $result = [];
        foreach ($deliveries as $delivery) {
            $deliveryData = [];
            foreach ($this->deliveryStatuses as $deliveryStatus) {
                $deliveryData[$deliveryStatus->getLabel()] = $deliveryMonitoringService->count([
                    [DeliveryMonitoringService::STATUS => $deliveryStatus->getUri()],
                    'AND',
                    ['delivery_id' => $delivery->getUri()]
                ]);
            }
            $deliveryData['label'] = $delivery->getLabel();
            $result[$delivery->getUri()] = $deliveryData;

        }
        return $result;
    }
}
