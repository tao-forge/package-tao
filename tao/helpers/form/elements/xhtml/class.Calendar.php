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

/**
 * The XHTML implementation of the Calendar Widget.
 *
 * @author Bertrand Chevrier, <bertrand@taotesting.com>
 * @package tao
 * @subpackage helpers_form_elements_xhtml
 */
class tao_helpers_form_elements_xhtml_Calendar
    extends tao_helpers_form_elements_Calendar
{

    /**
     * Rendering of the XHTML implementation of the Calendar Widget.
     *
     * @author Bertrand Chevrier, <bertrand@taotesting.com>
     * @return The XHTML stream of the Calendar Widget.
     */
    public function renderImplementation(tao_helpers_form_FormElementRenderingInfo $info = null)
    {
        $returnValue = (string) '';

		$uniqueId = uniqid('calendar_');
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

        return (string) $returnValue;
    }

}

?>
