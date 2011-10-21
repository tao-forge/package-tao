<?php

error_reporting(E_ALL);

/**
 * TAO - tao/helpers/form/validators/class.FileName.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 17.10.2011, 15:15:22 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package tao
 * @subpackage helpers_form_validators
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include tao_helpers_form_validators_Regex
 *
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('tao/helpers/form/validators/class.Regex.php');

/* user defined includes */
// section 127-0-1-1--6e9cb256:133113ef9e0:-8000:0000000000006B8F-includes begin
// section 127-0-1-1--6e9cb256:133113ef9e0:-8000:0000000000006B8F-includes end

/* user defined constants */
// section 127-0-1-1--6e9cb256:133113ef9e0:-8000:0000000000006B8F-constants begin
// section 127-0-1-1--6e9cb256:133113ef9e0:-8000:0000000000006B8F-constants end

/**
 * Short description of class tao_helpers_form_validators_FileName
 *
 * @access public
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package tao
 * @subpackage helpers_form_validators
 */
class tao_helpers_form_validators_FileName
    extends tao_helpers_form_validators_Regex
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array options
     * @return mixed
     */
    public function __construct($options = array())
    {
        // section 127-0-1-1--6e9cb256:133113ef9e0:-8000:0000000000006B94 begin
        
    	if(isset($options['format'])){
    		unset($options['format']);	//the pattern cannot be overriden
    	}
    	
    	$pattern = "/^[a-zA-Z0-9_\-]*\.[a-zA-Z0-9]*$/";
    	
    	parent::__construct(array_merge(array('format' => $pattern), $options));
    	
        // section 127-0-1-1--6e9cb256:133113ef9e0:-8000:0000000000006B94 end
    }

} /* end of class tao_helpers_form_validators_FileName */

?>