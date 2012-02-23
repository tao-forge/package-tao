<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - common\ext\class.Namespace.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 23.02.2012, 13:38:58 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage ext
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * Enables you to manage the module namespaces
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 */
require_once('common/ext/class.NamespaceManager.php');

/* user defined includes */
// section 127-0-1-1-1cf6e8c2:12dbd7e3b2a:-8000:0000000000001588-includes begin
// section 127-0-1-1-1cf6e8c2:12dbd7e3b2a:-8000:0000000000001588-includes end

/* user defined constants */
// section 127-0-1-1-1cf6e8c2:12dbd7e3b2a:-8000:0000000000001588-constants begin
// section 127-0-1-1-1cf6e8c2:12dbd7e3b2a:-8000:0000000000001588-constants end

/**
 * Short description of class common_ext_Namespace
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage ext
 */
class common_ext_Namespace
{
    // --- ASSOCIATIONS ---
    // generateAssociationEnd : 

    // --- ATTRIBUTES ---

    /**
     * A unique identifier of the namespace
     *
     * @access protected
     * @var int
     */
    protected $modelId = 0;

    /**
     * the namespace URI
     *
     * @access protected
     * @var string
     */
    protected $uri = '';

    // --- OPERATIONS ---

    /**
     * Create a namespace instance
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  int id
     * @param  string uri
     * @return mixed
     */
    public function __construct($id = 0, $uri = '')
    {
        // section 127-0-1-1-1cf6e8c2:12dbd7e3b2a:-8000:00000000000015AA begin
        
    	if($id > 0){
    		$this->modelId = $id;
    	}
    	if(!empty($uri)){
    		$this->uri = $uri;
    	}
    	
        // section 127-0-1-1-1cf6e8c2:12dbd7e3b2a:-8000:00000000000015AA end
    }

    /**
     * Get the identifier of the namespace instance
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return int
     */
    public function getModelId()
    {
        $returnValue = (int) 0;

        // section 127-0-1-1-1cf6e8c2:12dbd7e3b2a:-8000:00000000000015B4 begin
        
        $returnValue = $this->modelId;
        
        // section 127-0-1-1-1cf6e8c2:12dbd7e3b2a:-8000:00000000000015B4 end

        return (int) $returnValue;
    }

    /**
     * Get the namespace URI
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getUri()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-1cf6e8c2:12dbd7e3b2a:-8000:00000000000015B6 begin
        
        $returnValue = $this->uri;
        
        // section 127-0-1-1-1cf6e8c2:12dbd7e3b2a:-8000:00000000000015B6 end

        return (string) $returnValue;
    }

    /**
     * Magic method, return the Namespace URI
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function __toString()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-1cf6e8c2:12dbd7e3b2a:-8000:00000000000015B8 begin
        
        $returnValue = $this->getUri();
        
        // section 127-0-1-1-1cf6e8c2:12dbd7e3b2a:-8000:00000000000015B8 end

        return (string) $returnValue;
    }

    /**
     * Remove a namespace from the ontology. All triples bound to the model will
     * be removed.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return boolean
     */
    public function remove()
    {
        $returnValue = (bool) false;

        // section 10-13-1-85--11334893:135aa33a460:-8000:000000000000193A begin
        // section 10-13-1-85--11334893:135aa33a460:-8000:000000000000193A end

        return (bool) $returnValue;
    }

} /* end of class common_ext_Namespace */

?>