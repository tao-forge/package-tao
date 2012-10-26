<?php


require_once dirname(__FILE__) . '/GenerisTestRunner.php';



/**
 * Test class for Class.
 * 
 * @author lionel.lecaque@tudor.lu
 * @package test
 */


class ClassTestCase extends UnitTestCase {
	protected $object;
	
	public function setUp(){

        GenerisTestRunner::initTest();

		$this->object = new core_kernel_classes_Class(RDF_RESOURCE);
		$this->object->debug = __METHOD__;
	}
    
	public function testGetSubClasses(){

		$generisResource = new core_kernel_classes_Class(CLASS_GENERIS_RESOURCE);
	
		$subClass0 = $generisResource->createSubClass('test0','test0 Comment');
		$subClass1 = $subClass0->createSubClass('test1','test1 Comment');

	
		$subClass2 = $subClass0->createSubClass('test2','test2 Comment');
		$subClass3 = $subClass2->createSubClass('test3','test3 Comment');
		$subClass4 = $subClass3->createSubClass('test4','test4 Comment');
		
		$subClassesArray = $subClass0->getSubClasses();
		foreach ( $subClassesArray as $subClass) {
			$this->assertTrue($subClass->isSubClassOf($subClass0));
		}
		
		$subClassesArray2 = $subClass0->getSubClasses(true);
		foreach ( $subClassesArray2 as $subClass) {
			if($subClass->getLabel() == 'test1'){
				$this->assertTrue($subClass->isSubClassOf($subClass0));
			}
			if($subClass->getLabel() == 'test2'){
				$this->assertTrue($subClass->isSubClassOf($subClass0));
			}
			if($subClass->getLabel() == 'test3'){
				$this->assertTrue($subClass->isSubClassOf($subClass2));
			}
			if($subClass->getLabel() == 'test4'){
				$this->assertTrue($subClass->isSubClassOf($subClass3));
				$this->assertTrue($subClass->isSubClassOf($subClass2));
				$this->assertFalse($subClass->isSubClassOf($subClass1));
			}
			
		}

		$subClass0->delete();
		$subClass1->delete();
		$subClass2->delete();
		$subClass3->delete();
		$subClass4->delete();
	}

	public function testGetParentClasses(){
		$class = new core_kernel_classes_Class(GENERIS_BOOLEAN);
		$indirectParentClasses = $class->getParentClasses(true);

		$this->assertTrue(count($indirectParentClasses) == 2);
		$expectedResult = array (CLASS_GENERIS_RESOURCE , RDF_RESOURCE);
		foreach ($indirectParentClasses  as $parentClass) {
			$this->assertIsA($parentClass,'core_kernel_classes_Class');	
			$this->assertTrue(in_array($parentClass->uriResource,$expectedResult));
		}
		
		$directParentClass = $class->getParentClasses(); 
		$this->assertTrue(count($directParentClass) == 1);
		foreach ($directParentClass  as $parentClass) {
			$this->assertIsA($parentClass,'core_kernel_classes_Class');	
			$this->assertEqual($parentClass->uriResource, CLASS_GENERIS_RESOURCE); 
		}

	}
	

	

