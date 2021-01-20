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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\taoTestTaker\actions\form;

use oat\generis\model\GenerisRdf;

class Search extends \tao_actions_form_Search
{
    
    /**
     * (non-PHPdoc)
     * @see tao_actions_form_Search::getClassProperties()
     */
    protected function getClassProperties()
    {
        $testTakerProps = \tao_helpers_form_GenerisFormFactory::getClassProperties($this->clazz, $this->getTopClazz());
        $userProps = \tao_helpers_form_GenerisFormFactory::getClassProperties(new \core_kernel_classes_Class(GenerisRdf::CLASS_GENERIS_USER), $this->getTopClazz());
        return array_merge($testTakerProps, $userProps);
    }
}
