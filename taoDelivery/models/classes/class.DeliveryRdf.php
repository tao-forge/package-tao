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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */
use oat\oatbox\user\User;
use oat\oatbox\service\ServiceManager;
use oat\taoDelivery\model\AssignmentService;

/**
 * Deprecated delivery implementation, replaced by Assigmnet abstraction
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoDelivery
 * @deprecated
 */
class taoDelivery_models_classes_DeliveryRdf extends core_kernel_classes_Resource
{
    /**
     * (non-PHPdoc)
     * @see \oat\taoFrontOffice\model\Delivery::getId()
     */
    public function getId()
    {
        return $this->getUri();
    }
    
    /**
     * (non-PHPdoc)
     * @see core_kernel_classes_Resource::getLabel()
     */
    public function getLabel()
    {
        return parent::getLabel();
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\taoFrontOffice\model\Delivery::getDescription()
     */
    public function getDescription()
    {
        return '';
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\taoFrontOffice\model\Delivery::isTakeable()
     */
    public function isTakeable(User $testTaker) {
        return $this->getAssignmentService()->isDeliveryExecutionAllowed($this->getId(), $testTaker);
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\taoFrontOffice\model\Delivery::getRuntime()
     */
    public function getRuntime()
    {
        return $this->getAssignmentService()->getRuntime($this->getId());
    }
    
    /**
     * @return AssignmentService
     */
    public function getAssignmentService()
    {
        return ServiceManager::getServiceManager()->get(AssignmentService::CONFIG_ID);
    }
}