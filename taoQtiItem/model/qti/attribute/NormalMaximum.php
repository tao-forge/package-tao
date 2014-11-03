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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

namespace oat\taoQtiItem\model\qti\attribute;

use oat\taoQtiItem\model\qti\attribute\NormalMaximum;
use oat\taoQtiItem\model\qti\attribute\Attribute;

/**
 * The NormalMaximum attribute
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 
 */
class NormalMaximum extends Attribute
{
	
	static protected $name = 'normalMaximum';
	static protected $type = 'oat\\taoQtiItem\\model\\qti\\datatype\\Float';
	static protected $defaultValue = null;
	static protected $required = false;

}