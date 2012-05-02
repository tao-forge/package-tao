<?php

error_reporting(E_ALL);

/**
 * TAO - tao/helpers/form/validators/class.Password.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 22.12.2011, 14:51:41 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 * @subpackage helpers_form_validators
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * The validators enable you to perform a validation callback on a form element.
 * It's provide a model of validation and must be overriden.
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('tao/helpers/form/class.Validator.php');

/* user defined includes */
// section 127-0-1-1-4660071d:12596d6b0e5:-8000:0000000000001D57-includes begin
// section 127-0-1-1-4660071d:12596d6b0e5:-8000:0000000000001D57-includes end

/* user defined constants */
// section 127-0-1-1-4660071d:12596d6b0e5:-8000:0000000000001D57-constants begin
// section 127-0-1-1-4660071d:12596d6b0e5:-8000:0000000000001D57-constants end

/**
 * Short description of class tao_helpers_form_validators_Password
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 * @subpackage helpers_form_validators
 */
class tao_helpers_form_validators_Password
    extends tao_helpers_form_Validator
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  array options
     * @return mixed
     */
    public function __construct($options = array())
    {
        // section 127-0-1-1-4660071d:12596d6b0e5:-8000:0000000000001D5C begin
		
		parent::__construct($options);
		
		$this->message = __('Passwords are not matching');
        // section 127-0-1-1-4660071d:12596d6b0e5:-8000:0000000000001D5C end
    }

    /**
     * Short description of method evaluate
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  values
     * @return boolean
     */
    public function evaluate($values)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-4660071d:12596d6b0e5:-8000:0000000000001D66 begin
        if (is_array($values) && count($values) == 2) {
        	list($first, $second) = $values;
        	$returnValue = $first == $second;
        	
        } elseif (isset($this->options['password2_ref'])) {
			$secondElement = $this->options['password2_ref'];
			if (is_null($secondElement) || ! $secondElement instanceof tao_helpers_form_FormElement) {
				throw new common_Exception("Please set the reference of the second password element");
			}
			if($values == $secondElement->getRawValue() && trim($values) != ''){
				$returnValue = true;
			}
        }
		
        // section 127-0-1-1-4660071d:12596d6b0e5:-8000:0000000000001D66 end

        return (bool) $returnValue;
    }

} /* end of class tao_helpers_form_validators_Password */

?>