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

use oat\taoQtiItem\controller\QTIform\CSSuploader;
use oat\taoQtiItem\model\qti\Item;
use \tao_helpers_form_FormContainer;
use \tao_helpers_form_FormFactory;
use \tao_helpers_form_xhtml_TagWrapper;




/**
 * Short description of class oat\taoQtiItem\controller\QTIform\CSSuploader
 *
 * @access public
 * @author Jehan Bihin, <jehan.bihin@tudor.lu>
 * @package taoItems
 
 */
class CSSuploader
    extends tao_helpers_form_FormContainer
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute item
     *
     * @access protected
     * @var Item
     */
    protected $item = null;

    /**
     * Short description of attribute itemUri
     *
     * @access public
     * @var string
     */
    public $itemUri = '';

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Jehan Bihin, <jehan.bihin@tudor.lu>
     * @param  Item item
     * @param  string itemUri
     */
    public function __construct( Item $item, $itemUri)
    {
        

		$this->item = $item;
		$this->itemUri = $itemUri;
		$returnValue = parent::__construct(array(), array());

        
    }

    /**
     * Short description of method initForm
     *
     * @access public
     * @author Jehan Bihin, <jehan.bihin@tudor.lu>
     */
    public function initForm()
    {
        

		$this->form = tao_helpers_form_FormFactory::getForm('css_uploader');

		$this->form->setDecorators(array(
			'element'			=> new tao_helpers_form_xhtml_TagWrapper(array('tag' => 'div')),
			'group'				=> new tao_helpers_form_xhtml_TagWrapper(array('tag' => 'div', 'cssClass' => 'form-group')),
			'error'				=> new tao_helpers_form_xhtml_TagWrapper(array('tag' => 'div', 'cssClass' => 'form-error ui-state-error ui-corner-all')),
			'help'				=> new tao_helpers_form_xhtml_TagWrapper(array('tag' => 'span','cssClass' => 'form-help')),
			'actions-bottom'	=> new tao_helpers_form_xhtml_TagWrapper(array('tag' => 'div', 'cssClass' => 'form-toolbar')),
			'actions-top'		=> new tao_helpers_form_xhtml_TagWrapper(array('tag' => 'div', 'cssClass' => 'form-toolbar'))
		));

		$submitElt = tao_helpers_form_FormFactory::getElement('submit', 'Submit');
		$submitElt->setValue('Upload');
		$this->form->setActions(array($submitElt), 'bottom');

        
    }

    /**
     * Short description of method initElements
     *
     * @access public
     * @author Jehan Bihin, <jehan.bihin@tudor.lu>
     */
    public function initElements()
    {
        

		$serialElt = tao_helpers_form_FormFactory::getElement('itemSerial', 'Hidden');
		$serialElt->setValue($this->item->getSerial());
		$this->form->addElement($serialElt);

		$elt = tao_helpers_form_FormFactory::getElement('itemUri', 'Hidden');
		$elt->setValue($this->itemUri);
		$this->form->addElement($elt);

		$labelElt = tao_helpers_form_FormFactory::getElement('title', 'Textbox');
		$labelElt->setDescription(__('File name'));
		$this->form->addElement($labelElt);

		$importFileElt = tao_helpers_form_FormFactory::getElement("css_import", 'AsyncFile');
		$importFileElt->setAttribute('auto', true);
		$importFileElt->setDescription(__("Upload the style sheet"));
		$importFileElt->setHelp(__("CSS format required"));
		$importFileElt->addValidators(array(
			tao_helpers_form_FormFactory::getValidator('NotEmpty'),
			tao_helpers_form_FormFactory::getValidator('FileSize', array('max' => 3000000)),
			tao_helpers_form_FormFactory::getValidator('FileMimeType', array('mimetype' => array('text/css', 'text/plain'), 'extension' => array('css')))
		));
		$this->form->addElement($importFileElt);

        
    }

} /* end of class oat\taoQtiItem\controller\QTIform\CSSuploader */

?>