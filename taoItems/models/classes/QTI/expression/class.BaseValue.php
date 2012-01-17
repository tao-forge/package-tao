<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/QTI/expression/class.BaseValue.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 16.01.2012, 18:09:12 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI_expression
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include taoItems_models_classes_QTI_expression_CommonExpression
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/expression/class.CommonExpression.php');

/* user defined includes */
// section 127-0-1-1-605722c1:12c112b6508:-8000:0000000000002A77-includes begin
// section 127-0-1-1-605722c1:12c112b6508:-8000:0000000000002A77-includes end

/* user defined constants */
// section 127-0-1-1-605722c1:12c112b6508:-8000:0000000000002A77-constants begin
// section 127-0-1-1-605722c1:12c112b6508:-8000:0000000000002A77-constants end

/**
 * Short description of class taoItems_models_classes_QTI_expression_BaseValue
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI_expression
 */
class taoItems_models_classes_QTI_expression_BaseValue
    extends taoItems_models_classes_QTI_expression_CommonExpression
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method getRule
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getRule()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-605722c1:12c112b6508:-8000:0000000000002A98 begin
         // JSON ENCODE the value to get quote when quote are required function of the variable base type
        // not so easy ;)
        //$returnValue = json_encode($this->value);
        // @todo make usable for complex variable such as pair, directed pair ..
        // @todo centralize the management of the options (attributes)
        $options = Array();
        $value = null;
        
        switch ($this->attributes['baseType']){
            case "boolean":
                $options['type'] = "boolean";
                $value = json_encode ($this->value);
                break;
            case "integer":
                $options['type'] = "integer";
                $value = json_encode ($this->value);
                break;
            case "float":
                $options['type'] = "float";
                $value = json_encode ($this->value);
                break;
            case "identifier":
            case "string":
                $options['type'] = "string";
                $value = json_encode ($this->value);
                break;
            case "pair":
                $options['type'] = "list";
                $value = '"'.implode ('","', $this->value).'"';
                break;
            case "directedPair":
                $options['type'] = "tuple";
                $value = '"'.implode ('","', (array)$this->value).'"'; // Méchant casting, won't work with a dictionnary, but with a tuple it is okay
                break;
            default:
                throw new common_Exception("taoItems_models_classes_QTI_response_BaseValue::getRule an error occured : the type ".$this->attributes['baseType']." is unknown");
        }

        $returnValue = 'createVariable('
            . (count($options) ? '"'.addslashes(json_encode($options)).'"' : 'null') .
            ', '. $value .
        ')';
        // section 127-0-1-1-605722c1:12c112b6508:-8000:0000000000002A98 end

        return (string) $returnValue;
    }

} /* end of class taoItems_models_classes_QTI_expression_BaseValue */

?>