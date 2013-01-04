<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core/kernel/versioning/local/class.File.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 19.12.2012, 14:52:56 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package core
 * @subpackage kernel_versioning_local
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include core_kernel_versioning_FileInterface
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('core/kernel/versioning/interface.FileInterface.php');

/* user defined includes */
// section 10-30-1--78-73b2f78e:13bb35d7c97:-8000:0000000000001E37-includes begin
// section 10-30-1--78-73b2f78e:13bb35d7c97:-8000:0000000000001E37-includes end

/* user defined constants */
// section 10-30-1--78-73b2f78e:13bb35d7c97:-8000:0000000000001E37-constants begin
// section 10-30-1--78-73b2f78e:13bb35d7c97:-8000:0000000000001E37-constants end

/**
 * Short description of class core_kernel_versioning_local_File
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package core
 * @subpackage kernel_versioning_local
 */
class core_kernel_versioning_local_File
        implements core_kernel_versioning_FileInterface
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute instance
     *
     * @access private
     * @var File
     */
    private static $instance = null;

    // --- OPERATIONS ---

    /**
     * Short description of method commit
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  File resource
     * @param  string message
     * @param  string path
     * @param  boolean recursive
     * @return boolean
     * @see core_kernel_versioning_File::commit()
     */
    public function commit( core_kernel_classes_File $resource, $message, $path, $recursive = false)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-6b8f17d3:132493e0488:-8000:000000000000165A begin
        common_Logger::i(__FUNCTION__.' called on local directory', 'LOCALVCS');
        $returnValue = true;
        // section 127-0-1-1-6b8f17d3:132493e0488:-8000:000000000000165A end

        return (bool) $returnValue;
    }

    /**
     * Short description of method update
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  File resource
     * @param  string path
     * @param  int revision
     * @return boolean
     * @see core_kernel_versioning_File::update()
     */
    public function update( core_kernel_classes_File $resource, $path, $revision = null)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-6b8f17d3:132493e0488:-8000:000000000000165C begin
        common_Logger::i(__FUNCTION__.' called on local directory', 'LOCALVCS');
        $returnValue = true;
        // section 127-0-1-1-6b8f17d3:132493e0488:-8000:000000000000165C end

        return (bool) $returnValue;
    }

    /**
     * Short description of method revert
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  File resource
     * @param  int revision
     * @param  string msg
     * @return boolean
     * @see core_kernel_versioning_File::revert()
     */
    public function revert( core_kernel_classes_File $resource, $revision = null, $msg = "")
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-6b8f17d3:132493e0488:-8000:000000000000165E begin
		throw new core_kernel_versioning_exception_Exception(__METHOD__.' not supported by Local Directory');
        // section 127-0-1-1-6b8f17d3:132493e0488:-8000:000000000000165E end

        return (bool) $returnValue;
    }

    /**
     * Short description of method delete
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  File resource
     * @param  string path
     * @return boolean
     * @see core_kernel_versioning_File::delete()
     */
    public function delete( core_kernel_classes_File $resource, $path)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-7caa4aeb:1324dd0a1a4:-8000:0000000000001678 begin
        common_Logger::i(__FUNCTION__.' called on local directory', 'LOCALVCS');
        unlink($path);
        // section 127-0-1-1-7caa4aeb:1324dd0a1a4:-8000:0000000000001678 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method add
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  File resource
     * @param  string path
     * @param  boolean recursive
     * @param  boolean force
     * @return boolean
     * @see core_kernel_versioning_File::add()
     */
    public function add( core_kernel_classes_File $resource, $path, $recursive = false, $force = false)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-13a27439:132dd89c261:-8000:00000000000016F1 begin
        common_Logger::i(__FUNCTION__.' called on local directory ', 'LOCALVCS');
        // section 127-0-1-1-13a27439:132dd89c261:-8000:00000000000016F1 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getHistory
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  File resource
     * @param  string path
     * @return array
     * @see core_kernel_versioning_File::gethistory()
     */
    public function getHistory( core_kernel_classes_File $resource, $path)
    {
        $returnValue = array();

        // section 127-0-1-1--57fd8084:132ecf4b934:-8000:00000000000016FB begin
		throw new core_kernel_versioning_exception_Exception(__METHOD__.' not supported by local directory');
        // section 127-0-1-1--57fd8084:132ecf4b934:-8000:00000000000016FB end

        return (array) $returnValue;
    }

    /**
     * Short description of method getStatus
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  File resource
     * @param  string path
     * @param  array options
     * @return int
     */
    public function getStatus( core_kernel_classes_File $resource, $path, $options = array())
    {
        $returnValue = (int) 0;

        // section 127-0-1-1-7a3aeccb:1351527b8af:-8000:0000000000001902 begin
        common_Logger::i(__FUNCTION__.' called on local directory', 'LOCALVCS');
        $returnValue = VERSIONING_FILE_STATUS_NORMAL;
        // section 127-0-1-1-7a3aeccb:1351527b8af:-8000:0000000000001902 end

        return (int) $returnValue;
    }

    /**
     * Short description of method resolve
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  File resource
     * @param  string path
     * @param  string version
     * @return boolean
     */
    public function resolve( core_kernel_classes_File $resource, $path, $version)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-7a3aeccb:1351527b8af:-8000:0000000000001921 begin
		throw new core_kernel_versioning_exception_Exception(__METHOD__.' not supported by Local Directory');
        // section 127-0-1-1-7a3aeccb:1351527b8af:-8000:0000000000001921 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return core_kernel_versioning_local_File
     */
    public static function singleton()
    {
        $returnValue = null;

        // section 10-30-1--78-73b2f78e:13bb35d7c97:-8000:0000000000001E43 begin
        if(is_null(self::$instance)){
			self::$instance = new self();
		}
		$returnValue = self::$instance;
		// section 10-30-1--78-73b2f78e:13bb35d7c97:-8000:0000000000001E43 end

        return $returnValue;
    }

} /* end of class core_kernel_versioning_local_File */

?>