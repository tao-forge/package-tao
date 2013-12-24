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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

/**
 * Service to manage the execution of deliveries
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoDelivery
 * @subpackage models_classes
 */
interface taoDelivery_models_classes_execution_DeliveryExecution
{
    /**
     * Returns the identifier of the delivery execution
     * 
     * @return string
     */
    public function getIdentifier();
    
    /**
     * Retuns a human readable test representation of the delivery execution
     * Should respect the current user's language
     * 
     * @return string
     */
    public function getLabel();
    
    /**
     * Returns when the delivery execution was started
     *
     * @param core_kernel_classes_Resource $assembly
     */
    public function getStartTime();

    /**
     * Returns the delivery execution status as resource
     * 
     * @return core_kernel_classes_Resource 
     */
    public function getStatus();
    
    /**
     * Returns the delivery execution delivery as resource
     * 
     * @return core_kernel_classes_Resource
     */
    public function getDelivery();
    
    /**
     * Returns the delivery executions user identifier
     * 
     * @return string
     */
    public function getUserIdentifier();
    
}
