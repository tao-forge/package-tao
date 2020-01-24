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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA;
 */

namespace oat\taoDeliveryRdf\test\unit\model\event;

use core_kernel_classes_Container;
use core_kernel_classes_Resource;
use oat\generis\test\MockObject;
use oat\generis\test\TestCase;
use oat\taoDeliveryRdf\model\event\DeliveryCreatedEvent;

class DeliveryCreatedEventTest extends TestCase
{
    private const DELIVERY_URI = 'https://delivery';
    private const TEST_URI = 'https://test_uri';

    /** @var core_kernel_classes_Resource|MockObject */
    private $deliveryMock;
    /** @var core_kernel_classes_Resource|MockObject */
    private $testResource;

    protected function setUp()
    {
        $this->deliveryMock = $this->createMock(core_kernel_classes_Resource::class);
        $this->testResource = $this->createMock(core_kernel_classes_Resource::class);
        $this->deliveryMock->method('getUri')->willReturn(self::DELIVERY_URI);
        $this->deliveryMock->method('getOnePropertyValue')->willReturn($this->testResource);
        $this->testResource->method('getUri')->willReturn(self::TEST_URI);
    }

    public function testSerializeForWebhook()
    {
        $event = new DeliveryCreatedEvent($this->deliveryMock);
        $result = $event->serializeForWebhook();

        $this->assertArrayHasKey('delivery_id', $result);
        $this->assertArrayHasKey('test_id', $result);
        $this->assertEquals(self::DELIVERY_URI, $result['delivery_id']);
        $this->assertEquals(self::TEST_URI, $result['test_id']);
    }

    public function testJsonSerialize()
    {
        $event = new DeliveryCreatedEvent($this->deliveryMock);
        $result = $event->jsonSerialize();
        $this->assertArrayHasKey('delivery', $result);
        $this->assertEquals($result['delivery'], self::DELIVERY_URI);
    }

    public function testGetName()
    {
        $event = new DeliveryCreatedEvent($this->deliveryMock);
        $result = $event->getName();

        $this->assertEquals($result, DeliveryCreatedEvent::class);
    }

    public function testGetWebhookEventName()
    {
        $event = new DeliveryCreatedEvent($this->deliveryMock);
        $result = $event->getWebhookEventName();
        $this->assertEquals('DeliveryCreatedEvent', $result);
    }
}
