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
require_once dirname(__FILE__) . '/TaoTestRunner.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 * Test the user management 
 * 
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package tao
 * @subpackage test
 */
class UserTestCase extends UnitTestCase {
	
	/**
	 * @var tao_models_classes_UserService
	 */
	protected $userService = null;
	
	/**
	 * @var array user data set
	 */
	protected $testUserData = array(
		PROPERTY_USER_LOGIN		=> 	'tjdoe',
		PROPERTY_USER_PASSWORD	=>	'test123',
		PROPERTY_USER_LASTNAME	=>	'Doe',
		PROPERTY_USER_FIRSTNAME	=>	'John',
		PROPERTY_USER_MAIL		=>	'jdoe@tao.lu',
		PROPERTY_USER_DEFLG		=>	'EN',
		PROPERTY_USER_UILG		=>	'EN',
		PROPERTY_USER_ROLES		=>  INSTANCE_ROLE_TAOMANAGER
	);
	
	/**
	 * @var array user data set with special chars
	 */
	protected $testUserUtf8Data = array(
		PROPERTY_USER_LOGIN		=> 	'f.lecé',
		PROPERTY_USER_PASSWORD	=>	'6crète!',
		PROPERTY_USER_LASTNAME	=>	'Lecéfranc',
		PROPERTY_USER_FIRSTNAME	=>	'François',
		PROPERTY_USER_MAIL		=>	'f.lecé@tao.lu',
		PROPERTY_USER_DEFLG		=>	'EN',
		PROPERTY_USER_UILG		=>	'FR',
		PROPERTY_USER_ROLES		=>  INSTANCE_ROLE_TAOMANAGER
	);
	
	/**
	 * @var core_kernel_classes_Resource
	 */
	protected $testUser = null;
	
	/**
	 * @var core_kernel_classes_Resource
	 */
	protected $testUserUtf8 = null;
	
	/**
	 * tests initialization
	 */
	public function setUp(){		
		TaoTestRunner::initTest();
		
		$this->testUserData[PROPERTY_USER_PASSWORD] = md5($this->testUserData[PROPERTY_USER_PASSWORD]);
		$this->testUserUtf8Data[PROPERTY_USER_PASSWORD] = md5($this->testUserUtf8Data[PROPERTY_USER_PASSWORD]);
	}
	
	/**
	 * Test the user service implementation
	 * @see tao_models_classes_ServiceFactory::get
	 */
	public function testService(){
		$userService = tao_models_classes_UserService::singleton();
		$this->assertIsA($userService, 'tao_models_classes_Service');
		$this->assertIsA($userService, 'tao_models_classes_UserService');
		
		$this->userService = $userService;
	}

	/**
	 * Test user insertion
	 * @see tao_models_classes_UserService::saveUser
	 */
	public function testAddUser(){

		//insert it
		$this->assertTrue($this->userService->loginAvailable($this->testUserData[PROPERTY_USER_LOGIN]));
		$tmclass = new core_kernel_classes_Class(CLASS_GENERIS_USER);
		$this->testUser = $tmclass->createInstance();
		$this->assertNotNull($this->testUser);
		$this->assertTrue($this->testUser->exists());
		$this->assertTrue($this->userService->bindProperties($this->testUser, $this->testUserData));
		$this->assertFalse($this->userService->loginAvailable($this->testUserData[PROPERTY_USER_LOGIN]));
		
		//check inserted data
		$this->testUser = $this->userService->getOneUser($this->testUserData[PROPERTY_USER_LOGIN]);
		$this->assertIsA($this->testUser, 'core_kernel_classes_Resource');
		foreach($this->testUserData as $prop => $value){
			try{
				$p = new core_kernel_classes_Property($prop);
				$v = $this->testUser->getUniquePropertyValue($p);
				$v = ($v instanceof core_kernel_classes_Literal) ? $v->literal : $v->getUri();
				$this->assertEqual($value, $v);
			}
			catch(common_Exception $ce){ 
				$this->fail($ce);
			}
		}
	}
	
	/**
	 * Test user insertion with special chars
	 * @see tao_models_classes_UserService::saveUser
	 */
	public function testAddUtf8User(){
		
		$this->assertTrue($this->userService->loginAvailable($this->testUserUtf8Data[PROPERTY_USER_LOGIN]));
		$tmclass = new core_kernel_classes_Class(CLASS_GENERIS_USER);
		$this->testUserUtf8 = $tmclass->createInstance();
		$this->assertNotNull($this->testUserUtf8);
		$this->assertTrue($this->testUserUtf8->exists());
		$this->assertTrue($this->userService->bindProperties($this->testUserUtf8, $this->testUserUtf8Data) );
		$this->assertFalse($this->userService->loginAvailable($this->testUserUtf8Data[PROPERTY_USER_LOGIN]));
		
		//check inserted data
		$this->testUserUtf8 = $this->userService->getOneUser($this->testUserUtf8Data[PROPERTY_USER_LOGIN]);
		$this->assertIsA($this->testUserUtf8, 'core_kernel_classes_Resource');
		foreach($this->testUserUtf8Data as $prop => $value){
			try{
				$p = new core_kernel_classes_Property($prop);
				$v = $this->testUserUtf8->getUniquePropertyValue($p);
				$v = ($v instanceof core_kernel_classes_Literal) ? $v->literal : $v->getUri();
				$this->assertEqual($value, $v);
			}
			catch(common_Exception $ce){ 
				$this->fail($ce);
			}
		}
	}
	
	/**
	 * Test user removing
	 * @see tao_models_classes_UserService::removeUser
	 */
	public function testDelete(){
		$this->testUser = $this->userService->getOneUser($this->testUserData[PROPERTY_USER_LOGIN]);
		$this->assertIsA($this->testUser, 'core_kernel_classes_Resource');
		$this->assertTrue($this->userService->removeUser($this->testUser));
		$this->assertTrue($this->userService->loginAvailable($this->testUserData[PROPERTY_USER_LOGIN]));
		
		
		$this->testUserUtf8 = $this->userService->getOneUser($this->testUserUtf8Data[PROPERTY_USER_LOGIN]);
		$this->assertIsA($this->testUserUtf8, 'core_kernel_classes_Resource');
		$this->assertTrue($this->userService->removeUser($this->testUserUtf8));
		$this->assertTrue($this->userService->loginAvailable($this->testUserUtf8Data[PROPERTY_USER_LOGIN]));
	}
}
?>