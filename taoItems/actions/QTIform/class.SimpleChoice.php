<?php

error_reporting(E_ALL);

/**
 * This container initialize the qti item form:
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 * @subpackage actions_form
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * This container initialize the login form.
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 * @subpackage actions_form
 */
class taoItems_actions_QTIform_SimpleChoice
    extends taoItems_actions_QTIform_Choice{
	
	public function initElements(){
		
		parent::setCommonElements();
		
		//add other elements if needed:
		
		//add textarea:
		$dataElt = tao_helpers_form_FormFactory::getElement('content', 'Textbox');//should be a textarea... need to solve the conflict with the 
		$dataElt->setDescription(__('Content'));
		$choiceData = $this->choice->getData();
		if(!empty($choiceData)){
			$dataElt->setData($choiceData);
		}
		$this->form->addElement($dataElt);
		
	}

}

?>