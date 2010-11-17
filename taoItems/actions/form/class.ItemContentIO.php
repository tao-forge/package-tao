<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/actions/form/class.ItemContentIO.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 14.10.2010, 11:46:55 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage actions_form
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * Create a form from a  resource of your ontology. 
 * Each property will be a field, regarding it's widget.
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('tao/actions/form/class.Instance.php');

/* user defined includes */
// section 127-0-1-1-7c161ae7:12af1a41c59:-8000:0000000000002595-includes begin
// section 127-0-1-1-7c161ae7:12af1a41c59:-8000:0000000000002595-includes end

/* user defined constants */
// section 127-0-1-1-7c161ae7:12af1a41c59:-8000:0000000000002595-constants begin
// section 127-0-1-1-7c161ae7:12af1a41c59:-8000:0000000000002595-constants end

/**
 * Short description of class taoItems_actions_form_ItemContentIO
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage actions_form
 */
class taoItems_actions_form_ItemContentIO
    extends tao_actions_form_Instance
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method initForm
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    public function initForm()
    {
        // section 127-0-1-1-7c161ae7:12af1a41c59:-8000:0000000000002596 begin
        
    	parent::initForm();
    	
    	$actions = tao_helpers_form_FormFactory::getCommonActions();
    	$this->form->setActions($actions, 'top');
    	$this->form->setActions($actions, 'bottom');
    	
        // section 127-0-1-1-7c161ae7:12af1a41c59:-8000:0000000000002596 end
    }

    /**
     * Short description of method initElements
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    public function initElements()
    {
        // section 127-0-1-1-7c161ae7:12af1a41c59:-8000:0000000000002598 begin
        
    	$itemService = tao_models_classes_ServiceFactory::get('Items');
    	if($itemService->hasItemModel($this->instance, array(TAO_ITEM_MODEL_XHTML))){
    		$extension 	= array('xhtml', 'html', 'htm');
    		$mimeType 	= array('text/xml', 'application/xml', 'text/html');
    	}
    	else{
    		$extension 	= array('xml');
    		$mimeType 	= array('text/xml', 'application/xml');
    	}
    	
    	$importFileElt = tao_helpers_form_FormFactory::getElement("file_import", 'AsyncFile');
		$importFileElt->setDescription(__("Upload the item content"));
		$importFileElt->addValidators(array(
			tao_helpers_form_FormFactory::getValidator('NotEmpty'),
			tao_helpers_form_FormFactory::getValidator('FileSize', array('max' => 3000000)),	
			tao_helpers_form_FormFactory::getValidator('FileMimeType', array('mimetype' => $mimeType, 'extension' => $extension))
		));
		$this->form->addElement($importFileElt);
		
		$disableValidationElt = tao_helpers_form_FormFactory::getElement("disable_validation", 'Checkbox');
		$disableValidationElt->setDescription("Disable validation");
		$disableValidationElt->setOptions(array("on" => ""));
		$this->form->addElement($disableValidationElt);
		
		$this->form->createGroup('import', 'Import item content',  array($importFileElt->getName(), $disableValidationElt->getName()));
			
    	//add an hidden elt for the class uri
		$classUriElt = tao_helpers_form_FormFactory::getElement('classUri', 'Hidden');
		$classUriElt->setValue(tao_helpers_Uri::encode($this->clazz->uriResource));
		$this->form->addElement($classUriElt);
			
		if(!is_null($this->instance)){
			//add an hidden elt for the instance Uri
			$instanceUriElt = tao_helpers_form_FormFactory::getElement('uri', 'Hidden');
			$instanceUriElt->setValue(tao_helpers_Uri::encode($this->instance->uriResource));
			$this->form->addElement($instanceUriElt);
		}
    	
    	
		if($itemService->hasItemContent($this->instance)){
			if(trim($itemService->getItemContent($this->instance)) != ''){
				$this->addDownloadSection();
			}
		}
		
        // section 127-0-1-1-7c161ae7:12af1a41c59:-8000:0000000000002598 end
    }

    /**
     * Short description of method addDownloadSection
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    public function addDownloadSection()
    {
        // section 127-0-1-1--86595b6:12baa1eac16:-8000:0000000000002596 begin
        
    	if(is_null($this->form->getElement('file_download'))){
			$downloadUrl = _url('getItemContent', null, null, array(
					'uri' 		=> tao_helpers_Uri::encode($this->instance->uriResource),
					'classUri' 	=> tao_helpers_Uri::encode($this->clazz->uriResource)
			));
			
			$downloadFileElt = tao_helpers_form_FormFactory::getElement("file_download", 'Free');
			$downloadFileElt->setValue("<a href='$downloadUrl' class='blink' target='_blank'><img src='".BASE_WWW."/img/text-xml-file.png' alt='xml' class='icon'  /> ".__('Download item content')."</a>");
			$this->form->addElement($downloadFileElt);
			
			$this->form->createGroup('export', 'Download', array($downloadFileElt->getName()));
    	}
        // section 127-0-1-1--86595b6:12baa1eac16:-8000:0000000000002596 end
    }

} /* end of class taoItems_actions_form_ItemContentIO */

?>