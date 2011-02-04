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
 * include tao_helpers_form_elements_Combobox
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('tao/helpers/form/elements/class.Combobox.php');

/* user defined includes */
// section 127-0-1-1-3ed01c83:12409dc285c:-8000:00000000000019ED-includes begin
// section 127-0-1-1-3ed01c83:12409dc285c:-8000:00000000000019ED-includes end

/* user defined constants */
// section 127-0-1-1-3ed01c83:12409dc285c:-8000:00000000000019ED-constants begin
// section 127-0-1-1-3ed01c83:12409dc285c:-8000:00000000000019ED-constants end

/**
 * Short description of class tao_helpers_form_elements_xhtml_Combobox
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 * @subpackage helpers_form_elements_xhtml
 */
class tao_helpers_form_elements_xhtml_Combobox
    extends tao_helpers_form_elements_Combobox
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
    public function render()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-3ed01c83:12409dc285c:-8000:00000000000019FA begin

		if(!isset($this->attributes['noLabel'])){
			$returnValue .= "<label class='form_desc' for='{$this->name}'>"._dh($this->getDescription())."</label>";
		}
		else{
			unset($this->attributes['noLabel']);
		}
		$returnValue .= "<select name='{$this->name}' id='{$this->name}' ";
		$returnValue .= $this->renderAttributes();
		$returnValue .= ">";
		if(!empty($this->emptyOption)){
			$this->options = array_merge(
				array(' ' => $this->emptyOption),
				$this->options
			);
		}
		foreach($this->options as $optionId => $optionLabel){
			 $returnValue .= "<option value='{$optionId}' ";
			 if($this->value == $optionId){
			 	$returnValue .= " selected='selected' ";
			 }
			 $returnValue .= ">"._dh($optionLabel)."</option>";
		}
		$returnValue .= "</select>";
		
        // section 127-0-1-1-3ed01c83:12409dc285c:-8000:00000000000019FA end

        return (string) $returnValue;
    }

} /* end of class tao_helpers_form_elements_xhtml_Combobox */

?>