	public function testGetProperties(){
		$list = new core_kernel_classes_Class(RDF_LIST);
		$properties = $list->getProperties();
		$this->assertTrue(count($properties) == 2);
		$expectedResult = array (	RDF_FIRST, RDF_REST);
	
		foreach ($properties as $property) {
			
			$this->assertTrue($property instanceof core_kernel_classes_Property);
			$this->assertTrue(in_array($property->uriResource,$expectedResult));
			if ($property->uriResource === RDF_FIRST) {
				$this->assertEqual($property->getRange()->uriResource, RDF_RESOURCE);
				$this->assertEqual($property->getLabel(),'first');
				$this->assertEqual($property->getComment(),'The first item in the subject RDF list.');		
			}
			if ($property->uriResource === RDF_REST) {
				$this->assertEqual($property->getRange()->uriResource, RDF_LIST);
				$this->assertEqual($property->getLabel(),'rest');
				$this->assertEqual($property->getComment(),'The rest of the subject RDF list after the first item.');		
			}
		}
		
		
		$class = $list->createSubClass('toto','toto');
		$properties2 = $class->getProperties(true);
		$this->assertFalse(empty($properties2));
		
		$class->delete();
	}

	

	
 	public function testGetInstances(){
 		$class = new core_kernel_classes_Class(CLASS_WIDGET);
 		$plop = $class->createInstance('test','comment');
 		$instances = $class->getInstances();
		$subclass = $class->createSubClass('subTest Class', 'subTest Class');
		$subclassInstance = $subclass->createInstance('test3','comment3');
		

 		$this->assertTrue(count($instances)  > 0);

 		foreach ($instances as $k=>$instance) {
 			$this->assertTrue($instance instanceof core_kernel_classes_Resource );
 						
 			if ($instance->uriResource === WIDGET_COMBO) {
 				$this->assertEqual($instance->getLabel(),'Drop down menu' );
 				$this->assertEqual($instance->getComment(),'In drop down menu, one may select 1 to N options' );
 			}
 		 	if ($instance->uriResource === WIDGET_RADIO) {
 				$this->assertEqual($instance->getLabel(),'Radio button' );
 				$this->assertEqual($instance->getComment(),'In radio boxes, one may select exactly one option' );
 			}
 		 	if ($instance->uriResource === WIDGET_CHECK) {
 				$this->assertEqual($instance->getLabel(),'Check box' );
 				$this->assertEqual($instance->getComment(),'In check boxes, one may select 0 to N options' );
 			}
 		  	if ($instance->uriResource === WIDGET_FTE) {
 				$this->assertEqual($instance->getLabel(),'A Text Box' );
 				$this->assertEqual($instance->getComment(),'A particular text box' );
 			}
 			if ($instance->uriResource === $subclassInstance->uriResource){
 				$this->assertEqual($instance->getLabel(),'test3' );
 				$this->assertEqual($instance->getComment(),'comment3' );
 			}			
 		}
 		
 		$instances2 = $class->getInstances(true);
 		$this->assertTrue(count($instances2)  > 0);
 		foreach ($instances2 as $k=>$instance) {
 			$this->assertTrue($instance instanceof core_kernel_classes_Resource );		
 			if ($instance->uriResource === WIDGET_COMBO) {
 				$this->assertEqual($instance->getLabel(),'Drop down menu' );
 				$this->assertEqual($instance->getComment(),'In drop down menu, one may select 1 to N options' );
 			}
 		 	if ($instance->uriResource === WIDGET_RADIO) {
 				$this->assertEqual($instance->getLabel(),'Radio button' );
 				$this->assertEqual($instance->getComment(),'In radio boxes, one may select exactly one option' );
 			}
 		 	if ($instance->uriResource === WIDGET_CHECK) {
 				$this->assertEqual($instance->getLabel(),'Check box' );
 				$this->assertEqual($instance->getComment(),'In check boxes, one may select 0 to N options' );
 			}
 		  	if ($instance->uriResource === WIDGET_FTE) {
 				$this->assertEqual($instance->getLabel(),'A Text Box' );
 				$this->assertEqual($instance->getComment(),'A particular text box' );
 			}
 			if ($instance->uriResource === $plop->uriResource){
 				$this->assertEqual($instance->getLabel(),'test' );
 				$this->assertEqual($instance->getComment(),'comment' );
 			}	
			if ($instance->uriResource === $plop->uriResource){
 				$this->assertEqual($instance->getLabel(),'test' );
 				$this->assertEqual($instance->getComment(),'comment' );
 			}	
 			
 		}
 		
 		$plop->delete();
 		$subclass->delete();
 		$subclassInstance->delete();
 	}
 	
 	
 	
