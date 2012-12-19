<?php

error_reporting(E_ALL);

/**
 * compares two form elements
 * possible options:
 * 'reference' FormElement, the form element to compare to
 * 'invert' boolean, validates only if values are not equal
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
// section 10-30-1--78--cac56b6:13bb38b902c:-8000:0000000000003C82-includes begin
// section 10-30-1--78--cac56b6:13bb38b902c:-8000:0000000000003C82-includes end

/* user defined constants */
// section 10-30-1--78--cac56b6:13bb38b902c:-8000:0000000000003C82-constants begin
// section 10-30-1--78--cac56b6:13bb38b902c:-8000:0000000000003C82-constants end

/**
 * compares two form elements
 * possible options:
 * 'reference' FormElement, the form element to compare to
 * 'invert' boolean, validates only if values are not equal
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 * @subpackage helpers_form_validators
 */
class tao_helpers_form_validators_Equals
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
        // section 10-30-1--78--cac56b6:13bb38b902c:-8000:0000000000003C86 begin
        parent::__construct($options);
    	if(!isset($this->options['reference']) || !$this->options['reference'] instanceof tao_helpers_form_FormElement){
			throw new common_Exception("No FormElement provided as reference for Equals validator");
		}
        $reference = $this->options['reference'];
		if (isset($this->options['invert']) && $this->options['invert']) {
			$this->message = __('This should not equal ').$reference->getDescription();
		} else {
			$this->message = __('This should equal ').$reference->getDescription();
		}
        // section 10-30-1--78--cac56b6:13bb38b902c:-8000:0000000000003C86 end
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

        // section 10-30-1--78--cac56b6:13bb38b902c:-8000:0000000000003C83 begin
        $invert = isset($this->options['invert']) ? $this->options['invert'] : false;
        $reference = $this->options['reference'];
		$equals = ($values == $reference->getRawValue());
		$returnValue = $invert ? !$equals : $equals;
        // section 10-30-1--78--cac56b6:13bb38b902c:-8000:0000000000003C83 end

        return (bool) $returnValue;
    }

} /* end of class tao_helpers_form_validators_Equals */

?>