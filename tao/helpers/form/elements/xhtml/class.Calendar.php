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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 22.12.2009, 16:53:45 with ArgoUML PHP module 
 * (last revised $Date: 2009-04-11 21:57:46 +0200 (Sat, 11 Apr 2009) $)
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 * @subpackage helpers_form_elements_xhtml
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include tao_helpers_form_elements_Calendar
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('tao/helpers/form/elements/class.Calendar.php');

/* user defined includes */
// section 127-0-1-1--54ddf4d1:12404ee79c9:-8000:00000000000018E6-includes begin
// section 127-0-1-1--54ddf4d1:12404ee79c9:-8000:00000000000018E6-includes end

/* user defined constants */
// section 127-0-1-1--54ddf4d1:12404ee79c9:-8000:00000000000018E6-constants begin
// section 127-0-1-1--54ddf4d1:12404ee79c9:-8000:00000000000018E6-constants end

/**
 * Short description of class tao_helpers_form_elements_xhtml_Calendar
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 * @subpackage helpers_form_elements_xhtml
 */
class tao_helpers_form_elements_xhtml_Calendar
    extends tao_helpers_form_elements_Calendar
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method render
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return string
     */
    public function render($uniqueId = '')
    {
        $returnValue = (string) '';

        // section 127-0-1-1--54ddf4d1:12404ee79c9:-8000:00000000000018E8 begin
		if(empty($uniqueId)){
			$uniqueId=uniqid('calendar_');
		}
		$elementId = tao_helpers_Display::TextCleaner($this->getDescription()).'_'.$uniqueId;
		
		
		if(!isset($this->attributes['noLabel'])){
			$returnValue .= "<label class='form_desc calendar' for='{$this->name}'>"._dh($this->getDescription())."</label>";
		}
		else{
			unset($this->attributes['noLabel']);
		}
		if(!isset($this->attributes['size'])){
			$this->attributes['size'] = 10;
		}
		
		$returnValue .= "<input type='text' name='{$this->name}' id='$elementId' ";
		$returnValue .= $this->renderAttributes();
		$returnValue .= ' value="'._dh($this->value).'"  />';
		
		$returnValue .="<script type=\"text/javascript\">
			require(['jquery','jqueryUI'], function($){
				$(\"#$elementId\").datepicker({ 
					dateFormat: 'yy-mm-dd',
					beforeShow: function(input, inst) {
						inst.dpDiv.css('z-index', 1001);
					}
				});
			});</script>";

        // section 127-0-1-1--54ddf4d1:12404ee79c9:-8000:00000000000018E8 end

        return (string) $returnValue;
    }

} /* end of class tao_helpers_form_elements_xhtml_Calendar*/

?>
