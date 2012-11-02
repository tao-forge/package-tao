<?php

error_reporting(E_ALL);

/**
 * TAO - tao/helpers/form/elements/xhtml/class.Versionedfile.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 12.04.2012, 12:32:50 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package tao
 * @subpackage helpers_form_elements_xhtml
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include tao_helpers_form_elements_Versionedfile
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 */
require_once('tao/helpers/form/elements/class.Versionedfile.php');

/* user defined includes */
// section 127-0-1-1-1ae05cc0:132f22f86c1:-8000:0000000000003F52-includes begin
// section 127-0-1-1-1ae05cc0:132f22f86c1:-8000:0000000000003F52-includes end

/* user defined constants */
// section 127-0-1-1-1ae05cc0:132f22f86c1:-8000:0000000000003F52-constants begin
// section 127-0-1-1-1ae05cc0:132f22f86c1:-8000:0000000000003F52-constants end

/**
 * Short description of class tao_helpers_form_elements_xhtml_Versionedfile
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package tao
 * @subpackage helpers_form_elements_xhtml
 */
class tao_helpers_form_elements_xhtml_Versionedfile
    extends tao_helpers_form_elements_Versionedfile
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute CSS_CLASS
     *
     * @access protected
     * @var string
     */
    const CSS_CLASS = 'editVersionedFile';

    // --- OPERATIONS ---

    /**
     * Short description of method render
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return string
     */
    public function render()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-1ae05cc0:132f22f86c1:-8000:0000000000003F53 begin
		
		if(array_key_exists('class', $this->attributes)){
			if(strstr($this->attributes['class'], self::CSS_CLASS) !== false){
				$this->attributes['class'] .= ' ' . self::CSS_CLASS;
			}
		}
		else{
			$this->attributes['class'] = self::CSS_CLASS;
		}
		
		$returnValue .= "<label class='form_desc' for='{$this->name}'>"._dh($this->getDescription())."</label>";
		
		if(helpers_Versioning::isEnabled()){
			$returnValue .= "<input type='button' for='{$this->name}' value='".__('Manage Versioned File')."' ";
			$returnValue .= $this->renderAttributes();
			$returnValue .= " />";
			$returnValue .= "<span for='{$this->name}' ".$this->renderAttributes()."></span>";
		}else{
			$returnValue .= "<span for='{$this->name}' ".$this->renderAttributes().">";
			$returnValue .= __('Versioning disabled');
			$returnValue .= "</span>";
		}
		
        // section 127-0-1-1-1ae05cc0:132f22f86c1:-8000:0000000000003F53 end

        return (string) $returnValue;
    }

} /* end of class tao_helpers_form_elements_xhtml_Versionedfile */

?>