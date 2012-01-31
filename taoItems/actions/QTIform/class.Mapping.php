<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/actions/QTIform/class.Mapping.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 31.01.2012, 16:44:22 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10066
 * @subpackage actions_QTIform
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include taoItems_actions_QTIform_ResponseProcessingOptions
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('taoItems/actions/QTIform/class.ResponseProcessingOptions.php');

/* user defined includes */
// section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FB7-includes begin
// section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FB7-includes end

/* user defined constants */
// section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FB7-constants begin
// section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FB7-constants end

/**
 * Short description of class taoItems_actions_QTIform_Mapping
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10066
 * @subpackage actions_QTIform
 */
class taoItems_actions_QTIform_Mapping
    extends taoItems_actions_QTIform_ResponseProcessingOptions
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method initElements
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     */
    public function initElements()
    {
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FC5 begin
        parent::initElements();
		$response = $this->interaction->getResponse();
		//default box:
		$defaultValueElt = tao_helpers_form_FormFactory::getElement('defaultValue', 'Textbox');
		$defaultValueElt->setDescription(__('Default value'));
		$defaultValue = 0;
		$mappingDefaultValue = $response->getMappingDefaultValue();
		if(empty($mappingDefaultValue)){
			$response->setMappingDefaultValue($defaultValue);
		}else{
			$defaultValue = $mappingDefaultValue;
		}
		$defaultValueElt->setValue($defaultValue);
		$this->form->addElement($defaultValueElt);
		
		//upperbound+lowerbound:
		$upperBoundElt = tao_helpers_form_FormFactory::getElement('upperBound', 'Textbox');
		$upperBoundElt->setDescription(__('Upper bound'));
		
		$lowerBoundElt = tao_helpers_form_FormFactory::getElement('lowerBound', 'Textbox');
		$lowerBoundElt->setDescription(__('Lower bound'));
		
		$mappingOptions = $response->getOption('mapping');
		if(is_array($mappingOptions)){
			if(isset($mappingOptions['upperBound'])) $upperBoundElt->setValue($mappingOptions['upperBound']);
			if(isset($mappingOptions['lowerBound'])) $lowerBoundElt->setValue($mappingOptions['lowerBound']);
		}
		$this->form->addElement($upperBoundElt);
		$this->form->addElement($lowerBoundElt);
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FC5 end
    }

} /* end of class taoItems_actions_QTIform_Mapping */

?>