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
 * Copyright (c) 2019 (original work) Open Assessment Technologies SA ;
 */

namespace oat\taoDelivery\test\unit\model\Capacity;

use common_persistence_KeyValuePersistence;
use oat\generis\persistence\PersistenceManager;
use oat\generis\test\TestCase;
use oat\oatbox\event\EventManager;
use oat\oatbox\log\LoggerService;
use oat\tao\model\metrics\MetricsService;
use oat\taoDelivery\model\Capacity\InfrastructureCapacityService;
use oat\taoDelivery\model\execution\Counter\DeliveryExecutionCounterInterface;
use oat\taoDelivery\model\Metrics\AwsLoadMetric;
use Psr\Log\LoggerInterface;

class InfrastructureCapacityServiceTest extends TestCase
{
    /**
     * @dataProvider provideConfigAndMetricData
     */
    public function testGetCapacity_WhenConfigOptionsAndMetricDataIsProvided_ThenExpectedValuesAreReturned(
        $awsLimit,
        $taoLimit,
        $cachedCapacity,
        $cachedActiveExecutions,
        $currentActiveExecutions,
        $currentAwsLoad,
        $expectedCapacity
    ) {
        $deliveryExecutionCounterMock = $this->createMock(DeliveryExecutionCounterInterface::class);
        $deliveryExecutionCounterMock->method('count')->willReturn($currentActiveExecutions);

        $serviceLocatorMock = $this->getServiceLocatorMock([
            DeliveryExecutionCounterInterface::SERVICE_ID => $deliveryExecutionCounterMock,
            PersistenceManager::SERVICE_ID => $this->createPersistenceManagerMock($cachedCapacity, $cachedActiveExecutions),
            EventManager::SERVICE_ID => $this->createMock(EventManager::class),
            LoggerService::SERVICE_ID => $this->createMock(LoggerInterface::class),
            MetricsService::class => $this->createMetricsMock($currentAwsLoad),
        ]);
        $service = new InfrastructureCapacityService([
            InfrastructureCapacityService::OPTION_INFRASTRUCTURE_LOAD_LIMIT => $awsLimit,
            InfrastructureCapacityService::OPTION_TAO_CAPACITY_LIMIT => $taoLimit,
            InfrastructureCapacityService::OPTION_TTL => 60,
            InfrastructureCapacityService::OPTION_PERSISTENCE => 'testPersistence',
        ]);
        $service->setServiceLocator($serviceLocatorMock);

        $this->assertEquals($expectedCapacity, $service->getCapacity());
    }

    /**
     * returns array with following structure:
     * <code>
     * array(
     *     array(
     *          (int) awsLimit,
     *          (int) taoLimit,
     *          (int) cachedCapacity,
     *          (int) cachedActiveExecutions,
     *          (int) currentActiveExecutions,
     *          (int) currentAwsLoad,
     *          (int) expectedCapacity
     *     )
     * )
     * </code>
     *
     * @return array[]
     */
    public function provideConfigAndMetricData()
    {
        return [
            // when there is no cached capacity, and server load is lower than configured threshold,
            // then calculated capacity is proportional amount from configured TAO limit
            [80, 3000, null, null, 0, 40, 1500], // no cached capacity, server load limit 80%, current load 40%
            [90, 3000, null, null, 0, 30, 2000], // no cached capacity, server load limit 90%, current load 30%
            // when there is no cached capacity, and server load is larger than configured threshold,
            // then calculated capacity is negative and a zero capacity is returned
            [80, 3000, null, null, 0, 80, 0],
            [80, 3000, null, null, 0, 81, 0],
            [80, 3000, null, null, 0, 100, 0],
            // when cache has a stored calculated server capacity, it is reduced by an increased active execution number
            [80, 3000, 1000, 1000, 1100, 30, 900],
            [80, 3000, 1000, 1000, 1101, 30, 899],
            // when active execution difference is equal to or larger than stored capacity, then 0 capacity is returned
            [80, 3000, 1000, 1000, 2000, null, 0],
            [80, 3000, 1000, 1000, 2100, null, 0],
        ];
    }

    /**
     * @param $currentAwsLoad
     * @return \oat\generis\test\MockObject
     */
    private function createMetricsMock($currentAwsLoad)
    {
        $awsLoadMetricMock = $this->createMock(AwsLoadMetric::class);
        $awsLoadMetricMock->method('collect')->willReturn($currentAwsLoad);
        $metricsServiceMock = $this->createMock(MetricsService::class);
        $metricsServiceMock->method('getOneMetric')->willReturn($awsLoadMetricMock);

        return $metricsServiceMock;
    }

    /**
     * @param $cachedCapacity
     * @param $cachedActiveExecutions
     * @return \oat\generis\test\MockObject
     */
    private function createPersistenceManagerMock($cachedCapacity, $cachedActiveExecutions)
    {
        $persistenceManagerMock = $this->createMock(PersistenceManager::class);
        $persistenceMock = $this->createMock(common_persistence_KeyValuePersistence::class);
        $persistenceMock->method('get')->willReturnCallback(function ($argument) use ($cachedCapacity, $cachedActiveExecutions) {
            switch ($argument) {
                case InfrastructureCapacityService::CAPACITY_CACHE_KEY:
                    return $cachedCapacity;
                case InfrastructureCapacityService::ACTIVE_EXECUTIONS_CACHE_KEY:
                    return $cachedActiveExecutions;
            }
        });
        $persistenceManagerMock->expects($this->once())->method('getPersistenceById')->willReturn($persistenceMock);

        return $persistenceManagerMock;
    }
}