	public function testIsSubClassOf(){
		$class = new core_kernel_classes_Class(GENERIS_BOOLEAN);
		$subClass = $class->createSubClass('test', 'test'); 
		$this->assertTrue($class->isSubClassOf(new core_kernel_classes_Class(CLASS_GENERIS_RESOURCE)));
		$this->assertTrue($subClass->isSubClassOf($class) );
		$this->assertFalse($subClass->isSubClassOf($subClass) );
		$this->assertTrue($subClass->isSubClassOf(new core_kernel_classes_Class(CLASS_GENERIS_RESOURCE)));
		$subClass->delete();

	}
	

	
	public function testSetSubClasseOf(){
		$class = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/generis.rdf#Boolean');
		$subClass = $class->createSubClass('test', 'test'); 
		$subClass1 = $subClass->createSubClass('subclass of test', 'subclass of test'); 
		$subClass2 = $subClass->createSubClass('subclass of test2', 'subclass of test2'); 
		
		$this->assertTrue($subClass->isSubClassOf($class) );
		$this->assertTrue($subClass1->isSubClassOf($class) );
		$this->assertTrue($subClass2->isSubClassOf($class) );
		
		$this->assertFalse($subClass2->isSubClassOf($subClass1) );
		$subClass2->setSubClassOf($subClass1);
		$this->assertTrue($subClass2->isSubClassOf($subClass1) );

		
		$subClass->delete();
		$subClass1->delete();
		$subClass2->delete();

	}

	public function testCreateInstance(){
		$class = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/generis.rdf#Boolean');
		$instance = $class->createInstance('toto' , 'tata');
		$this->assertEqual($instance->getLabel(), 'toto');
		$this->assertEqual($instance->getComment(), 'tata');
		$instance2 = $class->createInstance('toto' , 'tata');
		$this->assertNotIdentical($instance,$instance2);
		$instance->delete();
		$instance2->delete();
	}
	
	public function testCreateSubClass(){
		$class = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/generis.rdf#Boolean');
		$subClass = $class->createSubClass('toto' , 'tata');
		$this->assertNotEqual($class,$subClass);
		$this->assertEqual($subClass->getLabel(),'toto');
		$this->assertEqual($subClass->getComment(), 'tata');
		$subClassOfProperty = new core_kernel_classes_Property('http://www.w3.org/2000/01/rdf-schema#subClassOf');
		$subClassOfPropertyValue = $subClass->getPropertyValues($subClassOfProperty);
		$this->assertTrue(in_array($class->uriResource, array_values($subClassOfPropertyValue))); 
		$subClass->delete();
	}

	public function testCreateProperty(){
		$class = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/generis.rdf#Boolean');

		$property = $class->createProperty('tata','toto');
		$property2 = $class->createProperty('tata2','toto2',true);
		$this->assertTrue($property->getLabel() == 'tata');

		$this->assertTrue($property->getComment() == 'toto');
		$this->assertTrue($property2->isLgDependent());
		$this->assertTrue($property->getDomain()->get(0)->uriResource ==$class->uriResource );
		$property->delete();
		$property2->delete();
	}
	
    public function testSearchInstances() {

        $propertyClass = new core_kernel_classes_Class(RDF_PROPERTY);

        $propertyFilter = array(
            PROPERTY_IS_LG_DEPENDENT => GENERIS_TRUE
        );
        $options = array('like' => false, 'recursive' => 0);
        $languagesDependantProp = $propertyClass->searchInstances($propertyFilter, $options);

        $found = count($languagesDependantProp);
        $this->assertTrue($found > 0);

        $propertyFilter = array(
            PROPERTY_IS_LG_DEPENDENT => GENERIS_TRUE,
            RDF_TYPE => RDF_PROPERTY
        );
        $languagesDependantProp = $propertyClass->searchInstances($propertyFilter, $options);
        $nfound = count($languagesDependantProp);
        $this->assertTrue($nfound > 0);
        $this->assertEqual($found, $nfound);

        $userClass = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/generis.rdf#User');
        $user1 = $userClass->createInstance('user1');
        $user1->setPropertyValue(new core_kernel_classes_Property('prop1'), 'toto');
        $user1->setPropertyValue(new core_kernel_classes_Property('prop2'), 'titi');
        $user2 = $userClass->createInstance('user2');
        $user2->setPropertyValue(new core_kernel_classes_Property('prop1'), 'toto');
        $user2->setPropertyValue(new core_kernel_classes_Property('prop1'), 'titi');
        $user3 = $userClass->createInstance('user3');
        $user3->setPropertyValue(new core_kernel_classes_Property('prop1'), 'toto');
        $user3->setPropertyValue(new core_kernel_classes_Property('prop1'), 'titi');

        $propertyFilter = array(
            'prop1' => 'toto'
        );
        $options = array('like' => false, 'recursive' => 0, 'offset' => 0, 'limit' => 2); //User 2 & 3
        $languagesDependantProp = $userClass->searchInstances($propertyFilter, $options);
        $nfound = count($languagesDependantProp);
        $this->assertEqual($nfound, 2);

        $user1->delete();
        $user2->delete();
        $user3->delete();
    }
    
