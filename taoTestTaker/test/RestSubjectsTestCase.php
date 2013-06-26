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
 */
?>
<?php
require_once dirname(__FILE__) . '/../../tao/test/TaoTestRunner.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 * connects as a client agent on the rest controller
 * @author patrick
 * @package taoSubjects
 * @subpackage test
 */
class RestSubjectsTestCase extends UnitTestCase {
	

	
	private $host = ROOT_URL;
	private $userUri = "";
	private $login = "";
	private $password = "";
	/**
	 * tests initialization
	 */
	public function setUp(){		
		    TaoTestRunner::initTest();
		    //creates a user using remote script from joel
		    $process = curl_init($this->host.'/tao/test/connector/setUp.php');
		    curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
		    $returnedData = curl_exec($process);
		    $data = json_decode($returnedData, true);
		    $this->assertNotNull($data);
		    $this->login = $data['userData'][PROPERTY_USER_LOGIN];
		    $this->password = $data['userData'][PROPERTY_USER_PASSWORD];
		    $this->userUri			= $data['userUri'];
	}
	public function tearDown(){
	    //removes the created user
		    $process = curl_init(ROOT_URL.'tao/test/connector/tearDown.php');
		    curl_setopt($process, CURLOPT_POSTFIELDS, array('uri' => $this->userUri));
		    curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
		     $data = curl_exec($process);
	}
	/**
	 * shall be used beyond high level http connections unit tests (default parameters)
	 * @param returnType CURLINFO_HTTP_CODE, etc... (default returns rhe http response data
	 *
	 */
	private function curl($url, $method = CURLOPT_HTTPGET, $returnType = "data", $curlopt_httpheaders = array()){
	     $process = curl_init($url);
	     curl_setopt($process, $method, 1);
	     curl_setopt($process, CURLOPT_USERPWD, $this->login.":".$this->password);
	     curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
	     
	     $headers = array_merge(array (
		 "Accept: application/json"
		 ), $curlopt_httpheaders);
	     curl_setopt($process,CURLOPT_HTTPHEADER, $headers);
	    if ($method==CURLOPT_POST) {
		curl_setopt($process, CURLOPT_POSTFIELDS, "");
		
	    }
	     //curl_setopt($process,CURLOPT_HTTPHEADER,$curlopt_httpheaders);
	     $data = curl_exec($process);
	     if ($returnType != "data"){
		 $data = curl_getinfo($process, $returnType);
	     }
	     curl_close($process);
	     return $data;
	}

	private function checkPropertyValues($propertyValues, $property, $valueType="literal", $value){
	     foreach ($propertyValues as $propertyValue) {
		if ($propertyValue["predicateUri"] == $property){
		    
		    $this->assertEqual($propertyValue["values"][0]["valueType"], $valueType);
		    $this->assertEqual($propertyValue["values"][0]["value"],  $value);
		}
	    }
	}
	public function testHttp(){
	    
	    $url = $this->host.'taoSubjects/RestSubjects';
	    //HTTP Basic
	    $process = curl_init($url);
	     curl_setopt($process,CURLOPT_HTTPHEADER,array (
		 "Accept: application/json"
		 ));

	    //should return a 401
	    curl_setopt($process, CURLOPT_USERPWD, "dummy:dummy");
	    curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
	    $data = curl_exec($process);
	    $http_status = curl_getinfo($process, CURLINFO_HTTP_CODE);
	    $this->assertEqual($http_status, "401");
	    curl_close($process);

	    //should return a 401
	     $process = curl_init($url);
	     curl_setopt($process,CURLOPT_HTTPHEADER,array (
		 "Accept: application/json"
		 ));
	    curl_setopt($process, CURLOPT_USERPWD, $this->login.":dummy");
	    curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
	    $data = curl_exec($process);
	    $http_status = curl_getinfo($process, CURLINFO_HTTP_CODE);
	    $this->assertEqual($http_status, "401");
	    curl_close($process);

	    //should return a 406
	     $process = curl_init($url);
	     curl_setopt($process,CURLOPT_HTTPHEADER,array (
		 "Accept: dummy/dummy"
		 ));
	     curl_setopt($process, CURLOPT_USERPWD, $this->login.":".$this->password);
	     curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
	    $data = curl_exec($process);
	    $http_status = curl_getinfo($process, CURLINFO_HTTP_CODE);
	    $this->assertEqual($http_status, "406");
	    curl_close($process);

	         //should return a 200
	    $process = curl_init($url);
	     curl_setopt($process,CURLOPT_HTTPHEADER,array (
		 "Accept: application/xml"
		 ));
	     curl_setopt($process, CURLOPT_USERPWD, $this->login.":".$this->password);
	     curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
	    $data = curl_exec($process);
	    $http_status = curl_getinfo($process, CURLINFO_HTTP_CODE);
	    $this->assertEqual($http_status, "200");



	      //should return a 200, should return content encoding application/xml
	     $process = curl_init($url);
	     curl_setopt($process,CURLOPT_HTTPHEADER,array (
		 "Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8"
		 ));
	     curl_setopt($process, CURLOPT_USERPWD, $this->login.":".$this->password);
	     curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
	    $data = curl_exec($process);
	    $http_status = curl_getinfo($process, CURLINFO_HTTP_CODE);
	    $this->assertEqual($http_status, "200");
	     $contentType = curl_getinfo($process, CURLINFO_CONTENT_TYPE);
	     $this->assertEqual( $contentType, "application/xml");
	    curl_close($process);

	    //should return a 200
	    $http_status = $this->curl($url, CURLOPT_HTTPGET, CURLINFO_HTTP_CODE);
	    $this->assertEqual($http_status, "200");

	}

