<?php

error_reporting(E_ALL);

/**
 * This class helps you to manage meta references to resources
 * (classes and instances). 
 * You can define the caching method by resource kind.
 * By default, the classes reference is cached in memory
 * and the instances are not cached
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package core
 * @subpackage kernel_persistence_hardapi
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1-8da8919:12f7878e80a:-8000:000000000000162A-includes begin
// section 127-0-1-1-8da8919:12f7878e80a:-8000:000000000000162A-includes end

/* user defined constants */
// section 127-0-1-1-8da8919:12f7878e80a:-8000:000000000000162A-constants begin
// section 127-0-1-1-8da8919:12f7878e80a:-8000:000000000000162A-constants end

/**
 * This class helps you to manage meta references to resources
 * (classes and instances). 
 * You can define the caching method by resource kind.
 * By default, the classes reference is cached in memory
 * and the instances are not cached
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package core
 * @subpackage kernel_persistence_hardapi
 */
class core_kernel_persistence_hardapi_ResourceReferencer
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * single self instance
     *
     * @access private
     * @var ResourceReferencer
     */
    private static $_instance = null;

    /**
     * Short description of attribute CACHE_NONE
     *
     * @access public
     * @var int
     */
    const CACHE_NONE = 0;

    /**
     * Short description of attribute CACHE_MEMORY
     *
     * @access public
     * @var int
     */
    const CACHE_MEMORY = 1;

    /**
     * Short description of attribute CACHE_FILE
     *
     * @access public
     * @var int
     */
    const CACHE_FILE = 2;

    /**
     * Short description of attribute CACHE_DB
     *
     * @access public
     * @var int
     */
    const CACHE_DB = 3;

    /**
     * Short description of attribute cacheModes
     *
     * @access protected
     * @var array
     */
    protected $cacheModes = array();

    /**
     * Short description of attribute _classes
     *
     * @access private
     * @var mixed
     */
    private static $_classes = null;

    /**
     * Short description of attribute _resources
     *
     * @access private
     * @var array
     */
    private static $_resources = array();

    /**
     * Short description of attribute _resources_loaded
     *
     * @access private
     * @var boolean
     */
    private static $_resources_loaded = false;

    /**
     * Short description of attribute _properties
     *
     * @access private
     * @var mixed
     */
    private static $_properties = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access private
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    private function __construct()
    {
        // section 127-0-1-1-8da8919:12f7878e80a:-8000:0000000000001633 begin
        
    	//default cache values
		$this->cacheModes = array(
			'instance' 	=> self::CACHE_NONE,
			'class'		=> self::CACHE_MEMORY,
			'property'	=> self::CACHE_FILE
		);
    	
        // section 127-0-1-1-8da8919:12f7878e80a:-8000:0000000000001633 end
    }

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return core_kernel_persistence_hardapi_ResourceReferencer
     */
    public static function singleton()
    {
        $returnValue = null;

        // section 127-0-1-1-8da8919:12f7878e80a:-8000:0000000000001635 begin
        
        if (is_null(self::$_instance)){
			$class = __CLASS__;
        	self::$_instance = new $class();
        }
        $returnValue = self::$_instance;
        
        // section 127-0-1-1-8da8919:12f7878e80a:-8000:0000000000001635 end

        return $returnValue;
    }

    /**
     * Short description of method setCache
     *
     * @access protected
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string type
     * @param  int mode
     * @return mixed
     */
    protected function setCache($type, $mode)
    {
        // section 127-0-1-1-78ed0233:12fde709f61:-8000:000000000000170D begin
        
        if(!array_key_exists($type, $this->cacheModes)){
        	throw new core_kernel_persistence_hardapi_Exception("Unknow cacheable object $type");
        }
        $refClass = new ReflectionClass($this);
        if(!in_array($mode, $refClass->getConstants())){
        	throw new core_kernel_persistence_hardapi_Exception("Unknow CACHE MODE $mode");
        }
        
        $this->cacheModes[$type] = $mode;
        
        // section 127-0-1-1-78ed0233:12fde709f61:-8000:000000000000170D end
    }

    /**
     * Short description of method setClassCache
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  int mode
     * @return mixed
     */
    public function setClassCache($mode)
    {
        // section 127-0-1-1-8da8919:12f7878e80a:-8000:000000000000164C begin
    	
    	$this->setCache('class', $mode);
    	
        // section 127-0-1-1-8da8919:12f7878e80a:-8000:000000000000164C end
    }

    /**
     * Short description of method setInstanceCache
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  int mode
     * @return mixed
     */
    public function setInstanceCache($mode)
    {
        // section 127-0-1-1-8da8919:12f7878e80a:-8000:000000000000164F begin
        
    	$this->setCache('instance', $mode);
    	
        // section 127-0-1-1-8da8919:12f7878e80a:-8000:000000000000164F end
    }

    /**
     * Short description of method setPropertyCache
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  int mode
     * @return mixed
     */
    public function setPropertyCache($mode)
    {
        // section 127-0-1-1-78ed0233:12fde709f61:-8000:0000000000001711 begin

    	$this->setCache('property', $mode);
    	
        // section 127-0-1-1-78ed0233:12fde709f61:-8000:0000000000001711 end
    }

    /**
     * Short description of method loadClasses
     *
     * @access private
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  boolean force
     * @return mixed
     */
    private function loadClasses($force = false)
    {
        // section 127-0-1-1-8da8919:12f7878e80a:-8000:0000000000001666 begin
        
    	if(is_null(self::$_classes) || $force){
			$dbWrapper = core_kernel_classes_DbWrapper::singleton();
			$result = $dbWrapper->query('SELECT "id", "uri", "table", "topClass" FROM "class_to_table"');

			self::$_classes = array();
			while ($row = $result->fetch()) {
	        	self::$_classes[$row['uri']] = array(
	        		'id'	=> $row['id'],
	        		'uri' 	=> $row['uri'],
	        		'table' => $row['table'],
	        		'topClass' => $row['topClass']
	        	);
	        }
	}
    	
        // section 127-0-1-1-8da8919:12f7878e80a:-8000:0000000000001666 end
    }

    /**
     * Short description of method isClassReferenced
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Class class
     * @param  string table
     * @return boolean
     */
    public function isClassReferenced( core_kernel_classes_Class $class, $table = null)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-8da8919:12f7878e80a:-8000:0000000000001652 begin
        
        if(!is_null($class)){
			switch($this->cacheModes['class']){
				
				case self::CACHE_NONE:
					$dbWrapper = core_kernel_classes_DbWrapper::singleton();
					if(is_null($table)){
						$result = $dbWrapper->query('SELECT "id" FROM "class_to_table" WHERE "uri" = ?', array($class->uriResource));
					}
					else{
						$result = $dbWrapper->query('SELECT "id" FROM "class_to_table" WHERE "uri" = ? AND "table" = ?', array($class->uriResource, $table));
					}
					
					if($row = $result->fetch()){
						$returnValue = true;
					}
					break;
					
				case self::CACHE_MEMORY:
					
					$this->loadClasses();
					
						if(is_null($table)){
							foreach(self::$_classes as $aClass){
								if(isset($aClass['uri']) && $aClass['uri'] == $class->uriResource ){
									$returnValue = true;
									break;
								}
							}
						}
						else{
							foreach(self::$_classes as $aClass){
							if(isset($aClass['uri']) && $aClass['uri'] == $class->uriResource 
								&& isset($aClass['table']) && $aClass['table'] == $table){
								$returnValue = true;
								break;
							}
						}
					}
					
					break;
					
				default:
					throw core_kernel_persistence_hardapi_Exception("File and Db cache not yet implemented for classes");
					break;
					
			}
		}
        
        // section 127-0-1-1-8da8919:12f7878e80a:-8000:0000000000001652 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method referenceClass
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Class class
     * @param  array options
     * @return boolean
     */
    public function referenceClass( core_kernel_classes_Class $class, $options = array())
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-8da8919:12f7878e80a:-8000:0000000000001655 begin
        common_logger::d('Referencing: '.$class->getUri());
        
        // Get optional parameters
        $table = isset($options['table']) ? $options['table'] : '_'.core_kernel_persistence_hardapi_Utils::getShortName($class);
        $topClass = isset($options['topClass']) ? $options['topClass'] : new core_kernel_classes_Class(CLASS_GENERIS_RESOURCE);
        $additionalProperties = isset($options['additionalProperties']) ? $options['additionalProperties'] : array ();
        $classId = null;
        
        // Is the class is not already referenced
        if(!$this->isClassReferenced($class, $table)){
        	
        	$topClassUri = $topClass->uriResource;
			$dbWrapper = core_kernel_classes_DbWrapper::singleton();
			
			$query = 'INSERT INTO "class_to_table" ("uri", "table", "topClass") VALUES (?,?,?)';
			$result = $dbWrapper->exec($query, array(
				$class->uriResource, 
				$table,
				$topClassUri
			));
			
			// Get last inserted id
			$query = 'SELECT "id" FROM "class_to_table" WHERE "uri" = ? AND "table" = ?';
			$result = $dbWrapper->query($query, array(
				$class->uriResource, 
				$table
			));
			if ($row = $result->fetch()){
				$classId = $row['id'];
				$result->closeCursor();
			} else {
				throw new core_kernel_persistence_hardapi_Exception("Unable to retrieve the class Id of the referenced class {$class->uriResource}");
			}
			
			try{
				// Store additional properties
				if (!is_null($additionalProperties) && !empty($additionalProperties)){
					$query = 'INSERT INTO "class_additional_properties" ("class_id", "property_uri") VALUES';
					foreach ($additionalProperties as $additionalProperty){
						$query .= " ('{$classId}', '{$additionalProperty->uriResource}')";
					}
					$result = $dbWrapper->exec($query);
				} 		
				
				
				if($result !== false){
					
					$returnValue = true;
					if($this->cacheModes['class'] == self::CACHE_MEMORY && !is_null(self::$_classes)){
						$memQuery = 'SELECT "id", "uri", "table", "topClass" 
							FROM "class_to_table" 
							WHERE "uri" = ? 
							AND "table" = ?';
						$memResult = $dbWrapper->query($memQuery, array($class->uriResource, $table));
						while($row = $memResult->fetch()){
							self::$_classes[$row['uri']] = array(
				        		'id'		=> $row['id'],
				        		'uri' 		=> $row['uri'],
				        		'table' 	=> $row['table'],
								'topClass' 	=> $row['topClass']
				        	);
						}
					}
				}
			}
			catch (PDOException $e){
				throw new core_kernel_persistence_hardapi_Exception("Unable to reference the additional properties of the class {$class->uriResource} in class_additional_properties: " . $e->getMessage());
			}
		}
        
        // section 127-0-1-1-8da8919:12f7878e80a:-8000:0000000000001655 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method unReferenceClass
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Class class
     * @return boolean
     */
    public function unReferenceClass( core_kernel_classes_Class $class)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-8da8919:12f7878e80a:-8000:0000000000001658 begin
        
        if($this->isClassReferenced($class)){
                
			$tableName = '_'.core_kernel_persistence_hardapi_Utils::getShortName($class);
                        
            //need to instanciate table manager before unreferencing otherwise, the "remove table" will fail
            $tm = new core_kernel_persistence_hardapi_TableManager($tableName);
                        
            // Delete reference of the class in classs_to_table, resource_has_class, resource_to_table
			$dbWrapper = core_kernel_classes_DbWrapper::singleton();
                        
			// Remove references of the resources in the resource has class table
            $queries = array();
			$queries[] = 'DELETE 
				FROM "resource_has_class" 
				WHERE "resource_has_class"."resource_id" 
					IN (SELECT "resource_to_table"."id" FROM "resource_to_table" WHERE "resource_to_table"."table" = \''.$tableName.'\' );';
			// Remove reference of the class in the additional properties tables
			$queries[] = 'DELETE 
				FROM "class_additional_properties"
				WHERE "class_id" 
					IN (SELECT "class_to_table"."id" FROM "class_to_table" WHERE "class_to_table"."table" = \''.$tableName.'\' );';
			// Remove resferences of the resources int resource to table table
			$queries[] = 'DELETE FROM "resource_to_table" WHERE "resource_to_table"."table" = \''.$tableName.'\';';
			// Remove reference of the class in the class to table table
			$queries[] = 'DELETE FROM "class_to_table" WHERE "class_to_table"."table" = \''.$tableName.'\';';
			
			$returnValue = true;
			
			try{
				foreach ($queries as $query){
					$result = $dbWrapper->exec($query);
					
					if ($result === false){
						$returnValue = false;
					}
				}
	                        
				if($returnValue !== false){
					// delete table associated to the class
					$tm->remove();
					// remove class from the cache
					if($this->cacheModes['class'] == self::CACHE_MEMORY && is_array(self::$_classes)){
						foreach(self::$_classes as $index => $aClass){
							if($aClass['uri'] == $class->uriResource){
								unset(self::$_classes[$index]);
							}
						}
					}
				}
				
				core_kernel_persistence_ClassProxy::$ressourcesDelegatedTo = array();
				core_kernel_persistence_ResourceProxy::$ressourcesDelegatedTo = array();
				core_kernel_persistence_PropertyProxy::$ressourcesDelegatedTo = array();
			}
			catch (PDOException $e){
				throw new core_kernel_persistence_hardapi_Exception("Unable to unreference class {$class->uriResource} : " .$e->getMessage());
			}
		}
        
        // section 127-0-1-1-8da8919:12f7878e80a:-8000:0000000000001658 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method classLocations
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Class class
     * @return array
     */
    public function classLocations( core_kernel_classes_Class $class)
    {
        $returnValue = array();

        // section 127-0-1-1-46522299:12fc0802dbc:-8000:00000000000016C7 begin
        
        if(!is_null($class)){
			switch($this->cacheModes['class']){
				
				case self::CACHE_NONE:
			        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
			        
			        $query = "SELECT id, uri, table, topClass FROM class_to_table WHERE uri=? ";
			    	$result = $dbWrapper->query($query, array ($class->uriResource));

					while($row = $result->fetch()){
						$returnValue[$row['uri']] = array(
							'id'	=> $row['id'],
			        		'uri' 	=> $row['uri'],
			        		'table' => $row['table'],
			        		'topClass' => $row['topClass']
						);
					}
			        break;
			
			   case self::CACHE_MEMORY:
			   		$this->loadClasses();
			   		foreach( self::$_classes as $key =>  $res){
						if($res['uri'] == $class->uriResource){
							$returnValue[] = $res;
						}
					}
			   break;
			}
		}
		
        // section 127-0-1-1-46522299:12fc0802dbc:-8000:00000000000016C7 end

        return (array) $returnValue;
    }

    /**
     * Short description of method loadResources
     *
     * @access private
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  boolean force
     * @return mixed
     */
    private function loadResources($force = false)
    {
        // section 127-0-1-1-8da8919:12f7878e80a:-8000:000000000000166E begin
        
    	if(!self::$_resources_loaded || $force){
			$dbWrapper = core_kernel_classes_DbWrapper::singleton();
			$result = $dbWrapper->query('SELECT "uri", "table" FROM "resource_to_table"');
			while ($row = $result->fetch()) {
	        	self::$_resources[$row['uri']] = $row['table'];
	        }
	        self::$_resources_loaded = true;
		}
    	
    	
        // section 127-0-1-1-8da8919:12f7878e80a:-8000:000000000000166E end
    }

    /**
     * Short description of method isResourceReferenced
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @return boolean
     */
    public function isResourceReferenced( core_kernel_classes_Resource $resource)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-8da8919:12f7878e80a:-8000:000000000000165B begin
        
        if(!is_null($resource)){
			switch($this->cacheModes['instance']){
				
				case self::CACHE_NONE:
					if(array_key_exists($resource->uriResource, self::$_resources)){
						$returnValue = true;
						break;
					}
					
					$dbWrapper = core_kernel_classes_DbWrapper::singleton();
					$result = $dbWrapper->query('SELECT "table" FROM "resource_to_table" WHERE "uri" = ?', array($resource->uriResource));
					$fetch = $result->fetchAll();
					if(count($fetch) > 0){
						self::$_resources[$resource->uriResource] = $fetch[0]['table'];
						$returnValue = true;
					}	
					
					$result->closeCursor();
				break;
					
				case self::CACHE_MEMORY:
					
					$this->loadResources();
					$returnValue = array_key_exists($resource->uriResource, self::$_resources);
					break;
					
				default:
					throw core_kernel_persistence_hardapi_Exception("File and Db cache not yet implemented for resources");
					break;
			}
		}
        
        // section 127-0-1-1-8da8919:12f7878e80a:-8000:000000000000165B end

        return (bool) $returnValue;
    }

    /**
     * Short description of method referenceResource
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  string table
     * @param  array types
     * @param  boolean referenceClassLink
     * @return boolean
     */
    public function referenceResource( core_kernel_classes_Resource $resource, $table, $types = null, $referenceClassLink = false)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-8da8919:12f7878e80a:-8000:000000000000165E begin
        $types = !is_null($types) ? $types : $resource->getTypes();
        $rows = array ();
        if(!$this->isResourceReferenced($resource)){
			$dbWrapper = core_kernel_classes_DbWrapper::singleton();
			
			$query = 'INSERT INTO "resource_to_table" ("uri", "table") VALUES (?,?)';
			$insertResult = $dbWrapper->exec($query, array($resource->uriResource, $table));

			if($referenceClassLink && $insertResult !== false){
				$query = 'SELECT * FROM "resource_to_table" WHERE "uri" = ? AND "table" = ?';
				$result = $dbWrapper->query($query, array($resource->uriResource, $table));
				while($row = $result->fetch(PDO::FETCH_ASSOC)){
					$rows[] = $row;
				}
			}
			$returnValue = (bool) $insertResult;
			
        	if($referenceClassLink){
        		
				foreach($types as $type){
					
					$typeClass = new core_kernel_classes_Class($type->uriResource);
					if($this->isClassReferenced($typeClass)){
						
						$classLocations = $this->classLocations($typeClass);
						foreach ($classLocations as $classLocation){
							
							foreach($rows as $row){
								$query = "INSERT INTO resource_has_class (resource_id, class_id) VALUES (?,?)";
								$dbWrapper->exec($query, array($row['id'], $classLocation['id']));
							}
						}
					}
				}
			}
			if($returnValue){
				foreach($rows as $row){
					self::$_resources[$row['uri']] = $row['table'];
				}
			}
        }
        // section 127-0-1-1-8da8919:12f7878e80a:-8000:000000000000165E end

        return (bool) $returnValue;
    }

    /**
     * Short description of method unReferenceResource
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @return boolean
     */
    public function unReferenceResource( core_kernel_classes_Resource $resource)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-8da8919:12f7878e80a:-8000:0000000000001661 begin
        
        if($this->isResourceReferenced($resource)){
                
                $dbWrapper = core_kernel_classes_DbWrapper::singleton();

                //select id to be removed:
                $resourceId = core_kernel_persistence_hardapi_Utils::getResourceIdByTable($resource, 'resource_to_table');
                if($resourceId){
                        $queries[] = 'DELETE FROM "resource_has_class" WHERE "resource_has_class"."resource_id" = \'' . $resourceId . '\';';
                        $queries[] = 'DELETE FROM "resource_to_table" WHERE "resource_to_table"."id" = \'' . $resourceId . '\';';

                        $returnValue = true;
                        foreach ($queries as $query) {
                                $result = $dbWrapper->exec($query);

                                if ($result === false) {
                                        $returnValue = false;
                                }
                        }

                        if ($returnValue !== false) {
                                if (array_key_exists($resource->uriResource, self::$_resources)) {
                                        unset(self::$_resources[$resource->uriResource]);
                                }
                        }
                }

        }
        
        // section 127-0-1-1-8da8919:12f7878e80a:-8000:0000000000001661 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method resourceLocation
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @return string
     */
    public function resourceLocation( core_kernel_classes_Resource $resource)
    {
        $returnValue = (string) '';

        // section 127-0-1-1-56674b31:12fbf31d598:-8000:0000000000001505 begin
        
         if(!is_null($resource)){
			switch($this->cacheModes['instance']){
				
				case self::CACHE_NONE:
					if(array_key_exists($resource->uriResource, self::$_resources)){
						$returnValue = self::$_resources[$resource->uriResource];
						break;
					}
					
			        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
			        
			        $query = 'SELECT "table" FROM "resource_to_table" WHERE uri=?';
			    	$result = $dbWrapper->query($query, array ($resource->uriResource));

					if ($row = $result->fetch()){
						$returnValue = $row['table'];
						self::$_resources[$resource->uriResource] = $row['table'];
						$result->closeCursor();
					} else {
						common_Logger::w("Unable to find table for ressource " .$resource->getUri(), "GENERIS");
					}
					
			        break;
			
			   case self::CACHE_MEMORY:
			   		$this->loadResources();
			   		if(array_key_exists($resource->uriResource, self::$_resources)){
						$returnValue = self::$_resources[$resource->uriResource];
						break;
					}
			   break;
			   default:
					common_Logger::w('Unexpected cacheMode: '.$this->cacheModes['instance'], array('GENERIS'));
			}
		}
        // section 127-0-1-1-56674b31:12fbf31d598:-8000:0000000000001505 end

        return (string) $returnValue;
    }

    /**
     * Short description of method loadProperties
     *
     * @access private
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  boolean force
     * @param  array additionalProperties
     * @return mixed
     */
    private function loadProperties($force = false, $additionalProperties = array())
    {
        // section 127-0-1-1-78ed0233:12fde709f61:-8000:0000000000001723 begin
    	if(is_null(self::$_properties) || $force){
    		
			//file where is the data saved
			$file = realpath(GENERIS_CACHE_PATH) . '/hard-api-property.cache';
				
    		if(!$force && $this->cacheModes['property'] == self::CACHE_FILE){
    			
				//if the properties are cached in the file, we load it
				if(file_exists($file)){
					if(!is_readable($file)){
						throw new core_kernel_persistence_hardapi_Exception("Cache file $file must have read/write permissions");
					}
					$properties = @unserialize(file_get_contents($file));
					if($properties !== false && is_array($properties) && count($properties) > 0){
						self::$_properties = $properties;
						return;
					}
				}
			}

			//get all the compiled tables
    		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
    		$tables = array();
    		$query = 'SELECT DISTINCT "id","table" FROM "class_to_table"';
    		$result = $dbWrapper->query($query);
    		while($row = $result->fetch()){
    			$tables[$row['id']] = $row['table'];
    		}
    		
    		$additionalPropertiesTable = array();
    		$query = 'SELECT DISTINCT "class_id","property_uri" FROM "class_additional_properties"';
    		$result = $dbWrapper->query($query);
    		while($row = $result->fetch()){
    			$additionalPropertiesTable[$row['class_id']][] = new core_kernel_classes_Property($row['property_uri']);
    		}
    		//retrieve each property by table
			$this->loadClasses();
			    		
    		self::$_properties = array();
    		
    		foreach($tables as $classId => $table){

    			//check in $additionalPropertiesTable if current table is concerned by additionnal properties
    			if(isset($additionalPropertiesTable[$classId])){
	   				$additionalProperties = $additionalPropertiesTable[$classId];
    				
    			}
    			else{
    				$additionalProperties = array();
    			}
    			
    			$classUri = core_kernel_persistence_hardapi_Utils::getLongName($table);
    			$class = new core_kernel_classes_Class($classUri);
    			$topClassUri = self::$_classes[$classUri]['topClass'];
    			$topClass = new core_kernel_classes_Class($topClassUri);
                        $ps = new core_kernel_persistence_switcher_PropertySwitcher($class, $topClass);
                        $properties = $ps->getProperties($additionalProperties);
                        foreach ($properties as $property){
                                $propertyUri = $property->uriResource;
                                if ($property->isMultiple() || $property->isLgDependent()){

                                        if(isset(self::$_properties[$propertyUri])) {
                                                if (!in_array("{$table}Props", self::$_properties[$propertyUri])){
                                                        self::$_properties[$propertyUri][] = "{$table}Props";
                                                }
                                        } else {
                                                self::$_properties[$propertyUri] = array("{$table}Props");
                                        } 

                                } else {
									
                                        if(isset(self::$_properties[$propertyUri])) {
                                                if (!in_array("{$table}", self::$_properties[$propertyUri])){
                                                        self::$_properties[$propertyUri][] = "{$table}";
                                                }
                                        } else {
                                                self::$_properties[$propertyUri] = array("{$table}");
                                        } 
                                }
                          
            

                        }
         
    		}

    		//saving the properties in the cache file
    		if($this->cacheModes['property'] == self::CACHE_FILE){
    			
    			$returnValue = file_put_contents($file, serialize(self::$_properties));
				if(!$returnValue){
					throw new core_kernel_persistence_hardapi_Exception("cannot write the required property cache file in the location ".$file);
				}
    		}
    	}
    	
        // section 127-0-1-1-78ed0233:12fde709f61:-8000:0000000000001723 end
    }

    /**
     * Short description of method isPropertyReferenced
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Property property
     * @param  inClass
     * @return boolean
     */
    public function isPropertyReferenced( core_kernel_classes_Property $property, $inClass = null)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-78ed0233:12fde709f61:-8000:0000000000001714 begin
        
        if(!is_null($property)){
			switch($this->cacheModes['property']){
				
				case self::CACHE_FILE:
				case self::CACHE_MEMORY:
					$this->loadProperties();
					if(!empty($inClass)){
						$propertyLocation = $this->propertyLocation($property);
						if(!empty($propertyLocation)){
							if($inClass instanceof core_kernel_classes_Class){
								$classLocations = $this->classLocations($inClass);
								foreach($classLocations as $classTableData){
									if(in_array($classTableData['table'], $propertyLocation) ){
										$returnValue = true;
										break;
									}
								}
							}else if(is_string($inClass)){
								if(in_array((string) $inClass, $propertyLocation) ){
									$returnValue = true;
									break;
								}
							}
						}
					}else{
						$returnValue = array_key_exists($property->uriResource, self::$_properties);
//                                                var_dump($property->uriResource, $returnValue);
					}
					break;
					
				case self::CACHE_NONE:
					throw core_kernel_persistence_hardapi_Exception("Property are always cached");
				case self::CACHE_DB:
					throw core_kernel_persistence_hardapi_Exception("Db cache not yet implemented for classes");
			}
		}
        
        // section 127-0-1-1-78ed0233:12fde709f61:-8000:0000000000001714 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method propertyLocation
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Property property
     * @return array
     */
    public function propertyLocation( core_kernel_classes_Property $property)
    {
        $returnValue = array();

        // section 127-0-1-1-78ed0233:12fde709f61:-8000:0000000000001717 begin
        
        if(!is_null($property)){
			switch($this->cacheModes['property']){
				
				case self::CACHE_FILE:
				case self::CACHE_MEMORY:
					
					$this->loadProperties();
					if(isset(self::$_properties[$property->uriResource]) && is_array(self::$_properties[$property->uriResource])){
						$returnValue = self::$_properties[$property->uriResource];
					}
					break;
				default:
					throw new common_Exception('Unexpected cache-mode '.$this->cacheModes['property'].' for propertyLocation()');
			}
		}
        
        // section 127-0-1-1-78ed0233:12fde709f61:-8000:0000000000001717 end

        return (array) $returnValue;
    }

    /**
     * Short description of method referenceInstanceTypes
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Class class
     * @return boolean
     */
    public function referenceInstanceTypes( core_kernel_classes_Class $class)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-46522299:12fc0802dbc:-8000:00000000000016C4 begin
        
        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
        
        $query = "SELECT DISTINCT object FROM statements 
        			WHERE predicate = '".RDF_TYPE."' 
        			AND object != '{$class->uriResource}' 
         			AND subject IN (SELECT subject FROM statements 
        						WHERE predicate = '".RDF_TYPE."' 
        						AND object='{$class->uriResource}')";
        $result = $dbWrapper->query($query);

		$types = array();
        while($row = $result->fetch()){
        	$types[] = $row['object'];
        }
        
        $tableName = '_'.core_kernel_persistence_hardapi_Utils::getShortName($class);
        
        foreach($types as $type){
        	$this->referenceClass(new core_kernel_classes_Class($type), array ("table"=>$tableName));
        }
        
        // section 127-0-1-1-46522299:12fc0802dbc:-8000:00000000000016C4 end

        return (bool) $returnValue;
    }

    /**
     * please use clearCaches() instead
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @deprecated
     * @param  array additionalProperties
     * @return mixed
     */
    public function resetCache($additionalProperties = array())
    {
        // section 10-13-1--128-7c4fbea6:12fe371c06a:-8000:0000000000001573 begin
    	$this->loadClasses(true);
        $this->loadProperties(true, $additionalProperties);
        // section 10-13-1--128-7c4fbea6:12fe371c06a:-8000:0000000000001573 end
    }

    /**
     * Clears the caches without immediately recalculating them
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function clearCaches()
    {
        // section 127-0-1-1--770b92db:136a03f38fa:-8000:00000000000019B8 begin
    	self::$_properties	= null;
    	
    	self::$_classes		= null;
    	
    	self::$_resources			= array();
    	self::$_resources_loaded	= false;
    	
        $cachefile = realpath(GENERIS_CACHE_PATH) . '/hard-api-property.cache';
        if (file_exists($cachefile)) {
        	unlink($cachefile);
        }
        // section 127-0-1-1--770b92db:136a03f38fa:-8000:00000000000019B8 end
    }

    /**
     * Get additional properties used during class' compilation.
     * This function is usefull specially during unhardening
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Class clazz
     * @return array
     */
    public function getAdditionalProperties( core_kernel_classes_Class $clazz)
    {
        $returnValue = array();

        // section 127-0-1-1--642cfc1e:13160cfbaf5:-8000:000000000000162A begin
        
        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
        
		$query = "SELECT property_uri 
			FROM class_additional_properties, class_to_table 
			WHERE class_additional_properties.class_id = class_to_table.id
			AND class_to_table.uri = ?";
		$result = $dbWrapper->query($query, array($clazz->uriResource));
		
   		while($row = $result->fetch()){
			$returnValue[] = new core_kernel_classes_Property($row['property_uri']);
		}
        
        // section 127-0-1-1--642cfc1e:13160cfbaf5:-8000:000000000000162A end

        return (array) $returnValue;
    }

} /* end of class core_kernel_persistence_hardapi_ResourceReferencer */

?>