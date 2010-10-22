<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
require_once dirname(__FILE__) . '/../includes/common.php';

/**
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoItems
 * @subpackage test
 */
class QTIParsingResponseProcessingTestCase extends UnitTestCase {
	
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
	public function testMatching (){
		taoItems_models_classes_QTI_Data::setPersistance(false);
		
		//check if samples are loaded
		$file = dirname(__FILE__).'/samples/choice_multiple.xml';

		$qtiParser = new taoItems_models_classes_QTI_Parser($file);
		$qtiParser->validate();
		
		$this->assertTrue($qtiParser->isValid());
		$item = $qtiParser->load();
		
		echo $item->toXHTML();
		
		$this->assertIsA($item, 'taoItems_models_classes_QTI_Item');
	}
	
}
?>