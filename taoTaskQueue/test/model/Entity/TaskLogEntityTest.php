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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\taoTaskQueue\test\model\Entity;

use common_report_Report as Report;
use oat\taoTaskQueue\model\Entity\TaskLogEntity;
use oat\taoTaskQueue\model\TaskLogInterface;
use oat\taoTaskQueue\model\ValueObjects\TaskLogCategorizedStatus;

class TaskLogEntityTest extends \PHPUnit_Framework_TestCase
{
    public function testEntityCreated()
    {
        $entity = TaskLogEntity::createFromArray([
            'id' => 'rdf#i1508337970199318643',
            'task_name' => 'Task Name',
            'parameters' => json_encode(['param1' => 'value1', 'param2' => 'value2']),
            'label' => 'Task label',
            'status' => TaskLogInterface::STATUS_COMPLETED,
            'owner' => 'userId',
            'created_at' => '2017-11-16 14:11:42',
            'updated_at' => '2017-11-16 17:12:30',
            'report' => [
                'type' => 'info',
                'message' => 'Running task http://www.taoinstance.dev/ontologies/tao.rdf#i1508337970199318643',
                'data' => NULL,
                'children' => []
            ],
            'category' => 'export'
        ]);

        $this->assertInstanceOf(TaskLogEntity::class, $entity);
        $this->assertInstanceOf(TaskLogCategorizedStatus::class, $entity->getStatus());
        $this->assertInstanceOf(Report::class, $entity->getReport());
        $this->assertInstanceOf(\DateTime::class, $entity->getCreatedAt());
        $this->assertInstanceOf(\DateTime::class, $entity->getUpdatedAt());
        $this->assertInternalType('string', $entity->getId());
        $this->assertInternalType('string', $entity->getTaskName());
        $this->assertInternalType('array', $entity->getParameters());
        $this->assertInternalType('string', $entity->getLabel());
        $this->assertInternalType('string', $entity->getOwner());
        $this->assertInternalType('string', $entity->getCategory());

        $this->assertEquals([
            'id' => 'rdf#i1508337970199318643',
            'taskName' => 'Task Name',
            'taskLabel' => 'Task label',
            'status' => 'completed',
            'statusLabel' => 'Completed',
            'createdAt' => 1510837902,
            'createdAtElapsed' => (new \DateTime('now', new \DateTimeZone(TIME_ZONE)))->getTimestamp() - 1510837902,
            'updatedAt' => 1510848750,
            'updatedAtElapsed' => (new \DateTime('now', new \DateTimeZone(TIME_ZONE)))->getTimestamp() - 1510848750,
            'report' => [
                'type' => 'info',
                'message' => 'Running task http://www.taoinstance.dev/ontologies/tao.rdf#i1508337970199318643',
                'data' => NULL,
                'children' => []
            ],
            'hasFile' => false,
            'category' => 'export'
        ], $entity->jsonSerialize());
    }

    public function testCreateWithReportNull()
    {
        $entity = TaskLogEntity::createFromArray([
            'id' => 'rdf#i1508337970199318643',
            'task_name' => 'Task Name',
            'parameters' => json_encode(['param1' => 'value1', 'param2' => 'value2']),
            'label' => 'Task label',
            'status' => TaskLogInterface::STATUS_COMPLETED,
            'owner' => 'userId',
            'created_at' => '2017-02-01 12:00:01',
            'updated_at' => '2017-02-01 14:00:01',
            'report' => [],
        ]);

        $this->assertNull($entity->getReport());
    }

}
