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
 * Copyright (c) 2019 (original work) Open Assessment Technologies SA;
 */

namespace oat\taoProctoring\model\execution;

use common_ext_ExtensionsManager;
use oat\generis\model\OntologyAwareTrait;
use oat\oatbox\service\ConfigurableService;
use oat\oatbox\user\User;
use oat\taoDelivery\model\execution\DeliveryExecutionInterface;
use oat\taoProctoring\model\monitorCache\DeliveryMonitoringService;
use oat\taoProctoring\model\TestSessionConnectivityStatusService;
use oat\taoQtiTest\models\SessionStateService;

/**
 * Class DeliveryHelperService
 * @author Bartlomiej Marszal
 */
class DeliveryHelperService extends ConfigurableService
{
    use OntologyAwareTrait;
    /**
     * Cached value for prepopulated fields
     * @var array
     */
    private $extraFields = [];

    /**
     * Adjusts a list of delivery executions: add information, format the result
     * @param DeliveryExecution[] $deliveryExecutions
     * @return array
     * @throws \common_ext_ExtensionException
     * @internal param array $options
     */
    public function adjustDeliveryExecutions($deliveryExecutions)
    {
        $executions = [];
        /** @var array $cachedData */
        foreach ($deliveryExecutions as $cachedData) {

            $progressStr = $this->getProgressString($cachedData);

            $state = [
                'status' => $cachedData[DeliveryMonitoringService::STATUS],
                'progress' => __($progressStr)
            ];

            $testTaker = [];
            $extraFields = [];

            /* @var $user User */
            $testTaker['id'] = $cachedData[DeliveryMonitoringService::TEST_TAKER];
            $testTaker['test_taker_last_name'] = isset($cachedData[DeliveryMonitoringService::TEST_TAKER_LAST_NAME])
                ? _dh($cachedData[DeliveryMonitoringService::TEST_TAKER_LAST_NAME])
                : '';
            $testTaker['test_taker_first_name'] = isset($cachedData[DeliveryMonitoringService::TEST_TAKER_FIRST_NAME])
                ? _dh($cachedData[DeliveryMonitoringService::TEST_TAKER_FIRST_NAME])
                : '';

            foreach ($this->getUserExtraFields() as $field) {
                $value = isset($cachedData[$field['id']])
                    ? _dh($cachedData[$field['id']])
                    : '';
                if (\common_Utils::isUri($value)) {
                    $value = $this->getResource($value)->getLabel();
                }
                $extraFields[$field['id']] = $value;
            }

            $online = $this->isOnline($cachedData);
            $lastActivity = $this->getLastActivity($cachedData, $online);

            $executionState = $cachedData[DeliveryMonitoringService::STATUS];
            $extraTime = isset($cachedData[DeliveryMonitoringService::EXTRA_TIME])
                ? (float)$cachedData[DeliveryMonitoringService::EXTRA_TIME]
                : 0;
            $remaining = $this->getRemainingTime($cachedData);
            $approximatedRemaining = $this->getApproximatedRemainingTime($cachedData, $online);

            $execution = array(
                'id' => $cachedData[DeliveryMonitoringService::DELIVERY_EXECUTION_ID],
                'delivery' => array(
                    'uri' => $cachedData[DeliveryMonitoringService::DELIVERY_ID],
                    'label' => _dh($cachedData[DeliveryMonitoringService::DELIVERY_NAME]),
                ),
                'start_time' => $cachedData[DeliveryMonitoringService::START_TIME],
                'allowExtraTime' => isset($cachedData[DeliveryMonitoringService::ALLOW_EXTRA_TIME])
                    ? (bool)$cachedData[DeliveryMonitoringService::ALLOW_EXTRA_TIME]
                    : null,
                'timer' => [
                    'lastActivity' => $lastActivity,
                    'countDown' => DeliveryExecution::STATE_ACTIVE === $executionState && $online,
                    'approximatedRemaining' => $approximatedRemaining,
                    'remaining_time' => $remaining,
                    'extraTime' => $extraTime,
                    'extendedTime' => (isset($cachedData[DeliveryMonitoringService::EXTENDED_TIME]) && $cachedData[DeliveryMonitoringService::EXTENDED_TIME] > 1)
                        ? (float)$cachedData[DeliveryMonitoringService::EXTENDED_TIME]
                        : '',
                    'consumedExtraTime' => isset($cachedData[DeliveryMonitoringService::CONSUMED_EXTRA_TIME])
                        ? (float)$cachedData[DeliveryMonitoringService::CONSUMED_EXTRA_TIME]
                        : 0
                ],
                'testTaker' => $testTaker,
                'extraFields' => $extraFields,
                'state' => $state,
            );

            if ($online) {
                $execution['online'] = $online;
            }

            $executions[] = $execution;
        }

        return $executions;
    }

