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
?>
<?php

error_reporting(E_ALL);

/**
 * This container initialize the form used to map class properties to data to be
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 * @subpackage actions_form
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * This class provide a container for a specific form instance.
 * It's subclasses instanciate a form and it's elements to be used as a
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('tao/helpers/form/class.FormContainer.php');

/* user defined includes */
// section 127-0-1-1--250780b8:12843f3062f:-8000:00000000000023FB-includes begin
// section 127-0-1-1--250780b8:12843f3062f:-8000:00000000000023FB-includes end

/* user defined constants */
// section 127-0-1-1--250780b8:12843f3062f:-8000:00000000000023FB-constants begin
// section 127-0-1-1--250780b8:12843f3062f:-8000:00000000000023FB-constants end

/**
 * This container initialize the form used to map class properties to data to be
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 * @subpackage actions_form
 */
class tao_actions_form_CSVMapping
    extends tao_helpers_form_FormContainer
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
        // section 127-0-1-1--250780b8:12843f3062f:-8000:00000000000023FD begin
        
    	$this->form = tao_helpers_form_FormFactory::getForm('mapping');
    	
    	$importElt = tao_helpers_form_FormFactory::getElement('import', 'Free');
		$importElt->setValue( "<a href='#' class='form-submiter' ><img src='".TAOBASE_WWW."/img/import.png' /> ".__('Import')."</a>");
		$this->form->setActions(array($importElt), 'bottom');
		$this->form->setActions(array($importElt), 'top');
    	
        // section 127-0-1-1--250780b8:12843f3062f:-8000:00000000000023FD end
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
        // section 127-0-1-1--250780b8:12843f3062f:-8000:00000000000023FF begin
        if(!isset($this->options['class_properties'])){
    		throw new Exception('No class properties found');
    	}
    	if(!isset($this->options['csv_column'])){
    		throw new Exception('No csv columns found');
    	}
    	
        $columnsOptions = array();
    	$columnsOptionsLiteral =  array();
    	$columnsOptionsLiteral['csv_select'] = ' --- ' . __('Select') . ' --- ';
    	$columnsOptionsLiteral['csv_null']  = ' --- ' . __("Don't set");
        
        $columnsOptionsRanged = array();
        $columnsOptionsRanged['csv_select'] = ' --- ' . __('Select') . ' --- ';
        $columnsOptionsRanged['csv_null'] = ' --- ' . __('Use default value');
    	
    	// We build the list of CSV columns that can be mapped to
    	// the target class properties. 
    	if ($this->options['first_row_column_names']){
	    	foreach($this->options['csv_column'] as $i => $column){
	    		$columnsOptions[$i] = __('Column') . ' ' . ($i + 1) . ' : ' . $column;
	    	}
    	}
    	else{
    		// We do not know column so we display more neutral information
    		// about columns to the end user.
    		for ($i = 0; $i < count($this->options['csv_column']); $i++){
	    		$columnsOptions[$i] = __('Column') . ' ' . ($i + 1);
	    	}
    	}
    	
    	$i = 0;
    	foreach($this->options['class_properties'] as $propertyUri => $propertyLabel){
    		
    		$propElt = tao_helpers_form_FormFactory::getElement($propertyUri, 'Combobox');
    		$propElt->setDescription($propertyLabel);
            
            // literal or ranged?
            if (array_key_exists($propertyUri, $this->options['ranged_properties'])){
                $propElt->setOptions(array_merge($columnsOptionsRanged, $columnsOptions));
            }
            else{
                $propElt->setOptions(array_merge($columnsOptionsLiteral, $columnsOptions));
            }
            
    		$propElt->setValue('csv_select');
    		
    		$this->form->addElement($propElt);
    		
    		$i++;
    	}
    	$this->form->createGroup('property_mapping', __('Map the properties to the CSV columns'), array_keys($this->options['class_properties']));
    	
    	$ranged = array();
    	foreach($this->options['ranged_properties'] as $propertyUri => $propertyLabel){
    		$property = new core_kernel_classes_Property(tao_helpers_Uri::decode($propertyUri));
    		$propElt = tao_helpers_form_GenerisFormFactory::elementMap($property);
    		if(!is_null($propElt)){
    			$defName = tao_helpers_Uri::encode($property->getUri()) . TEMP_SUFFIX_CSV;
    			$propElt->setName($defName);
    			$this->form->addElement($propElt);
    			$ranged[$defName] = $propElt;
    		}
    	}
    	if(count($this->options['ranged_properties']) > 0){
    		$this->form->createGroup('ranged_property', __('Define the default values'), array_keys($ranged));
    	}
    	
        // section 127-0-1-1--250780b8:12843f3062f:-8000:00000000000023FF end
    }

} /* end of class tao_actions_form_CSVMapping */

?>