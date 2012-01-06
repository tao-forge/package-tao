<?php

error_reporting(E_ALL);

/**
 * TAO - tao\scripts\class.TaoTranslate.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 06.01.2012, 11:17:41 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author firstname and lastname of author, <author@example.org>
 * @package tao
 * @subpackage scripts
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include tao_scripts_Runner
 *
 * @author firstname and lastname of author, <author@example.org>
 */
require_once('tao/scripts/class.Runner.php');

/* user defined includes */
// section -64--88-1-7-6b37e1cc:1336002dd1f:-8000:0000000000003286-includes begin
// section -64--88-1-7-6b37e1cc:1336002dd1f:-8000:0000000000003286-includes end

/* user defined constants */
// section -64--88-1-7-6b37e1cc:1336002dd1f:-8000:0000000000003286-constants begin
// section -64--88-1-7-6b37e1cc:1336002dd1f:-8000:0000000000003286-constants end

/**
 * Short description of class tao_scripts_TaoTranslate
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 * @package tao
 * @subpackage scripts
 */
class tao_scripts_TaoTranslate
    extends tao_scripts_Runner
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute DEF_INPUT_DIR
     *
     * @access public
     * @var string
     */
    const DEF_INPUT_DIR = 'locales';

    /**
     * Short description of attribute DEF_OUTPUT_DIR
     *
     * @access public
     * @var string
     */
    const DEF_OUTPUT_DIR = 'locales';

    // --- OPERATIONS ---

    /**
     * Short description of method preRun
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return mixed
     */
    public function preRun()
    {
        // section -64--88-1-7-6b37e1cc:1336002dd1f:-8000:0000000000003287 begin
    	$this->options = array('verbose' => false,
        					   'action' => null);
        
        $this->options = array_merge($this->options, $this->parameters);
        
        if ($this->options['verbose'] == true) {
        	$this->verbose = true;
        } else {
        	$this->verbose = false;
        }
        
        // The 'action' parameter is always required.
        if ($this->options['action'] == null) {
        	self::err("Please enter the 'action' parameter.", true);
        } else {
        	$this->options['action'] = strtolower($this->options['action']);
        	$allowedActions = array('create',
        							'update',
        							'delete',
        							'updateall',
        							'deleteall');
        	
        	if (!in_array($this->options['action'], $allowedActions)) {
        		self::err("Please enter a valid 'action' parameter.", true);
        	} else {
        		$this->checkInput();
        	}
        }
        // section -64--88-1-7-6b37e1cc:1336002dd1f:-8000:0000000000003287 end
    }

    /**
     * Short description of method run
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return mixed
     */
    public function run()
    {
        // section -64--88-1-7-6b37e1cc:1336002dd1f:-8000:0000000000003289 begin
        self::out('GG');
        // section -64--88-1-7-6b37e1cc:1336002dd1f:-8000:0000000000003289 end
    }

    /**
     * Short description of method postRun
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return mixed
     */
    public function postRun()
    {
        // section -64--88-1-7-6b37e1cc:1336002dd1f:-8000:000000000000328B begin
        // section -64--88-1-7-6b37e1cc:1336002dd1f:-8000:000000000000328B end
    }

    /**
     * Short description of method checkInput
     *
     * @access private
     * @author firstname and lastname of author, <author@example.org>
     * @return mixed
     */
    private function checkInput()
    {
        // section 10-13-1-85--7b8e6d0a:134ae555568:-8000:0000000000003840 begin
        switch ($this->options['action']) {
        	case 'create':
        		$this->checkCreateInput();
        	break;
        	
        	case 'update':
        		$this->checkUpdateInput();
        	break;
        	
        	case 'updateAll':
        		$this->checkUpdateAllInput();
        	break;
        	
        	case 'delete':
        		$this->checkDeleteInput();
        	break;
        	
        	case 'deleteAll':
        		$this->checkDeleteAllInput();
        	break;
        	
        	default:
        		// Should not happen.
        		self::err("Fatal error while checking input parameters. Unknown 'action'.", true);
        	break;
        }
        // section 10-13-1-85--7b8e6d0a:134ae555568:-8000:0000000000003840 end
    }

    /**
     * Short description of method checkCreateInput
     *
     * @access private
     * @author firstname and lastname of author, <author@example.org>
     * @return mixed
     */
    private function checkCreateInput()
    {
        // section 10-13-1-85--7b8e6d0a:134ae555568:-8000:0000000000003842 begin
        $defaults = array('language' => null,
        				  'output' => 'defaultoutputdir',
        				  'build' => true,
        				  'force' => true);
        
        $this->options = array_merge($defaults, $this->options);
    	
    	if (is_null($this->options['language'])) {
        	self::err("Please provide a 'language' identifier such as en-US, fr-CA, IT, ...", true);
        } else {
        	// The 'output' parameter is optional.
        	if (!is_null($this->options['output'])) {
        		if (!is_dir($this->options['output'])) {
        			self::err("The 'output' parameter you provided is not a directory.", true);
        		} else if (!is_writable($this->options['output'])) {
        			self::err("The 'output' directory is not writable.", true);
        		}
        	}
        }
        // section 10-13-1-85--7b8e6d0a:134ae555568:-8000:0000000000003842 end
    }

    /**
     * Short description of method checkUpdateInput
     *
     * @access private
     * @author firstname and lastname of author, <author@example.org>
     * @return mixed
     */
    private function checkUpdateInput()
    {
        // section 10-13-1-85--7b8e6d0a:134ae555568:-8000:0000000000003844 begin
        $defaults = array('language' => null,
        				  'input' => 'defaultinputdir',
        				  'output' => 'defaultoutputdir');
        
        $this->options = array_merge($defaults, $this->options);
        
        if (is_null($this->options['language'])) {
        	self::err("Please provide a 'language' identifier such as en-US, fr-CA, IT, ...", true);
        } else {
        	// The input 'parameter' is optional.
        	if (!is_null($this->options['input'])) {
        		if (!is_dir($this->options['input'])) {
        			self::err("The 'input' parameter you provided is not a directory.", true);
        		} else if (!is_readable($this->options['input'])) {
        			self::err("The 'input' directory is not readable.", true);
        		}
        	}
        	
        	// The output 'parameter' is optional.
        	if (!is_null($this->options['output'])) {
        		if (!is_dir($this->options['output'])) {
        			self::err("The 'output' parameter you provided is not a directory.", true);
        		} else if (!is_writable($this->options['output'])) {
        			self::err("The 'output' directory is not writable.", true);
        		}
        	}
        }
        // section 10-13-1-85--7b8e6d0a:134ae555568:-8000:0000000000003844 end
    }

    /**
     * Short description of method checkUpdateAllInput
     *
     * @access private
     * @author firstname and lastname of author, <author@example.org>
     * @return mixed
     */
    private function checkUpdateAllInput()
    {
        // section 10-13-1-85--7b8e6d0a:134ae555568:-8000:0000000000003846 begin
        $defaults = array('input' => 'defaultinputdir',
        				  'output' => 'defaultoutputdir');
        
        $this->options = array_merge($defaults, $this->options);
        
    	// The input 'parameter' is optional.
        if (!is_null($this->options['input'])) {
        	if (!is_dir($this->options['input'])) {
        		self::err("The 'input' parameter you provided is not a directory.", true);
        	} else if (!is_readable($this->options['input'])) {
        		self::err("The 'input' directory is not readable.", true);
        	}
        }
        
        // The output 'parameter' is optional.
        if (!is_null($this->options['output'])) {
        	if (!is_dir($this->options['output'])) {
        		self::err("The 'output' parameter you provided is not a directory.", true);
        	} else if (!is_writable($this->options['output'])) {
        		self::err("The 'output' directory is not writable.", true);
        	}
        }
        // section 10-13-1-85--7b8e6d0a:134ae555568:-8000:0000000000003846 end
    }

    /**
     * Short description of method checkDeleteInput
     *
     * @access private
     * @author firstname and lastname of author, <author@example.org>
     * @return mixed
     */
    private function checkDeleteInput()
    {
        // section 10-13-1-85--7b8e6d0a:134ae555568:-8000:0000000000003848 begin
        $defaults = array('language' => null,
        				  'input' => 'defaultinputdir');
        
        $this->options = array_merge($defaults, $this->options);
        
    	if (is_null($this->options['language'])) {
        	self::err("Please provide a 'language' identifier such as en-US, fr-CA, IT, ...", true);
        } else {
        	// The input 'parameter' is optional.
        	if (!is_null($this->options['input'])) {
        		if (!is_dir($this->options['input'])) {
        			self::err("The 'input' parameter you provided is not a directory.", true);
        		} else if (!is_readable($this->options['input'])) {
        			self::err("The 'input' directory is not readable.", true);
        		}
        	}
        }
        // section 10-13-1-85--7b8e6d0a:134ae555568:-8000:0000000000003848 end
    }

    /**
     * Short description of method checkDeleteAllInput
     *
     * @access private
     * @author firstname and lastname of author, <author@example.org>
     * @return mixed
     */
    private function checkDeleteAllInput()
    {
        // section 10-13-1-85--7b8e6d0a:134ae555568:-8000:000000000000384A begin
     	$defaults = array('input' => 'defaultinputdir');
        
        $this->options = array_merge($defaults, $this->options);
        
    	// The input 'parameter' is optional.
        if (!is_null($this->options['input'])) {
        	if (!is_dir($this->options['input'])) {
        		self::err("The 'input' parameter you provided is not a directory.", true);
        	} else if (!is_readable($this->options['input'])) {
        		self::err("The 'input' directory is not readable.", true);
        	}
        }
        // section 10-13-1-85--7b8e6d0a:134ae555568:-8000:000000000000384A end
    }

} /* end of class tao_scripts_TaoTranslate */

?>