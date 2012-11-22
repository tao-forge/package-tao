<?php

error_reporting(E_ALL);

/**
 * Represents the concept of a TAO Distribution. A distribution comes with a set
 * extensions to be installed.
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage distrib
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 10-13-1-85-7504f477:13b27c1b582:-8000:0000000000001CF9-includes begin
// section 10-13-1-85-7504f477:13b27c1b582:-8000:0000000000001CF9-includes end

/* user defined constants */
// section 10-13-1-85-7504f477:13b27c1b582:-8000:0000000000001CF9-constants begin
// section 10-13-1-85-7504f477:13b27c1b582:-8000:0000000000001CF9-constants end

/**
 * Represents the concept of a TAO Distribution. A distribution comes with a set
 * extensions to be installed.
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage distrib
 */
class common_distrib_Distribution
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Id of the distribution.
     *
     * @access private
     * @var string
     */
    private $id = '';

    /**
     * Name of the distribution.
     *
     * @access private
     * @var string
     */
    private $name = '';

    /**
     * Description of the distribution.
     *
     * @access private
     * @var string
     */
    private $description = '';

    /**
     * Version of the distribution.
     *
     * @access private
     * @var string
     */
    private $version = '';

    /**
     * An array of extension IDs that are part of the distribution.
     *
     * @access private
     * @var array
     */
    private $extensions = array();

    // --- OPERATIONS ---

    /**
     * Get the ID of the distribution.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getId()
    {
        $returnValue = (string) '';

        // section 10-13-1-85-7504f477:13b27c1b582:-8000:0000000000001D0F begin
        $returnValue = $this->id;
        // section 10-13-1-85-7504f477:13b27c1b582:-8000:0000000000001D0F end

        return (string) $returnValue;
    }

    /**
     * Set the id of the distribution.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string id
     * @return void
     */
    public function setId($id)
    {
        // section 10-13-1-85-7504f477:13b27c1b582:-8000:0000000000001D13 begin
        $this->id = $id;
        // section 10-13-1-85-7504f477:13b27c1b582:-8000:0000000000001D13 end
    }

    /**
     * Get the name of the distribution.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getName()
    {
        $returnValue = (string) '';

        // section 10-13-1-85-7504f477:13b27c1b582:-8000:0000000000001D17 begin
        $returnValue = $this->name;
        // section 10-13-1-85-7504f477:13b27c1b582:-8000:0000000000001D17 end

        return (string) $returnValue;
    }

    /**
     * Set the name of the distribution.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string name The name of the distribution.
     * @return void
     */
    public function setName($name)
    {
        // section 10-13-1-85-7504f477:13b27c1b582:-8000:0000000000001D1B begin
        $this->name = $name;
        // section 10-13-1-85-7504f477:13b27c1b582:-8000:0000000000001D1B end
    }

    /**
     * Get the description of the distribution.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getDescription()
    {
        $returnValue = (string) '';

        // section 10-13-1-85-7504f477:13b27c1b582:-8000:0000000000001D20 begin
        $returnValue = $this->description;
        // section 10-13-1-85-7504f477:13b27c1b582:-8000:0000000000001D20 end

        return (string) $returnValue;
    }

    /**
     * Set the description of the distribution.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string description The description of the distribution.
     * @return void
     */
    public function setDescription($description)
    {
        // section 10-13-1-85-7504f477:13b27c1b582:-8000:0000000000001D24 begin
        $this->description = $description;
        // section 10-13-1-85-7504f477:13b27c1b582:-8000:0000000000001D24 end
    }

    /**
     * Get the version of the distribution.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getVersion()
    {
        $returnValue = (string) '';

        // section 10-13-1-85-7504f477:13b27c1b582:-8000:0000000000001D29 begin
        $returnValue = $this->version;
        // section 10-13-1-85-7504f477:13b27c1b582:-8000:0000000000001D29 end

        return (string) $returnValue;
    }

    /**
     * Set the version of the distribution.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string version The version of the distribution.
     * @return void
     */
    public function setVersion($version)
    {
        // section 10-13-1-85-7504f477:13b27c1b582:-8000:0000000000001D2D begin
        $this->version = $version;
        // section 10-13-1-85-7504f477:13b27c1b582:-8000:0000000000001D2D end
    }

    /**
     * Get an array of IDs of the extensions that comes along the distribution.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return array
     */
    public function getExtensions()
    {
        $returnValue = array();

        // section 10-13-1-85-7504f477:13b27c1b582:-8000:0000000000001D32 begin
        $returnValue = $this->extensions;
        // section 10-13-1-85-7504f477:13b27c1b582:-8000:0000000000001D32 end

        return (array) $returnValue;
    }

    /**
     * Set the extensions that come along the distribution.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array extensions An array of extension IDs.
     * @return void
     */
    public function setExtensions($extensions)
    {
        // section 10-13-1-85-7504f477:13b27c1b582:-8000:0000000000001D36 begin
        $this->extensions = $extensions;
        // section 10-13-1-85-7504f477:13b27c1b582:-8000:0000000000001D36 end
    }

    /**
     * Add an extension to the distribution.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string extension The ID of the extension to be added to the distribution.
     * @return void
     */
    public function addExtension($extension)
    {
        // section 10-13-1-85-7504f477:13b27c1b582:-8000:0000000000001D3B begin
        $exts = $this->getExtensions();
        array_push($exts, $extension);
        $exts = array_unique($exts);
        $this->setExtensions($exts);
        // section 10-13-1-85-7504f477:13b27c1b582:-8000:0000000000001D3B end
    }

    /**
     * Remove an extension from a distribution.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string extension The ID of the extension to remove.
     * @return void
     */
    public function removeExtension($extension)
    {
        // section 10-13-1-85-7504f477:13b27c1b582:-8000:0000000000001D41 begin
        $exts = $this->getExtensions();
        foreach ($exts as $k => $e){
        	if ($e == $extension){
        		unset($exts[$k]);
        		break;
        	}
        }
        // reorder keys.
        $exts = array_merge(array(), $exts);
        $this->setExtensions($exts);
        // section 10-13-1-85-7504f477:13b27c1b582:-8000:0000000000001D41 end
    }

    /**
     * Detects if an extension is involved or not in the distribution.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string extension The ID of the extension.
     * @return boolean
     */
    public function hasExtension($extension)
    {
        $returnValue = (bool) false;

        // section 10-13-1-85-7504f477:13b27c1b582:-8000:0000000000001D46 begin
        foreach($this->getExtensions() as $e){
        	if ($e == $extension){
        		$returnValue = true;
        		break;
        	}
        }
        // section 10-13-1-85-7504f477:13b27c1b582:-8000:0000000000001D46 end

        return (bool) $returnValue;
    }

    /**
     * Create a new instance of common_district_Distribution.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string id The ID of the distribution.
     * @param  string name The name of the distribution.
     * @param  string description The description of the distribution.
     * @param  string version The version of the distribution.
     * @param  array extensions An array of extension IDs.
     * @return mixed
     */
    public function __construct($id, $name, $description, $version, $extensions = array())
    {
        // section 10-13-1-85-7504f477:13b27c1b582:-8000:0000000000001D4B begin
        $this->setId($id);
        $this->setName($name);
        $this->setDescription($description);
        $this->setVersion($version);
        $this->setExtensions($extensions);
        // section 10-13-1-85-7504f477:13b27c1b582:-8000:0000000000001D4B end
    }

} /* end of class common_distrib_Distribution */

?>