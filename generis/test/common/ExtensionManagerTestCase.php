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
require_once dirname(__FILE__) . '/../../../tao/test/TaoTestRunner.php';
require_once INCLUDES_PATH.'/simpletest/autorun.php';



/**
 * Test class for ExtensionManager
 * 
 * @author lionel.lecaque@tudor.lu
 * @package test
 */


class ExtensionManagerTestCase extends UnitTestCase {
	
	public function testGetInstalledExtensions(){
		$db = core_kernel_classes_DbWrapper::singleton();
		$fakeExtensionSql = 'INSERT INTO "extensions" ("id", "name", "version", "loaded", "loadAtStartUp") '.
							"VALUES ('testExtension', 'test', '0.1', 1, 1);";
		
		$this->assertTrue($db->exec($fakeExtensionSql));
		$extensionManager = common_ext_ExtensionsManager::singleton();
		
		try{
			$extensionManager->reset();
			$ext = $extensionManager->getInstalledExtensions();
			$this->fail('should raise exception');
		}
		catch(common_ext_ManifestNotFoundException $ee){
			$this->assertEqual("Extension Manifest not found for extension 'testExtension'.", $ee->getMessage());
		}
		
		mkdir(EXTENSION_PATH.'/testExtension');
		file_put_contents(EXTENSION_PATH.'/testExtension/'.MANIFEST_NAME, "
			<?php
				return array(
					'name' => 'Test Extension',
					'description' => 'Test Extension',
					'version' => '0.25',
					'author' => 'CRP Henry Tudor',
					'dependances' => array('test01'),
					'models' => array(),
					'install' => array( 
						'sql' => dirname(__FILE__). '/install/db/testExtension.sql',
						'php' => dirname(__FILE__). '/install/install.php'
					),
					'registerToClassLoader' => true,
					'classLoaderPackages' => array( 
						dirname(__FILE__).'/actions/' , 
						dirname(__FILE__).'/models/',
					),
					'constants' => array(
						'example1' => 1,
						'example2' => array('a', 'b')
					)
				);
			?>
		");
		
		$this->assertTrue(file_exists(EXTENSION_PATH.'/testExtension/'.MANIFEST_NAME));
		
		$extensionManager->reset();
		$ext = $extensionManager->getInstalledExtensions();
		$this->assertTrue(isset($ext['testExtension']));
		
		$this->assertEqual($ext['testExtension']->getAuthor(), 'CRP Henry Tudor');
		$this->assertEqual($ext['testExtension']->getName(), 'Test Extension');
		$this->assertEqual($ext['testExtension']->getVersion(), '0.25');
		
		$ext = common_ext_ExtensionsManager::singleton()->getExtensionById('testExtension');
		$this->assertIsA($ext, 'common_ext_Extension');
		$this->assertEqual($ext->getConstant('example1'), 1);
		$this->assertEqual($ext->getConstant('example2'), array('a', 'b'));
		
		try {
			$ext->getConstant('unknown_constant');
			$this->fail('No exception on unknown constant');
		} catch (common_Exception $e) {
			$this->assertIsA($e, 'common_exception_Error');
		}
		
		unlink(EXTENSION_PATH.'/testExtension/'.MANIFEST_NAME);
		rmdir(EXTENSION_PATH.'/testExtension');
		
		$this->assertFalse(file_exists(EXTENSION_PATH.'/testExtension/'.MANIFEST_NAME));
		
		$fakeExtensionSql = 'DELETE FROM "extensions" where "id" = \'testExtension\'';
		$this->assertTrue($db->exec($fakeExtensionSql));
	}
	

	
	public function testGetModelsToLoad(){
		$extensinoManager = common_ext_ExtensionsManager::singleton();
		$models = $extensinoManager->getModelsToLoad();
		$this->assertTrue(is_array($models));
		$this->assertTrue(count($models) > 0);
		$this->assertTrue(in_array('http://www.w3.org/1999/02/22-rdf-syntax-ns', $models));
		$this->assertTrue(in_array('http://www.w3.org/2000/01/rdf-schema', $models));
		$this->assertTrue(in_array('http://www.tao.lu/Ontologies/generis.rdf', $models));
	}
}