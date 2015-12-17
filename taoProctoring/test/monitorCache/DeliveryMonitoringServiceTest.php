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

namespace oat\taoProctoring\test\monitorCache;

use oat\tao\test\TaoPhpUnitTestRunner;
use oat\taoProctoring\model\monitorCache\implementation\DeliveryMonitoringData;
use oat\taoProctoring\model\monitorCache\implementation\DeliveryMonitoringService;

/**
 * class DeliveryMonitoringData
 *
 * Represents data model of delivery execution.
 *
 * @package oat\taoProctoring
 * @author Aleh Hutnikau <hutnikau@1pt.com>
 */
class DeliveryMonitoringServiceTest extends TaoPhpUnitTestRunner
{
    /**
     * @var DeliveryMonitoringService
     */
    private $service;
    private $persistence;
    private $deliveryExecutionId = 'http://sample/first.rdf#i1450191587554175_test_record';

    public function setUp()
    {
        TaoPhpUnitTestRunner::initTest();
        $this->service = new DeliveryMonitoringService();
        $this->persistence = \common_persistence_Manager::getPersistence('default');
    }

    public function tearDown()
    {
        $this->deleteTestData();
    }

    /**
     * @after
     * @before
     */
    public function deleteTestData()
    {
        $sql = 'DELETE FROM ' . DeliveryMonitoringService::TABLE_NAME .
            ' WHERE ' . DeliveryMonitoringService::COLUMN_DELIVERY_EXECUTION_ID . " LIKE '%_test_record'";

        $this->persistence->exec($sql);
    }

    public function testSave()
    {
        $data = [
            DeliveryMonitoringService::COLUMN_TEST_TAKER => 'test_taker_id',
            DeliveryMonitoringService::COLUMN_STATUS => 'active',
        ];

        $secondaryData = [
            'secondary_data_key' => 'secondary_data_val',
            'secondary_data_key_2' => 'secondary_data_val_2',
        ];

        $dataModel = new DeliveryMonitoringData($this->deliveryExecutionId);

        //data is now valid
        $this->assertFalse($this->service->save($dataModel));
        //$data->validate() has been called
        $this->assertNotEmpty($dataModel->getErrors());

        foreach ($data as $key => $val) {
            $dataModel->add($key, $val);
        }

        foreach ($secondaryData as $secKey => $secVal) {
            $dataModel->add($secKey, $secVal);
        }

        $this->assertTrue($this->service->save($dataModel));
        $this->assertEmpty($dataModel->getErrors());

        $insertedData = $this->getRecordByDeliveryExecutionId($this->deliveryExecutionId);

        $this->assertNotEmpty($insertedData);

        foreach ($data as $key => $val) {
            $this->assertEquals($insertedData[0][$key], $val);
        }

        $insertedKvData = $this->getKvRecordsByParentId($insertedData[0]['id']);

        $this->assertNotEmpty($insertedKvData);
        $this->assertEquals(count($insertedKvData), count($secondaryData));

        foreach ($insertedKvData as $kvData) {
            $key = $kvData[DeliveryMonitoringService::KV_COLUMN_KEY];
            $val = $kvData[DeliveryMonitoringService::KV_COLUMN_VALUE];
            $this->assertTrue(isset($secondaryData[$key]));
            $this->assertEquals($secondaryData[$key], $val);
        }
    }


    public function testDelete()
    {
        $data = [
            DeliveryMonitoringService::COLUMN_TEST_TAKER => 'test_taker_id',
            DeliveryMonitoringService::COLUMN_STATUS => 'active',
            'secondary_data_key' => 'secondary_data_val',
            'secondary_data_key_2' => 'secondary_data_val_2',
        ];

        $dataModel = new DeliveryMonitoringData($this->deliveryExecutionId);

        foreach ($data as $key => $val) {
            $dataModel->add($key, $val);
        }

        $this->assertTrue($this->service->save($dataModel));
        $insertedData = $this->getRecordByDeliveryExecutionId($this->deliveryExecutionId);
        $this->assertNotEmpty($insertedData);

        $this->assertTrue($this->service->delete($dataModel));

        $insertedData = $this->getRecordByDeliveryExecutionId($this->deliveryExecutionId);
        $this->assertEmpty($insertedData);
    }


