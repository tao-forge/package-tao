<?php
/**  
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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

require_once dirname(__FILE__) . '/../../tao/test/TaoTestRunner.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoLti
 * @subpackage test
 */
class OauthTestCase extends UnitTestCase {
	
	private $oauthCustomer; 

	/**
	 * tests initialization
	 */
	public function setUp() {
		TaoTestRunner::initTest();
		$oauthClass = new core_kernel_classes_Class(CLASS_OAUTH_CONSUMER);
		$this->oauthCustomer = $oauthClass->createInstanceWithProperties(array(
		    PROPERTY_OAUTH_KEY			    => 'test_key',
		    PROPERTY_OAUTH_SECRET             => md5(rand()),
		));
	}
	
	public function tearDown() {
	   $this->oauthCustomer->delete();
	}

	public function testSignature() {
		$request = tao_models_classes_oauth_Request::createSigned($this->oauthCustomer, 'http://example.com/oauthtest');
		$this->assertTrue($request->isValid());
	}

}