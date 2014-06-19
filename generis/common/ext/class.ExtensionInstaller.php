<?php
/**  
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
 *			   2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

use oat\oatbox\AutoLoader;

/**
 * Short description of class common_ext_ExtensionInstaller
 *
 * @access public
 * @author lionel.lecaque@tudor.lu
 * @package generis
 * @see @license  GNU General Public (GPL) Version 2 http://www.opensource.org/licenses/gpl-2.0.php
 
 */
class common_ext_ExtensionInstaller
	extends common_ext_ExtensionHandler
{
	// --- ASSOCIATIONS ---


	// --- ATTRIBUTES ---

	/**
	 * States if local data must be installed or not.
	 *
	 * @access private
	 * @var boolean
	 */
	private $localData = false;

	// --- OPERATIONS ---

	/**
	 * install an extension
	 *
	 * @access public
	 * @author Jerome Bogaerts, <jerome@taotesting.com>
	 * @return void
	 */
	public function install()
	{
		
		
		common_Logger::i('Installing extension '.$this->extension->getId(), 'INSTALL');
		
		if ($this->extension->getId() == 'generis') {
			throw new common_ext_ForbiddenActionException('Tried to install generis using the ExtensionInstaller',
														  $this->extension->getId());
		}
		
		try{
			// not yet installed? 
			if (common_ext_ExtensionsManager::singleton()->isInstalled($this->extension->getId())) {
				throw new common_ext_AlreadyInstalledException('Problem installing extension ' . $this->extension->getId() .' : Already installed',
															   $this->extension->getId());
			}
			else{
				// we purge the whole cache.
				$cache = common_cache_FileCache::singleton();
				$cache->purge();	
			
				//check dependances
				if(!$this->checkRequiredExtensions()){
					// unreachable code
				}
					
				// deprecated, but might still be used
				$this->installWriteConfig();
				$this->installOntology();
				$this->installRegisterExt();
				$this->installLoadConstants();
				$this->installExtensionModel();
					
				core_kernel_persistence_smoothsql_SmoothModel::forceReloadModelIds();
					
				//reload the autoloader
				AutoLoader::reload();
				common_Logger::d('Installing custom script for extension ' . $this->extension->getId());
				$this->installCustomScript();
				common_Logger::d('Done installing custom script for extension ' . $this->extension->getId());
				
				if ($this->getLocalData() == true){
					common_Logger::d('Installing local data for extension ' . $this->extension->getId());
					$this->installLocalData();
					common_Logger::d('Done installing local data for extension ' . $this->extension->getId());
						
				}
				common_Logger::d('Extended install for extension ' . $this->extension->getId());
					
				// Method to be overriden by subclasses
				// to extend the installation mechanism.
				$this->extendedInstall();
				common_Logger::d('Done extended install for extension ' . $this->extension->getId());
			}
				
		}catch (common_ext_ExtensionException $e){
			// Rethrow
			common_Logger::e('Exception raised ' . $e->getMessage());
			throw $e;
		}

		
	}

	/**
	 * writes the config based on the config.sample
	 *
	 * @access protected
	 * @author Jerome Bogaerts, <jerome@taotesting.com>
	 * @return void
	 */
	protected function installWriteConfig()
	{
		
		$sampleFile	= $this->extension->getDir().'includes/config.php.sample';
		$finalFile	= $this->extension->getDir().'includes/config.php';
		
		if (file_exists($sampleFile)) {
			common_Logger::d('Writing config '.$finalFile.' for '.$this->extension->getId(), 'INSTALL');
			$myConfigWriter = new tao_install_utils_ConfigWriter(
				$sampleFile,
				$finalFile
			);
			$myConfigWriter->createConfig();
			
			// @todo solve this
			if ($this->extension->getId() == 'tao') {
				require_once($finalFile);
			}
		} elseif (file_exists($finalFile)){
		    helpers_File::remove($finalFile);
		}
		
		
	}

	/**
	 * inserts the datamodels
	 * specified in the Manifest
	 *
	 * @access protected
	 * @author Jerome Bogaerts, <jerome@taotesting.com>
	 * @return void
	 */
	protected function installOntology()
	{
		
		// insert model
		$modelCreator = new tao_install_utils_ModelCreator(LOCAL_NAMESPACE);
		foreach ($this->extension->getManifest()->getInstallModelFiles() as $rdfpath) {
			if (file_exists($rdfpath)){
				if (is_readable($rdfpath)){
					$xml = simplexml_load_file($rdfpath);
					$attrs = $xml->attributes('xml', true);
					if(!isset($attrs['base']) || empty($attrs['base'])){
						throw new common_ext_InstallationException('The namespace of '.$rdfpath.' has to be defined with the "xml:base" attribute of the ROOT node');
					}
					$ns = (string) $attrs['base'];
					if($ns != 'LOCAL_NAMESPACE##'){
					    //import the model in the ontology
					    common_Logger::d('Inserting model '.$rdfpath.' for '.$this->extension->getId(), 'INSTALL');
					    $modelCreator->insertModelFile($ns, $rdfpath);
					  
					}else{
					    common_Logger::d('Inserting model '.$rdfpath.' for '.$this->extension->getId() . ' in LOCAL NAMESPACE', 'INSTALL');
					    $modelCreator->insertLocalModelFile($rdfpath);
					}
					foreach ($this->getTranslatedModelFiles($rdfpath) as $translation) {
						$modelCreator->insertModelFile($ns, $translation);
					}
				}
				else{
					throw new common_ext_InstallationException("Unable to load ontology in '${rdfpath}' because the file is not readable.");
				}
			}
			else{
				throw new common_ext_InstallationException("Unable to load ontology in '${rdfpath}' because the file does not exist.");
			}
		}
		
	}

	/**
	 * returns the paths of the translations of a specified ontology file
	 *
	 * @access private
	 * @author Joel Bout, <joel@taotesting.com>
	 * @return array absolute paths to the translated rdf files
	 */
	private function getTranslatedModelFiles($rdfpath) {
		$returnValue = array();
		$localesPath = $this->extension->getDir() . 'locales' . DIRECTORY_SEPARATOR;
		if (file_exists($localesPath)) {
			$fileName = basename($rdfpath);
			foreach (new DirectoryIterator($localesPath) as $fileinfo) {
				if (!$fileinfo->isDot() && $fileinfo->isDir() && $fileinfo->getFilename() != '.svn' && $fileinfo->getFilename() != 'en-US') {
					$candidate = $fileinfo->getPathname() . DIRECTORY_SEPARATOR . $fileName;
					if (file_exists($candidate)) {
						$returnValue[] = $candidate;
					} 
				} 
			}
		}
		return $returnValue;
	}

	/**
	 * Registers the Extension with the extensionManager
	 *
	 * @access protected
	 * @author Jerome Bogaerts, <jerome@taotesting.com>
	 * @return void
	 */
	protected function installRegisterExt()
	{
		
		common_Logger::d('Registering extension '.$this->extension->getId(), 'INSTALL');
		common_ext_ExtensionsManager::singleton()->registerExtension($this->extension);
		common_ext_ExtensionsManager::singleton()->setEnabled($this->extension->getId());
		
		
	}

	/**
	 * Executes custom install scripts 
	 * specified in the Manifest
	 *
	 * @access protected
	 * @author Jerome Bogaerts, <jerome@taotesting.com>
	 * @return void
	 */
	protected function installCustomScript()
	{
		
		//install script
		foreach ($this->extension->getManifest()->getInstallPHPFiles() as $script) {
			common_Logger::d('Running custom install script '.$script.' for extension '.$this->extension->getId(), 'INSTALL');
			require_once $script;
		}
		
	}

	/**
	 * Installs example files and other non essential content
	 *
	 * @access protected
	 * @author Jerome Bogaerts, <jerome@taotesting.com>
	 * @return void
	 */
	protected function installLocalData()
	{
		
		$localData = $this->extension->getManifest()->getLocalData();
		if(isset($localData['rdf'])){
			$modelCreator = new tao_install_utils_ModelCreator(LOCAL_NAMESPACE);
			foreach ($localData['rdf'] as $rdfpath) {
				if(file_exists($rdfpath)){
					common_Logger::d('Inserting local data rdf '.$rdfpath.' for '.$this->extension->getId(), 'INSTALL');
					$modelCreator->insertLocalModelFile($rdfpath);
				}
			}
		}
		if(isset($localData['php'])) {
			$scripts = $localData['php'];
			$scripts = is_array($scripts) ? $scripts : array($scripts);
			foreach ($scripts as $script) {
				common_Logger::d('Running local data script '.$script.' for extension '.$this->extension->getId(), 'INSTALL');
				require_once $script;
			}
		}
		
	}

	/**
	 * Loads the /extension_folder/includes/constants.php file of the extension.
	 *
	 * @access public
	 * @author Jerome Bogaerts, <jerome@taotesting.com>
	 * @return void
	 */
	public function installLoadConstants()
	{
		
		common_Logger::i("Loading constants for extension " . $this->extension->getId());
		$this->extension->load();
		
	}

	/**
	 * Instantiate the Extension/Module/Action model in the persistent memory of
	 *
	 * @access public
	 * @author Jerome Bogaerts, <jerome@taotesting.com>
	 * @return void
	 */
	public function installExtensionModel()
	{
		
		//common_Logger::i("Spawning Extension/Module/Action model for extension '" . $this->extension->getId() . "'");
		
	}

	/**
	 * check required extensions are not missing
	 *
	 * @access protected
	 * @author Jerome Bogaerts, <jerome@taotesting.com>
	 * @return boolean
	 */
	protected function checkRequiredExtensions()
	{
		$returnValue = (bool) false;

		
		$extensionManager = common_ext_ExtensionsManager::singleton();
		$installedExtArray = $extensionManager->getInstalledExtensions();
		foreach ($this->extension->getDependencies() as $requiredExt => $requiredVersion) {
			if(!array_key_exists($requiredExt,$installedExtArray)){
				throw new common_ext_MissingExtensionException('Extension '. $requiredExt . ' is needed by the extension to be installed but is missing.',
															   $requiredExt);
			}
		}
		$returnValue = true;
		

		return (bool) $returnValue;
	}

	/**
	 * Instantiate a new ExtensionInstaller for a given Extension.
	 *
	 * @access public
	 * @author Jerome Bogaerts, <jerome@taotesting.com>
	 * @param  Extension extension The extension to install
	 * @param  boolean localData Import local data or not.
	 * @return mixed
	 */
	public function __construct( common_ext_Extension $extension, $localData = true)
	{
		
		parent::__construct($extension);
		$this->setLocalData($localData);
		
	}

	/**
	 * Sets localData field.
	 *
	 * @access public
	 * @author Jerome Bogaerts, <jerome@taotesting.com>
	 * @param  boolean value
	 * @return mixed
	 */
	public function setLocalData($value)
	{
		
		$this->localData = $value;
		
	}

	/**
	 * Retrieve localData field
	 *
	 * @access public
	 * @author Jerome Bogaerts, <jerome@taotesting.com>
	 * @return boolean
	 */
	public function getLocalData()
	{
		$returnValue = (bool) false;

		
		$returnValue = $this->localData;
		

		return (bool) $returnValue;
	}

	/**
	 * Short description of method extendedInstall
	 *
	 * @access public
	 * @author Jerome Bogaerts, <jerome@taotesting.com>
	 * @return void
	 */
	public function extendedInstall()
	{
		
		return;
		
	}

} /* end of class common_ext_ExtensionInstaller */

?>