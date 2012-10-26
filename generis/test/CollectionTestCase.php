<?php

error_reporting(E_ALL);
require_once dirname(__FILE__) . '/GenerisTestRunner.php';

/**
 * Test class for Collection.
 * 
 * @author lionel.lecaque@tudor.lu
 * @package test
 */


class CollectionTestCase extends UnitTestCase {

	protected $object;
	private $toto;
	private $tata;

	function __construct() {
    	parent::__construct();
    }
	
    /**
     * Setting the collection to test
     *
     */
    public function setUp(){
        GenerisTestRunner::initTest();
		$this->object = new common_Collection(new common_Object(__METHOD__));
		$this->toto =  new core_kernel_classes_Literal('toto',__METHOD__);
		$this->tata =  new core_kernel_classes_Literal('tata',__METHOD__);
		$this->object->sequence[0] = $this->toto;
		$this->object->sequence[1] = $this->tata;
	}
	
	/**
	 * Test common_Collection->add
	 *
	 */
	public function testAdd(){
		$titi = new common_Object(__METHOD__);
		$this->object->add($titi);
		$this->assertEqual($this->object->sequence[2] , $titi);
	}
	
	/**
	 * Test common_Collection->count
	 *
	 */
	public function testCount(){
		$this->assertTrue($this->object->count() == 2);
	}
	
	/**
	 * Test common_Collection->indexOf
	 *
	 */
	public function testIndexOf(){
		$this->assertTrue($this->object->indexOf($this->toto) == 0);
		$this->assertTrue($this->object->indexOf($this->tata) == 1);
		$this->assertFalse($this->object->indexOf(new common_Object(__METHOD__)) == 2);
	}
	
	/**
	 * Test common_Collection->get
	 *
	 */
	public function testGet(){
		$this->assertEqual($this->object->get(0),$this->object->sequence[0]);
		$this->assertEqual($this->object->get(0)->literal , 'toto');
	}
	
	/**
	 * Test common_Collection->isEmtpy
	 *
	 */
	public function testisEmpty(){
		$emtpy = new common_Collection(new common_Object(__METHOD__));
		$this->assertTrue($emtpy->isEmpty());
		$emtpy->add(new common_Object(__METHOD__));
		$this->assertFalse($emtpy->isEmpty());

	}
	
	/**
	 * Test common_Collection->remove
	 *
	 */
	public function testRemove(){
		$this->object->remove($this->toto);
		$this->assertFalse($this->object->indexOf($this->toto) == 0);

	}
	
	 /**
	  * Test common_Collection->union
	  */
	public function testUnion(){
		$collection = new common_Collection(new common_Object('__METHOD__'));
		$collection->add(new core_kernel_classes_Literal('plop'));
		$results = $this->object->union($collection);
		$this->assertIsA($results,'common_Collection');
		$this->assertFalse($results->isEmpty());
		$this->assertTrue($results->count() == 3);
		$this->assertTrue($results->get($results->indexOf($this->toto))->literal = 'toto');
		$this->assertTrue($results->get($results->indexOf($this->toto))->literal = 'tata');
		$this->assertTrue($results->get(2)->literal = 'plop');
	}
	
	 /**
	  * Test common_Collection->intersect
	  */
	public function testIntersect(){
		$collection = new common_Collection(new common_Object('__METHOD__'));
		$collection->add(new core_kernel_classes_Literal('plop'));
		$collection->add(new core_kernel_classes_Literal('plop2'));
		$collection->add($this->toto);
		$collection->add($this->tata);
		$results = $collection->intersect($this->object);
		$this->assertIsA($results,'common_Collection');
		$this->assertTrue($results->count() == 2);
		$this->assertTrue($results->get($results->indexOf($this->toto))->literal = 'toto');
		$this->assertTrue($results->get($results->indexOf($this->toto))->literal = 'tata');
	}
}

?>