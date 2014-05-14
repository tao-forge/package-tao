<?php
/**  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */
namespace oat\taoQtiItem\controller\QTIform;

use oat\taoQtiItem\controller\QTIform\TemplatesDrivenResponseOptions;
use oat\taoQtiItem\model\qti\response\ResponseProcessing;
use oat\taoQtiItem\model\qti\ResponseDeclaration;
use oat\taoQtiItem\model\qti\response\Template;
use \tao_helpers_form_FormContainer;
use \tao_helpers_form_FormFactory;
use \tao_helpers_Uri;


/**
 * This class provide a container for a specific form instance.
 * It's subclasses instanciate a form and it's elements to be used as a
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * 
 */
class TemplatesDrivenResponseOptions
    extends tao_helpers_form_FormContainer
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute responseProcessing
     *
     * @access public
     * @var ResponseProcessing
     */
    public $responseProcessing = null;

    /**
     * Short description of attribute response
     *
     * @access public
     * @var Response
     */
    public $response = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  ResponseProcessing responseProcessing
     * @param  Response response
     * @return mixed
     */
    public function __construct( ResponseProcessing $responseProcessing,  ResponseDeclaration $response)
    {
        
		$this->responseProcessing = $responseProcessing;
        $this->response = $response;
        parent::__construct();
        
    }

    /**
     * Short description of method initForm
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function initForm()
    {
        
        $this->form = tao_helpers_form_FormFactory::getForm('InteractionResponseProcessingForm');

		$this->form->setActions(array(), 'bottom');
        
    }

    /**
     * Short description of method initElements
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function initElements()
    {
        
        $serialElt = tao_helpers_form_FormFactory::getElement('responseSerial', 'Hidden');
		$serialElt->setValue($this->response->getSerial());
		$this->form->addElement($serialElt);
		
        $rpElt = tao_helpers_form_FormFactory::getElement('responseprocessingSerial', 'Hidden');
		$rpElt->setValue($this->responseProcessing->getSerial());
		$this->form->addElement($rpElt);
		
		$mapKey = tao_helpers_Uri::encode(Template::MAP_RESPONSE);
		$mapPointKey = tao_helpers_Uri::encode(Template::MAP_RESPONSE_POINT);
		
		$availableTemplates = array(
			tao_helpers_Uri::encode(Template::MATCH_CORRECT) => __('correct')
		);
		
		$interaction = $this->response->getAssociatedInteraction();
		if(!is_null($interaction)){
			switch(strtolower($interaction->getType())){
				case 'order':
				case 'graphicorder':{
					break;
				}
				case 'selectpoint';
				case 'positionobject':{
					$availableTemplates[$mapPointKey] = __('map point');
					break;
				}
				default:{
					$availableTemplates[$mapKey] = __('map');
				}
			}
		}
		
		$ResponseProcessingTplElt = tao_helpers_form_FormFactory::getElement('processingTemplate', 'Combobox');
		$ResponseProcessingTplElt->setDescription(__('Processing type'));
		$ResponseProcessingTplElt->setOptions($availableTemplates);
		$ResponseProcessingTplElt->setValue($this->responseProcessing->getTemplate($this->response));
		$this->form->addElement($ResponseProcessingTplElt);
        
    }

} /* end of class oat\taoQtiItem\controller\QTIform\TemplatesDrivenResponseOptions */

?>