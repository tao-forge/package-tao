<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/QTI/class.Response.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 08.09.2010, 10:41:33 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * The QTI_Data class represent the abstract model for all the QTI objects.
 * It contains all the attributes of the different kind of QTI objects.
 * It manages the identifiers and serial creation.
 * It provides the serialisation and persistance methods.
 * And give the interface for the rendering.
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/class.Data.php');

/**
 * include taoItems_models_classes_QTI_Interaction
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/class.Interaction.php');

/**
 * include taoItems_models_classes_QTI_Outcome
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/class.Outcome.php');

/* user defined includes */
// section 127-0-1-1--4be859a6:12a33452171:-8000:000000000000241D-includes begin
// section 127-0-1-1--4be859a6:12a33452171:-8000:000000000000241D-includes end

/* user defined constants */
// section 127-0-1-1--4be859a6:12a33452171:-8000:000000000000241D-constants begin
// section 127-0-1-1--4be859a6:12a33452171:-8000:000000000000241D-constants end

/**
 * Short description of class taoItems_models_classes_QTI_Response
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI
 */
class taoItems_models_classes_QTI_Response
    extends taoItems_models_classes_QTI_Data
{
    // --- ASSOCIATIONS ---
    // generateAssociationEnd :     // generateAssociationEnd : 

    // --- ATTRIBUTES ---

    /**
     * Short description of attribute correctResponses
     *
     * @access protected
     * @var array
     */
    protected $correctResponse = array();

    /**
     * Short description of attribute mapping
     *
     * @access protected
     * @var array
     */
    protected $mapping = array();

    /**
     * Short description of attribute mappingDefaultValue
     *
     * @access protected
     * @var string
     */
    protected $mappingDefaultValue = '';

    // --- OPERATIONS ---

    /**
     * Short description of method getCorrectResponses
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return array
     */
    public function getCorrectResponse()
    {
        $returnValue = array();

        // section 127-0-1-1-5ae00f6b:12a36da0066:-8000:0000000000002407 begin
        
        $returnValue = $this->correctResponse;
        
        // section 127-0-1-1-5ae00f6b:12a36da0066:-8000:0000000000002407 end

        return (array) $returnValue;
    }

    /**
     * Short description of method setCorrectResponses
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  array responses
     * @return mixed
     */
    public function setCorrectResponse($response)
    {
        // section 127-0-1-1-5ae00f6b:12a36da0066:-8000:0000000000002404 begin

    	if( ! $response instanceof taoItems_models_classes_QTI_Variable){
    		throw new InvalidArgumentException("wrong entry in reponses list");
    	}
    	$this->correctResponse = $response;
    	
        // section 127-0-1-1-5ae00f6b:12a36da0066:-8000:0000000000002404 end
    }

    /**
     * Short description of method getMapping
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return array
     */
    public function getMapping()
    {
        $returnValue = array();

        // section 127-0-1-1--a2bd9f7:12ae6efc8e9:-8000:00000000000025E3 begin
        
        $returnValue = $this->mapping;
        
        // section 127-0-1-1--a2bd9f7:12ae6efc8e9:-8000:00000000000025E3 end

        return (array) $returnValue;
    }

    /**
     * Short description of method setMapping
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  array map
     * @return mixed
     */
    public function setMapping($map)
    {
        // section 127-0-1-1--a2bd9f7:12ae6efc8e9:-8000:00000000000025E5 begin
        
    	$this->mapping = $map;
    	
        // section 127-0-1-1--a2bd9f7:12ae6efc8e9:-8000:00000000000025E5 end
    }

    /**
     * Short description of method getMappingDefaultValue
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return string
     */
    public function getMappingDefaultValue()
    {
        $returnValue = (string) '';

        // section 127-0-1-1--a2bd9f7:12ae6efc8e9:-8000:00000000000025E8 begin
        
        $returnValue = $this->mappingDefaultValue;
        
        // section 127-0-1-1--a2bd9f7:12ae6efc8e9:-8000:00000000000025E8 end

        return (string) $returnValue;
    }

    /**
     * Short description of method setMappingDefaultValue
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string value
     * @return mixed
     */
    public function setMappingDefaultValue($value)
    {
        // section 127-0-1-1--a2bd9f7:12ae6efc8e9:-8000:00000000000025EA begin
        
    	$this->mappingDefaultValue = $value;
    	
        // section 127-0-1-1--a2bd9f7:12ae6efc8e9:-8000:00000000000025EA end
    }

} /* end of class taoItems_models_classes_QTI_Response */

?>