<?php

error_reporting(E_ALL);

/**
 * TAO - tao/models/classes/table/class.PropertyDP.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 30.08.2012, 18:07:50 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 * @subpackage models_classes_table
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include tao_models_classes_table_dataProvider
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('tao/models/classes/table/interface.dataProvider.php');

/* user defined includes */
// section 127-0-1-1--8febfab:13977a059a7:-8000:0000000000003BA0-includes begin
// section 127-0-1-1--8febfab:13977a059a7:-8000:0000000000003BA0-includes end

/* user defined constants */
// section 127-0-1-1--8febfab:13977a059a7:-8000:0000000000003BA0-constants begin
// section 127-0-1-1--8febfab:13977a059a7:-8000:0000000000003BA0-constants end

/**
 * Short description of class tao_models_classes_table_PropertyDP
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 * @subpackage models_classes_table
 */
class tao_models_classes_table_PropertyDP
        implements tao_models_classes_table_dataProvider
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute singleton
     *
     * @access public
     * @var PropertyDP
     */
    public static $singleton = null;

    /**
     * Short description of attribute cache
     *
     * @access public
     * @var array
     */
    public $cache = array();

    // --- OPERATIONS ---

    /**
     * Short description of method prepare
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  array resources
     * @param  array columns
     * @return mixed
     */
    public function prepare($resources, $columns)
    {
        // section 127-0-1-1--8febfab:13977a059a7:-8000:0000000000003BDA begin
        // section 127-0-1-1--8febfab:13977a059a7:-8000:0000000000003BDA end
    }

    /**
     * Short description of method getValue
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  Column column
     * @return string
     */
    public function getValue( core_kernel_classes_Resource $resource,  tao_models_classes_table_Column $column)
    {
        $returnValue = (string) '';

        // section 127-0-1-1--8febfab:13977a059a7:-8000:0000000000003BDE begin
        $result = $resource->getOnePropertyValue($column->getProperty());
        $returnValue = $result instanceof core_kernel_classes_Resource ? $result->getLabel() : (string)$result;
        // section 127-0-1-1--8febfab:13977a059a7:-8000:0000000000003BDE end

        return (string) $returnValue;
    }

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return tao_models_classes_table_PropertyDP
     */
    public static function singleton()
    {
        $returnValue = null;

        // section 127-0-1-1--8febfab:13977a059a7:-8000:0000000000003BF0 begin
        if (is_null(self::$singleton)) {
        	self::$singleton = new self();
        }
        $returnValue = self::$singleton;
        // section 127-0-1-1--8febfab:13977a059a7:-8000:0000000000003BF0 end

        return $returnValue;
    }

} /* end of class tao_models_classes_table_PropertyDP */

?>