    //Test search instances with a model shared between smooth and hard implentation
    public function testSearchInstancesMultipleImpl()
    {
        $clazz = new core_kernel_classes_Class(RDF_CLASS);
        $sub1Clazz = $clazz->createSubClass();
        $sub1ClazzInstance = $sub1Clazz->createInstance('test case instance');
        $sub2Clazz = $sub1Clazz->createSubClass();
        $sub2ClazzInstance = $sub2Clazz->createInstance('test case instance');
        $sub3Clazz = $sub2Clazz->createSubClass();
        $sub3ClazzInstance = $sub3Clazz->createInstance('test case instance');
        
        $options = array(
            'recursive'				=> true,
            'append'				=> true,
            'createForeigns'		=> true,
            'referencesAllTypes'	=> true,
            'rmSources'				=> true
        );
        
        //Test the search instances on the smooth impl
        $propertyFilter = array(
            RDFS_LABEL => 'test case instance'
        );
        $instances = $clazz->searchInstances($propertyFilter, array('recursive'=>true));
        $this->assertTrue(array_key_exists($sub1ClazzInstance->uriResource, $instances));
        $this->assertTrue(array_key_exists($sub2ClazzInstance->uriResource, $instances));
        $this->assertTrue(array_key_exists($sub3ClazzInstance->uriResource, $instances));
        
        common_Logger::d('starting hardify');
        //Test the search instances on the hard impl
        $switcher = new core_kernel_persistence_Switcher();
        $switcher->hardify($sub1Clazz, $options);
        unset ($switcher); //Required to update cache data
        common_Logger::d('done hardify');
        
        $propertyFilter = array(
            RDFS_LABEL => 'test case instance'
        );
        $instances = $sub1Clazz->searchInstances($propertyFilter, array('recursive'=>true));
        $this->assertTrue(array_key_exists($sub1ClazzInstance->uriResource, $instances));
        $this->assertTrue(array_key_exists($sub2ClazzInstance->uriResource, $instances));
        $this->assertTrue(array_key_exists($sub3ClazzInstance->uriResource, $instances));
        
        $switcher = new core_kernel_persistence_Switcher();
        $switcher->unhardify($sub1Clazz, $options);
        unset ($switcher); //Required to update cache data
        //Test the search instances on a shared model (smooth + hard)
        //Disable recursivity on hardify, and hardify the sub2Clazz
        
        $switcher = new core_kernel_persistence_Switcher();
        $options['recursive'] = false;
        $switcher->hardify($sub2Clazz, $options);
        unset ($switcher); //Required to update cache data
        
        $instances = $sub1Clazz->searchInstances($propertyFilter, array('recursive'=>true));
        $this->assertTrue(array_key_exists($sub1ClazzInstance->uriResource, $instances));
        $this->assertTrue(array_key_exists($sub2ClazzInstance->uriResource, $instances));
        $this->assertTrue(array_key_exists($sub3ClazzInstance->uriResource, $instances));
        
        try{
            $instances = $sub1Clazz->searchInstances($propertyFilter, array('recursive'=>true, 'offset'=>1));
            $this->assertTrue(false);
        }catch(common_Exception $e){
            $this->assertTrue(true);
        }
        
        $switcher = new core_kernel_persistence_Switcher();
        $switcher->unhardify($sub2Clazz, $options);
        unset ($switcher); //Required to update cache data

        //clean data test
        foreach($sub1Clazz->getInstances(true) as $instance){ $instance->delete(); }
        $sub1Clazz->delete(true);
        $sub2Clazz->delete(true);
        $sub3Clazz->delete(true);
    }
    
	
	public function testSearchInstancesHard($hard = true){
		
		if(!$hard) return;
		
		core_kernel_persistence_PersistenceProxy::forceMode(PERSISTENCE_HARD);
		
		$class = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAO.rdf#Languages');
		if(core_kernel_persistence_hardapi_ResourceReferencer::singleton()->isClassReferenced($class)){
		
			$propertyFilter = array(
				RDFS_LABEL => 'English'
			);
			$options = array('like' => false, 'recursive' => 0);
					
			$languagesDependantProp = $class->searchInstances($propertyFilter, $options);
			
			$found = count($languagesDependantProp);
			$this->assertTrue($found > 0);
			
			$propertyFilter = array(
				RDF_VALUE => 'EN',
				RDF_TYPE	=> 'http://www.tao.lu/Ontologies/TAO.rdf#Languages'
			);
			$languagesDependantProp = $class->searchInstances($propertyFilter, $options);
			$nfound = count($languagesDependantProp);
			$this->assertTrue($nfound > 0);
			$this->assertEqual($found, $nfound);
			
		}
		
		core_kernel_persistence_PersistenceProxy::restoreImplementation();
		
	}
	
