<?php

error_reporting(E_ALL);

/**
 * Any Object that claims to be annotable should implement this interface.
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package tao
 * @subpackage helpers_translation
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section -64--88-56-1--5deb8f54:136cf746d4c:-8000:0000000000003947-includes begin
// section -64--88-56-1--5deb8f54:136cf746d4c:-8000:0000000000003947-includes end

/* user defined constants */
// section -64--88-56-1--5deb8f54:136cf746d4c:-8000:0000000000003947-constants begin
// section -64--88-56-1--5deb8f54:136cf746d4c:-8000:0000000000003947-constants end

/**
 * Any Object that claims to be annotable should implement this interface.
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package tao
 * @subpackage helpers_translation
 */
interface tao_helpers_translation_Annotable
{


    // --- OPERATIONS ---

    /**
     * Sets the collection of annotations bound to this Translation Object.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array annotations An associative array of annotations where keys are the annotation names and values are annotation values.
     * @return void
     */
    public function setAnnotations($annotations);

    /**
     * Returns an associative array that represents a collection of annotations
     * keys are annotation names and values annotation values.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return array
     */
    public function getAnnotations();

    /**
     * Adds an annotation with a given name and value. If value is not provided,
     * annotation will be taken into account as a flag.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string name The name of the annotation to add.
     * @param  string value The value of the annotation to add.
     * @return void
     */
    public function addAnnotation($name, $value = '');

    /**
     * Removes an annotation for a given annotation name.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string name The name of the annotation to remove.
     * @return void
     */
    public function removeAnnotation($name);

    /**
     * Get an annotation for a given annotation name. Returns an associative
     * where keys are 'name' and 'value'.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string name
     * @return array
     */
    public function getAnnotation($name);

} /* end of interface tao_helpers_translation_Annotable */

?>