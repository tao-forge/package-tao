<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 19.07.2011, 17:27:05 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_persistence
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000139F-includes begin
// section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000139F-includes end

/* user defined constants */
// section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000139F-constants begin
// section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000139F-constants end

/**
 * Short description of class core_kernel_persistence_ClassInterface
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_persistence
 */
interface core_kernel_persistence_ClassInterface
{


    // --- OPERATIONS ---

    /**
     * Short description of method getSubClasses
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Resource resource
     * @param  boolean recursive
     * @return array
     */
    public function getSubClasses( core_kernel_classes_Resource $resource, $recursive = false);

    /**
     * Short description of method isSubClassOf
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Resource resource
     * @param  Class parentClass
     * @return boolean
     */
    public function isSubClassOf( core_kernel_classes_Resource $resource,  core_kernel_classes_Class $parentClass);

    /**
     * Short description of method getParentClasses
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Resource resource
     * @param  boolean recursive
     * @return array
     */
    public function getParentClasses( core_kernel_classes_Resource $resource, $recursive = false);

    /**
     * Short description of method getProperties
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Resource resource
     * @param  boolean recursive
     * @return array
     */
    public function getProperties( core_kernel_classes_Resource $resource, $recursive = false);

    /**
     * Short description of method getInstances
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Resource resource
     * @param  boolean recursive
     * @param  array params
     * @return array
     */
    public function getInstances( core_kernel_classes_Resource $resource, $recursive = false, $params = array());

    /**
     * Short description of method setInstance
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Resource resource
     * @param  Resource instance
     * @return core_kernel_classes_Resource
     */
    public function setInstance( core_kernel_classes_Resource $resource,  core_kernel_classes_Resource $instance);

    /**
     * Short description of method setSubClassOf
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Resource resource
     * @param  Class iClass
     * @return boolean
     */
    public function setSubClassOf( core_kernel_classes_Resource $resource,  core_kernel_classes_Class $iClass);

    /**
     * Short description of method setProperty
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Resource resource
     * @param  Property property
     * @return boolean
     */
    public function setProperty( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property);

    /**
     * Short description of method createInstance
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Resource resource
     * @param  string label
     * @param  string comment
     * @param  string uri
     * @return core_kernel_classes_Resource
     */
    public function createInstance( core_kernel_classes_Resource $resource, $label = '', $comment = '', $uri = '');

    /**
     * Short description of method createSubClass
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Resource resource
     * @param  string label
     * @param  string comment
     * @return core_kernel_classes_Class
     */
    public function createSubClass( core_kernel_classes_Resource $resource, $label = '', $comment = '');

    /**
     * Short description of method createProperty
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Resource resource
     * @param  string label
     * @param  string comment
     * @param  boolean isLgDependent
     * @return core_kernel_classes_Property
     */
    public function createProperty( core_kernel_classes_Resource $resource, $label = '', $comment = '', $isLgDependent = false);

    /**
     * Short description of method searchInstances
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Resource resource
     * @param  array propertyFilters
     * @param  array options
     * @return array
     */
    public function searchInstances( core_kernel_classes_Resource $resource, $propertyFilters = array(), $options = array());

    /**
     * Short description of method countInstances
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Resource resource
     * @return Integer
     */
    public function countInstances( core_kernel_classes_Resource $resource);

    /**
     * Short description of method getInstancesPropertyValues
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Resource resource
     * @param  Property property
     * @param  array propertyFilters
     * @param  array options
     * @return array
     */
    public function getInstancesPropertyValues( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property, $propertyFilters = array(), $options = array());

} /* end of interface core_kernel_persistence_ClassInterface */

?>