<?php

error_reporting(E_ALL);

/**
 * A data binder focusing on binding a source of data to a generis instance
 *
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @package tao
 * @subpackage models_classes_dataBinding
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * This abstract class represents a Data Binder that is able to bind data from a
 * source (e.g. a form) to another one (e.g. a persistent memory such as a
 *
 * Implementors have to implement the bind method to introduce their main logic
 * data binding.
 *
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 */
require_once('tao/models/classes/dataBinding/class.AbstractDataBinder.php');

/* user defined includes */
// section 127-0-1-1-2d2ef7de:13d10c8a117:-8000:0000000000003CA4-includes begin
// section 127-0-1-1-2d2ef7de:13d10c8a117:-8000:0000000000003CA4-includes end

/* user defined constants */
// section 127-0-1-1-2d2ef7de:13d10c8a117:-8000:0000000000003CA4-constants begin
// section 127-0-1-1-2d2ef7de:13d10c8a117:-8000:0000000000003CA4-constants end

/**
 * A data binder focusing on binding a source of data to a generis instance
 *
 * @access public
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @package tao
 * @subpackage models_classes_dataBinding
 */
class tao_models_classes_dataBinding_GenerisInstanceDataBinder
    extends tao_models_classes_dataBinding_AbstractDataBinder
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * A target Resource.
     *
     * @access private
     * @var Resource
     */
    private $targetInstance = null;

    // --- OPERATIONS ---

    /**
     * Creates a new instance of binder.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  Resource targetInstance The
     * @return mixed
     */
    public function __construct( core_kernel_classes_Resource $targetInstance)
    {
        // section 127-0-1-1-2d2ef7de:13d10c8a117:-8000:0000000000003CC8 begin
        $this->targetInstance = $targetInstance;
        // section 127-0-1-1-2d2ef7de:13d10c8a117:-8000:0000000000003CC8 end
    }

    /**
     * Returns the target instance.
     *
     * @access protected
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return core_kernel_classes_Resource
     */
    protected function getTargetInstance()
    {
        $returnValue = null;

        // section 127-0-1-1-2d2ef7de:13d10c8a117:-8000:0000000000003CAD begin
        $returnValue = $this->targetInstance;
        // section 127-0-1-1-2d2ef7de:13d10c8a117:-8000:0000000000003CAD end

        return $returnValue;
    }

    /**
     * Simply bind data from the source to a specific generis class instance.
     *
     * The array of the data to be bound must contain keys that are property
     * The repspective values can be either scalar or vector (array) values or
     * values.
     *
     * - If the element of the $data array is scalar, it is simply bound using
     * - If the element of the $data array is a vector, the property values are
     * with the values of the vector.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  array data An array of values where keys are Property URIs and values are either scalar or vector values.
     * @return mixed
     */
    public function bind($data)
    {
        $returnValue = null;

        // section 127-0-1-1-2d2ef7de:13d10c8a117:-8000:0000000000003CE0 begin
        try {
	        $instance = $this->getTargetInstance();
	        
	        foreach($data as $propertyUri => $propertyValue){
	        
	        	if($propertyUri == RDF_TYPE){
	        		foreach($instance->getTypes() as $type){
	        			$instance->removeType($type);
	        		}
	        		if(!is_array($propertyValue)){
	        			$types = array($propertyValue) ;
	        		}
	        		foreach($types as $type){
	        			$instance->setType(new core_kernel_classes_Class($type));
	        		}
	        		continue;
	        	}
	        
	        	$prop = new core_kernel_classes_Property( $propertyUri );
	        	$values = $instance->getPropertyValuesCollection($prop);
	        	if($values->count() > 0){
	        		if(is_array($propertyValue)){
	        			$instance->removePropertyValues($prop);
	        			foreach($propertyValue as $aPropertyValue){
	        				$instance->setPropertyValue(
	        						$prop,
	        						$aPropertyValue
	        				);
	        			}
	        
	        		}
	        		else{
	        			$instance->editPropertyValues(
	        					$prop,
	        					$propertyValue
	        			);
	        			if(strlen(trim($propertyValue))==0){
	        				//if the property value is an empty space(the default value in a select input field), delete the corresponding triplet (and not all property values)
	        				$instance->removePropertyValues($prop, array('pattern' => ''));
	        			}
	        		}
	        	}
	        	else{
	        
	        		if(is_array($propertyValue)){
	        
	        			foreach($propertyValue as $aPropertyValue){
	        				$instance->setPropertyValue(
	        						$prop,
	        						$aPropertyValue
	        				);
	        			}
	        		}
	        		else{
	        			$instance->setPropertyValue(
	        					$prop,
	        					$propertyValue
	        			);
	        		}
	        	}
	        }
	        
	        $returnValue = $instance;
        }
        catch (common_Exception $e){
        	$msg = "An error occured while binding property values to instance '': " . $e->getMessage();
        	$instanceUri = $instance->getUri();
        	throw new tao_models_classes_dataBinding_GenerisInstanceDataBindingException($msg);
        }
        // section 127-0-1-1-2d2ef7de:13d10c8a117:-8000:0000000000003CE0 end

        return $returnValue;
    }

} /* end of class tao_models_classes_dataBinding_GenerisInstanceDataBinder */

?>