	public function testSearchInstancesVeryHard($hard=true){
		
		if(!$hard) return;
		
		$class = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject');
		if(core_kernel_persistence_hardapi_ResourceReferencer::singleton()->isClassReferenced($class)){
			
			//test simple search:
			$propertyFilter = array(
				'http://www.tao.lu/Ontologies/generis.rdf#login' => 's1',
				'http://www.tao.lu/Ontologies/generis.rdf#password'	=> 'e10adc3949ba59abbe56e057f20f883e'
			);
			$options = array('like' => false, 'recursive' => 0);
			$languagesDependantProp = $class->searchInstances($propertyFilter, $options);
			$nfound = count($languagesDependantProp);
			$this->assertTrue($nfound > 0);
			
			//test like option
			$propertyFilter = array(
				'http://www.tao.lu/Ontologies/generis.rdf#login' => '%s1',
				'http://www.tao.lu/Ontologies/generis.rdf#password'	=> 'e10adc3949ba59abbe56e057f20f883e'
			);
			$options = array('like' => true, 'recursive' => 0);
			$languagesDependantProp = $class->searchInstances($propertyFilter, $options);
			$likeFound = count($languagesDependantProp);
			$this->assertTrue($likeFound > 0);
			$this->assertEqual($nfound, $likeFound);
			
			//test reference resource prop value:
			$propertyFilter = array(
				'http://www.tao.lu/Ontologies/generis.rdf#login' => '%s1',
				'http://www.tao.lu/Ontologies/generis.rdf#password'	=> 'e10adc3949ba59abbe56e057f20f883e',
				'http://www.tao.lu/Ontologies/generis.rdf#userDefLg' => '%FR%'
			);
			
			$options = array('like' => true, 'recursive' => false);
			//test language filter (property 'http://www.tao.lu/Ontologies/generis.rdf#userDefLg' must be set to language dependent first!):
			// $options['lang'] = 'EN';
			
			$languagesDependantProp = $class->searchInstances($propertyFilter, $options);
			$refFound = count($languagesDependantProp);
			$this->assertTrue($refFound > 0);
			$this->assertEqual($nfound, $refFound);
		}
	}
	
