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
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);\n *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core\kernel\impl\class.ApiSearchI.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 29.03.2010, 14:19:52 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_impl
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include core_kernel_api_ApiSearch
 *
 * @author firstname and lastname of author, <author@example.org>
 */
require_once('core/kernel/api/interface.ApiSearch.php');

/**
 * session has been set public because when implementing an interface, the son
 * this class may not read this attribute otherwise in php 5.2
 *
 * @author firstname and lastname of author, <author@example.org>
 */
require_once('core/kernel/impl/class.ApiI.php');

/* user defined includes */
// section 10-13-1--99-c056755:11a5428ab79:-8000:00000000000010B2-includes begin
// section 10-13-1--99-c056755:11a5428ab79:-8000:00000000000010B2-includes end

/* user defined constants */
// section 10-13-1--99-c056755:11a5428ab79:-8000:00000000000010B2-constants begin
// section 10-13-1--99-c056755:11a5428ab79:-8000:00000000000010B2-constants end

/**
 * Short description of class core_kernel_impl_ApiSearchI
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_impl
 */
class core_kernel_impl_ApiSearchI
    extends core_kernel_impl_ApiI
        implements core_kernel_api_ApiSearch
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * returns all resources as a collection containing for some properties,
     * that contains somehow (subStringOf with wildcards) all of the provided
     * keywords must be an array of strings
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  array keyword
     * @return core_kernel_classes_ContainerCollection
     */
    public function fullTextSearch($keyword)
    {
        $returnValue = null;

        // section 10-13-1--99--4fdec042:11a2a3b44dc:-8000:00000000000010AE begin
		
    	$returnValue = new core_kernel_classes_ContainerCollection(new core_kernel_classes_Container(__METHOD__),__METHOD__);
        
        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
        
       	$keyword = trim($dbWrapper->dbConnector->quote($keyword), "'\"");
        $query =  "SELECT DISTINCT subject FROM statements WHERE object LIKE '%{$keyword}%'";
    	$result	= $dbWrapper->query($query);
        
        while ($row = $result->fetch()) {
       		$returnValue->add(new core_kernel_classes_Resource($row['subject']));
        }
        
        // section 10-13-1--99--4fdec042:11a2a3b44dc:-8000:00000000000010AE end

        return $returnValue;
    }

    /**
     * performs a search in the knowledge base for resources having for all
     * the corresponding values
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  array criteria
     * @return core_kernel_classes_ContainerCollection
     */
    public function search($criteria)
    {
        $returnValue = null;

        // section 10-13-1--99--4fdec042:11a2a3b44dc:-8000:00000000000010B1 begin
		
        $returnValue = new core_kernel_classes_ContainerCollection(new core_kernel_classes_Container(__METHOD__),__METHOD__);
        
        if(is_array($criteria)){
        	$results = $this->searchInstances($criteria, null, array('like' => false));
        	foreach($results as $resource){
        		$returnValue->add($resource);
        	}
        }
        // section 10-13-1--99--4fdec042:11a2a3b44dc:-8000:00000000000010B1 end

        return $returnValue;
    }

    /**
     * Short description of method searchInstances
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  array propertyFilters
     * @param  Class topClazz
     * @param  array options
     * @return array
     */
    public function searchInstances($propertyFilters = array(),  core_kernel_classes_Class $topClazz = null, $options = array())
    {
        $returnValue = array();

        // section -87--2--3--76-51a982f1:1278aabc987:-8000:00000000000088FC begin

		if(count($propertyFilters) == 0){
			return $returnValue;
		}
		
		$dbWrapper = core_kernel_classes_DbWrapper::singleton();

		$langToken = '';
		if(isset($options['lang'])){
			if(preg_match('/^[a-zA-Z]{2,4}$/', $options['lang'])){
				$langToken = " AND (l_language = '' OR l_language = '{$options['lang']}') ";
			}
		}
		$like = true;
		if(isset($options['like'])){
			$like = ($options['like'] === true);
		}

		$query = "SELECT DISTINCT `subject` FROM `statements` WHERE ";

		$conditions = array();
		foreach($propertyFilters as $propUri => $pattern){
			
			$propUri = trim($dbWrapper->dbConnector->quote($propUri), "'\"");
			
			if(is_string($pattern)){
				if(!empty($pattern)){

					$pattern = trim($dbWrapper->dbConnector->quote($pattern), "'\"");
					
					if($like){
						$object = trim(str_replace('*', '%', $pattern));
						if(!preg_match("/^%/", $object)){
							$object = "%".$object;
						}
						if(!preg_match("/%$/", $object)){
							$object = $object."%";
						}
						$conditions[] = " (`predicate` = '{$propUri}' AND `object` LIKE '{$object}' $langToken ) ";
					}
					else{
						$conditions[] = " (`predicate` = '{$propUri}' AND `object` = '{$pattern}' $langToken ) ";
					}
				}
			}
			if(is_array($pattern)){
				if(count($pattern) > 0){
					$multiCondition =  " (`predicate` = '{$propUri}' AND  ";
					foreach($pattern as $i => $patternToken){
						
						$patternToken = trim($dbWrapper->dbConnector->escape($patternToken), "'\"");
						
						if($i > 0){
							$multiCondition .= " OR ";
						}
						$object = trim(str_replace('*', '%', $patternToken));
						if(!preg_match("/^%/", $object)){
							$object = "%".$object;
						}
						if(!preg_match("/%$/", $object)){
							$object = $object."%";
						}
						$multiCondition .= " `object` LIKE '{$object}' ";
					}
					$conditions[] = "{$multiCondition} {$langToken} ) ";
				}
			}
		}
		if(count($conditions) == 0){
			return $returnValue;
		}
		$matchingUris = array();

		$intersect = true;
		if(isset($options['chaining'])){
			if($options['chaining'] == 'or'){
				$intersect = false;
			}
		}
		
		if(count($conditions) > 0){
			$i = 0;
			foreach($conditions as $condition){
				$tmpMatchingUris = array();
				$result = $dbWrapper->query($query . $condition);
				while ($row = $result->fetch()){
					$tmpMatchingUris[] = $row['subject'];
				}
				if($intersect){
					//EXCLUSIVES CONDITIONS
					if($i == 0){
						$matchingUris = $tmpMatchingUris;
					}
					else{
						$matchingUris = array_intersect($matchingUris, $tmpMatchingUris);
					}
				}
				else{
					//INCLUSIVES CONDITIONS
					$matchingUris = array_merge($matchingUris, $tmpMatchingUris);
				}
				$i++;
			}
		}
		
		if(!is_null($topClazz)){
			$recursive = true;
			if(isset($options['recursive']) && $options['recursive'] === false){
                   $recursive = false;
			}
            $instances = $topClazz->getInstances($recursive);
			foreach($matchingUris as $matchingUri){
				if(isset($instances[$matchingUri])){
					$returnValue[] = $instances[$matchingUri];
				}
			}
		}
		else{
			foreach($matchingUris as $matchingUri){
				$returnValue[] = new core_kernel_classes_Resource($matchingUri)	;
			}
		}
		
        // section -87--2--3--76-51a982f1:1278aabc987:-8000:00000000000088FC end

        return (array) $returnValue;
    }

	/**
     * Short description of method searchInstances
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  array propertyFilters
     * @param  Class topClazz
     * @param  array options
     * @return array
     */
    public function searchInstancesHard ($propertyFilters = array(),  core_kernel_classes_Class $topClazz = null, $options = array())
    {
        $returnValue = array();

        // section -87--2--3--76-51a982f1:1278aabc987:-8000:00000000000088FC begin

        if(count($propertyFilters) == 0){
			return $returnValue;
		}
		
		$dbWrapper = core_kernel_classes_DbWrapper::singleton();

		// Get the models | Will be stored in Session, or it is yet
		$models = array ();
		$queryModels = "SELECT * FROM `models`";
		$result = $dbWrapper->query($queryModels);
		while ($row = $result->fetch()){
			$models[$row['baseURI']] = $row['modelID'];
		}
		
		// Get the Class to Table data | Will be stored in session
		$classToTable = array ();
    	$queryC2T = "SELECT * FROM `class_to_table`";
		$result = $dbWrapper->query($queryC2T);
		while ($row = $result->fetch()){
			$classToTable[$row['uri']] = $row['table'];
		}
		
		// Define the target table
		// Require $topClazz ?!
		$targetTable = $classToTable[$topClazz->getUri()];
		
		// The (cow) query (aux blagues) 
		$query = "SELECT uri FROM `{$targetTable}` WHERE ";
		$conditions = array ();
		
		// Build conditions function of the property filter
		foreach ($propertyFilters as $propertyUri=>$propertyValue){
			$propertyExplode = explode('#', $propertyUri);
			$namespace = $models[$propertyExplode[0].'#'];
			$namespace = strlen ($namespace)<2 ? "0".$namespace : $namespace;
			$columnName = $namespace . $propertyExplode[1];
			
			$condition = "`{$columnName}` = '{$propertyValue}'";
			array_push ($conditions, $condition);
		}
		
		$result = $dbWrapper->query($query . implode('AND ', $conditions));
   		while ($row = $result->fetch()){
   			$returnValue[] = new core_kernel_classes_Resource ($row['uri']);
		}
		
        // section -87--2--3--76-51a982f1:1278aabc987:-8000:00000000000088FC end

        return (array) $returnValue;
    }
    
} /* end of class core_kernel_impl_ApiSearchI */

?>