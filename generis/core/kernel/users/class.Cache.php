<?php

error_reporting(E_ALL);

/**
 * A facade aiming at helping client code to put User data in the Cache memory.
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package core
 * @subpackage kernel_users
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 10-13-1-85--436aa729:13c4e543eba:-8000:0000000000001F3A-includes begin
// section 10-13-1-85--436aa729:13c4e543eba:-8000:0000000000001F3A-includes end

/* user defined constants */
// section 10-13-1-85--436aa729:13c4e543eba:-8000:0000000000001F3A-constants begin
// section 10-13-1-85--436aa729:13c4e543eba:-8000:0000000000001F3A-constants end

/**
 * A facade aiming at helping client code to put User data in the Cache memory.
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package core
 * @subpackage kernel_users
 */
class core_kernel_users_Cache
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute SERIAL_PREFIX_INCLUDED_ROLES
     *
     * @access public
     * @var string
     */
    const SERIAL_PREFIX_INCLUDED_ROLES = 'roles-ir';

    // --- OPERATIONS ---

    /**
     * Retrieve roles included in a given Generis Role from the Cache memory.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource role A Generis Role Resource.
     * @return array
     */
    public static function retrieveIncludedRoles( core_kernel_classes_Resource $role)
    {
        $returnValue = array();

        // section 10-13-1-85--1142a8d8:13c5c9f800e:-8000:0000000000001F4D begin
        try{
        	$serial = self::buildIncludedRolesSerial($role);
        	$fileCache = common_cache_FileCache::singleton();
        	$fromCache = $fileCache->get($serial); // array of URIs.
        	
        	foreach ($fromCache as $uri){
        		$returnValue[$uri] = new core_kernel_classes_Resource($uri);
        	}
        }
        catch (common_cache_NotFoundException $e){
        	$roleUri = $role->getUri();
        	$msg = "Includes roles related to Role with URI '${roleUri}' is not in the Cache memory.";
        	throw new core_kernel_users_CacheException($msg);
        }
        // section 10-13-1-85--1142a8d8:13c5c9f800e:-8000:0000000000001F4D end

        return (array) $returnValue;
    }

    /**
     * Put roles included in a Generis Role in the Cache memory.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource role
     * @param  array includedRoles
     * @return boolean
     */
    public static function cacheIncludedRoles( core_kernel_classes_Resource $role, $includedRoles)
    {
        $returnValue = (bool) false;

        // section 10-13-1-85--436aa729:13c4e543eba:-8000:0000000000001F3B begin
        // Make a simple array of URIs with the included roles.
        $toCache = array();
        foreach ($includedRoles as $resource){
        	$toCache[] = $resource->getUri();
        }
        
        $serial = self::buildIncludedRolesSerial($role);
        $fileCache = common_cache_FileCache::singleton();
        try{
        	$fileCache->put($toCache, $serial);
        	$returnValue = true;
        }
        catch (common_Exception $e){
        	$roleUri = $role->getUri();
        	$msg = "An error occured while writing included roles in the cache memory for Role '${roleUri}': ";
        	$msg.= $e->getMessage();
        	throw new core_kernel_users_CacheException($msg);
        }
        // section 10-13-1-85--436aa729:13c4e543eba:-8000:0000000000001F3B end

        return (bool) $returnValue;
    }

    /**
     * Remove roles included in a Generis Role from the Cache memory.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource role A Generis Role as a Resource.
     * @return boolean
     */
    public static function removeIncludedRoles( core_kernel_classes_Resource $role)
    {
        $returnValue = (bool) false;

        // section 10-13-1-85--436aa729:13c4e543eba:-8000:0000000000001F3E begin
        $serial = self::buildIncludedRolesSerial($role);
        $fileCache = common_cache_FileCache::singleton();
        $fileCache->remove($serial);
        
        $returnValue = (file_exists(GENERIS_CACHE_PATH . $serial)) ? false : true;
        // section 10-13-1-85--436aa729:13c4e543eba:-8000:0000000000001F3E end

        return (bool) $returnValue;
    }

    /**
     * Returns true if the roles included in a given Generis Role are in the
     * memory.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource role The Generis Role you want to check if its included roles are in the cache memory.
     * @return boolean
     */
    public static function areIncludedRolesInCache( core_kernel_classes_Resource $role)
    {
        $returnValue = (bool) false;

        // section 10-13-1-85--436aa729:13c4e543eba:-8000:0000000000001F43 begin
        $serial = self::buildIncludedRolesSerial($role);
        $fileCache = common_cache_FileCache::singleton();
        $returnValue = $fileCache->has($serial);
        // section 10-13-1-85--436aa729:13c4e543eba:-8000:0000000000001F43 end

        return (bool) $returnValue;
    }

    /**
     * Build a serial aiming at identifying the includes roles of a given
     * Role in the Cache memory.
     *
     * @access private
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource role The role you want to create a serial for.
     * @return string
     */
    private static function buildIncludedRolesSerial( core_kernel_classes_Resource $role)
    {
        $returnValue = (string) '';

        // section 10-13-1-85-7aff3a42:13c4e6071d5:-8000:0000000000001F45 begin
        $returnValue = self::SERIAL_PREFIX_INCLUDED_ROLES . urlencode($role->getUri());
        // section 10-13-1-85-7aff3a42:13c4e6071d5:-8000:0000000000001F45 end

        return (string) $returnValue;
    }

    /**
     * Removes all entries related to included roles from the Cache memory.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return void
     */
    public static function flush()
    {
        // section 10-13-1-85--436aa729:13c4e543eba:-8000:0000000000001F41 begin
        $cacheDir = GENERIS_CACHE_PATH;
        $matching = self::SERIAL_PREFIX_INCLUDED_ROLES;
        if (@is_readable($cacheDir) && @is_dir($cacheDir)){
        	$files = scandir($cacheDir);
        	if ($files !== false){
        		foreach ($files as $f){
        			$canonicalPath = GENERIS_CACHE_PATH . $f;
        			if ($f[0] != '.' && @is_writable($canonicalPath) && preg_match("/^${matching}/", $f)){
        				unlink($canonicalPath);
        			}
        		}
        	}
        	else{
        		$msg = 'The Generis Cache cannot be scanned.';
        		throw new core_kernel_users_CacheException($msg);
        	}
        }
        else{
        	$msg = 'The Generis Cache is not readable or is not a directory.';
        	throw new core_kernel_users_CacheException($msg);
        }
        // section 10-13-1-85--436aa729:13c4e543eba:-8000:0000000000001F41 end
    }

} /* end of class core_kernel_users_Cache */

?>