    /**
     * @dataProvider loadFixture
     */
    public function testFind($data)
    {
        /*$this->service->find([
            ['error_code' => '1'],
            'OR',
            ['error_code' => '2'],
        ]);*/
        $this->service->find([
            [['error_code' => '1'], 'AND', [DeliveryMonitoringService::COLUMN_STATUS => 'active']],
            'OR',
            ['error_code' => '2'],
        ]);
        exit();


        $result = $this->service->find([
            [DeliveryMonitoringService::COLUMN_DELIVERY_EXECUTION_ID => 'http://sample/first.rdf#i1450191587554175_test_record']
        ]);
        $this->assertEquals(count($result), 1);
        $this->assertEquals($result[0][DeliveryMonitoringService::COLUMN_DELIVERY_EXECUTION_ID], 'http://sample/first.rdf#i1450191587554175_test_record');


        $result = $this->service->find([
            ['error_code' => '1'],
            'OR',
            ['error_code' => '2'],
        ]);
        $this->assertEquals(count($result), 2);


        $result = $this->service->find([
            [DeliveryMonitoringService::COLUMN_STATUS => 'finished'],
            ['error_code' => '1'],
        ]);
        $this->assertEquals(count($result), 0);


        $result = $this->service->find([
            [DeliveryMonitoringService::COLUMN_STATUS => 'finished'],
            ['error_code' => '0'],
        ]);
        $this->assertEquals(count($result), 1);
    }

    public function loadFixture()
    {
        $this->setUp();

        $data = [
            [
                DeliveryMonitoringService::COLUMN_DELIVERY_EXECUTION_ID => 'http://sample/first.rdf#i1450191587554175_test_record',
                DeliveryMonitoringService::COLUMN_TEST_TAKER => 'test_taker_1',
                DeliveryMonitoringService::COLUMN_STATUS => 'active',
                'error_code' => 1,
                'session_id' => 'i1450191587554175',
            ],
            [
                DeliveryMonitoringService::COLUMN_DELIVERY_EXECUTION_ID => 'http://sample/first.rdf#i1450191587554176_test_record',
                DeliveryMonitoringService::COLUMN_TEST_TAKER => 'test_taker_2',
                DeliveryMonitoringService::COLUMN_STATUS => 'paused',
                'error_code' => 2,
                'session_id' => 'i1450191587554176',
            ],
            [
                DeliveryMonitoringService::COLUMN_DELIVERY_EXECUTION_ID => 'http://sample/first.rdf#i1450191587554177_test_record',
                DeliveryMonitoringService::COLUMN_TEST_TAKER => 'test_taker_3',
                DeliveryMonitoringService::COLUMN_STATUS => 'finished',
                'error_code' => 3,
                'session_id' => 'i1450191587554177',
            ],
            [
                DeliveryMonitoringService::COLUMN_DELIVERY_EXECUTION_ID => 'http://sample/first.rdf#i1450191587554178_test_record',
                DeliveryMonitoringService::COLUMN_TEST_TAKER => 'test_taker_4',
                DeliveryMonitoringService::COLUMN_STATUS => 'finished',
                'error_code' => 0,
                'session_id' => 'i1450191587554178',
            ],
        ];

        foreach ($data as $item) {
            $dataModel = new DeliveryMonitoringData($item[DeliveryMonitoringService::COLUMN_DELIVERY_EXECUTION_ID]);
            $dataModel->set($item);
            $this->service->save($dataModel);
        }

        return [
            [$data],
        ];
    }

    private function getRecordByDeliveryExecutionId($id)
    {
        $sql = 'SELECT * FROM ' . DeliveryMonitoringService::TABLE_NAME .
            ' WHERE ' . DeliveryMonitoringService::COLUMN_DELIVERY_EXECUTION_ID . '=?';

        return $this->persistence->query($sql, [$id])->fetchAll();
    }

    private function getKvRecordsByParentId($parentId)
    {
        $sql = 'SELECT * FROM ' . DeliveryMonitoringService::KV_TABLE_NAME .
            ' WHERE ' . DeliveryMonitoringService::KV_COLUMN_PARENT_ID . '=?';

        return $this->persistence->query($sql, [$parentId])->fetchAll();
    }
}