	public function testCrud(){

	    //get the complete list (should be empty)
	    $url = $this->host.'taoSubjects/RestSubjects';
	    $returnedData = $this->curl($url);
	    $data = json_decode($returnedData, true);
	    $this->assertEqual( $data["success"], true);

	    //create a new test taker without aprameters, should return a 400
	    $http_status = $this->curl($url, CURLOPT_POST, CURLINFO_HTTP_CODE);
	    $this->assertEqual($http_status, "400");

	    //login but no password, should return a 400
	    $http_status = $this->curl($url, CURLOPT_POST, CURLINFO_HTTP_CODE, array('login: dummy'));
	    $this->assertEqual($http_status, "400");

	    //should be 200
	    $genLogin = 'dummy'.rand(0,65535);
	   $returnedData = $this->curl($url, CURLOPT_POST, "data", array('login: '.$genLogin, 'password: dummy'));
	   $data = json_decode($returnedData, true);
	    $this->assertEqual( $data["success"], true);
	   $uriSubject = $data["data"]["uriResource"];
	    //get this test taker
	     $returnedData = $this->curl($url, CURLOPT_HTTPGET, "data", array('uri: '.$uriSubject));
	    $data = json_decode($returnedData, true);
	    $this->assertEqual( $data["success"], true);
	    $this->assertEqual( $data["data"]["uri"], $uriSubject);

	    $this->checkPropertyValues($data["data"]["properties"], PROPERTY_USER_LOGIN, "literal", $genLogin);
	    $this->checkPropertyValues($data["data"]["properties"], PROPERTY_USER_PASSWORD, "literal", md5('dummy'));

	    //modifying the login of a subject is not allowed : 412
	     $returnedData = $this->curl($url, CURLOPT_PUT, CURLINFO_HTTP_CODE, array('uri: '.$uriSubject, 'login: blabla'));
	     $this->assertEqual( $returnedData, 412);
	    //get all test takers
	     //modifying the login of a subject is not allowed : 412
	     $returnedData = $this->curl($url, CURLOPT_PUT, CURLINFO_HTTP_CODE, array('uri: '.$uriSubject, 'password: blabla'));
	     $this->assertEqual( $returnedData, 200);
	    //edit this test taker
	     $returnedData = $this->curl($url, CURLOPT_PUT, "data", array('uri: '.$uriSubject, 'firstName: patrick','password: blabla'));
	     $returnedData = $this->curl($url, CURLOPT_HTTPGET, "data", array('uri: '.$uriSubject));
	    $data = json_decode($returnedData, true);
	   
	    $this->assertEqual( $data["success"], true);
	    $this->assertEqual( $data["data"]["uri"], $uriSubject);
	    $this->checkPropertyValues($data["data"]["properties"], PROPERTY_USER_LOGIN, "literal", $genLogin);
	    $this->checkPropertyValues($data["data"]["properties"], PROPERTY_USER_PASSWORD, "literal", md5('blabla'));
	    $this->checkPropertyValues($data["data"]["properties"], PROPERTY_USER_LASTNAME, "literal", 'patrick');
	    
	      //get this test taker
	     

	    //remove this test taker

	    //get all test takers

	    //


	    
	}

}
?>