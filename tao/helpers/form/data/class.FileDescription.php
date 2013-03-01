<?php

error_reporting(E_ALL);

/**
 * The FileDescription data type contains all the data that a form collects or
 * about a file.
 *
 * @author Jerome Bogaerts <jerome@taotesting.com>
 * @package tao
 * @subpackage helpers_form_data
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1--2c821474:13d11698078:-8000:0000000000003CC7-includes begin
// section 127-0-1-1--2c821474:13d11698078:-8000:0000000000003CC7-includes end

/* user defined constants */
// section 127-0-1-1--2c821474:13d11698078:-8000:0000000000003CC7-constants begin
// section 127-0-1-1--2c821474:13d11698078:-8000:0000000000003CC7-constants end

/**
 * The FileDescription data type contains all the data that a form collects or
 * about a file.
 *
 * @abstract
 * @access public
 * @author Jerome Bogaerts <jerome@taotesting.com>
 * @package tao
 * @subpackage helpers_form_data
 */
abstract class tao_helpers_form_data_FileDescription
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * The name of the file e.g. thumbnail.png.
     *
     * @access private
     * @var string
     */
    private $name = '';

    /**
     * The size of the file in bytes.
     *
     * @access private
     * @var int
     */
    private $size = 0;

    /**
     * The filed stored in persistent memory (if already stored).
     *
     * @access private
     * @var File
     */
    private $file = null;

    // --- OPERATIONS ---

    /**
     * Creates a new instance of FileDescription.
     *
     * @access public
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @param  string name The name of the file such as thumbnail.svg
     * @param  int size The size of the file in bytes.
     * @return mixed
     */
    public function __construct($name, $size)
    {
        // section 127-0-1-1--2c821474:13d11698078:-8000:0000000000003CD2 begin
        $this->name = $name;
        $this->size = $size;
        // section 127-0-1-1--2c821474:13d11698078:-8000:0000000000003CD2 end
    }

    /**
     * Returns the name of the file e.g. test.xml.
     *
     * @access public
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @return string
     */
    public function getName()
    {
        $returnValue = (string) '';

        // section 127-0-1-1--2c821474:13d11698078:-8000:0000000000003CF2 begin
        if (!empty($this->file) && empty($this->name)){
        	// collect information about the file instance itself.
        	$this->name = $this->file->getLabel();
        }
        
        $returnValue = $this->name;
        // section 127-0-1-1--2c821474:13d11698078:-8000:0000000000003CF2 end

        return (string) $returnValue;
    }

    /**
     * Returns the size of the file in bytes.
     *
     * @access public
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @return int
     */
    public function getSize()
    {
        $returnValue = (int) 0;

        // section 127-0-1-1--2c821474:13d11698078:-8000:0000000000003CF5 begin
        if (!empty($this->file) && empty($this->size)){
        	// collect from the file instance itself.
        	$fileInfo = $this->file->getFileInfo();
        	$this->size = $fileInfo->getSize();
        }
        
        $returnValue = $this->size;
        // section 127-0-1-1--2c821474:13d11698078:-8000:0000000000003CF5 end

        return (int) $returnValue;
    }

    /**
     * Gets the file bound to the FileDescription (returns null if not file
     * in persistent memory).
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return core_kernel_classes_File
     */
    public function getFile()
    {
        $returnValue = null;

        // section 127-0-1-1--26b8e569:13d1573b9cd:-8000:0000000000003C48 begin
        $returnValue = $this->file;
        // section 127-0-1-1--26b8e569:13d1573b9cd:-8000:0000000000003C48 end

        return $returnValue;
    }

    /**
     * Set the File corresponding to the FileDescription in persistent memory.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  File file
     * @return void
     */
    public function setFile( core_kernel_classes_File $file)
    {
        // section 127-0-1-1--26b8e569:13d1573b9cd:-8000:0000000000003C4A begin
        $this->file = $file;
        
        // Reset data about the file to make them computed from
        // the Generis File instance.
        $this->name = '';
        $this->size = 0;
        // section 127-0-1-1--26b8e569:13d1573b9cd:-8000:0000000000003C4A end
    }

} /* end of abstract class tao_helpers_form_data_FileDescription */

?>