	//Test the function getInstancesPropertyValues of the class Class with literal properties
	public function testGetInstancesPropertyValuesWithLiteralProperties () {
		// create a class
		$class = new core_kernel_classes_Class(CLASS_GENERIS_RESOURCE);
		$subClass = $class->createSubClass('GetInstancesPropertyValuesClass', 'GetInstancesPropertyValues_Class');
		// create a first property for this class
		$p1 = core_kernel_classes_ClassFactory::createProperty($subClass, 'GetInstancesPropertyValues_Property1', 'GetInstancesPropertyValues_Property1', false, LOCAL_NAMESPACE. "#P1");
		$p1->setRange(new core_kernel_classes_Class(RDFS_LITERAL));
		// create a second property for this class
		$p2 = core_kernel_classes_ClassFactory::createProperty($subClass, 'GetInstancesPropertyValues_Property2', 'GetInstancesPropertyValues_Property2', false, LOCAL_NAMESPACE."#P2");
		$p2->setRange(new core_kernel_classes_Class(RDFS_LITERAL));
		// create a second property for this class
		$p3 = core_kernel_classes_ClassFactory::createProperty($subClass, 'GetInstancesPropertyValues_Property3', 'GetInstancesPropertyValues_Property3', false, LOCAL_NAMESPACE."#P3");
		$p2->setRange(new core_kernel_classes_Class(RDFS_LITERAL));
		// $i1
		$i1 = $subClass->createInstance("i1", "i1");
		$i1->setPropertyValue($p1, "p11 litteral");
		$i1->setPropertyValue($p2, "p21 litteral");
		$i1->setPropertyValue($p3, "p31 litteral");
		$i1->getLabel();
		// $i2
		$i2 = $subClass->createInstance("i2", "i2");
		$i2->setPropertyValue($p1, "p11 litteral");
		$i2->setPropertyValue($p2, "p22 litteral");
		$i2->setPropertyValue($p3, "p31 litteral");
		$i2->getLabel();
		
		// Search * P1 for P1=P11 litteral
		// Expected 2 results, but 1 possibility
		$propertyFilters = array (
			LOCAL_NAMESPACE. "#P1" => "p11 litteral"
		);
		$result = $subClass->getInstancesPropertyValues($p1, $propertyFilters);
		$this->assertEqual(count($result), 2);
		$this->assertTrue (in_array("p11 litteral", $result));
		
		// Search * P1 for P1=P11 litteral WITH DISTINCT options
		// Expected 1 results, and 1 possibility
		$propertyFilters = array (
			LOCAL_NAMESPACE. "#P1" => "p11 litteral"
		);
		$result = $subClass->getInstancesPropertyValues($p1, $propertyFilters, array ("distinct" => true));
		$this->assertEqual(count($result), 1);
		$this->assertTrue (in_array("p11 litteral", $result));
		
		// Search * P2 for P1=P11 litteral WITH DISTINCT options
		// Expected 2 results, and 2 possibilities
		$propertyFilters = array (
			LOCAL_NAMESPACE. "#P1" => "p11 litteral"
		);
		$result = $subClass->getInstancesPropertyValues($p2, $propertyFilters, array ("distinct" => true));
		$this->assertEqual(count($result), 2);
		$this->assertTrue (in_array("p21 litteral", $result));
		$this->assertTrue (in_array("p22 litteral", $result));
		
		// Search * P2 for P1=P12 litteral WITH DISTINCT options
		// Expected 0 results, and 0 possibilities
		$propertyFilters = array (
			LOCAL_NAMESPACE. "#P1" => "p12 litteral"
		);
		$result = $subClass->getInstancesPropertyValues($p2, $propertyFilters, array ("distinct" => true));
		$this->assertEqual(count($result), 0);
		
		// Search * P1 for P2=P21 litteral WITH DISTINCT options
		// Expected 1 results, and 1 possibilities
		$propertyFilters = array (
			LOCAL_NAMESPACE. "#P2" => "p21 litteral"
		);
		$result = $subClass->getInstancesPropertyValues($p1, $propertyFilters, array ("distinct" => true));
		$this->assertEqual(count($result), 1);
		$this->assertTrue (in_array("p11 litteral", $result));
		
		// Search * P1 for P2=P22 litteral WITH DISTINCT options
		// Expected 1 results, and 1 possibilities
		$propertyFilters = array (
			LOCAL_NAMESPACE. "#P2" => "p22 litteral"
		);
		$result = $subClass->getInstancesPropertyValues($p1, $propertyFilters, array ("distinct" => true));
		$this->assertEqual(count($result), 1);
		$this->assertTrue (in_array("p11 litteral", $result));
		
		// Search * P3 for P1=P11 & P2=P21 litteral WITH DISTINCT options
		// Expected 1 results, and 1 possibilities
		$propertyFilters = array (
			LOCAL_NAMESPACE. "#P1" => "p11 litteral"
			, LOCAL_NAMESPACE. "#P2" => "p21 litteral"
		);
		$result = $subClass->getInstancesPropertyValues($p3, $propertyFilters, array ("distinct" => true));
		$this->assertEqual(count($result), 1);
		$this->assertTrue (in_array("p31 litteral", $result));
		
		// Search * P2 for P1=P11 & P3=P31 litteral WITH DISTINCT options
		// Expected 2 results, and 2 possibilities
		$propertyFilters = array (
			LOCAL_NAMESPACE. "#P1" => "p11 litteral"
			, LOCAL_NAMESPACE. "#P3" => "p31 litteral"
		);
		$result = $subClass->getInstancesPropertyValues($p2, $propertyFilters, array ("distinct" => true));
		$this->assertEqual(count($result), 2);
		$this->assertTrue (in_array("p21 litteral", $result));
		$this->assertTrue (in_array("p22 litteral", $result));
		
		// Clean the model		
		$i1->delete();
		$i2->delete();
		
		$p1->delete();
		$p2->delete();
		$p3->delete();
		
		$subClass->delete();
	}
	
