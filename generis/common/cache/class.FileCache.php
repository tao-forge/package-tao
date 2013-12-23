<?php
/*  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);              2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * Generis Object Oriented API - common\cache\class.FileCache.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 18.01.2013, 15:31:57 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage cache
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * basic interface a cache implementation has to implement
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 */
require_once('common/cache/interface.Cache.php');

/**
 * Classes that implement this class claims their instances are serializable and
 * be identified by a unique serial string.
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 */
require_once('common/interface.Serializable.php');

/**
 * Short description of class common_cache_FileCache
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage cache
 */
class common_cache_FileCache
        implements common_cache_Cache
{

    /**
     * Short description of attribute instance
     *
     * @access private
     * @var FileCache
     */
    private static $instance = null;

    /**
     * puts "something" into the cache,
     *      * If this is an object and implements Serializable,
     *      * we use the serial provided by the object
     *      * else a serial must be provided
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  mixed
     * @param  string serial
     * @return mixed
     */
    public function put($mixed, $serial = null)
    {
        if ($mixed instanceof common_Serializable) {
        	if (!is_null($serial) && $serial != $mixed->getSerial()) {
        		throw new common_exception_Error('Serial mismatch for Serializable '.$mixed->getSerial());
        	}
        	$serial = $mixed->getSerial();
        }
        
        $data = "<?php return ".common_Utils::toPHPVariableString($mixed).";";
       	
        try{
        	// Acquire the lock and open with mode 'c'. Indeed, we do not use mode 'w' because
        	// it could truncate the file before it gets the lock!
        	$filePath = $this->getFilePath($serial);
        	if (false !== ($fp = @fopen($filePath, 'c')) && true === flock($fp, LOCK_EX)){
        		
        		// We first need to truncate.
        		ftruncate($fp, 0);
        		
        		fwrite($fp, $data);
        		@flock($fp, LOCK_UN);
        		@fclose($fp);
        	}
        	else{
        		$msg = "Unable to write cache file '${filePath}'.";
        		throw new common_exception_FileSystemError($msg);
        	}
        	
        }
        catch (common_exception_FileSystemError $e){
        	$msg  = "An unexpected error occured while creating a temporary ";
        	$msg .= "file to cache data with serial '${serial}': " . $e->getMessage();
        	
        	throw new common_cache_Exception($msg);
        }
    }

    /**
     * gets the entry associted to the serial
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string serial
     * @return common_Serializable
     */
    public function get($serial)
    {
        $returnValue = null;
        
        // Acquire a shared lock for reading on the main lock file.
        // We acquire the lock here because we have a critical section below:
        // 1. Check if we have something for this serial.
        // 2. Include the file corresponding to the serial. 
        $filePath = $this->getFilePath($serial);
        if (false !== ($fp = @fopen($filePath, 'r')) && true === flock($fp, LOCK_SH)){
        	$returnValue = include $this->getFilePath($serial);
        	
        	@flock($fp, LOCK_UN);
        	@fclose($fp);
        }
        else{
        	$msg = "Unable to read cache file '${filePath}'.";
        	throw new common_cache_NotFoundException($msg);
        }

        return $returnValue;
    }

    /**
     * test whenever an entry associted to the serial exists
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string serial
     * @return boolean
     */
    public function has($serial)
    {
        $returnValue = (bool) false;
        $returnValue = file_exists($this->getFilePath($serial));

        return (bool) $returnValue;
    }

    /**
     * removes an entry from the cache
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string serial
     * @return mixed
     */
    public function remove($serial)
    {
        @unlink($this->getFilePath($serial));
    }

    /**
     * empties the cache
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return mixed
     */
    public function purge()
    {
        $returnValue = null;

    	$cachepath =  GENERIS_CACHE_PATH;
        if (false !== ($files = scandir($cachepath))){
            foreach ($files as $f) {
                $filePath = $cachepath . $f;
                if (substr($f, 0, 1) != '.' && file_exists($filePath)){
                    @unlink($filePath);
                }
            }
        }

        return $returnValue;
    }

    /**
     * Short description of method getFilePath
     *
     * @access private
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string serial
     * @return string
     */
    private function getFilePath($serial)
    {
        $returnValue = (string) '';
        $returnValue = GENERIS_CACHE_PATH . $serial;

        return (string) $returnValue;
    }

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return common_cache_FileCache
     */
    public static function singleton()
    {
        $returnValue = null;

        if (!isset(self::$instance)){
        	self::$instance = new self();
        }
        
        return self::$instance;

        return $returnValue;
    }

}

?>