<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - tao/helpers/form/elements/class.Treeview.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 07.07.2010, 18:03:25 with ArgoUML PHP module 
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
 * include tao_helpers_form_elements_MultipleElement
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('tao/helpers/form/elements/class.MultipleElement.php');

/* user defined includes */
// section 127-0-1-1-1a593a5d:129ad6a35a4:-8000:0000000000002040-includes begin
// section 127-0-1-1-1a593a5d:129ad6a35a4:-8000:0000000000002040-includes end

/* user defined constants */
// section 127-0-1-1-1a593a5d:129ad6a35a4:-8000:0000000000002040-constants begin
// section 127-0-1-1-1a593a5d:129ad6a35a4:-8000:0000000000002040-constants end

/**
 * Short description of class tao_helpers_form_elements_Treeview
 *
 * @abstract
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 * @subpackage helpers_form_elements
 */
abstract class tao_helpers_form_elements_Treeview
    extends tao_helpers_form_elements_MultipleElement
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute widget
     *
     * @access protected
     * @var string
     */
    protected $widget = 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TreeView';

    // --- OPERATIONS ---

} /* end of abstract class tao_helpers_form_elements_Treeview */

?>