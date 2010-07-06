<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - tao/helpers/form/elements/class.Authoring.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 06.07.2010, 17:45:45 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 * @subpackage helpers_form_elements
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * Represents a FormElement entity
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('tao/helpers/form/class.FormElement.php');

/* user defined includes */
// section 127-0-1-1-51e1cabd:124e3b3d559:-8000:0000000000001B44-includes begin
// section 127-0-1-1-51e1cabd:124e3b3d559:-8000:0000000000001B44-includes end

/* user defined constants */
// section 127-0-1-1-51e1cabd:124e3b3d559:-8000:0000000000001B44-constants begin
// section 127-0-1-1-51e1cabd:124e3b3d559:-8000:0000000000001B44-constants end

/**
 * Short description of class tao_helpers_form_elements_Authoring
 *
 * @abstract
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 * @subpackage helpers_form_elements
 */
abstract class tao_helpers_form_elements_Authoring
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
    protected $widget = 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#Authoring';

    // --- OPERATIONS ---

} /* end of abstract class tao_helpers_form_elements_Authoring */

?>