    /**
     * @param array $cachedData
     * @param $online
     * @return float
     */
    private function getApproximatedRemainingTime(array $cachedData, $online)
    {
        $now = microtime(true);
        $remaining = $this->getRemainingTime($cachedData);
        $elapsedApprox = 0;
        $executionState = $cachedData[DeliveryMonitoringService::STATUS];

        if (
            $executionState === DeliveryExecution::STATE_ACTIVE
            && isset($cachedData[DeliveryMonitoringService::LAST_TEST_TAKER_ACTIVITY])
        ) {
            $lastActivity = $cachedData[DeliveryMonitoringService::LAST_TEST_TAKER_ACTIVITY];
            $elapsedApprox = $now - $lastActivity;
            $duration = isset($cachedData[DeliveryMonitoringService::ITEM_DURATION])
                ? (float)$cachedData[DeliveryMonitoringService::ITEM_DURATION]
                : 0;
            $elapsedApprox += $duration;
        }

        if (is_bool($online) && $online === false) {
            $elapsedApprox = 0;
        }

        return round((float)$remaining - $elapsedApprox);
    }

    /**
     * @param array $cachedData
     * @return int
     */
    private function getRemainingTime(array $cachedData)
    {
        $remaining = isset($cachedData[DeliveryMonitoringService::REMAINING_TIME])
            ? (int)$cachedData[DeliveryMonitoringService::REMAINING_TIME]
            : 0;
        return $remaining;
    }


    /**
     * Get array of user specific extra fields to be displayed in the monitoring data table
     *
     * @return array
     * @throws \common_ext_ExtensionException
     */
    private function getUserExtraFields()
    {
        if (!$this->extraFields) {
            $proctoringExtension = $this->getExtensionManagerService()->getExtensionById('taoProctoring');
            $userExtraFields = $proctoringExtension->getConfig('monitoringUserExtraFields');

            $userExtraFieldsSettings = $proctoringExtension->getConfig('monitoringUserExtraFieldsSettings');
            if (!empty($userExtraFields) && is_array($userExtraFields)) {
                foreach ($userExtraFields as $name => $uri) {
                    $property = $this->getProperty($uri);
                    $settings = array_key_exists($name, $userExtraFieldsSettings)
                        ? $userExtraFieldsSettings[$name]
                        : [];
                    $this->extraFields[] = array_merge(array(
                        'id' => $name,
                        'property' => $property,
                        'label' => __($property->getLabel()),
                    ), $settings);
                }
            }
        }

        return $this->extraFields;
    }

    /**
     * @return TestSessionConnectivityStatusService
     */
    private function getTestSessionConnectivityStatusService()
    {
        return $this->getServiceLocator()->get(TestSessionConnectivityStatusService::SERVICE_ID);
    }

    /**
     * @return SessionStateService
     */
    private function getSessionStateService()
    {
        return $this->getServiceLocator()->get(SessionStateService::SERVICE_ID);
    }

    /**
     * @return common_ext_ExtensionsManager
     */
    private function getExtensionManagerService()
    {
        return $this->getServiceLocator()->get(common_ext_ExtensionsManager::SERVICE_ID);
    }

    /**
     * @param array $cachedData
     * @return mixed|string
     */
    private function getProgressString(array $cachedData)
    {
        $progressStr = $cachedData[DeliveryMonitoringService::CURRENT_ASSESSMENT_ITEM];

        if (($progress = json_decode($progressStr, true)) !== null) {
            if (in_array($cachedData[DeliveryMonitoringService::STATUS], [DeliveryExecutionInterface::STATE_TERMINATED, DeliveryExecutionInterface::STATE_FINISHED], true)) {
                return $progress['title'];
            }
            $format = $this->getSessionStateService()->hasOption(SessionStateService::OPTION_STATE_FORMAT)
                ? $this->getSessionStateService()->getOption(SessionStateService::OPTION_STATE_FORMAT)
                : __('%s - item %p/%c');
            $map = array(
                '%s' => $progress['title'] ?? '',
                '%p' => $progress['itemPosition'] ?? '',
                '%c' => $progress['itemCount'] ?? ''
            );
            $progressStr = strtr($format, $map);
        }
        return $progressStr;
    }

    /**
     * @param array $cachedData
     * @return bool|null
     */
    private function isOnline(array $cachedData)
    {
        $online = null;
        if ($this->getTestSessionConnectivityStatusService()->hasOnlineMode()) {
            $online = $this->getTestSessionConnectivityStatusService()->isOnline($cachedData[DeliveryMonitoringService::DELIVERY_EXECUTION_ID]);
        }
        return $online;
    }

    /**
     * @param array $cachedData
     * @param bool|null $online
     * @return float|null
     */
    private function getLastActivity(array $cachedData, ?bool $online)
    {
        if (isset($cachedData[DeliveryMonitoringService::LAST_TEST_TAKER_ACTIVITY]) && $online) {
            $lastActivity = $cachedData[DeliveryMonitoringService::LAST_TEST_TAKER_ACTIVITY];
        } else {
            $lastActivity = null;
        }

        return $lastActivity;
    }
}