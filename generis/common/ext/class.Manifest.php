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
 * Generis Object Oriented API - common/ext/class.Manifest.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 18.02.2013, 15:42:12 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Jerome Bogaerts <jerome@taotesting.com>
 * @package common
 * @since 2.3
 * @subpackage ext
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C02-includes begin
// section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C02-includes end

/* user defined constants */
// section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C02-constants begin
// section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C02-constants end

/**
 * Short description of class common_ext_Manifest
 *
 * @access public
 * @author Jerome Bogaerts <jerome@taotesting.com>
 * @package common
 * @since 2.3
 * @subpackage ext
 */
class common_ext_Manifest
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute filePath
     *
     * @access private
     * @var string
     */
    private $filePath = '';

    /**
     * Short description of attribute name
     *
     * @access private
     * @var string
     */
    private $name = '';

    /**
     * Short description of attribute description
     *
     * @access private
     * @var string
     */
    private $description = '';

    /**
     * Short description of attribute author
     *
     * @access private
     * @var string
     */
    private $author = '';

    /**
     * Short description of attribute version
     *
     * @access private
     * @var string
     */
    private $version = '';

    /**
     * Short description of attribute dependencies
     *
     * @access private
     * @var array
     */
    private $dependencies = array();

    /**
     * Short description of attribute models
     *
     * @access private
     * @var array
     */
    private $models = array();

    /**
     * Short description of attribute modelsRights
     *
     * @access private
     * @var array
     */
    private $modelsRights = array();

    /**
     * Short description of attribute installModelFiles
     *
     * @access private
     * @var array
     */
    private $installModelFiles = array();

    /**
     * Short description of attribute installChecks
     *
     * @access private
     * @var array
     */
    private $installChecks = array();

    /**
     * Short description of attribute classLoaderPackages
     *
     * @access private
     * @var array
     */
    private $classLoaderPackages = array();

    /**
     * Short description of attribute constants
     *
     * @access private
     * @var array
     */
    private $constants = array();

    /**
     * Short description of attribute installPHPFiles
     *
     * @access private
     * @var array
     */
    private $installPHPFiles = array();

    /**
     * Short description of attribute managementRole
     *
     * @access private
     * @var Resource
     */
    private $managementRole = null;

    /**
     * Local data which can be added as an example
     * uses same format as install data
     *
     * @access private
     * @var array
     */
    private $localData = array();

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string filePath
     * @return mixed
     */
    public function __construct($filePath)
    {
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C26 begin
        
    	// the file exists, we can refer to the $filePath.
    	if (is_readable($filePath)){
    		$this->setFilePath($filePath);
    		$array = require($this->getFilePath());
    		
    		// legacy support
    		if (isset($array['additional']) && is_array($array['additional'])) {
				foreach ($array['additional'] as $key => $val) {
					$array[$key] = $val;
				}
				unset($array['additional']);
			}
    		
    		// mandatory
    		if (!empty($array['name'])){
    			$this->setName($array['name']);
    		}
    		else{
    			throw new common_ext_MalformedManifestException("The 'name' component is mandatory in manifest located at '{$this->getFilePath()}'.");
    		}
    		
    		if (!empty($array['description'])){
    			$this->setDescription($array['description']);
    		}
    		
    		if (!empty($array['author'])){
    			$this->setAuthor($array['author']);	
    		}
    		
    		// mandatory
    		if (!empty($array['version'])){
    			$this->setVersion($array['version']);
    		}
    		else{
    			throw new common_ext_MalformedManifestException("The 'version' component is mandatory in manifest located at '{$this->getFilePath()}'.");
    		}
    		
    		if (!empty($array['dependencies'])){
    			$this->setDependencies($array['dependencies']);
    		} elseif (!empty($array['dependances'])){
    			// legacy
    			$this->setDependencies($array['dependances']);
    		} 
    		
    		if (!empty($array['models'])){
    			$this->setModels($array['models']);
    		}
    		
    		if (!empty($array['modelsRight'])){
    			$this->setModelsRights($array['modelsRight']);
    		}
    		
    		if (!empty($array['install'])){
    			if (!empty($array['install']['rdf'])){
    				
					$files = is_array($array['install']['rdf']) ? $array['install']['rdf'] : array($array['install']['rdf']);
    				$this->setInstallModelFiles($files);
    			}
    			
    			if (!empty($array['install']['checks'])){
    				$this->setInstallChecks($array['install']['checks']);
    			}
    			
    			if (!empty($array['install']['php'])){
					$files = is_array($array['install']['php']) ? $array['install']['php'] : array($array['install']['php']);
    				$this->setInstallPHPFiles($files);
    			}
    		}
    		if (!empty($array['local'])){
    			$this->localData = $array['local']; 
    		}
    		
    		
    		// mandatory
    		if (!empty($array['classLoaderPackages'])){
    			$this->setClassLoaderPackages($array['classLoaderPackages']);
    		}
    		else{
    			throw new common_ext_MalformedManifestException("The 'classLoaderPackages' component is mandatory in manifest located at '{$this->getFilePath()}'.");
    		}
    		
    		if (!empty($array['constants'])){
    			$this->setConstants($array['constants']);
    		}
    		
    		if (!empty($array['managementRole'])){
    			$role = new core_kernel_classes_Resource($array['managementRole']);
    			$this->setManagementRole($role);
    		}
    	}
    	else{
    		throw new common_ext_ManifestNotFoundException("The Extension Manifest file located at '${filePath}' could not be read.");
    	}
    	
        $this->setFilePath($filePath);
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C26 end
    }

    /**
     * Short description of method getFilePath
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return string
     */
    public function getFilePath()
    {
        $returnValue = (string) '';

        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C41 begin
        if (!empty($this->filePath)){
        	$returnValue = $this->filePath;
        }
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C41 end

        return (string) $returnValue;
    }

    /**
     * Short description of method setFilePath
     *
     * @access private
     * @author firstname and lastname of author, <author@example.org>
     * @param  string filePath
     * @return void
     */
    private function setFilePath($filePath)
    {
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C57 begin
        $this->filePath = $filePath;
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C57 end
    }

    /**
     * Short description of method getName
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return string
     */
    public function getName()
    {
        $returnValue = (string) '';

        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C43 begin
        if (!empty($this->name)){
        	$returnValue = $this->name;
        }
        else{
        	$returnValue = null;
        }
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C43 end

        return (string) $returnValue;
    }

    /**
     * Short description of method setName
     *
     * @access private
     * @author firstname and lastname of author, <author@example.org>
     * @param  string name
     * @return void
     */
    private function setName($name)
    {
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C5A begin
        $this->name = $name;
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C5A end
    }

    /**
     * Short description of method getDescription
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return string
     */
    public function getDescription()
    {
        $returnValue = (string) '';

        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C45 begin
        if (!empty($this->description)){
        	$returnValue = $this->description;
        }
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C45 end

        return (string) $returnValue;
    }

    /**
     * Short description of method setDescription
     *
     * @access private
     * @author firstname and lastname of author, <author@example.org>
     * @param  string description
     * @return void
     */
    private function setDescription($description)
    {
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C5D begin
        $this->description = $description;
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C5D end
    }

    /**
     * Short description of method getAuthor
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return string
     */
    public function getAuthor()
    {
        $returnValue = (string) '';

        // section 10-13-1-85-9f61b60:13ae4914bf6:-8000:0000000000001C62 begin
        $returnValue = $this->author;
        // section 10-13-1-85-9f61b60:13ae4914bf6:-8000:0000000000001C62 end

        return (string) $returnValue;
    }

    /**
     * Short description of method setAuthor
     *
     * @access private
     * @author firstname and lastname of author, <author@example.org>
     * @param  string author
     * @return void
     */
    private function setAuthor($author)
    {
        // section 10-13-1-85-9f61b60:13ae4914bf6:-8000:0000000000001C64 begin
        $this->author = $author;
        // section 10-13-1-85-9f61b60:13ae4914bf6:-8000:0000000000001C64 end
    }

    /**
     * Short description of method getVersion
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return string
     */
    public function getVersion()
    {
        $returnValue = (string) '';

        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C47 begin
        if (!empty($this->version)){
        	$returnValue = $this->version;
        }
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C47 end

        return (string) $returnValue;
    }

    /**
     * Short description of method setVersion
     *
     * @access private
     * @author firstname and lastname of author, <author@example.org>
     * @param  string version
     * @return void
     */
    private function setVersion($version)
    {
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C60 begin
        $this->version = $version;
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C60 end
    }

    /**
     * Short description of method getDependencies
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return array
     */
    public function getDependencies()
    {
        $returnValue = array();

        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C49 begin
        $returnValue = $this->dependencies;
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C49 end

        return (array) $returnValue;
    }

    /**
     * Short description of method setDependencies
     *
     * @access private
     * @author firstname and lastname of author, <author@example.org>
     * @param  array dependencies
     * @return void
     */
    private function setDependencies($dependencies)
    {
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C63 begin
        $this->dependencies = $dependencies;
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C63 end
    }

    /**
     * Short description of method getModels
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return array
     */
    public function getModels()
    {
        $returnValue = array();

        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C4B begin
        $returnValue = $this->models;
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C4B end

        return (array) $returnValue;
    }

    /**
     * Short description of method setModels
     *
     * @access private
     * @author firstname and lastname of author, <author@example.org>
     * @param  array models
     * @return void
     */
    private function setModels($models)
    {
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C66 begin
        $this->models = $models;
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C66 end
    }

    /**
     * Short description of method getModelsRights
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return array
     */
    public function getModelsRights()
    {
        $returnValue = array();

        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C4D begin
        $returnValue = $this->modelsRights;
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C4D end

        return (array) $returnValue;
    }

    /**
     * Short description of method setModelsRights
     *
     * @access private
     * @author firstname and lastname of author, <author@example.org>
     * @param  array modelsRights
     * @return void
     */
    private function setModelsRights($modelsRights)
    {
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C69 begin
        $this->modelsRights = $modelsRights;
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C69 end
    }

    /**
     * returns an array of rdf files
     * to import during install
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return array
     */
    public function getInstallModelFiles()
    {
        $returnValue = array();

        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C4F begin
        $returnValue = $this->installModelFiles;
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C4F end

        return (array) $returnValue;
    }

    /**
     * Short description of method setInstallModelFiles
     *
     * @access private
     * @author firstname and lastname of author, <author@example.org>
     * @param  array installModelFiles
     * @return void
     */
    private function setInstallModelFiles($installModelFiles)
    {
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C6E begin
        $this->installModelFiles = array();
        $installModelFiles = is_array($installModelFiles) ? $installModelFiles : array($installModelFiles);
		foreach ($installModelFiles as $row) {
			if (is_string($row)) {
				$rdfpath = $row;
			} elseif (is_array($row) && isset($row['file'])) {
				$rdfpath = $row['file'];
			} else {
				throw new common_ext_InstallationException('Error in definition of model to add into the ontology for '.$this->extension->getID(), 'INSTALL');
			}
    		$this->installModelFiles[] = $rdfpath;
		}
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C6E end
    }

    /**
     * Short description of method getInstallChekcs
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return array
     */
    public function getInstallChekcs()
    {
        $returnValue = array();

        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C51 begin
        $returnValue = $this->installChecks;
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C51 end

        return (array) $returnValue;
    }

    /**
     * Short description of method setInstallChecks
     *
     * @access private
     * @author firstname and lastname of author, <author@example.org>
     * @param  array installChecks
     * @return void
     */
    private function setInstallChecks($installChecks)
    {
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C71 begin
        // Check if the content is well formed.
    	if (!is_array($installChecks)){
    		throw new common_ext_MalformedManifestException("The 'install->checks' component must be an array.");	
    	}
    	else{
    		foreach ($installChecks as $check){
    			// Mandatory fields for any kind of check are 'id' (string), 
    			// 'type' (string), 'value' (array).
    			if (empty($check['type'])){
    				throw new common_ext_MalformedManifestException("The 'install->checks->type' component is mandatory.");	
    			}else if (!is_string($check['type'])){
    				throw new common_ext_MalformedManifestException("The 'install->checks->type' component must be a string.");
    			}
    			
    			if (empty($check['value'])){
    				throw new common_ext_MalformedManifestException("The 'install->checks->value' component is mandatory.");
    			}
    			else if (!is_array($check['value'])){
    				throw new common_ext_MalformedManifestException("The 'install->checks->value' component must be an array.");	
    			}
    			
    			if (empty($check['value']['id'])){
    				throw new common_ext_MalformedManifestException("The 'install->checks->value->id' component is mandatory.");	
    			}
    			else if (!is_string($check['value']['id'])){
    				throw new common_ext_MalformedManifestException("The 'install->checks->value->id' component must be a string.");	
    			}
    			
    			switch ($check['type']){
    				case 'CheckPHPRuntime':
    					if (empty($check['value']['min'])){
    						throw new common_ext_MalformedManifestException("The 'install->checks->value->min' component is mandatory for PHPRuntime checks.");	
    					}
    				break;
    				
    				case 'CheckPHPExtension':
    					if (empty($check['value']['name'])){
    						throw new common_ext_MalformedManifestException("The 'install->checks->value->name' component is mandatory for PHPExtension checks.");
    					}
    				break;
    				
    				case 'CheckPHPINIValue':
    					if (empty($check['value']['name'])){
    						throw new common_ext_MalformedManifestException("The 'install->checks->value->name' component is mandatory for PHPINIValue checks.");
    					}
    					else if ($check['value']['value'] == ''){
    						throw new common_ext_MalformedManifestException("The 'install->checks->value->value' component is mandatory for PHPINIValue checks.");
    					}
    				break;
    				
    				case 'CheckFileSystemComponent':
    					if (empty($check['value']['location'])){
    						throw new common_ext_MalformedManifestException("The 'install->checks->value->location' component is mandatory for FileSystemComponent checks.");	
    					}
    					else if (empty($check['value']['rights'])){
    						throw new common_ext_MalformedManifestException("The 'install->checks->value->rights' component is mandatory for FileSystemComponent checks.");	
    					}
    				break;
    				
    				case 'CheckCustom':
    					if (empty($check['value']['name'])){
    						throw new common_ext_MalformedManifestException("The 'install->checks->value->name' component is mandatory for Custom checks.");	
    					}
    					else if (empty($check['value']['extension'])){
    						throw new common_ext_MalformedManifestException("The 'install->checks->value->extension' component is mandatory for Custom checks.");		
    					}
    				break;
    				
    				default:
    					throw new common_ext_MalformedManifestException("The 'install->checks->type' component value is unknown.");	
    				break;
    			}
    		}
    	}
    	
        $this->installChecks = $installChecks;
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C71 end
    }

    /**
     * Short description of method getInstallPHPFiles
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return array
     */
    public function getInstallPHPFiles()
    {
        $returnValue = array();

        // section 10-13-1-85--5c116a76:13c00a41227:-8000:0000000000001E85 begin
        $returnValue = $this->installPHPFiles;
        // section 10-13-1-85--5c116a76:13c00a41227:-8000:0000000000001E85 end

        return (array) $returnValue;
    }
    
   /**
     * gets an array of
     * * rdf files to include
     * * php scripts to execute
     * in order to add some sample data to an install
     *
     * @access public
     * @author joel.bout <joel@taotesting.com>
     * @return array
     */
    public function getLocalData()
    {
        return $this->localData;
    }

    /**
     * Short description of method setInstallPHPFiles
     *
     * @access private
     * @author firstname and lastname of author, <author@example.org>
     * @param  array installPHPFiles
     * @return void
     */
    private function setInstallPHPFiles($installPHPFiles)
    {
        // section 10-13-1-85--5c116a76:13c00a41227:-8000:0000000000001E87 begin
        $this->installPHPFiles = $installPHPFiles;
        // section 10-13-1-85--5c116a76:13c00a41227:-8000:0000000000001E87 end
    }

    /**
     * Short description of method getClassLoaderPackages
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return array
     */
    public function getClassLoaderPackages()
    {
        $returnValue = array();

        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C53 begin
        $returnValue = $this->classLoaderPackages;
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C53 end

        return (array) $returnValue;
    }

    /**
     * Short description of method setClassLoaderPackages
     *
     * @access private
     * @author firstname and lastname of author, <author@example.org>
     * @param  array classLoaderPackages
     * @return void
     */
    private function setClassLoaderPackages($classLoaderPackages)
    {
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C75 begin
        $this->classLoaderPackages = $classLoaderPackages;
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C75 end
    }

    /**
     * Short description of method getConstants
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return array
     */
    public function getConstants()
    {
        $returnValue = array();

        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C55 begin
        $returnValue = $this->constants;
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C55 end

        return (array) $returnValue;
    }

    /**
     * Short description of method setConstants
     *
     * @access private
     * @author firstname and lastname of author, <author@example.org>
     * @param  array constants
     * @return void
     */
    private function setConstants($constants)
    {
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C78 begin
        $this->constants = $constants;
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C78 end
    }

    /**
     * Extract checks from a given manifest file.
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string file The path to a manifest.php file.
     * @return common_configuration_ComponentCollection
     */
    public static function extractChecks($file)
    {
        $returnValue = null;

        // section 10-13-1-85-4049d0c6:13b28618bf6:-8000:0000000000001D6D begin
        if (is_readable($file)){
        	$manifestPath = $file;
	    	$content = file_get_contents($manifestPath);
	    	$matches = array();
	    	preg_match_all("/(?:\"|')\s*checks\s*(?:\"|')\s*=>(\s*array\s*\((\s*array\((?:.*)\s*\)\)\s*,{0,1})*\s*\))/", $content, $matches);
	    	
	    	if (!empty($matches[1][0])){
	    		$returnValue = eval('return ' . $matches[1][0] . ';');
	    		
	    		foreach ($returnValue as &$component){
		    		if (strpos($component['type'], 'FileSystemComponent') !== false){
		    			$root = realpath(dirname(__FILE__) . '/../../../');
	        			$component['value']['location'] = $root . '/' . $component['value']['location'];
	        		}	
	    		}
	    	}
	    	else{
	    		$returnValue = array();	
	    	}
        }
        else{
        	$msg = "Extension Manifest file could not be found in '${file}'.";
        	throw new common_ext_ManifestNotFoundException($msg);
        }
        // section 10-13-1-85-4049d0c6:13b28618bf6:-8000:0000000000001D6D end

        return $returnValue;
    }

    /**
     * Get the Role dedicated to manage this extension. Returns null if there is
     * Management Role.
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return core_kernel_classes_Resource
     */
    public function getManagementRole()
    {
        $returnValue = null;

        // section 127-0-1-1-4028d1e7:13cedb79c9e:-8000:0000000000001FA2 begin
        $returnValue = $this->managementRole;
        // section 127-0-1-1-4028d1e7:13cedb79c9e:-8000:0000000000001FA2 end

        return $returnValue;
    }

    /**
     * Set the Management Role of the Extension Manifest.
     *
     * @access private
     * @author firstname and lastname of author, <author@example.org>
     * @param  Resource managementRole The Management Role of the Extension as a Generis Resource.
     * @return void
     */
    private function setManagementRole( core_kernel_classes_Resource $managementRole)
    {
        // section 127-0-1-1-4028d1e7:13cedb79c9e:-8000:0000000000001FA6 begin
        $this->managementRole = $managementRole;
        // section 127-0-1-1-4028d1e7:13cedb79c9e:-8000:0000000000001FA6 end
    }

} /* end of class common_ext_Manifest */

?>