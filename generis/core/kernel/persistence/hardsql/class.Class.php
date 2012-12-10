<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core/kernel/persistence/hardsql/class.Class.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 28.02.2012, 17:23:20 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package core
 * @subpackage kernel_persistence_hardsql
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include core_kernel_persistence_PersistenceImpl
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('core/kernel/persistence/class.PersistenceImpl.php');

/**
 * include core_kernel_persistence_ClassInterface
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('core/kernel/persistence/interface.ClassInterface.php');

/* user defined includes */
// section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000013A7-includes begin
// section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000013A7-includes end

/* user defined constants */
// section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000013A7-constants begin
// section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000013A7-constants end

/**
 * Short description of class core_kernel_persistence_hardsql_Class
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package core
 * @subpackage kernel_persistence_hardsql
 */
class core_kernel_persistence_hardsql_Class
    extends core_kernel_persistence_PersistenceImpl
        implements core_kernel_persistence_ClassInterface
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute instance
     *
     * @access public
     * @var Resource
     */
    public static $instance = null;

    // --- OPERATIONS ---

    /**
     * Short description of method getSubClasses
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  boolean recursive
     * @return array
     */
    public function getSubClasses( core_kernel_classes_Resource $resource, $recursive = false)
    {
        $returnValue = array();

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014EB begin
		if(class_exists('core_kernel_persistence_smoothsql_Class')){
			//the model is not hardened and remains in the soft table:
			$returnValue = core_kernel_persistence_smoothsql_Class::singleton()->getSubClasses($resource, $recursive);
		}else{
			throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
		}
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014EB end

        return (array) $returnValue;
    }

    /**
     * Short description of method isSubClassOf
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  Class parentClass
     * @return boolean
     */
    public function isSubClassOf( core_kernel_classes_Resource $resource,  core_kernel_classes_Class $parentClass)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014F0 begin
		if(class_exists('core_kernel_persistence_smoothsql_Class')){
			//the model is not hardened and remains in the soft table:
			$returnValue = core_kernel_persistence_smoothsql_Class::singleton()->isSubClassOf($resource, $parentClass);
		}else{
			throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
		}
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014F0 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getParentClasses
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  boolean recursive
     * @return array
     */
    public function getParentClasses( core_kernel_classes_Resource $resource, $recursive = false)
    {
        $returnValue = array();

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014F5 begin
		throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014F5 end

        return (array) $returnValue;
    }

    /**
     * Short description of method getProperties
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  boolean recursive
     * @return array
     */
    public function getProperties( core_kernel_classes_Resource $resource, $recursive = false)
    {
        $returnValue = array();

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014FA begin
		throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014FA end

        return (array) $returnValue;
    }

    /**
     * Short description of method getInstances
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  boolean recursive
     * @param  array params
     * @return array
     */
    public function getInstances( core_kernel_classes_Resource $resource, $recursive = false, $params = array())
    {
        $returnValue = array();

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001500 begin

		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		$classLocations = core_kernel_persistence_hardapi_ResourceReferencer::singleton()->classLocations($resource);

		if(isset($params['limit'])){
			$offset = 0;
			$limit = intval($params['limit']);
			if ($limit==0){
				$limit = 1000000;
			}
			if(isset($params['offset'])){
				$offset = intval($params['offset']);
			}
		}

		foreach ($classLocations as $classLocation){

			$tableName = $classLocation['table'];
			$sqlQuery = 'SELECT "uri" FROM "'.$tableName.'"';
			if(isset($params['limit'])){
				$offset = 0;
				$limit = intval($params['limit']);
				if ($limit==0){
					$limit = 1000000;
				}
				if(isset($params['offset'])){
					$offset = intval($params['offset']);
				}
				
				$sqlQuery  = $dbWrapper->limitStatement($sqlQuery, $limit, $offset);
			}

			try{
				$sqlResult = $dbWrapper->query($sqlQuery);
				while ($row = $sqlResult->fetch()){
	
					$instance = new core_kernel_classes_Resource($row['uri']);
					$returnValue[$instance->uriResource] = $instance ;
				}
				if($recursive == true){
						
					$subClasses = $resource->getSubClasses(true);
					foreach ($subClasses as $subClass){
	
						if (isset($limit)){
	
							$limit = $limit - count($returnValue);
							if ($limit > 0){
								$returnValue = array_merge($returnValue, $subClass->getInstances(true), array('limit'=>$limit));
							} else {
								break 2;
							}
						}else{
							$returnValue = array_merge($returnValue, $subClass->getInstances(true));
						}
					}
				}
			}
			catch (PDOException $e){
				throw new core_kernel_persistence_hardsql_Exception("Unable to get instances for the resource {$resource->uriResource} in the table {$tableName} : " .$e->getMessage());
			}
		}

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001500 end

        return (array) $returnValue;
    }

    /**
     * Short description of method setInstance
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  Resource instance
     * @return core_kernel_classes_Resource
     */
    public function setInstance( core_kernel_classes_Resource $resource,  core_kernel_classes_Resource $instance)
    {
        $returnValue = null;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001506 begin
		throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001506 end

        return $returnValue;
    }

    /**
     * Short description of method setSubClassOf
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  Class iClass
     * @return boolean
     */
    public function setSubClassOf( core_kernel_classes_Resource $resource,  core_kernel_classes_Class $iClass)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000150F begin
		throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000150F end

        return (bool) $returnValue;
    }

    /**
     * Short description of method setProperty
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @return boolean
     */
    public function setProperty( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001512 begin
		throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001512 end

        return (bool) $returnValue;
    }

    /**
     * Should not be called by application code, please use
     * instead
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  string label
     * @param  string comment
     * @param  string uri
     * @return core_kernel_classes_Resource
     */
    public function createInstance( core_kernel_classes_Resource $resource, $label = '', $comment = '', $uri = '')
    {
        $returnValue = null;

        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F27 begin

		$dbWrapper = core_kernel_classes_DbWrapper::singleton();

		if($uri == ''){

			$subject = common_Utils::getNewUri();
		}
		else {

			//$uri should start with # and be well formed
			$modelUri = core_kernel_classes_Session::singleton()->getNameSpace();
			$subject = $modelUri . $uri;
		}

		$returnValue = new core_kernel_classes_Resource($subject,__METHOD__);

		try{
			$table = '_'.core_kernel_persistence_hardapi_Utils::getShortName ($resource);
			$query = 'INSERT INTO "'.$table.'" ("uri") VALUES (?)';
			$result = $dbWrapper->exec($query, array($subject));

			// reference the newly created instance
			core_kernel_persistence_hardapi_ResourceReferencer::singleton()->referenceResource($returnValue, $table, array($resource), true);

			if (!empty($label)){
				$returnValue->setPropertyValue(new core_kernel_classes_Property(RDFS_LABEL), $label);
			}
			if (!empty($comment)){
				$returnValue->setPropertyValue(new core_kernel_classes_Property(RDFS_COMMENT), $comment);
			}
		}
		catch (PDOException $e){
			throw new core_kernel_persistence_hardsql_Exception("Unable to create instance for the resource {$resource->uriResource} in the table {$table} : " .$e->getMessage());
		}

        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F27 end

        return $returnValue;
    }

    /**
     * Short description of method createSubClass
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  string label
     * @param  string comment
     * @param  string uri
     * @return core_kernel_classes_Class
     */
    public function createSubClass( core_kernel_classes_Resource $resource, $label = '', $comment = '', $uri = '')
    {
        $returnValue = null;

        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F32 begin
		throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F32 end

        return $returnValue;
    }

    /**
     * Short description of method createProperty
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  string label
     * @param  string comment
     * @param  boolean isLgDependent
     * @return core_kernel_classes_Property
     */
    public function createProperty( core_kernel_classes_Resource $resource, $label = '', $comment = '', $isLgDependent = false)
    {
        $returnValue = null;

        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F3C begin
		throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F3C end

        return $returnValue;
    }

    public function searchInstances( core_kernel_classes_Resource $resource, $propertyFilters = array(), $options = array()){
    	$returnValue = array();
    	
    	/*
		options lists:
		like			: (bool) 	true/false (default: true)
		chaining		: (string) 	'or'/'and' (default: 'and')
		recursive		: (int) 	recursivity depth (default: 0)
		lang			: (string) 	e.g. 'EN', 'FR' (default: '') for all properties!
		offset  		: default 0
		limit           : default select all
		order			: property to order by
		orderdir		: direction of order (default: 'ASC')
		*/
    	
    	$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		
		// 'like' option.
		$like = true;
		if (isset($options['like'])){
			$like = ($options['like'] == true);
		}
		
		// 'chaining' option.
		$chaining = 'and';
		if (!empty($options['chaining'])){
			$chainingValue = strtolower($options['chaining']);
			if ($chainingValue == 'and' || $chainingValue == 'or'){
				$chaining = $chainingValue;
			}
		}
		
		// 'recursive' option.
		$recursive = 0;
		if (isset($options['recursive'])){
			$recursive = intval($options['recursive']);
		}
		
		// 'offset' option. If not provided, we set it to null.
		$offset = null;
		if (isset($options['offset'])){
			$offset = intval($options['offset']);
		}
		
		// 'limit' option. If not provided, we wet it to null as well.
		$limit = null;
		if (isset($options['limit'])){
			$limit = intval($options['limit']);
		}
		
		// 'order' and 'orderdir' options.
		$order = null;
		$orderdir = 'ASC';
		if (!empty($options['order'])){
			$order = $options['order'];
			if (!empty($options['orderdir'])){
				$orderdirValue = strtolower($options['orderdir']);
				
				if ($orderdirValue == 'asc' || $orderdirValue == 'desc'){
					$orderdir = $orderdirValue;
				}
			}
		}
		
		// 'lang' option.
		$lang = '';
		if (!empty($options['lang'])){
			$lang = $options['lang'];
		}
		
		// 'additionalClasses' option.
		$classes = array($resource);
		if (!empty($options['additionalClasses'])){
			$classes = array_merge($classes, $options['additionalClasses']);
		}
		
		$queries = array();
		foreach ($classes as $class){
			$query = '';
			
			// Get information about where and how to query the database.
	    	$baseTableName = '';
			$referencer = core_kernel_persistence_hardapi_ResourceReferencer::singleton();
			$classLocations = $referencer->classLocations($class);
			if (isset($classLocations[0])){
				// table to query located.
				$baseTableName = $classLocations[0]['table'];
				$propsTableName = $baseTableName . 'Props';
				$baseConditions = array(); 		// Conditions to apply on base table.
				$propsConditions = array(); 	// Conditions to apply on properties table.
				
				
				foreach ($propertyFilters as $propUri => $pattern){
					$property = new core_kernel_classes_Property($propUri);
					$propertyLocations = $referencer->propertyLocation($property);
					$patterns = (!is_array($pattern)) ? array($pattern) : $pattern;
					
					if (in_array($propsTableName, $propertyLocations)){
						// The property to search is stored in the properties table.
						$pUri = $dbWrapper->dbConnector->quote($propUri);
						$condition = '("p"."property_uri" = ' . $pUri . ' AND (';
						$subConditions = array();
						foreach ($patterns as $pattern){
							$searchPattern = core_kernel_persistence_hardapi_Utils::buildSearchPattern($pattern, $like);
							$subConditions[] = 'COALESCE("p"."property_value", "p"."property_foreign_uri") ' . $searchPattern;
						}
						$condition .= implode(' OR ', $subConditions) . '))';
						$propsConditions[] = $condition;
					}
					else if (in_array($baseTableName, $propertyLocations)){
						// The property to search is stored in the base table.
						$propShortName = core_kernel_persistence_hardapi_Utils::getShortName($property);
						$subConditions = array();
						foreach ($patterns as $pattern){
							$searchPattern = core_kernel_persistence_hardapi_Utils::buildSearchPattern($pattern, $like);
							$subConditions[] = '"b"."' . $propShortName . '" ' . $searchPattern;
						}
						$baseConditions[] = '(' . implode(' OR ', $subConditions) . ')';
					}
					
					// else do nothing, the property is not referenced anywhere.
				}
				
				$query .= 'SELECT "b"."id", "b"."uri" FROM "' . $baseTableName . '" "b" ';
				
				if (empty($propsConditions) && !empty($baseConditions)){
					// Scenario with only scalar values.
					$query .= 'WHERE ';
					$finalCondition = array();
					foreach ($baseConditions as $bC){
						$finalCondition[] = '(' . $bC . ')';
					}
					$query .= implode(' ' . strtoupper($chaining) . ' ', $finalCondition);
				}
				else{
					// Mixed approach scenario.
					if ($chaining == 'and'){
						for ($i = 0; $i < count($propsConditions); $i++){
							$joinName = 'bp' . $i;
							$query .= 'INNER JOIN(';
							$query .= 	'SELECT "b"."id", "b"."uri" FROM "' . $baseTableName . '" "b" ';
							$query .= 	'INNER JOIN "' . $propsTableName . '" "p" ON ("b"."id" = "p"."instance_id" AND (';
							$query .=	$propsConditions[$i];
							$query .=   '))';
							$query .= ') AS "' . $joinName . '" ON ("b"."id" = "'. $joinName . '"."id")';
						}
						
						if (!empty($baseConditions)){
							$query .= ' WHERE ';
							$query .= implode(' AND ', $baseConditions);
						}
					}
					else{
						$query .= 'INNER JOIN(';
						$query .= 	'SELECT "b"."id", "b"."uri" FROM "' . $baseTableName . '" "b" ';
						$query .= 	'INNER JOIN "' . $propsTableName . '" "p" ON ("b"."id" = "p"."instance_id" AND(';
						$query .=	implode(' OR ', array_merge($propsConditions, $baseConditions));
						$query .= 	'))';
						$query .= ') AS "bp" ON ("b"."id" = "bp"."id")';
					}
				}
				
				$queries[] = $query;
			}	
		}
		
		if (!empty($queries)){
			$finalQuery = implode(' UNION ', $queries);
			$finalQuery .= ' GROUP BY id';
			
			if (!empty($limit)){
				$finalQuery .= ' LIMIT ' . $limit;
			}
			
			if (!empty($offset)){
				$finalQuery .= ' OFFSET ' . $offset;
			}
			
			try{
				$result = $dbWrapper->query($finalQuery);
				while ($row = $result->fetch()){
					$returnValue[$row['uri']] = new core_kernel_classes_Resource($row['uri']);
				}
			}
			catch (PDOException $e){
				throw new core_kernel_persistence_hardsql_Exception("Unable to search instances for the resource {$resource->uriResource} : " .$e->getMessage());
			}
		}
		
    	return (array) $returnValue;
    }
    
    /**
     * Short description of method searchInstances
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  array propertyFilters
     * @param  array options
     * @return array
     */
    public function oldsearchInstances( core_kernel_classes_Resource $resource, $propertyFilters = array(), $options = array())
    {
        $returnValue = array();

        // section 10-13-1--128--26678bb4:12fbafcb344:-8000:00000000000014F0 begin
		/*
		options lists:
		like			: (bool) 	true/false (default: true)
		chaining		: (string) 	'or'/'and' (default: 'and')
		recursive		: (int) 	recursivity depth (default: 0)
		lang			: (string) 	e.g. 'EN', 'FR' (default: '') for all properties!
		offset  		: default 0
		limit           : default select all
		order			: property to order by
		orderdir		: direction of order (default: 'ASC')
		*/
		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		
		// 'like' option.
		$like = true;
		if (!empty($options['like'])){
			$like = ($options['like'] === true);
		}
		
		// 'chaining' option.
		$chaining = 'and';
		if (!empty($options['chaining'])){
			$chainingValue = strtolower($options['chaining']);
			if ($chainingValue == 'and' || $chainingValue == 'or'){
				$chaining = $chainingValue;
			}
		}
		
		// 'recursive' option.
		$recursive = 0;
		if (isset($options['recursive'])){
			$recursive = intval($options['recursive']);
		}
		
		// 'offset' option. If not provided, we set it to null.
		$offset = null;
		if (isset($options['offset'])){
			$offset = intval($options['offset']);
		}
		
		// 'limit' option. If not provided, we wet it to null as well.
		$limit = null;
		if (isset($options['limit'])){
			$limit = intval($options['limit']);
		}
		
		// 'order' and 'orderdir' options.
		$order = null;
		$orderdir = 'ASC';
		if (!empty($options['order'])){
			$order = $options['order'];
			if (!empty($options['orderdir'])){
				$orderdirValue = strtolower($options['orderdir']);
				
				if ($orderdirValue == 'asc' || $orderdirValue == 'desc'){
					$orderdir = $orderdirValue;
				}
			}
		}

		$tableName = '';
		$referencer = core_kernel_persistence_hardapi_ResourceReferencer::singleton();
		$classLocations = $referencer->classLocations($resource);
		if (isset($classLocations[0])){
			$tableName = $classLocations[0]['table'];
		}
		else{
			return $returnValue;
		}

		$tablePropertiesName = $tableName.'Props';
		$tableNames = array('t0' => $tableName);

		$conditions = array();
		$joinConditions = array();
		$propertyFoundCount = 0;
		foreach ($propertyFilters as $propUri => $pattern){

			$property = new core_kernel_classes_Property($propUri);
			$propName = core_kernel_persistence_hardapi_Utils::getShortName($property);

			$propsTabIndex = '00';

			$propertyLocation = $referencer->propertyLocation($property);
			if (in_array($tablePropertiesName, $propertyLocation)){
				$propertyFoundCount++;
				$classPropsTabIndex = count($tableNames);
				$tableNames['t'.$classPropsTabIndex] = $tablePropertiesName;

				$langToken = "";
				if (!empty($options['lang']) && $property->isLgDependent()){
					if(preg_match('/^[a-zA-Z]{2,4}$/', $options['lang'])){
						$langToken = ' AND ( "p"."l_language" = \'\' OR "p"."l_language" = \''.$options['lang'].'\')';
					}
				}

				$condition = "";

				if (is_string($pattern)){

					if (!empty($pattern)){

						$searchPattern = core_kernel_persistence_hardapi_Utils::buildSearchPattern($pattern, $like);
						$condition = '("p"."property_value" ' . $searchPattern . ' OR "p"."property_foreign_uri" ' . $searchPattern . ') ' . $langToken;
					}
				}
				else if (is_array($pattern)){
					if (count($pattern) > 0){
						$multiCondition =  "(";
						foreach ($pattern as $i => $patternToken){

							if (!empty($patternToken)){
								$searchPattern = core_kernel_persistence_hardapi_Utils::buildSearchPattern($patternToken, $like);

								if ($i > 0){
									$multiCondition .= " OR ";
								}
								$multiCondition .= ' ( ("p"."property_value" '.$searchPattern.' OR "p"."property_foreign_uri" '.$searchPattern.') '.$langToken.')';
							}
						}
						$condition = "{$multiCondition} ) ";
					}
				}
				if (!empty($condition) && $chaining == 'and'){
					$joinConditions[] = 'INNER JOIN "' . $tablePropertiesName . '" "p" ON (' . $condition  . ' AND "p"."property_uri" = \''.$propUri.'\' AND "b"."id" = "p"."instance_id")';
				}
				else{
					$conditions[] = ' ( "b"."id" = "p"."instance_id" AND "p"."property_uri" = \''.$propUri.'\' AND '.$condition.' )';
				}

			}
			else if (in_array($tableName, $propertyLocation)){
				$propertyFoundCount++;
				$propsTabIndex = count($tableNames);

				if (is_string($pattern)){
					if(!empty($pattern)){
						$searchPattern = core_kernel_persistence_hardapi_Utils::buildSearchPattern($pattern, $like);
						$conditions[] = ' ( "b"."'.$propName.'" '.$searchPattern.' )';
					}
				}
				else if (is_array($pattern)){
					if (count($pattern) > 0){
						$multiCondition =  "(";
						foreach ($pattern as $i => $patternToken){

							$searchPattern = core_kernel_persistence_hardapi_Utils::buildSearchPattern($patternToken, $like);

							if($i > 0){
								$multiCondition .= " OR ";
							}
							$multiCondition .= ' ( "b"."'.$propName.'" '.$searchPattern.' )';
						}
						$conditions[] = "{$multiCondition}) ";
					}
				}

			}
		}
		
		if (!empty($propertyFilters) && $propertyFoundCount == 0){
			return $returnValue;
		}
		
		$targetTables = array('"' . $tableNames['t0'] . '" "b"');
		if (!empty($conditions) && $chaining == 'or'){
			array_push($targetTables, '"' . $tablePropertiesName . '" "p"');
		}
		
		$targetTablesQuery = implode(',', $targetTables);
		
		$sqlQuery = 'SELECT DISTINCT "b"."id", "b"."uri" FROM ' . $targetTablesQuery. ' ';
		$sqlQuery .= implode(' ', $joinConditions);

		if (!empty($conditions)){
			$sqlQuery .= ' WHERE ';
			$intersect = true;
			if ($chaining == 'or'){
				$intersect = false;
			}

			$j = 0;
			foreach ($conditions as $condition){
				if ($j > 0){
					$sqlQuery .= ($intersect) ? ' AND ' : ' OR ';
				}
				$sqlQuery .= $condition;
				$j++;
			}
		}
		
		// Deal with the 'offset' & 'limit' options.
		if (null !== $limit){
			$queryOffset = 0;
			$queryLimit = $limit;
			
			if ($queryLimit === 0){
				$queryLimit = 1000000;
			}
			
			if (null !== $offset){
				$queryOffset = $offset;
			}
			
			$sqlQuery = $dbWrapper->limitStatement($sqlQuery, $queryLimit, $queryOffset);
		}
		
		try{
			$sqlResult = $dbWrapper->query($sqlQuery);
			$idUris = array();
			while ($row = $sqlResult->fetch()){
				$idUris[$row['id']] = $row['uri'];
			}
			
			if (!empty($order)){
				$ids = array_keys($idUris);
				$ids = implode(', ', $ids);
				$orderProp = new core_kernel_classes_Property($order);
				$propertyLocation = $referencer->propertyLocation($orderProp);
				$orderPropShortName = core_kernel_persistence_hardapi_Utils::getShortName($orderProp);
				
				$sqlQuery  = 'SELECT "uri" FROM (';
				$sqlQuery .= 'SELECT "b"."uri", "p"."property_uri" AS "property_uri", "p"."property_value" AS "property_value" FROM "' . $tableNames['t0'] . '" "b" ';
				$sqlQuery .= 'INNER JOIN "' . $tablePropertiesName . '" "p" ';
				$sqlQuery .= 'ON ("b"."id" IN(' . $ids . ') AND "b"."id" = "p"."instance_id") ';
				$sqlQuery .= 'UNION ';
				
				if (in_array($tableNames['t0'], $propertyLocation)){
					$sqlQuery .= 'SELECT "b"."uri", \'' . $order . '\' AS "property_uri", "b"."' . $orderPropShortName. '" AS "property_value" ';
				}
				else{
					$sqlQuery .= 'SELECT "b"."uri", "p"."property_uri" AS "property_uri", "p"."property_value" AS "property_value" ';
				}
				$sqlQuery .= 'FROM "' . $tableNames['t0'] . '" "b" ';
				$sqlQuery .= 'JOIN "' . $tablePropertiesName . '" "p" ';
				$sqlQuery .= 'ON ("b"."id" IN(' . $ids . ') AND "b"."id" = "p"."instance_id") ';
				$sqlQuery .= ') AS "search" WHERE property_uri = \'' . $order . '\' GROUP BY "uri" ORDER BY "search"."property_value" ' . $orderdir;
				
				$sqlResult = $dbWrapper->query($sqlQuery);
				while ($row = $sqlResult->fetch()){
					$instance = new core_kernel_classes_Resource($row['uri']);
					$returnValue[$instance->uriResource] = $instance;
				}
			}
			else{
				foreach ($idUris as $iu){
					$instance = new core_kernel_classes_Resource($iu);
					$returnValue[$instance->uriResource] = $instance;	
				}
			}
			
			foreach ($options['additionalClasses'] as $aC){
				$returnValue = array_merge($returnValue, $aC->searchInstances($propertyFilters, array_merge($options, array('additionalClasses' => array()))));
				// The query limit might be exceeded (again).
				if (isset($queryLimit) && count($returnValue) > $queryLimit){
					$returnValue = array_slice($returnValue, 0, $queryLimit);
					break;
				}
			}
		}
		catch (PDOException $e){
			throw new core_kernel_persistence_hardsql_Exception("Unable to search instances for the resource {$resource->uriResource} : " .$e->getMessage());
		}
        // section 10-13-1--128--26678bb4:12fbafcb344:-8000:00000000000014F0 end

        return (array) $returnValue;
    }

    /**
     * Short description of method countInstances
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  array propertyFilters
     * @param  array options
     * @return Integer
     */
    public function countInstances( core_kernel_classes_Resource $resource, $propertyFilters = array(), $options = array())
    {
        $returnValue = null;

        // section 127-0-1-1--700ce06c:130dbc6fc61:-8000:000000000000159D begin

		$returnValue = 0;
		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		$classLocations = core_kernel_persistence_hardapi_ResourceReferencer::singleton()->classLocations($resource);
		foreach ($classLocations as $classLocation){

			$tableName = $classLocation['table'];
			$sqlQuery = 'SELECT count(*) AS "count" FROM "'.$tableName.'"';
				
			$sqlResult = $dbWrapper->query($sqlQuery);
			if ($row = $sqlResult->fetch()){
				$returnValue += $row['count'];
				$sqlResult->closeCursor();
			}
		}

        // section 127-0-1-1--700ce06c:130dbc6fc61:-8000:000000000000159D end

        return $returnValue;
    }

    /**
     * Short description of method getInstancesPropertyValues
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @param  array propertyFilters
     * @param  array options
     * @return array
     */
    public function getInstancesPropertyValues( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property, $propertyFilters = array(), $options = array())
    {
        $returnValue = array();

        // section 127-0-1-1--120bf54f:13142fdf597:-8000:000000000000312D begin
        
        //throw new core_kernel_persistence_ProhibitedFunctionException("The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
	        
    	$distinct = isset($options['distinct']) ? $options['distinct'] : false;
    	$recursive = isset($options['recursive']) ? $options['recursive'] : false;
        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
		$referencer = core_kernel_persistence_hardapi_ResourceReferencer::singleton();
        $uris = '';
        $searchInstancesOptions = array (
        	'recursive' 	=> $recursive
        );
        
        // Search all instances for the property filters paramter
        if(!empty($propertyFilters)){
        	$instances = $resource->searchInstances($propertyFilters, $searchInstancesOptions);
        }else{
        	$instances = $resource->getInstances($recursive);
        }
        
        if ($instances){
        	$tables = array();
	        foreach ($instances as $instance){
	        	$uris .= '\''.$instance->uriResource.'\',';
	        	array_push($tables, core_kernel_persistence_hardapi_ResourceReferencer::singleton()->resourceLocation($instance));
	        } 
	        $tables = array_unique($tables);
	        $uris = substr($uris, 0, strlen($uris)-1);
	        
	        // Get all the available property values in the subset of instances
	        
	        $table = $tables[0];
			if(empty($table)){
				return $returnValue;
			}
			$propertyAlias = core_kernel_persistence_hardapi_Utils::getShortName($property);
			$propertyLocation = $referencer->propertyLocation($property);
	
			// Select in the properties table of the class
			if (in_array("{$table}Props", $propertyLocation)
			|| ! $referencer->isPropertyReferenced($property)){
				
				$tableProps = $table."Props";
				$session = core_kernel_classes_Session::singleton();
				// Define language if required
				$lang = '';
				$defaultLg = '';
				if (isset($options['lg'])){
					$lang = $options['lg'];
				}
				else{
					$lang = $session->getDataLanguage();
					$defaultLg = ' OR "l_language" = \''.$session->defaultLg.'\' ';
				}
	            
				$query = 'SELECT '.$distinct.' "property_value", "property_foreign_uri"
					FROM "'.$table.'"
					INNER JOIN "'.$tableProps.'" on "'.$table.'"."id" = "'.$tableProps.'"."instance_id"
				   	WHERE "'.$table.'"."uri" IN ('.$uris.')
					AND "'.$tableProps.'"."property_uri" = ?
					AND ( "l_language" = ? OR "l_language" = \'\' '.$defaultLg.')';

				try{
					$result	= $dbWrapper->query($query, array($property->uriResource, $lang));
	
					while ($row = $result->fetch()){
						$returnValue[] = $row['property_value'] != null ? $row['property_value'] : $row['property_foreign_uri'];
					}
				}
				catch (PDOException $e){
					throw new core_kernel_persistence_hardsql_Exception("Unable to get property (multiple) values for {$resource->uriResource} in {$table} : " .$e->getMessage());
				}
				
			}
			// Select in the main table of the class
			else{
				try{
					$query =  'SELECT '.($distinct?'DISTINCT':'').' "'.$propertyAlias.'" as "propertyValue" FROM "'.$table.'" WHERE "uri" IN ('.$uris.')';
					$result	= $dbWrapper->query($query);
					
					while ($row = $result->fetch()){
						if ($row['propertyValue'] != null){
							$returnValue[] = $row['propertyValue'];
						}
					}
				}
				catch (PDOException $e){
					if ($e->getCode() == $dbWrapper->getColumnNotFoundErrorCode()) {
						// Column doesn't exists is not an error. Try to get a property which does not exist is allowed
					}
					else if ($e->getCode() !== '00000'){
						throw new core_kernel_persistence_hardsql_Exception("Unable to get property (single) values for {$resource->uriResource} in {$table} : " .$e->getMessage());
					}
				}
			}
        }
        
        // section 127-0-1-1--120bf54f:13142fdf597:-8000:000000000000312D end

        return (array) $returnValue;
    }

    /**
     * Short description of method unsetProperty
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @return boolean
     */
    public function unsetProperty( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-4f08ff91:131764e4b1f:-8000:00000000000031F8 begin
        throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
        // section 127-0-1-1-4f08ff91:131764e4b1f:-8000:00000000000031F8 end

        return (bool) $returnValue;
    }

    /**
     * Should not be called by application code, please use
     * core_kernel_classes_ResourceFactory::create() 
     * or core_kernel_classes_Class::createInstanceWithProperties()
     * instead
     *
     * Creates a new instance using the properties provided.
     * May NOT contain additional types in the properties array
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Class type
     * @param  array properties
     * @return core_kernel_classes_Resource
     * @see core_kernel_classes_ResourceFactory
     */
    public function createInstanceWithProperties( core_kernel_classes_Class $type, $properties)
    {
        $returnValue = null;

        // section 127-0-1-1--49b11f4f:135c41c62e3:-8000:0000000000001947 begin
        common_Logger::d('creating with proprties '.implode(',', array_keys($properties)));
        if (isset($properties[RDF_TYPE])) {
        	throw new core_kernel_persistence_Exception('Additional types in createInstanceWithProperties not permited');
        }
        
        $uri = common_Utils::getNewUri();
        $table = '_'.core_kernel_persistence_hardapi_Utils::getShortName ($type);
        
        // prepare properties
        $hardPropertyNames = array("uri" => $uri);
		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
        
        if (is_array($properties)) {
			if (count($properties) > 0) {

				// Get the table name
				$referencer = core_kernel_persistence_hardapi_ResourceReferencer::singleton();
				
				$session = core_kernel_classes_Session::singleton();

				$queryProps = array();

				foreach ($properties as $propertyUri => $value) {

					$property = new core_kernel_classes_Property($propertyUri);
					$propertyLocation = $referencer->propertyLocation($property);

					if (in_array("{$table}Props", $propertyLocation)
						|| !$referencer->isPropertyReferenced($property)) {

						$propertyRange = $property->getRange();
						$lang = ($property->isLgDependent() ? $session->getDataLanguage() : '');
						$formatedValues = array();
						if ($value instanceof core_kernel_classes_Resource) {
							$formatedValues[] = $value->uriResource;
						} else if (is_array($value)) {
							foreach ($value as $val) {
								if ($val instanceof core_kernel_classes_Resource) {
									$formatedValues[] = $val->uriResource;
								} else {
									$formatedValues[] = trim($dbWrapper->dbConnector->quote($val), "'\"");
								}
							}
						} else {
							$formatedValues[] = trim($dbWrapper->dbConnector->quote($value), "'\"");
						}

						if (is_null($propertyRange) || $propertyRange->uriResource == RDFS_LITERAL) {
							
							foreach ($formatedValues as $formatedValue) {
								$queryProps[] = "'{$property->uriResource}', '{$formatedValue}', null, '{$lang}'";
							}
						} else {
							foreach ($formatedValues as $formatedValue) {
								$queryProps[] = "'{$property->uriResource}', null, '{$formatedValue}', '{$lang}'";
							}
						}
					} else {

						$propertyName = core_kernel_persistence_hardapi_Utils::getShortName($property);
						if ($value instanceof core_kernel_classes_Resource) {
							$value = $value->uriResource;
						} else if (is_array($value)) {
							throw new core_kernel_persistence_hardsql_Exception("try setting multivalue for the non multiple property {$property->getLabel()} ({$property->uriResource})");
						} else {
							$value = trim($dbWrapper->dbConnector->quote($value), "'\"");
						}

						$hardPropertyNames[$propertyName] = $value;
					}
				}
			}
		}
        
        // spawn
        $returnValue = new core_kernel_classes_Resource($uri,__METHOD__);
		$varnames = '"'.implode('","', array_keys($hardPropertyNames)).'"';
		
		try{
			$query = 'INSERT INTO "'.$table.'" ('.$varnames.') VALUES ('.implode(',', array_fill(0, count($hardPropertyNames), '?')).')';
			$result = $dbWrapper->exec($query, array_values($hardPropertyNames));
			
			// reference the newly created instance
			core_kernel_persistence_hardapi_ResourceReferencer::singleton()->referenceResource($returnValue, $table, array($type), true);
			
			// @todo this shoould be retrievable without an aditional query
			$instanceId = core_kernel_persistence_hardsql_Utils::getInstanceId($returnValue);
			
			// @todo Merge into a single query
			if (!empty($queryProps)) {
				$prefixed = array();
				foreach ($queryProps as $row) {
					$prefixed[] = ' ('.$instanceId.', '.$row.')';
				}
				
				try{
					$query = 'INSERT INTO "' . $table . 'Props" ("instance_id", "property_uri", "property_value", "property_foreign_uri", "l_language") VALUES ' . implode(',', $prefixed);
					$result = $dbWrapper->exec($query);
				}
				catch (PDOException $e){
					throw new core_kernel_persistence_hardsql_Exception("Unable to set properties (multiple) Value for the instance {$returnValue->uriResource} in {$tableName} : " . $e->getMessage());
				}
			}
		}
		catch (PDOException $e){
			throw new core_kernel_persistence_hardsql_Exception("Unable to create instance for the class {$type->uriResource} in the table {$table} : " .$e->getMessage());
		}
        // section 127-0-1-1--49b11f4f:135c41c62e3:-8000:0000000000001947 end

        return $returnValue;
    }

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return core_kernel_classes_Resource
     */
    public static function singleton()
    {
        $returnValue = null;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001495 begin

		if (core_kernel_persistence_hardsql_Class::$instance == null){
			core_kernel_persistence_hardsql_Class::$instance = new core_kernel_persistence_hardsql_Class();
		}
		$returnValue = core_kernel_persistence_hardsql_Class::$instance;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001495 end

        return $returnValue;
    }

    /**
     * Short description of method isValidContext
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @return boolean
     */
    public function isValidContext( core_kernel_classes_Resource $resource)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F57 begin

		if (core_kernel_persistence_hardapi_ResourceReferencer::singleton()->isClassReferenced ($resource)){
			$returnValue = true;
		}

        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F57 end

        return (bool) $returnValue;
    }

} /* end of class core_kernel_persistence_hardsql_Class */

?>