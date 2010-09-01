<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/QTI/class.Response.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 01.09.2010, 11:23:14 with ArgoUML PHP module 
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
 * include taoItems_models_classes_QTI_Data
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
 * include taoItems_models_classes_QTI_Score
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/class.Score.php');

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
     * Short description of attribute score
     *
     * @access protected
     * @var Score
     */
    protected $score = null;

    /**
     * Short description of attribute correctResponses
     *
     * @access protected
     * @var array
     */
    protected $correctResponses = array();

    // --- OPERATIONS ---

    /**
     * Short description of method getScore
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return taoItems_models_classes_QTI_Score
     */
    public function getScore()
    {
        $returnValue = null;

        // section 127-0-1-1-5ae00f6b:12a36da0066:-8000:00000000000023FF begin
        
        $returnValue = $this->score;
        
        // section 127-0-1-1-5ae00f6b:12a36da0066:-8000:00000000000023FF end

        return $returnValue;
    }

    /**
     * Short description of method setScore
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Score score
     * @return mixed
     */
    public function setScore( taoItems_models_classes_QTI_Score $score)
    {
        // section 127-0-1-1-5ae00f6b:12a36da0066:-8000:0000000000002401 begin
        
    	$this->score = $score;
    	
        // section 127-0-1-1-5ae00f6b:12a36da0066:-8000:0000000000002401 end
    }

    /**
     * Short description of method getCorrectResponses
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return array
     */
    public function getCorrectResponses()
    {
        $returnValue = array();

        // section 127-0-1-1-5ae00f6b:12a36da0066:-8000:0000000000002407 begin
        
        $returnValue = $this->correctResponses;
        
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
    public function setCorrectResponses($responses)
    {
        // section 127-0-1-1-5ae00f6b:12a36da0066:-8000:0000000000002404 begin
        
    	if(!is_array($responses)){
    		$responses = array($responses);
    	}
    	$this->correctResponses = $responses;
    	
        // section 127-0-1-1-5ae00f6b:12a36da0066:-8000:0000000000002404 end
    }

    /**
     * Short description of method toXHTML
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return string
     */
    public function toXHTML()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-5ae00f6b:12a36da0066:-8000:0000000000002414 begin
        // section 127-0-1-1-5ae00f6b:12a36da0066:-8000:0000000000002414 end

        return (string) $returnValue;
    }

    /**
     * Short description of method toQTI
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return string
     */
    public function toQTI()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-5ae00f6b:12a36da0066:-8000:0000000000002416 begin
        // section 127-0-1-1-5ae00f6b:12a36da0066:-8000:0000000000002416 end

        return (string) $returnValue;
    }

    /**
     * Short description of method toForm
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return tao_helpers_form_Form
     */
    public function toForm()
    {
        $returnValue = null;

        // section 127-0-1-1-25600304:12a5c17a5ca:-8000:000000000000249D begin
        // section 127-0-1-1-25600304:12a5c17a5ca:-8000:000000000000249D end

        return $returnValue;
    }

} /* end of class taoItems_models_classes_QTI_Response */

?>