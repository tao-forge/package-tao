<?php
require_once dirname(__FILE__) . '/GenerisTestRunner.php';


class HardDbSubjectTestCase extends UnitTestCase {

	protected $targetSubjectClass = null;
	protected $targetSubjectSubClass = null;
	protected $dataIntegrity = array ();

	public function setUp(){
        GenerisTestRunner::initTest();
	}

	private function countStatements (){
		$query =  'SELECT count(*) FROM "statements"';
		$result = core_kernel_classes_DbWrapper::singleton()->query($query);
		$row = $result->fetch();
		$result->closeCursor();
		return $row[0];
	}
	
	public function testCreateContextOfThetest(){
		// Top Class : TaoSubject
		$subjectClass = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject');
		// Create a new subject class for the unit test
		$this->targetSubjectClass = $subjectClass->createSubClass ("Sub Subject Class (Unit Test)");

		// Add an instance to this subject class
		$this->subject1 = $this->targetSubjectClass->createInstance ("Sub Subject (Unit Test)");
		$this->assertEqual (count($this->targetSubjectClass->getInstances ()), 1);

		// Create a new subject sub class to the previous sub class
		$this->targetSubjectSubClass = $this->targetSubjectClass->createSubClass ("Sub Sub Subject Class (Unit Test)");
		// Add an instance to this sub subject class
		$this->subject2 = $this->targetSubjectSubClass->createInstance ("Sub Sub Subject (Unit Test)");
		$this->assertEqual (count($this->targetSubjectSubClass->getInstances ()), 1);

		$this->assertEqual (count($this->targetSubjectClass->getInstances ()), 1);
		// If get instances in the sub classes of the targetSubjectClass, we should get 2 instances
		$this->assertEqual (count($this->targetSubjectClass->getInstances (true)), 2);
		
		$this->dataIntegrity['statements0'] = $this->countStatements();
		$this->dataIntegrity['subSubjectClassCount0'] = $this->targetSubjectClass->countInstances();
		$this->dataIntegrity['subSubSubjectClassCount0'] = $this->targetSubjectSubClass->countInstances();
	}

	public function testHardifier () {
		
		$switcher = new core_kernel_persistence_Switcher();
		$switcher->hardify($this->targetSubjectClass, array(
			'topClass'		=> new core_kernel_classes_Class('http://www.tao.lu/Ontologies/generis.rdf#User'),
			'recursive'		=> true,
			'createForeigns'=> false,
			'rmSources'		=> true
		));
		unset ($switcher);
		
		$this->assertIsA(core_kernel_persistence_ClassProxy::singleton()->getImpToDelegateTo($this->targetSubjectClass), 'core_kernel_persistence_hardsql_Class');
		$this->assertIsA(core_kernel_persistence_ClassProxy::singleton()->getImpToDelegateTo($this->targetSubjectSubClass), 'core_kernel_persistence_hardsql_Class');
	}

	public function testUnhardifier () {
		
		$switcher = new core_kernel_persistence_Switcher();
		$switcher->unhardify($this->targetSubjectClass, array(
			'recursive'			=> true,
			'removeForeigns'	=> false
		));
		unset ($switcher);
	}
	
	public function testDataIntegrity (){
		$this->dataIntegrity['statements1'] = $this->countStatements();
		$this->dataIntegrity['subSubjectClassCount1'] = $this->targetSubjectClass->countInstances();
		$this->dataIntegrity['subSubSubjectClassCount1'] = $this->targetSubjectSubClass->countInstances();
		
		$this->assertEqual($this->dataIntegrity['statements0'], $this->dataIntegrity['statements1']);
		$this->assertEqual($this->dataIntegrity['subSubjectClassCount0'], $this->dataIntegrity['subSubjectClassCount1']);
		$this->assertEqual($this->dataIntegrity['subSubSubjectClassCount0'], $this->dataIntegrity['subSubSubjectClassCount1']);
		
		$this->assertFalse(core_kernel_persistence_ClassProxy::singleton()->isValidContext('hardsql', $this->targetSubjectClass));
		$this->assertTrue(core_kernel_persistence_ClassProxy::singleton()->isValidContext('smoothsql', $this->targetSubjectClass));
		$this->assertIsA(core_kernel_persistence_ClassProxy::singleton()->getImpToDelegateTo($this->targetSubjectClass), 'core_kernel_persistence_smoothsql_Class');
		$this->assertFalse(core_kernel_persistence_ClassProxy::singleton()->isValidContext('hardsql', $this->targetSubjectSubClass));
		$this->assertTrue(core_kernel_persistence_ClassProxy::singleton()->isValidContext('smoothsql', $this->targetSubjectSubClass));
		$this->assertIsA(core_kernel_persistence_ClassProxy::singleton()->getImpToDelegateTo($this->targetSubjectSubClass), 'core_kernel_persistence_smoothsql_Class');
	}
	
	public function testClean (){
		// Remove the resources
		foreach ($this->targetSubjectClass->getInstances() as $instance){
			$instance->delete ();
		}
		foreach ($this->targetSubjectSubClass->getInstances() as $instance){
			$instance->delete ();
		}
		
		$this->targetSubjectClass->delete(true);
		$this->targetSubjectSubClass->delete(true);
	}
	
}
