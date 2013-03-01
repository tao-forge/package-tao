<?php

error_reporting(E_ALL);

/**
 * This class is a simple "search and replace" PHP-Like template renderer. 
 * It parses a file with php short tags and replace the variables by the
 * in attributes
 *
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoItems
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1-649cc98e:12ad7cf4ab2:-8000:0000000000002592-includes begin
// section 127-0-1-1-649cc98e:12ad7cf4ab2:-8000:0000000000002592-includes end

/* user defined constants */
// section 127-0-1-1-649cc98e:12ad7cf4ab2:-8000:0000000000002592-constants begin
// section 127-0-1-1-649cc98e:12ad7cf4ab2:-8000:0000000000002592-constants end

/**
 * This class is a simple "search and replace" PHP-Like template renderer. 
 * It parses a file with php short tags and replace the variables by the
 * in attributes
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoItems
 * @subpackage models_classes
 */
class taoItems_models_classes_TemplateRenderer
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute file
     *
     * @access protected
     * @var string
     */
    protected $file = '';

    /**
     * Short description of attribute variables
     *
     * @access protected
     * @var array
     */
    protected $variables = array();

    /**
     * Short description of attribute context
     *
     * @access protected
     * @var array
     */
    protected static $context = array();

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  string templatePath
     * @param  array variables
     * @return mixed
     */
    public function __construct($templatePath, $variables = array())
    {
        // section 127-0-1-1-649cc98e:12ad7cf4ab2:-8000:00000000000025A1 begin
        
    	if(file_exists($templatePath)){
    		if(is_readable($templatePath) && preg_match("/\.tpl\.php$/", basename($templatePath))){
    			$this->file = $templatePath;
    		}
    	}
    	if(empty($this->file)){
    		common_Logger::w('Template ',$templatePath.' not found');
    		throw new InvalidArgumentException("Unable to load the template file from $templatePath");
    	}
		if(!tao_helpers_File::securityCheck($this->file)){
			throw new Exception("Security warning: $templatePath is not safe.");
		}
    	
    	
    	$this->variables = $variables;
    	
        // section 127-0-1-1-649cc98e:12ad7cf4ab2:-8000:00000000000025A1 end
    }

    /**
     * Short description of method setContext
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  array parameters
     * @param  string prefix
     * @return mixed
     */
    public static function setContext($parameters, $prefix = '')
    {
        // section 127-0-1-1-3c043620:12bd493a38b:-8000:000000000000272E begin
        
    	self::$context = array();
    	
    	foreach($parameters as $key => $value){
    		self::$context[$prefix . $key] = $value;
    	}
    	
        // section 127-0-1-1-3c043620:12bd493a38b:-8000:000000000000272E end
    }

    /**
     * sets the template to be used
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  string templatePath
     * @return mixed
     */
    public function setTemplate($templatePath)
    {
        // section 10-30-1--78--43051535:13d25564359:-8000:0000000000003C81 begin
        $this->file = $templatePath;
        // section 10-30-1--78--43051535:13d25564359:-8000:0000000000003C81 end
    }

    /**
     * adds or replaces the data for a specific key
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  string key
     * @param  value
     * @return mixed
     */
    public function setData($key, $value)
    {
        // section 10-30-1--78--43051535:13d25564359:-8000:0000000000003C7D begin
        $this->variables[$key] = $value;
        // section 10-30-1--78--43051535:13d25564359:-8000:0000000000003C7D end
    }

    /**
     * Short description of method render
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return string
     */
    public function render()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-649cc98e:12ad7cf4ab2:-8000:00000000000025A5 begin
        
        //extract in the current context the array: 'key' => 'value'  to $key = 'value';
        extract(self::$context);
        extract($this->variables);
      
        ob_start();
        
        include $this->file;
        
        $returnValue = ob_get_contents();
        
        ob_end_clean();
        
        //clean the extracted variables
        foreach(array_merge($this->variables, self::$context) as $key => $name){
        	unset($$key);
        }
       
        // section 127-0-1-1-649cc98e:12ad7cf4ab2:-8000:00000000000025A5 end

        return (string) $returnValue;
    }

} /* end of class taoItems_models_classes_TemplateRenderer */

?>