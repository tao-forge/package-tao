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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *
 */

namespace oat\taoMediaManager\model;

use core_kernel_classes_Class;
use tao_helpers_form_Form;

/**
 * Service methods to manage the Media
 *
 * @access public
 * @author Antoine Robin, <antoine.robin@vesperiagroup.com>
 * @package taoMediaManager

 */
class ZipExportForm extends \tao_helpers_form_FormContainer
{

    public function initForm()
    {


        $this->form = new \tao_helpers_form_xhtml_Form('export');

        $this->form->setDecorators(array(
                'element'			=> new \tao_helpers_form_xhtml_TagWrapper(array('tag' => 'div')),
                'group'				=> new \tao_helpers_form_xhtml_TagWrapper(array('tag' => 'div', 'cssClass' => 'form-group')),
                'error'				=> new \tao_helpers_form_xhtml_TagWrapper(array('tag' => 'div', 'cssClass' => 'form-error ui-state-error ui-corner-all')),
                'actions-bottom'	=> new \tao_helpers_form_xhtml_TagWrapper(array('tag' => 'div', 'cssClass' => 'form-toolbar')),
                //'actions-top'		=> new tao_helpers_form_xhtml_TagWrapper(array('tag' => 'div', 'cssClass' => 'form-toolbar'))
            ));

        $exportElt = \tao_helpers_form_FormFactory::getElement('export', 'Free');
        $exportElt->setValue('<a href="#" class="form-submitter btn-success small"><span class="icon-export"></span> ' .__('Export').'</a>');

        $this->form->setActions(array($exportElt), 'bottom');

    }

    /**
     * overriden
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function initElements()
    {

        $fileName = '';

        $instances = array();
        if (isset($this->data['instance'])){
            $resource = $this->data['instance'];
        }
        elseif (isset($this->data['class'])) {
            $resource = $this->data['class'];
        } else {
            throw new \common_Exception('No class nor instance specified for export');
        }

        $fileName = strtolower(\tao_helpers_Display::textCleaner($resource->getLabel(), '*'));

        $hiddenElt = \tao_helpers_form_FormFactory::getElement('resource', 'Hidden');
        $hiddenElt->setValue($resource->getUri());
        $this->form->addElement($hiddenElt);


        $nameElt = \tao_helpers_form_FormFactory::getElement('filename', 'Textbox');
        $nameElt->setDescription(__('File name'));
        $nameElt->addValidator(\tao_helpers_form_FormFactory::getValidator('NotEmpty'));
        $nameElt->setValue($fileName);
        $nameElt->setUnit(".zip");
        $this->form->addElement($nameElt);

        $instances = \tao_helpers_Uri::encodeArray($instances, \tao_helpers_Uri::ENCODE_ARRAY_KEYS);

        $this->form->createGroup('options', __('Export Media as Zip file'), array('zip_desc', 'filename', 'ziptpl'));
    }
}
