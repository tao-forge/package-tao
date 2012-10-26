<?php
require_once dirname(__FILE__) . '/../../tao/test/TaoTestRunner.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoSubjects
 * @subpackage test
 */
class SubjectsTestCase extends UnitTestCase {
	
	/**
	 * 
	 * @var taoSubjects_models_classes_SubjectsService
	 */
	protected $subjectsService = null;
	
	/**
	 * tests initialization
	 */
	public function setUp(){		
		TaoTestRunner::initTest();
	}
	
	/**
	 * Test the user service implementation
	 * @see tao_models_classes_ServiceFactory::get
	 * @see taoSubjects_models_classes_SubjectsService::__construct
	 */
	public function testService(){
		
		$subjectsService = taoSubjects_models_classes_SubjectsService::singleton();
		$this->assertIsA($subjectsService, 'tao_models_classes_Service');
		$this->assertIsA($subjectsService, 'taoSubjects_models_classes_SubjectsService');
		
		$this->subjectsService = $subjectsService;
	}
	
	/**
	 * Usual CRUD (Create Read Update Delete) on the subject class  
	 */
	public function testCrud(){
		
		//check parent class
		$this->assertTrue(defined('TAO_SUBJECT_CLASS'));
		$subjectClass = $this->subjectsService->getSubjectClass();
		$this->assertIsA($subjectClass, 'core_kernel_classes_Class');
		$this->assertEqual(TAO_SUBJECT_CLASS, $subjectClass->uriResource);
		
		//create a subclass
		$subSubjectClassLabel = 'subSubject class';
		$subSubjectClass = $this->subjectsService->createSubClass($subjectClass, $subSubjectClassLabel);
		$this->assertIsA($subSubjectClass, 'core_kernel_classes_Class');
		$this->assertEqual($subSubjectClassLabel, $subSubjectClass->getLabel());
		$this->assertTrue($this->subjectsService->isSubjectClass($subSubjectClass));
		
		//create an instance of the Item class
		$subjectInstanceLabel = 'subject instance';
		$subjectInstance = $this->subjectsService->createInstance($subjectClass, $subjectInstanceLabel);
		$this->assertIsA($subjectInstance, 'core_kernel_classes_Resource');
		$this->assertEqual($subjectInstanceLabel, $subjectInstance->getLabel());
		
		//create instance of subSubject
		$subSubjectInstanceLabel = 'subSubject instance';
		$subSubjectInstance = $this->subjectsService->createInstance($subSubjectClass);
		$this->assertTrue(defined('RDFS_LABEL'));
		$subSubjectInstance->removePropertyValues(new core_kernel_classes_Property(RDFS_LABEL));
		$subSubjectInstance->setLabel($subSubjectInstanceLabel);
		$this->assertIsA($subSubjectInstance, 'core_kernel_classes_Resource');
		$this->assertEqual($subSubjectInstanceLabel, $subSubjectInstance->getLabel());
		
		$subSubjectInstanceLabel2 = 'my sub subject instance';
		$subSubjectInstance->setLabel($subSubjectInstanceLabel2);
		$this->assertEqual($subSubjectInstanceLabel2, $subSubjectInstance->getLabel());
		
		
		$this->assertTrue($subSubjectInstance->delete());
		$this->assertTrue($subSubjectClass->delete());
	}
}
?>