<?php

error_reporting(E_ALL);

/**
 * List represents the collection list as managed by the the
 * tao matching api
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_Matching
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * Collection is an abstract class which represents
 * the variables "collection".
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 */
require_once('taoItems/models/classes/Matching/class.Collection.php');

/* user defined includes */
// section 127-0-1-1--58a488d5:12baaa39fdd:-8000:0000000000002939-includes begin
// section 127-0-1-1--58a488d5:12baaa39fdd:-8000:0000000000002939-includes end

/* user defined constants */
// section 127-0-1-1--58a488d5:12baaa39fdd:-8000:0000000000002939-constants begin
// section 127-0-1-1--58a488d5:12baaa39fdd:-8000:0000000000002939-constants end

/**
 * List represents the collection list as managed by the the
 * tao matching api
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_Matching
 */
class taoItems_models_classes_Matching_List
    extends taoItems_models_classes_Matching_Collection
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array data data used to construct the tuple
     * @return mixed
     */
    public function __construct($data)
    {
        // section 127-0-1-1-2688e070:12bac090945:-8000:000000000000294F begin
        
    	$this->setValue ($data);
    	
        // section 127-0-1-1-2688e070:12bac090945:-8000:000000000000294F end
    }

    /**
     * Get the type of the variable
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     * @see Variable
     */
    public function getType()
    {
        $returnValue = (string) '';

        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:000000000000293F begin
        
        return 'list';
        
        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:000000000000293F end

        return (string) $returnValue;
    }

    /**
     * Get an element by its index. Return null if the element does not exist.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  int key
     * @return taoItems_models_classes_Matching_Variable
     */
    public function getElement($key)
    {
        $returnValue = null;

        // section 127-0-1-1-2688e070:12bac090945:-8000:0000000000002960 begin
        
        if (isset($this->value[$key])){
        	$returnValue = $this->value[$key];
        }
        
        // section 127-0-1-1-2688e070:12bac090945:-8000:0000000000002960 end

        return $returnValue;
    }

    /**
     * Match a list with an other
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Variable list
     * @return boolean
     * @see Variable
     */
    public function match( taoItems_models_classes_Matching_Variable $list)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:0000000000002945 begin
		
        $returnValue = true;
        
		// If the cardinality is not the same return false
        if ($list->getType() != $this->getType()){
        	return false;	
        }
		
        // If the cardinality is not the same return false
        if ($this->length() != $list->length()){
        	return false;	
        }
        
        // Test if the both lists have the same content
		$checkedElts = array();//store checked elements from the second list, to ensure bijective matching
        for ($i=0; $i<$this->length(); $i++) {
	        $tempResult = false;
			
	        for ($j=0; $j<$list->length(); $j++) {
	        	if ($this->getElement($i)->getType () != $list->getElement($j)->getType()){
					//@todo : add a matching exception
	        		throw new Exception('taoItems_models_classes_Matching_List::match an error occured : types of the elements to match are not the same ['. $this->getElement($i)->getType () .'] and ['. $list->getElement($j)->getType() .']');
	        		$returnValue = false;
	        	} 
	        	else if ($this->getElement($i)->match($list->getElement($j)) && !in_array($j, $checkedElts)){
	            	$checkedElts[] = $j;
					$tempResult = true;
	                break;
	            }
	        }
	        if (!$tempResult){
	        	$returnValue = false;
				break;	
			}
        }     
        
        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:0000000000002945 end

        return (bool) $returnValue;
    }

    /**
     * Set value of the list from an array of data. The array of data could be
     * array of Variables or an array of "base type" variables
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  data
     * @return mixed
     * @see Variable
     */
    public function setValue(   $data)
    {
        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:0000000000002959 begin

        $this->value = array();

        if ($data == null){
            return;
        }
        
    	foreach ($data as $key=>$elt){
    	    if ($elt instanceOf taoItems_models_classes_Matching_Variable) {
                $this->value[$key] = $elt;
            } else {
                $this->value[$key] = taoItems_models_classes_Matching_VariableFactory::create ($elt);
            }
    	}

        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:0000000000002959 end
    }

    /**
     * Export the variable in jSon format.
     * {
     *     "identifier":"myVariableIdentifier",
     *     "value": [
     *         "myVar1"
     *         "myVar2"
     *     ]
     * }
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     */
    public function toJSon()
    {
        $returnValue = null;

        // section 127-0-1-1--6df7f690:12c1ba8488c:-8000:0000000000002B39 begin
        
        $toJSON = Array ();
        foreach ($this->getValue() as $value){
            $toJSON[] = $value->toJSon();
        }
        $returnValue = json_encode($toJSON);
        
        // section 127-0-1-1--6df7f690:12c1ba8488c:-8000:0000000000002B39 end

        return $returnValue;
    }

} /* end of class taoItems_models_classes_Matching_List */

?>