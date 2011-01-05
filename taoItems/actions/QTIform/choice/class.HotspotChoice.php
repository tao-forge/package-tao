<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems\actions\QTIform\choice\class.HotspotChoice.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 05.01.2011, 11:32:50 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @package taoItems
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10316
 * @subpackage actions_QTIform_choice
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include taoItems_actions_QTIform_choice_Choice
 *
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10254
 */
require_once('taoItems/actions/QTIform/choice/class.Choice.php');

/* user defined includes */
// section 10-13-1-39-643eb156:12d51696e7c:-8000:000000000000500B-includes begin
// section 10-13-1-39-643eb156:12d51696e7c:-8000:000000000000500B-includes end

/* user defined constants */
// section 10-13-1-39-643eb156:12d51696e7c:-8000:000000000000500B-constants begin
// section 10-13-1-39-643eb156:12d51696e7c:-8000:000000000000500B-constants end

/**
 * Short description of class taoItems_actions_QTIform_choice_HotspotChoice
 *
 * @access public
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @package taoItems
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10316
 * @subpackage actions_QTIform_choice
 */
class taoItems_actions_QTIform_choice_HotspotChoice
    extends taoItems_actions_QTIform_choice_Choice
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method initElements
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @return mixed
     */
    public function initElements()
    {
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:000000000000500C begin
		
		parent::setCommonElements();
		
		//add hotspot label:
		$labelElt = tao_helpers_form_FormFactory::getElement('hotspotLabel', 'Textbox');
		$labelElt->setDescription(__('Label'));
		$labelElt->setValue($this->choice->getOption('hotspotLabel'));
		$this->form->addElement($labelElt);
		
		$shapeElt = tao_helpers_form_FormFactory::getElement('shape', 'Combobox');
		$shapeElt->setDescription(__('Shape'));
		$shapeElt->setAttribute('class', 'qti-shape');
		$shapeElt->setOptions(array(
			'default' => __('default'),
			'circle' => __('circle'),
			'ellipse' => __('ellipse'),
			'rect' => __('rectangle'),
			'poly' => __('polygon')
		));
		$shapeElt->setValue($this->choice->getOption('shape'));
		$this->form->addElement($shapeElt);
		
		$coordsElt = tao_helpers_form_FormFactory::getElement('coords', 'Hidden');
		$coordsElt->setValue($this->choice->getOption('coords'));
		$this->form->addElement($coordsElt);
		
		$this->form->createGroup('choicePropOptions_'.$this->choice->getSerial(), __('Advanced properties'), array('fixed', 'hotspotLabel'));
		
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:000000000000500C end
    }

} /* end of class taoItems_actions_QTIform_choice_HotspotChoice */

?>