<?php

error_reporting(E_ALL);

/**
 * TAO - tao/helpers/form/validators/class.DateTime.php
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
// section 127-0-1-1-1327d07d:131adaf13fc:-8000:0000000000002E79-includes begin
// section 127-0-1-1-1327d07d:131adaf13fc:-8000:0000000000002E79-includes end

/* user defined constants */
// section 127-0-1-1-1327d07d:131adaf13fc:-8000:0000000000002E79-constants begin
// section 127-0-1-1-1327d07d:131adaf13fc:-8000:0000000000002E79-constants end

/**
 * Short description of class tao_helpers_form_validators_DateTime
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 * @subpackage helpers_form_validators
 */
class tao_helpers_form_validators_DateTime
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
        // section 127-0-1-1-1327d07d:131adaf13fc:-8000:0000000000002E7A begin
		 
		parent::__construct($options);
		
        // section 127-0-1-1-1327d07d:131adaf13fc:-8000:0000000000002E7A end
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

        // section 127-0-1-1-1327d07d:131adaf13fc:-8000:0000000000002E84 begin
		$value = trim($values);
		/*
		if(empty($value)){
			$returnValue = true;//no need to go further. To check if not empty, use the NotEmpty validator
			return $returnValue;
		}
		*/
		
		try{
			$dateTime = new DateTime($value);
				
			if(!empty($this->options['comparator']) && $this->options['datetime2_ref'] instanceof tao_helpers_form_FormElement){
				//try comparison:
				try{
					$dateTime2 = new DateTime($this->options['datetime2_ref']->getValue());
				}catch(Exception $e){}
				
				if($dateTime2 instanceof DateTime){
					$this->message = __('Invalid date range');
					
					switch ($this->options['comparator']){
						case 'after':
						case 'later':
						case 'sup':
						case '>':{
							if($dateTime > $dateTime2){
								$returnValue = true;
							}else{
								$this->message .= ' (' . __('must be after: ').$dateTime2->format('Y-m-d').')';
								//TODO should add supprot of time: H:i:s
							}
							break;
						}
						case 'before':
						case 'earlier':
						case 'inf':
						case '<':{
							if($dateTime < $dateTime2){
								$returnValue = true;
							}else{
								$this->message .= ' (' . __('must be before: ').$dateTime2->format('Y-m-d H:i:s').')';
								//TODO should add supprot of time: H:i:s
							}
							break;
						}
						default:
							throw new common_Exception('Usuported comparator in DateTime Validator: '.$this->options['comparator']);
					}
				}
			
			}else{
				$returnValue = true; 
			}
		}catch(Exception $e){
			$this->message = __('The value of this field must be a valide date format, e.g. YYYY-MM-DD HH:MM:SS');
			$returnValue = false;
		}
		
        // section 127-0-1-1-1327d07d:131adaf13fc:-8000:0000000000002E84 end

        return (bool) $returnValue;
    }

} /* end of class tao_helpers_form_validators_DateTime */

?>