<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - tao/helpers/form/class.FormElement.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 06.10.2009, 12:07:20 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
 * @package tao
 * @subpackage helpers_form
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include tao_helpers_form_Form
 *
 * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
 */
require_once('tao/helpers/form/class.Form.php');

/* user defined includes */
// section 10-13-1-45--48e788d1:123dcd97db5:-8000:00000000000018A5-includes begin
// section 10-13-1-45--48e788d1:123dcd97db5:-8000:00000000000018A5-includes end

/* user defined constants */
// section 10-13-1-45--48e788d1:123dcd97db5:-8000:00000000000018A5-constants begin
// section 10-13-1-45--48e788d1:123dcd97db5:-8000:00000000000018A5-constants end

/**
 * Short description of class tao_helpers_form_FormElement
 *
 * @abstract
 * @access public
 * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
 * @package tao
 * @subpackage helpers_form
 */
abstract class tao_helpers_form_FormElement
{
    // --- ASSOCIATIONS ---
    // generateAssociationEnd : 

    // --- ATTRIBUTES ---

    /**
     * Short description of attribute name
     *
     * @access protected
     * @var string
     */
    protected $name = '';

    /**
     * Short description of attribute value
     *
     * @access protected
     * @var mixed
     */
    protected $value = null;

    /**
     * Short description of attribute attributes
     *
     * @access protected
     * @var array
     */
    protected $attributes = array();

    /**
     * Short description of attribute widget
     *
     * @access protected
     * @var string
     */
    protected $widget = '';

    /**
     * Short description of attribute description
     *
     * @access protected
     * @var string
     */
    protected $description = '';

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
     * @param  string name
     * @return mixed
     */
    public function __construct($name = '')
    {
        // section 10-13-1-45--48e788d1:123dcd97db5:-8000:00000000000018CA begin
		$this->name = $name;
        // section 10-13-1-45--48e788d1:123dcd97db5:-8000:00000000000018CA end
    }

    /**
     * Short description of method getName
     *
     * @access public
     * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
     * @return string
     */
    public function getName()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-3ed01c83:12409dc285c:-8000:0000000000001A35 begin
		$returnValue = $this->name;
        // section 127-0-1-1-3ed01c83:12409dc285c:-8000:0000000000001A35 end

        return (string) $returnValue;
    }

    /**
     * Short description of method setName
     *
     * @access public
     * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
     * @param  string name
     * @return mixed
     */
    public function setName($name)
    {
        // section 127-0-1-1-3ed01c83:12409dc285c:-8000:0000000000001948 begin
        $this->name = $name;
        // section 127-0-1-1-3ed01c83:12409dc285c:-8000:0000000000001948 end
    }

    /**
     * Short description of method getValue
     *
     * @access public
     * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
     * @return mixed
     */
    public function getValue()
    {
        $returnValue = null;

        // section 10-13-1-45--48e788d1:123dcd97db5:-8000:00000000000018D0 begin
        $returnValue = $this->value;
        // section 10-13-1-45--48e788d1:123dcd97db5:-8000:00000000000018D0 end

        return $returnValue;
    }

    /**
     * Short description of method setValue
     *
     * @access public
     * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
     * @param  string value
     * @return mixed
     */
    public function setValue($value)
    {
        // section 10-13-1-45--48e788d1:123dcd97db5:-8000:00000000000018D2 begin
		$this->value = $value;
        // section 10-13-1-45--48e788d1:123dcd97db5:-8000:00000000000018D2 end
    }

    /**
     * Short description of method addAttribute
     *
     * @access public
     * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
     * @param  string key
     * @param  string value
     * @return mixed
     */
    public function addAttribute($key, $value)
    {
        // section 127-0-1-1-3ed01c83:12409dc285c:-8000:0000000000001919 begin
		$this->attributes[$key] = $value;
        // section 127-0-1-1-3ed01c83:12409dc285c:-8000:0000000000001919 end
    }

    /**
     * Short description of method setAttributes
     *
     * @access public
     * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
     * @param  array attributes
     * @return mixed
     */
    public function setAttributes($attributes)
    {
        // section 127-0-1-1-3ed01c83:12409dc285c:-8000:000000000000191D begin
		$this->attributes = $attributes;
        // section 127-0-1-1-3ed01c83:12409dc285c:-8000:000000000000191D end
    }

    /**
     * Short description of method renderAttributes
     *
     * @access protected
     * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
     * @return string
     */
    protected function renderAttributes()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-3ed01c83:12409dc285c:-8000:000000000000194F begin
		foreach($this->attributes as $key => $value){
			$returnValue .= " {$key}='{$value}' "; 
		}
        // section 127-0-1-1-3ed01c83:12409dc285c:-8000:000000000000194F end

        return (string) $returnValue;
    }

    /**
     * Short description of method getWidget
     *
     * @access public
     * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
     * @return string
     */
    public function getWidget()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-3ed01c83:12409dc285c:-8000:0000000000001A0A begin
		$returnValue = $this->widget;
        // section 127-0-1-1-3ed01c83:12409dc285c:-8000:0000000000001A0A end

        return (string) $returnValue;
    }

    /**
     * Short description of method getDescription
     *
     * @access public
     * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
     * @return string
     */
    public function getDescription()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-3ed01c83:12409dc285c:-8000:0000000000001A25 begin

		if(empty($this->description)){
			$returnValue = ucfirst($this->name);
		}
		else{
			$returnValue = $this->description;
		}
		
        // section 127-0-1-1-3ed01c83:12409dc285c:-8000:0000000000001A25 end

        return (string) $returnValue;
    }

    /**
     * Short description of method setDescription
     *
     * @access public
     * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
     * @param  string description
     * @return mixed
     */
    public function setDescription($description)
    {
        // section 127-0-1-1-3ed01c83:12409dc285c:-8000:0000000000001A27 begin
		$this->description = $description;
        // section 127-0-1-1-3ed01c83:12409dc285c:-8000:0000000000001A27 end
    }

} /* end of abstract class tao_helpers_form_FormElement */

?>