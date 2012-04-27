<?php

error_reporting(E_ALL);

/**
 * no response processing
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI_response_interactionResponseProcessing
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * The response processing of a single interaction
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/response/interactionResponseProcessing/class.InteractionResponseProcessing.php');

/* user defined includes */
// section 127-0-1-1-786830e4:134f066fb13:-8000:0000000000009004-includes begin
// section 127-0-1-1-786830e4:134f066fb13:-8000:0000000000009004-includes end

/* user defined constants */
// section 127-0-1-1-786830e4:134f066fb13:-8000:0000000000009004-constants begin
// section 127-0-1-1-786830e4:134f066fb13:-8000:0000000000009004-constants end

/**
 * no response processing
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI_response_interactionResponseProcessing
 */
class taoItems_models_classes_QTI_response_interactionResponseProcessing_None
    extends taoItems_models_classes_QTI_response_interactionResponseProcessing_InteractionResponseProcessing
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute CLASS_ID
     *
     * @access public
     * @var string
     */
    const CLASS_ID = 'none';

    /**
     * Short description of attribute default
     *
     * @access protected
     * @var string
     */
    protected $default = '';

    // --- OPERATIONS ---

    /**
     * Short description of method getDefaultValue
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getDefaultValue()
    {
        $returnValue = (string) '';

        // section 127-0-1-1--7e5cf656:136ee8922cb:-8000:00000000000039BF begin
        return $this->default;
        // section 127-0-1-1--7e5cf656:136ee8922cb:-8000:00000000000039BF end

        return (string) $returnValue;
    }

    /**
     * Short description of method setDefaultValue
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string value
     * @return mixed
     */
    public function setDefaultValue($value)
    {
        // section 127-0-1-1--7e5cf656:136ee8922cb:-8000:00000000000039BC begin
        $this->default = $value;
        // section 127-0-1-1--7e5cf656:136ee8922cb:-8000:00000000000039BC end
    }

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

        // section 127-0-1-1-786830e4:134f066fb13:-8000:0000000000009017 begin
        $returnValue = 'if(isNull(null, getResponse("'.$this->getResponse()->getIdentifier().'"))) { '.
        	'setOutcomeValue("'.$this->getOutcome()->getIdentifier().'", '.$this->getDefaultValue().'); };';
        // section 127-0-1-1-786830e4:134f066fb13:-8000:0000000000009017 end

        return (string) $returnValue;
    }

    /**
     * although no ResponseRules are nescessary to have no responseProcessing,
     * add some rules to associate the interaction response with a sepcific
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function toQTI()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-4c0a0972:134fa47975d:-8000:000000000000362E begin
		$returnValue = '<responseCondition>
		    <responseIf>
	            <isNull>
	                <variable identifier="'.$this->getResponse()->getIdentifier().'" />
	            </isNull>
		        <setOutcomeValue identifier="'.$this->getOutcome()->getIdentifier().'">
		        	<baseValue baseType="'.$this->getOutcome()->options['baseType'].'">0</baseValue>
		        </setOutcomeValue>
		    </responseIf>
		</responseCondition>';
        // section 127-0-1-1-4c0a0972:134fa47975d:-8000:000000000000362E end

        return (string) $returnValue;
    }

} /* end of class taoItems_models_classes_QTI_response_interactionResponseProcessing_None */

?>