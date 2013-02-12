<?php

error_reporting(E_ALL);

/**
 * TAO - tao\helpers\funcACL\class.Cache.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 05.02.2013, 14:16:33 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @package tao
 * @subpackage helpers_funcACL
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 10-13-1-85--1d76564e:13ca4d5068d:-8000:0000000000003C63-includes begin
// section 10-13-1-85--1d76564e:13ca4d5068d:-8000:0000000000003C63-includes end

/* user defined constants */
// section 10-13-1-85--1d76564e:13ca4d5068d:-8000:0000000000003C63-constants begin
// section 10-13-1-85--1d76564e:13ca4d5068d:-8000:0000000000003C63-constants end

/**
 * Short description of class tao_helpers_funcACL_Cache
 *
 * @access public
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @package tao
 * @subpackage helpers_funcACL
 */
class tao_helpers_funcACL_Cache
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute SERIAL_PREFIX_MODULE
     *
     * @access public
     * @var string
     */
    const SERIAL_PREFIX_MODULE = 'acl';

    // --- OPERATIONS ---

    /**
     * Short description of method cacheExtension
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  Extension extension
     * @return void
     */
    public static function cacheExtension( common_ext_Extension $extension)
    {
        // section 10-13-1-85--1d76564e:13ca4d5068d:-8000:0000000000003C64 begin
        foreach (tao_helpers_funcACL_Model::getModules($extension->getID()) as $module){
        	self::cacheModule($module);
        }
        // section 10-13-1-85--1d76564e:13ca4d5068d:-8000:0000000000003C64 end
    }

    /**
     * Short description of method cacheModule
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  Resource module
     * @return void
     */
    public static function cacheModule( core_kernel_classes_Resource $module)
    {
        // section 10-13-1-85--1d76564e:13ca4d5068d:-8000:0000000000003C67 begin
        $userService = core_kernel_users_Service::singleton();
        $serial = self::buildModuleSerial($module);
        $roleClass = new core_kernel_classes_Class(CLASS_ROLE);
        $grantedModulesProperty = new core_kernel_classes_Property(PROPERTY_ACL_MODULE_GRANTACCESS);
        $grantedActionsProperty = new core_kernel_classes_Property(PROPERTY_ACL_ACTION_GRANTACCESS);
        $actionIdentifierProperty = new core_kernel_classes_Property(PROPERTY_ACL_ACTION_ID);
        $memberOfProperty = new core_kernel_classes_Property(PROPERTY_ACL_ACTION_MEMBEROF);
        
        $toCache = array('module' => array(), 'actions' => array());
        
        // retrive roles that grant that module.
        $filters = array($grantedModulesProperty->getUri() => $module->getUri());
        $options = array('recursive' => false, 'like' => false);
        
        foreach ($roleClass->searchInstances($filters, $options) as $grantedRole){
        	$toCache['module'][] = $grantedRole->getUri();
        }
        
        foreach ($roleClass->getInstances() as $role){
        	$actions = $role->getPropertyValues($grantedActionsProperty);
        	
        	foreach ($actions as $grantedAction){
	        	try {
					$grantedAction = new core_kernel_classes_Resource($grantedAction);
					$memberOf = $grantedAction->getUniquePropertyValue($memberOfProperty);
					
					if ($memberOf->getUri() == $module->getUri()){
						
						$grantedActionUri = $grantedAction->getUri();
						if (!isset($toCache['actions'][$grantedActionUri])){
							$toCache['actions'][$grantedActionUri] = array();	
						}
						
						$toCache['actions'][$grantedActionUri][] = $role->getUri();
					}
	        	}
	        	catch (Exception $e){
	        		$moduleLabel = $module->getLabel();
	        		$actionLabel = $grantedAction->getLabel();
	        		common_Logger::w("Action '${moduleLabel}/${actionLabel}' has no 'actionMemberOf' property value.");
	        	}
        	}
        }
        
        $fileCache = common_cache_FileCache::singleton();
        $fileCache->put($toCache, $serial);
        // section 10-13-1-85--1d76564e:13ca4d5068d:-8000:0000000000003C67 end
    }

    /**
     * Short description of method retrieveModule
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  Resource module
     * @return array
     */
    public static function retrieveModule( core_kernel_classes_Resource $module)
    {
        $returnValue = array();

        // section 10-13-1-85--1d76564e:13ca4d5068d:-8000:0000000000003C6D begin
        try{
        	$fileCache = common_cache_FileCache::singleton();
        	$returnValue = $fileCache->get(self::buildModuleSerial($module));
        }
        catch (common_exception_FileSystemError $e){
        	$msg = "Module cache for ACL not found.";
        	throw new common_cache_Exception($msg);
        }
        // section 10-13-1-85--1d76564e:13ca4d5068d:-8000:0000000000003C6D end

        return (array) $returnValue;
    }

    /**
     * Short description of method flush
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return void
     */
    public static function flush()
    {
        // section 10-13-1-85--1d76564e:13ca4d5068d:-8000:0000000000003C70 begin
    	$cacheDir = GENERIS_CACHE_PATH;
        $matching = self::SERIAL_PREFIX_MODULE;
        if (@is_readable($cacheDir) && @is_dir($cacheDir)){
        	$files = scandir($cacheDir);
        	if ($files !== false){
        		foreach ($files as $f){
        			$canonicalPath = $cacheDir . $f;
        			if ($f[0] != '.' && @is_writable($canonicalPath) && preg_match("/^${matching}/", $f)){
        				unlink($canonicalPath);
        			}
        		}
        	}
        	else{
        		$msg = 'The ACL Cache cannot be scanned.';
        		throw new tao_helpers_funcACL_CacheException($msg);
        	}
        }
        else{
        	$msg = 'The ACL Cache is not readable or is not a directory.';
        	throw new tao_helpers_funcACL_CacheException($msg);
        }
        // section 10-13-1-85--1d76564e:13ca4d5068d:-8000:0000000000003C70 end
    }

    /**
     * Short description of method buildModuleSerial
     *
     * @access private
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  Resource module
     * @return string
     */
    private static function buildModuleSerial( core_kernel_classes_Resource $module)
    {
        $returnValue = (string) '';

        // section 10-13-1-85--1d76564e:13ca4d5068d:-8000:0000000000003C72 begin
        $uri = explode('#', $module->getUri());
		list($type, $extId) = explode('_', $uri[1]);
        $returnValue = self::SERIAL_PREFIX_MODULE . $extId . urlencode($module->getUri());
        // section 10-13-1-85--1d76564e:13ca4d5068d:-8000:0000000000003C72 end

        return (string) $returnValue;
    }

    /**
     * Short description of method removeModule
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  Resource module
     * @return void
     */
    public static function removeModule( core_kernel_classes_Resource $module)
    {
        // section 10-13-1-85--1d76564e:13ca4d5068d:-8000:0000000000003C7C begin
        $fileCache = common_cache_FileCache::singleton();
        $fileCache->remove(self::buildModuleSerial($module));
        // section 10-13-1-85--1d76564e:13ca4d5068d:-8000:0000000000003C7C end
    }

} /* end of class tao_helpers_funcACL_Cache */

?>