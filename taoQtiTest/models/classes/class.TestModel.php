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

/**
 * the qti TestModel
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoQtiTest
 * @subpackage models_classes
 */
class taoQtiTest_models_classes_TestModel
	implements taoTests_models_classes_TestModel
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---
    const CONFIG_QTITEST_FOLDER = 'qtiTestFolder';

    // --- OPERATIONS ---
    /**
     * default constructor to ensure the implementation
     * can be instanciated
     */
    public function __construct() {
    }
    
    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::onTestModelSet()
     */
    public function prepareContent( core_kernel_classes_Resource $test, $items = array()) {
    	$props = self::getQtiTestDirectory()->getPropertiesValues(array(
				PROPERTY_FILE_FILESYSTEM,
				PROPERTY_FILE_FILEPATH
			));
		$repository = new core_kernel_versioning_Repository(current($props[PROPERTY_FILE_FILESYSTEM]));
		$path = (string)current($props[PROPERTY_FILE_FILEPATH]);
		$file = $repository->createFile(md5($test->getUri()).'.xml', $path);
		$ext = common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiTest');
		$emptyTestXml = file_get_contents($ext->getDir().'models'.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'qtiTest.xml');
		$file->setContent($emptyTestXml);
		common_Logger::i('Created '.$file->getAbsolutePath());
		$test->setPropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP), $file);
    }
    
    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::onTestModelSet()
     */
    public function deleteContent( core_kernel_classes_Resource $test) {
    	$content = $test->getOnePropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP));
    	if (!is_null($content)) {
			$file = new core_kernel_file_File($content);
    		if(file_exists($file->getAbsolutePath())){
	        	if (!@unlink($file->getAbsolutePath())){
	        		throw new common_exception_Error('Unable to remove the file '.$file->getAbsolutePath());
	        	}
    		}
			$file->delete();
			$test->removePropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP), $file);
    	}
    }
    
    public function getItems( core_kernel_classes_Resource $test) {
    	return array();
    }

    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::onChangeTestLabel()
     */
    public function onChangeTestLabel( core_kernel_classes_Resource $test) {
    	// do nothing
    }
    
    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::getAuthoring()
     */
    public function getAuthoring( core_kernel_classes_Resource $test) {
    	$ext = common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiTest');
    	$widget = new Renderer($ext->getConstant('DIR_VIEWS').'templates'.DIRECTORY_SEPARATOR.'authoring_button.tpl');
		$widget->setData('uri', $test->getUri());
		$widget->setData('label', __('Authoring %s', $test->getLabel()));
    	return $widget->render();
    }
    
    public static function setQtiTestDirectory(core_kernel_file_File $folder) {
    	$ext = common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiTest');
    	$ext->setConfig(self::CONFIG_QTITEST_FOLDER, $folder->getUri());
    }
    
    public static function getQtiTestDirectory() {
    	
    	$ext = common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiTest');
        $uri = $ext->getConfig(self::CONFIG_QTITEST_FOLDER);
        if (empty($uri)) {
        	throw new common_Exception('No default repository defined for uploaded files storage.');
        }
		return new core_kernel_file_File($uri);
	}
}

?>