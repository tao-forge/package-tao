<?php

error_reporting(E_ALL);

/**
 * TAO - tao/helpers/form/elements/class.Password.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 19.12.2011, 14:29:19 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 * @subpackage helpers_form_elements
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * Represents a FormElement entity
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('tao/helpers/form/class.FormElement.php');

/* user defined includes */
// section 127-0-1-1-750eebc6:13440d254e0:-8000:000000000000607F-includes begin
// section 127-0-1-1-750eebc6:13440d254e0:-8000:000000000000607F-includes end

/* user defined constants */
// section 127-0-1-1-750eebc6:13440d254e0:-8000:000000000000607F-constants begin
// section 127-0-1-1-750eebc6:13440d254e0:-8000:000000000000607F-constants end

/**
 * Short description of class tao_helpers_form_elements_Password
 *
 * @abstract
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 * @subpackage helpers_form_elements
 */
abstract class tao_helpers_form_elements_Password
    extends tao_helpers_form_FormElement
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute widget
     *
     * @access protected
     * @var string
     */
    protected $widget = 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#Password';

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string name
     * @return mixed
     */
    public function __construct($name = '')
    {
        // section 127-0-1-1-1ee974ce:13456771564:-8000:000000000000348C begin
    	parent::__construct($name);
    	$this->addValidators(array(
    		tao_helpers_form_FormFactory::getValidator('Password'),
    		tao_helpers_form_FormFactory::getValidator('Length', array('min' => 3))
    	));
        // section 127-0-1-1-1ee974ce:13456771564:-8000:000000000000348C end
    }

} /* end of abstract class tao_helpers_form_elements_Password */

?>