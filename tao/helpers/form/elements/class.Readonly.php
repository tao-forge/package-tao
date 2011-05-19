<?php

error_reporting(E_ALL);

/**
 * TAO - tao\helpers\form\elements\class.Readonly.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 19.05.2011, 15:13:39 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package tao
 * @subpackage helpers_form_elements
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * Represents a FormElement entity
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 */
require_once('tao/helpers/form/class.FormElement.php');

/* user defined includes */
// section 10-13-1--128-66e372da:130084c89b7:-8000:0000000000002E40-includes begin
// section 10-13-1--128-66e372da:130084c89b7:-8000:0000000000002E40-includes end

/* user defined constants */
// section 10-13-1--128-66e372da:130084c89b7:-8000:0000000000002E40-constants begin
// section 10-13-1--128-66e372da:130084c89b7:-8000:0000000000002E40-constants end

/**
 * Short description of class tao_helpers_form_elements_Readonly
 *
 * @abstract
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package tao
 * @subpackage helpers_form_elements
 */
abstract class tao_helpers_form_elements_Readonly
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
    protected $widget = 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#Readonly';

    // --- OPERATIONS ---

} /* end of abstract class tao_helpers_form_elements_Readonly */

?>