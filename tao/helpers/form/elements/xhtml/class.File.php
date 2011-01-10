<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - tao/helpers/form/elements/xhtml/class.File.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 22.12.2009, 16:53:44 with ArgoUML PHP module 
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
 * include tao_helpers_form_elements_File
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('tao/helpers/form/elements/class.File.php');

/* user defined includes */
// section 127-0-1-1-3453b76:1254af40027:-8000:0000000000001CC9-includes begin
// section 127-0-1-1-3453b76:1254af40027:-8000:0000000000001CC9-includes end

/* user defined constants */
// section 127-0-1-1-3453b76:1254af40027:-8000:0000000000001CC9-constants begin
// section 127-0-1-1-3453b76:1254af40027:-8000:0000000000001CC9-constants end

/**
 * Short description of class tao_helpers_form_elements_xhtml_File
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 * @subpackage helpers_form_elements_xhtml
 */
class tao_helpers_form_elements_xhtml_File
    extends tao_helpers_form_elements_File
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

        // section 127-0-1-1-3453b76:1254af40027:-8000:0000000000001CCA begin
		
		$returnValue .= "<label class='form_desc' for='{$this->name}'>". _dh($this->getDescription())."</label>";
		$returnValue .= "<input type='hidden' name='MAX_FILE_SIZE' value='".tao_helpers_form_elements_File::MAX_FILE_SIZE."' />";
		$returnValue .= "<input type='file' name='{$this->name}' id='{$this->name}' ";
		$returnValue .= $this->renderAttributes();
		$returnValue .= " value='{$this->value}'  />";
		
        // section 127-0-1-1-3453b76:1254af40027:-8000:0000000000001CCA end

        return (string) $returnValue;
    }

} /* end of class tao_helpers_form_elements_xhtml_File */

?>