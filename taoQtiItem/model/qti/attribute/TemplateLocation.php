<?php
/*  
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
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

namespace oat\taoQtiItem\model\qti\attribute;

use oat\taoQtiItem\model\qti\attribute\TemplateLocation;
use oat\taoQtiItem\model\qti\attribute\Attribute;

/**
 * The TemplateLocation attribute
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 * @subpackage models_classes_QTI
 */
class TemplateLocation extends Attribute
{
	
	static protected $name = 'templateLocation';
	static protected $type = 'oat\\taoQtiItem\\model\\qti\\datatype\\Uri';
	static protected $defaultValue = null;
	static protected $required = false;

} /* end of class oat\taoQtiItem\model\qti\attribute\TemplateLocation */