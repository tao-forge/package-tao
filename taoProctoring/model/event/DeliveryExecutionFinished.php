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
 */

namespace oat\taoProctoring\model\event;

use oat\tao\model\webhooks\WebhookSerializableEventInterface;
use oat\taoDelivery\model\execution\DeliveryExecution;

/**
 * This event is fired whenever a delivery execution goes to the `finished` state
 */
class DeliveryExecutionFinished implements WebhookSerializableEventInterface
{
    const EVENT_NAME = self::class;
    const WEBHOOK_EVENT_NAME = 'DeliveryExecutionFinished';

    /**
     * @var DeliveryExecution
     */
    private $deliveryExecution;
    
    /**
     * @return string
     */
    public function getName()
    {
        return self::EVENT_NAME;
    }

    /**
     * @param DeliveryExecution $deliveryExecution
     */
    public function __construct(DeliveryExecution $deliveryExecution)
    {
        $this->deliveryExecution = $deliveryExecution;
    }

    /**
     * Returns the finished delivery execution instance
     * 
     * @return DeliveryExecution
     */
    public function getDeliveryExecution()
    {
        return $this->deliveryExecution;
    }

    /**
     * @return string
     */
    public function getWebhookEventName()
    {
        return self::WEBHOOK_EVENT_NAME;
    }

    /**
     * @return array
     */
    public function serializeForWebhook()
    {
        return [
            'deliveryExecutionId' => $this->deliveryExecution->getIdentifier()
        ];
    }
}
