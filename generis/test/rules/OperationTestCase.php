<?php
error_reporting(E_ALL);
require_once dirname(__FILE__) . '/../../../tao/test/TestRunner.php';
require_once INCLUDES_PATH.'/simpletest/autorun.php';


class OperationTestCase extends UnitTestCase {


	public function setUp(){
		TestRunner::initTest();
	}
	
	public function testEvaluate(){
		$constant5 = core_kernel_rules_TermFactory::createConst('5');
		$constant12 = core_kernel_rules_TermFactory::createConst('12');
		
		//5 + 12
		$operation = core_kernel_rules_OperationFactory::createOperation(
			$constant5,
			$constant12,
			new core_kernel_classes_Resource(INSTANCE_OPERATOR_ADD)
		);
		$result = $operation->evaluate();
		$this->assertIsA($result,'core_kernel_classes_Literal');
		$this->assertEqual($result->literal,'17');
		
		//5 - 12
		$operation = core_kernel_rules_OperationFactory::createOperation(
			$constant5,
			$constant12,
			new core_kernel_classes_Resource(INSTANCE_OPERATOR_MINUS)
		);
		$result = $operation->evaluate();
		$this->assertIsA($result,'core_kernel_classes_Literal');
		$this->assertEqual($result->literal,'-7');
		
		//5 * 12
		$operation = core_kernel_rules_OperationFactory::createOperation(
			$constant5,
			$constant12,
			new core_kernel_classes_Resource(INSTANCE_OPERATOR_MULTIPLY)
		);
		$result = $operation->evaluate();
		$this->assertIsA($result,'core_kernel_classes_Literal');
		$this->assertEqual($result->literal,'60');
		
		//60 / 12
		$constant60 = core_kernel_rules_TermFactory::createConst('60');		
		$operation = core_kernel_rules_OperationFactory::createOperation(
			$constant60,
			$constant12,
			new core_kernel_classes_Resource(INSTANCE_OPERATOR_DIVISION)
		);
		$result = $operation->evaluate();
		$this->assertIsA($result,'core_kernel_classes_Literal');
		$this->assertEqual($result->literal,'5');
		
		// 60 concat 12 
		$operation = core_kernel_rules_OperationFactory::createOperation(
			$constant60,
			$constant12,
			new core_kernel_classes_Resource(INSTANCE_OPERATOR_CONCAT)
		);
		$result = $operation->evaluate();
		$this->assertIsA($result,'core_kernel_classes_Literal');
		$this->assertEqual($result->literal,'60 12');
		
		// raise excption bad operator
		$operation = core_kernel_rules_OperationFactory::createOperation(
			$constant60,
			$constant12,
			new core_kernel_classes_Resource(INSTANCE_OPERATOR_UNION)
		);
		
		try {
			$operation->evaluate();
			$this->fail('should raise exception : problem evaluating operation, operator do not match with operands');
		} catch (common_Exception $e) {
			$this->assertEqual($e->getMessage(),'problem evaluating operation, operator do not match with operands');
		}

		
		
		$constant60->delete();
		$constant5->delete();
		$constant12->delete();
		$operation->delete();
	}

}