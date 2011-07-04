<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 21.04.2011, 16:48:01 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package core
 * @subpackage kernel_persistence_switcher
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1-8da8919:12f7878e80a:-8000:00000000000015F9-includes begin
// section 127-0-1-1-8da8919:12f7878e80a:-8000:00000000000015F9-includes end

/* user defined constants */
// section 127-0-1-1-8da8919:12f7878e80a:-8000:00000000000015F9-constants begin
// section 127-0-1-1-8da8919:12f7878e80a:-8000:00000000000015F9-constants end

/**
 * Short description of class core_kernel_persistence_switcher_PropertySwicther
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package core
 * @subpackage kernel_persistence_switcher
 */
class core_kernel_persistence_switcher_PropertySwitcher
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * The Class that belongs the properties to switch
     *
     * @access protected
     * @var Class
     */
    protected $class = null;

    /**
     * Get the properties between the class and all it's parent until this
     *
     * @access protected
     * @var Class
     */
    protected $topClass = null;

    /**
     * Short description of attribute _properties
     *
     * @access private
     * @var array
     */
    private $_properties = array();

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Class class
     * @param  Class topClass Instanciate the property swicther with 
the class that belongs the properties to switch.
The topClass enables you to define an interval
bewteen a class and it's parent to retrieve the properties.
     * @return mixed
     */
    public function __construct( core_kernel_classes_Class $class,  core_kernel_classes_Class $topClass = null)
    {
        // section 127-0-1-1-8da8919:12f7878e80a:-8000:0000000000001609 begin
        
    	$this->class = $class;
		$this->topClass = $topClass;
    	
        // section 127-0-1-1-8da8919:12f7878e80a:-8000:0000000000001609 end
    }

    /**
     * Found all the properties of the class. 
     * It gets also the parent's properties between
     * the class and the topClass. 
     * If the topClass is not defined, the GenerisResource class is used.
     * If there is more than one parent's class, the best path is calculated.
     *
     * @access protected
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return array
     */
    protected function findProperties()
    {
        $returnValue = array();

        // section 127-0-1-1-8da8919:12f7878e80a:-8000:0000000000001612 begin
        
        if(is_null($this->topClass)){
			$parents = $this->class->getParentClasses(true);
		}
		else{
			
			//determine the parent path
			$parents = array();
			$top = false;
			do{
				if(!isset($lastLevelParents)){
					$parentClasses = $this->class->getParentClasses(false);
				}
				else{
					$parentClasses = array();
					foreach($lastLevelParents as $parent){
						$parentClasses = array_merge($parentClasses, $parent->getParentClasses(false));
					}
				}
				if(count($parentClasses) == 0){
					break;
				}
				$lastLevelParents = array();
				foreach($parentClasses as $parentClass){
					if($parentClass->uriResource == RDF_CLASS){
						continue;
					}
					if($parentClass->uriResource == $this->topClass->uriResource) {
						$parents[$parentClass->uriResource] = $parentClass;	
						$top = true;
						break;
					}
					
					$allParentClasses = $parentClass->getParentClasses(true);
					if(array_key_exists($this->topClass->uriResource, $allParentClasses)){
						 $parents[$parentClass->uriResource] = $parentClass;
					}
					$lastLevelParents[$parentClass->uriResource] = $parentClass;
				}
			}while(!$top);
		}
		$returnValue = array_merge(
			array(
				RDFS_LABEL 		=> new core_kernel_classes_Property(RDFS_LABEL),
				RDFS_COMMENT	=> new core_kernel_classes_Property(RDFS_COMMENT)
			),
			$this->class->getProperties(false)
		);
		foreach($parents as $parent){
			$returnValue = array_merge($returnValue, $parent->getProperties(false));
    	}
    	$this->_properties = $returnValue;
        
        // section 127-0-1-1-8da8919:12f7878e80a:-8000:0000000000001612 end

        return (array) $returnValue;
    }

    /**
     * The only way to retrieve the found properties.
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param array additionalProp
     * @return array
     */
    public function getProperties($additionalProps=array())
    {
        $returnValue = array();

        // section 127-0-1-1-8da8919:12f7878e80a:-8000:0000000000001615 begin
        
        if(count($this->_properties) == 0){
    		$returnValue = $this->findProperties();
    	}
    	else{
    		$returnValue = $this->_properties;
    	}
    	
        foreach ($additionalProps as $additionalProp){
        	$returnValue[$additionalProp->uriResource] = $additionalProp;
        }
    	
        // section 127-0-1-1-8da8919:12f7878e80a:-8000:0000000000001615 end

        return (array) $returnValue;
    }

    /**
     * Analyse the properties to find the best way to 
     * switch the create columns from the properties.
     * The columns is an associative array with a particular format:
     *  name  is the column name
     *  foreign is the name of the foreign table is reference
     *  multiple if it must be managed in a separate table
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param array additionalProp
     * @return array
     */
    public function getTableColumns($additionalProps = array(), $blackListedProps = array())
    {
        $returnValue = array();

        // section 127-0-1-1-8da8919:12f7878e80a:-8000:0000000000001618 begin
        
        $properties = $this->getProperties($additionalProps);
                
    	/// HERE repalce what the switcher is doing: determine the column type: literal/class, translate, multiple values
    	foreach($properties as $property){

			$column = array('name' => core_kernel_persistence_hardapi_Utils::getShortName($property));
				
			$range = $property->getRange();
			
			if(!is_null($range) && $range->uriResource != RDFS_LITERAL && !in_array($range->uriResource, $blackListedProps)){
				//constraint to the class that represents the range
				
				$column['foreign'] = '_'.core_kernel_persistence_hardapi_Utils::getShortName($range);
			}
			
			if ($property->isLgDependent() === true || $property->isMultiple()=== true ){
				//to put to the side table
				$column['multi'] = true;
			}
			$returnValue[] = $column;
		}
		
		
        // section 127-0-1-1-8da8919:12f7878e80a:-8000:0000000000001618 end

        return (array) $returnValue;
    }

    public function propertyDescriptor(core_kernel_classes_Property $property, $hardRangeClassOnly = false){

    	$returnValue = array(
		   'name'   => core_kernel_persistence_hardapi_Utils::getShortName($property),
		   'isMultiple'  => $property->isMultiple(),
		   'isLgDependent' => $property->isLgDependent(),
		   'range'   => array()
    	);

    	$range = $property->getRange();
    	$rangeClassName = core_kernel_persistence_hardapi_Utils::getShortName($range);
    	if($hardRangeClassOnly){
    		if(core_kernel_persistence_hardapi_ResourceReferencer::singleton()->isClassReferenced($range)){
    			$returnValue[] = $rangeClassName;
    		}
    	}else{
    		$returnValue[] = $rangeClassName;
    	}

    	return (array) $returnValue;

    }
    
} /* end of class core_kernel_persistence_switcher_PropertySwicther */

?>