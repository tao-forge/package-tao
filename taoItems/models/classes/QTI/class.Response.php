<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems\models\classes\QTI\class.Response.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 20.10.2010, 16:01:55 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
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
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 */
require_once('taoItems/models/classes/QTI/class.Data.php');

/**
 * include taoItems_models_classes_QTI_Interaction
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 */
require_once('taoItems/models/classes/QTI/class.Interaction.php');

/**
 * include taoItems_models_classes_QTI_Outcome
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
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
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
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
    protected $correctResponses = array();

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
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
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
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
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
     * Short description of method getMapping
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
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
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
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
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
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
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  string value
     * @return mixed
     */
    public function setMappingDefaultValue($value)
    {
        // section 127-0-1-1--a2bd9f7:12ae6efc8e9:-8000:00000000000025EA begin
        
    	$this->mappingDefaultValue = $value;
    	
        // section 127-0-1-1--a2bd9f7:12ae6efc8e9:-8000:00000000000025EA end
    }

    /**
     * Short description of method toXHTML
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @return string
     */
    public function toXHTML()
    {
        $returnValue = (string) '';

        // section 127-0-1-1--4fa404a7:12bc4fc4a20:-8000:00000000000026DB begin
        
        $returnValue = parent::toXHTML();
        
        // section 127-0-1-1--4fa404a7:12bc4fc4a20:-8000:00000000000026DB end

        return (string) $returnValue;
    }

    /**
     * Short description of method toQTI
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @return string
     */
    public function toQTI()
    {
        $returnValue = (string) '';

        // section 127-0-1-1--4fa404a7:12bc4fc4a20:-8000:00000000000026DD begin
        
        $template = self::getTemplatePath() . 'qti.response.tpl.php';
        $variables 	= $this->extractVariables(); 
        
        $variables['mappingOptions'] = '';
        if(is_array($this->getOption('mapping'))){
        	$variables['mappingOptions'] = $this->xmlizeOptions($this->getOption('mapping'), true);
        }
        $options = $this->getOptions();
        unset($options['mapping']);
        $variables['rowOptions'] = $this->xmlizeOptions($options, true);
		
		//parse and render the template
		$tplRenderer = new taoItems_models_classes_QTI_TemplateRenderer($template, $variables);
		$returnValue = $tplRenderer->render();
        
        // section 127-0-1-1--4fa404a7:12bc4fc4a20:-8000:00000000000026DD end

        return (string) $returnValue;
    }

    /**
     * Short description of method toForm
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @return tao_helpers_form_xhtml_Form
     */
    public function toForm()
    {
        $returnValue = null;

        // section 127-0-1-1--67198282:12bb0429ae8:-8000:000000000000266C begin
		$qtiService = tao_models_classes_ServiceFactory::get("taoItems_models_classes_QTI_Service");
		$interaction = $qtiService->getComposingData($this);
		if(!$interaction instanceof taoItems_models_classes_QTI_Interaction){
			throw new Exception('cannot find the parent interaction of the current response');
		}
		
		$responseFormClass = 'taoItems_actions_QTIform_response_'.ucfirst(strtolower($interaction->getType())).'Interaction';
		if(class_exists($responseFormClass)){
			$formContainer = new $responseFormClass($this);
			$myForm = $formContainer->getForm();
			$returnValue = $myForm;
		}
		
		// if(in_array(strtolower($interaction->getType()), array('textentry', 'extendedtext'))){}
		
        // section 127-0-1-1--67198282:12bb0429ae8:-8000:000000000000266C end

        return $returnValue;
    }

} /* end of class taoItems_models_classes_QTI_Response */

?>