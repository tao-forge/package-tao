<?php

error_reporting(E_ALL);

/**
 * TAO - tao/models/classes/class.LanguageService.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 15.11.2012, 11:42:42 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * The Service class is an abstraction of each service instance. 
 * Used to centralize the behavior related to every servcie instances.
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('tao/models/classes/class.GenerisService.php');

/* user defined includes */
// section 10-30-1--78--4ba5e2df:13b03a2f5f0:-8000:0000000000003C12-includes begin
// section 10-30-1--78--4ba5e2df:13b03a2f5f0:-8000:0000000000003C12-includes end

/* user defined constants */
// section 10-30-1--78--4ba5e2df:13b03a2f5f0:-8000:0000000000003C12-constants begin
// section 10-30-1--78--4ba5e2df:13b03a2f5f0:-8000:0000000000003C12-constants end

/**
 * Short description of class tao_models_classes_LanguageService
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 * @subpackage models_classes
 */
class tao_models_classes_LanguageService
    extends tao_models_classes_GenerisService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method createLanguage
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string code
     * @return core_kernel_classes_Resource
     */
    public function createLanguage($code)
    {
        $returnValue = null;

        // section 10-30-1--78--4ba5e2df:13b03a2f5f0:-8000:0000000000003C14 begin
        throw new common_exception_Error(__METHOD__.' not yet implemented in '.__CLASS__);
        // section 10-30-1--78--4ba5e2df:13b03a2f5f0:-8000:0000000000003C14 end

        return $returnValue;
    }

    /**
     * Short description of method getLanguageByCode
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string code
     * @return core_kernel_classes_Resource
     */
    public function getLanguageByCode($code)
    {
        $returnValue = null;

        // section 10-30-1--78--4ba5e2df:13b03a2f5f0:-8000:0000000000003C17 begin
        $langClass = new core_kernel_classes_Class(CLASS_LANGUAGES);
	    $langs = $langClass->searchInstances(array(
	    	RDF_VALUE => $code
	    ), array(
	    	'like' => FALSE
	    ));
	    if (count($langs) == 1) {
	    	$returnValue = current($langs);
	    } else {
	    	common_Logger::w('Could not find language with code '.$code);
	    }
        // section 10-30-1--78--4ba5e2df:13b03a2f5f0:-8000:0000000000003C17 end

        return $returnValue;
    }

    /**
     * Short description of method getCode
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource language
     * @return string
     */
    public function getCode( core_kernel_classes_Resource $language)
    {
        $returnValue = (string) '';

        // section 10-30-1--78--4ba5e2df:13b03a2f5f0:-8000:0000000000003C1A begin
	    $valueProperty = new core_kernel_classes_Property(RDF_VALUE);
        $returnValue = $language->getUniquePropertyValue($valueProperty);
        // section 10-30-1--78--4ba5e2df:13b03a2f5f0:-8000:0000000000003C1A end

        return (string) $returnValue;
    }

    /**
     * Short description of method getAvailableLanguagesByUsage
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource usage
     * @return array
     */
    public function getAvailableLanguagesByUsage( core_kernel_classes_Resource $usage)
    {
        $returnValue = array();

        // section 10-30-1--78--4ba5e2df:13b03a2f5f0:-8000:0000000000003C1D begin
    	$langClass = new core_kernel_classes_Class(CLASS_LANGUAGES);
	    $returnValue = $langClass->searchInstances(array(
	    	PROPERTY_LANGUAGE_USAGES => $usage
	    ), array(
	    	'like' => FALSE
	    ));
        // section 10-30-1--78--4ba5e2df:13b03a2f5f0:-8000:0000000000003C1D end

        return (array) $returnValue;
    }

    /**
     * Short description of method getDefaultLanguageByUsage
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource usage
     * @return core_kernel_classes_Resource
     */
    public function getDefaultLanguageByUsage( core_kernel_classes_Resource $usage)
    {
        $returnValue = null;

        // section 10-30-1--78--4ba5e2df:13b03a2f5f0:-8000:0000000000003C20 begin
        throw new common_exception_Error(__METHOD__.' not yet implemented in '.__CLASS__);
        // section 10-30-1--78--4ba5e2df:13b03a2f5f0:-8000:0000000000003C20 end

        return $returnValue;
    }

} /* end of class tao_models_classes_LanguageService */

?>