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

namespace oat\taoItems\controller\form;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use oat\taoItems\model\ItemsService;
use oat\tao\model\form\RestForm;

/**
 * Class tao_actions_form_RestItemForm
 *
 * Implementation of oat\tao\model\form\RestForm to manage generis item forms for edit and create
 */
class RestItemForm extends RestForm implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Get editable properties
     *
     * @return array
     */
    protected function getClassProperties()
    {
        $properties = parent::getClassProperties();
        unset($properties[ItemsService::PROPERTY_ITEM_MODEL]);
        unset($properties[ItemsService::PROPERTY_ITEM_CONTENT]);
        unset($properties[ItemsService::PROPERTY_ITEM_CONTENT_SRC]);
        return $properties;
    }
}