	//Test the function getInstancesPropertyValues of the class Class  with resource properties
	public function testGetInstancesPropertyValuesWithResourceProperties () {
		// create a class
		$class = new core_kernel_classes_Class(CLASS_GENERIS_RESOURCE);
		$subClass = $class->createSubClass('GetInstancesPropertyValuesClass', 'GetInstancesPropertyValues_Class');
		// create a first property for this class
		$p1 = core_kernel_classes_ClassFactory::createProperty($subClass, 'GetInstancesPropertyValues_Property1', 'GetInstancesPropertyValues_Property1', false, LOCAL_NAMESPACE. "#P1");
		$p1->setRange(new core_kernel_classes_Class(GENERIS_BOOLEAN));
		// create a second property for this class
		$p2 = core_kernel_classes_ClassFactory::createProperty($subClass, 'GetInstancesPropertyValues_Property2', 'GetInstancesPropertyValues_Property2', false, LOCAL_NAMESPACE. "#P2");
		$p1->setRange(new core_kernel_classes_Class(GENERIS_BOOLEAN));
		// create a second property for this class
		$p3 = core_kernel_classes_ClassFactory::createProperty($subClass, 'GetInstancesPropertyValues_Property3', 'GetInstancesPropertyValues_Property3', false, LOCAL_NAMESPACE. "#P3");
		$p1->setRange(new core_kernel_classes_Class(RDFS_LITERAL));
		// $i1
		$i1 = $subClass->createInstance("i1", "i1");
		$i1->setPropertyValue($p1, GENERIS_TRUE);
		$i1->setPropertyValue($p2, new core_kernel_classes_Class(GENERIS_TRUE));
		$i1->setPropertyValue($p3, "p31 litteral");
		$i1->getLabel();
		// $i2
		$i2 = $subClass->createInstance("i2", "i2");
		$i2->setPropertyValue($p1, GENERIS_TRUE);
		$i2->setPropertyValue($p2, new core_kernel_classes_Class(GENERIS_FALSE));
		$i2->setPropertyValue($p3, "p31 litteral");
		$i2->getLabel();
		
		// Search * P1 for P1=GENERIS_TRUE
		// Expected 2 results, but 1 possibility
		$propertyFilters = array (
			LOCAL_NAMESPACE. "#P1" => GENERIS_TRUE
		);
		$result = $subClass->getInstancesPropertyValues($p1, $propertyFilters);
		$this->assertEqual(count($result), 2);
		foreach ($result as $property) {
			$this->assertTrue($property->uriResource == GENERIS_TRUE);
		}
		// Search * P1 for P1=GENERIS_TRUE WITH DISTINCT options
		// Expected 1 results, and 1 possibility
		$propertyFilters = array (
			LOCAL_NAMESPACE. "#P1" => GENERIS_TRUE
		);
		$result = $subClass->getInstancesPropertyValues($p1, $propertyFilters, array ("distinct" => true));
		$this->assertEqual(count($result), 1);
		$this->assertTrue($result[0]->uriResource == GENERIS_TRUE);
		
		// Search * P2 for P1=GENERIS_TRUE WITH DISTINCT options
		// Expected 2 results, and 2 possibilities
		$propertyFilters = array (
			LOCAL_NAMESPACE. "#P1" => GENERIS_TRUE
		);
		$result = $subClass->getInstancesPropertyValues($p2, $propertyFilters, array ("distinct" => true));
		$this->assertEqual(count($result), 2);
		foreach ($result as $property){
			$this->assertTrue ($property->uriResource == GENERIS_TRUE || $property->uriResource == GENERIS_FALSE);
		}
		
		// Search * P2 for P1=NotExistingProperty litteral WITH DISTINCT options
		// Expected 1 results, and 1 possibilities
		$propertyFilters = array (
			LOCAL_NAMESPACE. "#P1" => "NotExistingProperty"
		);
		$result = $subClass->getInstancesPropertyValues($p2, $propertyFilters, array ("distinct" => true));
		$this->assertEqual(count($result), 0);
		
		// Search * P1 for P2=GENERIS_TRUE litteral WITH DISTINCT options
		// Expected 1 results, and 1 possibilities
		$propertyFilters = array (
			LOCAL_NAMESPACE. "#P2" => GENERIS_TRUE
		);
		$result = $subClass->getInstancesPropertyValues($p1, $propertyFilters, array ("distinct" => true));
		$this->assertEqual(count($result), 1);
		$this->assertTrue($result[0]->uriResource == GENERIS_TRUE);
		
		// Search * P1 for P2=GENERIS_FALSE WITH DISTINCT options
		// Expected 1 results, and 1 possibilities
		$propertyFilters = array (
			LOCAL_NAMESPACE. "#P2" => GENERIS_FALSE
		);
		$result = $subClass->getInstancesPropertyValues($p1, $propertyFilters, array ("distinct" => true));
		$this->assertEqual(count($result), 1);
		$this->assertTrue($result[0]->uriResource == GENERIS_TRUE);
		
		// Search * P3 for P1=GENERIS_TRUE & P2=GENERIS_TRUE litteral WITH DISTINCT options
		// Expected 1 results, and 1 possibilities
		$propertyFilters = array (
			LOCAL_NAMESPACE. "#P1" => GENERIS_TRUE
			, LOCAL_NAMESPACE. "#P2" => GENERIS_TRUE
		);
		$result = $subClass->getInstancesPropertyValues($p3, $propertyFilters, array ("distinct" => true));
		$this->assertEqual(count($result), 1);
		$this->assertTrue (in_array("p31 litteral", $result));

		// Search * P2 for P1=P11 & P3=P31 litteral WITH DISTINCT options
		// Expected 2 results, and 2 possibilities
		$propertyFilters = array (
			LOCAL_NAMESPACE. "#P1" => GENERIS_TRUE
			, LOCAL_NAMESPACE. "#P3" => "p31 litteral"
		);
		$result = $subClass->getInstancesPropertyValues($p2, $propertyFilters, array ("distinct" => true));
		$this->assertEqual(count($result), 2);
		foreach ($result as $property){
			$this->assertTrue ($property->uriResource == GENERIS_TRUE || $property->uriResource == GENERIS_FALSE);
		}
		
		// Clean the model		
		$i1->delete();
		$i2->delete();
		
		$p1->delete();
		$p2->delete();
		$p3->delete();
		
		$subClass->delete();
	}
	
}
?>