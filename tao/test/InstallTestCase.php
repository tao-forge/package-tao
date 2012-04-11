<?php
require_once dirname(__FILE__) . '/TestRunner.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 * 
 * @author Jerome Bogaerts, <taosupport@tudor.lu>
 * @package tao
 * @subpackage test
 */
class InstallTestCase extends UnitTestCase {
	
	/**
	 * Test the Installation Model Creator.
	 */
	public function testModelCreator() {
		$extensionManager = common_ext_ExtensionsManager::singleton();
		$extensions = $extensionManager->getInstalledExtensions();
		
		$files = tao_install_utils_ModelCreator::getTranslationModelsFromExtension($extensions['tao']);
		$ns = 'http://www.tao.lu/Ontologies/TAO.rdf';
		$this->assertTrue(is_array($files));
		//$this->assertTrue(array_key_exists($ns, $files));
		//$this->assertTrue(count($files) == 1);
	}
}
?>