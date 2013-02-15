<?php

error_reporting(E_ALL);

/**
 * TAO - tao/models/classes/class.FileSourceService.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 15.02.2013, 14:03:05 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * The Service class is an abstraction of each service instance. 
 * Used to centralize the behavior related to every servcie instances.
 *
 * @author Joel Bout, <joel@taotesting.com>
 */
require_once('tao/models/classes/class.GenerisService.php');

/* user defined includes */
// section 10-30-1--78-1b01f2ef:13ac03fd34f:-8000:0000000000003C03-includes begin
// section 10-30-1--78-1b01f2ef:13ac03fd34f:-8000:0000000000003C03-includes end

/* user defined constants */
// section 10-30-1--78-1b01f2ef:13ac03fd34f:-8000:0000000000003C03-constants begin
// section 10-30-1--78-1b01f2ef:13ac03fd34f:-8000:0000000000003C03-constants end

/**
 * Short description of class tao_models_classes_FileSourceService
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 * @subpackage models_classes
 */
class tao_models_classes_FileSourceService
    extends tao_models_classes_GenerisService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method getFileSourceClass
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return core_kernel_classes_Class
     */
    public function getFileSourceClass()
    {
        $returnValue = null;

        // section 10-30-1--78--66279e2e:13bfb5107cf:-8000:0000000000003C9E begin
        $returnValue = new core_kernel_classes_Class(CLASS_GENERIS_VERSIONEDREPOSITORY);
        // section 10-30-1--78--66279e2e:13bfb5107cf:-8000:0000000000003C9E end

        return $returnValue;
    }

    /**
     * Short description of method addLocalSource
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  string label
     * @param  string path
     * @return core_kernel_versioning_Repository
     */
    public function addLocalSource($label, $path)
    {
        $returnValue = null;

        // section 10-30-1--78-1b01f2ef:13ac03fd34f:-8000:0000000000003C05 begin
        $res = $this->getFileSourceClass()->createInstanceWithProperties(array(
        	RDFS_LABEL										=> $label,
        	PROPERTY_GENERIS_VERSIONEDREPOSITORY_TYPE		=> INSTANCE_GENERIS_VCS_TYPE_LOCAL,
        	PROPERTY_GENERIS_VERSIONEDREPOSITORY_PATH		=> $path,
        	PROPERTY_GENERIS_VERSIONEDREPOSITORY_ENABLED	=> GENERIS_TRUE
        ));
        $returnValue = new core_kernel_versioning_Repository($res);
        // section 10-30-1--78-1b01f2ef:13ac03fd34f:-8000:0000000000003C05 end

        return $returnValue;
    }

    /**
     * Short description of method deleteFileSource
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource fileSource
     * @return boolean
     */
    public function deleteFileSource( core_kernel_classes_Resource $fileSource)
    {
        $returnValue = (bool) false;

        // section 10-30-1--78-1b01f2ef:13ac03fd34f:-8000:0000000000003C07 begin
        // section 10-30-1--78-1b01f2ef:13ac03fd34f:-8000:0000000000003C07 end

        return (bool) $returnValue;
    }

} /* end of class tao_models_classes_FileSourceService */

?>