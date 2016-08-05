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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\taoProctoring\scripts;

use oat\taoProctoring\model\implementation\TestSessionService;
use oat\oatbox\service\ServiceManager;
use oat\taoProctoring\model\DeliveryExecutionStateService;
use oat\oatbox\action\Action;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use common_Logger;
use common_report_Report as Report;
use oat\taoDelivery\models\classes\execution\DeliveryExecution;

/**
 * Script that terminates assessments, paused longer than XXX
 * Run example: `sudo php index.php 'oat\taoProctoring\scripts\TerminatePausedAssessment'`
 */
class TerminatePausedAssessment implements Action, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * @var \Report
     */
    protected $report;

    /**
     * @var array
     */
    protected $params;

    /**
     * @param array $params
     * @return Report
     */
    public function __invoke($params)
    {
        $this->params = $params;

        $this->report = new Report(
            Report::TYPE_INFO,
            'Termination expired paused executions...'
        );
        common_Logger::d('Termination expired paused execution started at ' . date(DATE_RFC3339));

        \common_ext_ExtensionsManager::singleton()->getExtensionById('taoDeliveryRdf');
        \common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiTest');

        $deliveryExecutionService = \taoDelivery_models_classes_execution_ServiceProxy::singleton();

        $deliveryClass = new \core_kernel_classes_Class(CLASS_COMPILEDDELIVERY);
        $deliveries = $deliveryClass->getInstances(true);
        $count = 0;
        $testSessionService = ServiceManager::getServiceManager()->get(TestSessionService::SERVICE_ID);
        foreach ($deliveries as $delivery) {
            if ($delivery->exists()) {
                $deliveryExecutions = $deliveryExecutionService->getExecutionsByDelivery($delivery);
                foreach ($deliveryExecutions as $deliveryExecution) {
                    if ($testSessionService->isExpired($deliveryExecution)) {
                        try {
                            $this->terminateExecution($deliveryExecution);
                            $count++;
                        } catch (\Exception $e) {
                            $this->addReport(Report::TYPE_ERROR,$e->getMessage());
                        }
                    }
                }
                common_Logger::d('Checked ' . $delivery->getLabel() . ' with ' . count($deliveryExecutions) . ' corresponding executions');
            }
        }

        $msg = $count > 0 ? "{$count} executions has been terminated." : "Expired executions not found.";
        $this->addReport(Report::TYPE_INFO, $msg);

        common_Logger::d('Termination expired paused execution finished at ' . date(DATE_RFC3339));

        return $this->report;
    }

    /**
     * $terminate delivery execution
     * @param DeliveryExecution $deliveryExecution
     */
    protected function terminateExecution(DeliveryExecution $deliveryExecution) {
        $deliveryExecutionStateService = ServiceManager::getServiceManager()->get(DeliveryExecutionStateService::SERVICE_ID);
        $deliveryExecutionStateService->terminateExecution(
            $deliveryExecution,
            ['reasons' => 'Paused delivery execution was expired', 'comment' => '']
        );
        $this->addReport(Report::TYPE_INFO, "Delivery execution {$deliveryExecution->getUri()} has been terminated.");
    }

    /**
     * @param $type
     * @param string $message
     */
    protected function addReport($type, $message)
    {
        $this->report->add(new Report(
            $type,
            $message
        ));
    }
}
