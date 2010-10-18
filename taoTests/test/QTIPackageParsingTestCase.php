<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
require_once dirname(__FILE__) . '/../includes/common.php';

/**
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoItems
 * @subpackage test
 */
class QTIPackageParsingTestCase extends UnitTestCase {
	
	protected $qtiService;
	
	/**
	 * tests initialization
	 */
	public function setUp(){		
		TestRunner::initTest();
		$this->qtiService = tao_models_classes_ServiceFactory::get("taoItems_models_classes_QTI_Service");
	}
	
	
	/**
	 * test qti file parsing: validation and loading in a non-persistant context
	 */
	public function testFileParsing(){
		
		taoItems_models_classes_QTI_Data::setPersistance(false);
		
//		//check if wrong files are not validated correctly
//		foreach(glob(dirname(__FILE__).'/samples/wrong/*.*') as $file){
//			
//			$qtiParser = new taoItems_models_classes_QTI_Parser($file);
//			
//			$qtiParser->validate();
//			
//			$this->assertFalse($qtiParser->isValid());
//			$this->assertTrue(count($qtiParser->getErrors()) > 0);
//			$this->assertTrue(strlen($qtiParser->displayErrors()) > 0);
//		}
		
		//check if samples are loaded 
		foreach(glob(dirname(__FILE__).'/samples/*.xml') as $file){
			
			$qtiParser = new taoTests_models_classes_QTI_Parser($file);
			$qtiParser->validate();
			
			if(!$qtiParser->isValid())
				echo $qtiParser->displayErrors();
			
			$this->assertTrue($qtiParser->isValid());
		}
	}
	
}
?>