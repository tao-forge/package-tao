<?php
/*  
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
namespace oat\taoQtiItem\controller\QTIform\choice;

use oat\taoQtiItem\controller\QTIform\choice\InlineChoice;
use oat\taoQtiItem\controller\QTIform\choice\Choice;
use \tao_helpers_form_FormFactory;

/**
 * Short description of class oat\taoQtiItem\controller\QTIform\choice\InlineChoice
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoItems
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10296
 
 */
class InlineChoice
    extends Choice
{

    /**
     * Short description of method initElements
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     */
    public function initElements()
    {
		parent::setCommonElements();
		
		//add Textbox:
		$dataElt = tao_helpers_form_FormFactory::getElement('data', 'Textbox');//the widget for an inline choice data is a text box!!
		$dataElt->setDescription(__('Value'));
		$choiceData = (string) $this->choice->getContent();
		if(!empty($choiceData)){
			$dataElt->setValue($choiceData);
		}
		$this->form->addElement($dataElt);
		
		$this->form->createGroup('choicePropOptions_'.$this->choice->getSerial(), __('Advanced properties'), array('fixed'));
		
    }

}