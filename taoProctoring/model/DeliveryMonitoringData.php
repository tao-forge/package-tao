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

namespace oat\taoProctoring\model;

/**
 * Interface DeliveryMonitoring
 *
 * Represents data model of delivery execution.
 *
 * @package oat\taoProctoring\model
 * @author Aleh Hutnikau <hutnikau@1pt.com>
 */
interface DeliveryMonitoringData
{
    /**
     * DeliveryMonitoringData constructor.
     * @param \taoDelivery_models_classes_execution_DeliveryExecution $deliveryExecution
     */
    public function __construct(\taoDelivery_models_classes_execution_DeliveryExecution $deliveryExecution);

    /**
     * Save delivery execution data
     * @param array $data data to be saved (key => value).
     * @return mixed
     */
    public function setData(array $data);

    /**
     * Get delivery execution data
     * @return array
     */
    public function getData();

}