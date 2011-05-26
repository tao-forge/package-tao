<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 23.05.2011, 16:11:47 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_persistence_smoothsql
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 10-13-1-85-61dcfc6d:1301cc5c657:-8000:000000000000190D-includes begin
// section 10-13-1-85-61dcfc6d:1301cc5c657:-8000:000000000000190D-includes end

/* user defined constants */
// section 10-13-1-85-61dcfc6d:1301cc5c657:-8000:000000000000190D-constants begin
// section 10-13-1-85-61dcfc6d:1301cc5c657:-8000:000000000000190D-constants end

/**
 * Short description of class core_kernel_persistence_smoothsql_Utils
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_persistence_smoothsql
 */
class core_kernel_persistence_smoothsql_Utils
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method sortByLanguage
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  dataset
     * @param  string langColname
     * @return array
     */
    public function sortByLanguage($dataset, $langColname)
    {
        $returnValue = array();

        // section 10-13-1-85-61dcfc6d:1301cc5c657:-8000:000000000000190E begin
        $session = core_kernel_classes_Session::singleton(); 
    	$selectedLanguage = ($session->getLg() != '') ? $session->getLg() : $session->defaultLg;
    	$defaultLanguage = $session->defaultLg;
    	$fallbackLanguage = '';
    					  
    	$sortedResults = array($selectedLanguage => array(),
    						   $defaultLanguage => array(),
    						   $fallbackLanguage => array());
    					  
    	while ($row = $dataset->FetchRow()) {
    		$sortedResults[$row[$langColname]][] = array('value' => $row['object'], 
    													 'language' => $row[$langColname]);
    	}
    	
    	$returnValue = array_merge($sortedResults[$selectedLanguage], 
    							   (count($sortedResults) > 2) ? $sortedResults[$defaultLanguage] : array(),
    							   $sortedResults[$fallbackLanguage]);
    							   
   		$dataset->moveFirst();
        // section 10-13-1-85-61dcfc6d:1301cc5c657:-8000:000000000000190E end

        return (array) $returnValue;
    }

    /**
     * Short description of method getFirstLanguage
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  array values
     * @return array
     */
    public function getFirstLanguage($values)
    {
        $returnValue = array();

        // section 10-13-1-85-61dcfc6d:1301cc5c657:-8000:0000000000001912 begin
   		if (count($values) > 0) {
    		$previousLanguage = $values[0]['language'];

    		foreach ($values as $value) {
    			if ($value['language'] == $previousLanguage) {
    				$returnValue[] = $value['value'];
    			}
    			else {
    				break;
    			}
    		}
    	}
        // section 10-13-1-85-61dcfc6d:1301cc5c657:-8000:0000000000001912 end

        return (array) $returnValue;
    }

    /**
     * Short description of method filterByLanguage
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  dataset
     * @param  string langColname
     * @return array
     */
    public function filterByLanguage($dataset, $langColname)
    {
        $returnValue = array();

        // section 10-13-1-85-61dcfc6d:1301cc5c657:-8000:0000000000001915 begin
        $result = self::sortByLanguage($dataset, $langColname);
    	$returnValue = self::getFirstLanguage($result);
        // section 10-13-1-85-61dcfc6d:1301cc5c657:-8000:0000000000001915 end

        return (array) $returnValue;
    }

    /**
     * Short description of method identifyFirstLanguage
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  array values
     * @return string
     */
    public function identifyFirstLanguage($values)
    {
        $returnValue = (string) '';

        // section 10-13-1-85--4651ba20:1301d2ffa69:-8000:0000000000001915 begin
    	if (count($values) > 0) {
    		$previousLanguage = $values[0]['language'];
    		$returnValue = $previousLanguage;
    		
    		foreach ($values as $value) {
    			if ($value['language'] == $previousLanguage) {
    				continue;
    			}
    			else {
    				$returnValue = $previousLanguage;
    				break;
    			}
    		}
    	}
        // section 10-13-1-85--4651ba20:1301d2ffa69:-8000:0000000000001915 end

        return (string) $returnValue;
    }

} /* end of class core_kernel_persistence_smoothsql_Utils */

?>