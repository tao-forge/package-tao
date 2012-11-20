<?php
require_once dirname(__FILE__) . '/TaoTestRunner.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 * @author Jerome Bogaerts, <taosupport@tudor.lu>
 * @package tao
 * @subpackage test
 */
class UriHelperTestCase extends UnitTestCase {
    
    public function setUp()
    {		
        parent::setUp();
		TaoTestRunner::initTest();
	}
	
	public function testUriDomain(){
		$uri = 'http://www.google.fr';
		$domain = tao_helpers_Uri::getDomain($uri);
		$this->assertEqual($domain, 'www.google.fr');
		
		$uri = 'http://www.google.fr/';
		$domain = tao_helpers_Uri::getDomain($uri);
		$this->assertEqual($domain, 'www.google.fr');
		
		$uri = 'http://www.google.fr/translate';
		$domain = tao_helpers_Uri::getDomain($uri);
		$this->assertEqual($domain, 'www.google.fr');
		
		$uri = 'http://www.google.fr/translate?word=yes';
		$domain = tao_helpers_Uri::getDomain($uri);
		$this->assertEqual($domain, 'www.google.fr');
		
		$uri = 'ftp://sub.domain.filetransfer.ulc.ag.be';
		$domain = tao_helpers_Uri::getDomain($uri);
		$this->assertEqual($domain, 'sub.domain.filetransfer.ulc.ag.be');
		
		$uri = 'flupsTu8tridou:kek';
		$domain = tao_helpers_Uri::getDomain($uri);
		$this->assertNull($domain, "domain should be null but is equal to '${domain}'.");
		
		$uri = 'http://mytaoplatform/';
		$domain = tao_helpers_Uri::getDomain($uri);
		$this->assertTrue($domain, 'mytaoplatform');
	}
	
	public function testUriPath(){
		$uri = 'http://www.google.fr';
		$path = tao_helpers_Uri::getPath($uri);
		$this->assertNull($path);
		
		$uri = 'http://www.google.fr/';
		$path = tao_helpers_Uri::getPath($uri);
		$this->assertEqual($path, '/');
		
		$uri = 'http://www.google.fr/translate';
		$path = tao_helpers_Uri::getPath($uri);
		$this->assertEqual($path, '/translate');
		
		$uri = 'http://www.google.fr/translate?word=yes';
		$path = tao_helpers_Uri::getPath($uri);
		$this->assertEqual($path, '/translate');
		
		$uri = 'http://www.google.fr/translate/funky?word=yes';
		$path = tao_helpers_Uri::getPath($uri);
		$this->assertEqual($path, '/translate/funky');
		
		$uri = 'http://www.google.fr/translate/funky/?word=yes';
		$path = tao_helpers_Uri::getPath($uri);
		$this->assertEqual($path, '/translate/funky/');
		
		$uri = 'ftp://sub.domain.filetransfer.ulc.ag.be';
		$path = tao_helpers_Uri::getPath($uri);
		$this->assertNull($path);
		
		$uri = 'flupsTu8tridoujkek';
		$path = tao_helpers_Uri::getPath($uri);
		$this->assertNull($path);
		
		$uri = 'http://mytaoplatform/';
		$path = tao_helpers_Uri::getPath($uri);
		$this->assertTrue($path, '/');
	}
	
	public function testIsValidAsCookieDomain(){
		$this->assertFalse(tao_helpers_Uri::isValidAsCookieDomain('http://mytaoplatform'));
		$this->assertFalse(tao_helpers_Uri::isValidAsCookieDomain('http://my-tao-platform'));
		$this->assertFalse(tao_helpers_Uri::isValidAsCookieDomain('mytaoplatform'));
		$this->assertFalse(tao_helpers_Uri::isValidAsCookieDomain('mytaoplatform/items/'));
		$this->assertFalse(tao_helpers_Uri::isValidAsCookieDomain(''));
		$this->assertTrue(tao_helpers_Uri::isValidAsCookieDomain('http://mytaoplatform.com'));
		$this->assertTrue(tao_helpers_Uri::isValidAsCookieDomain('http://my-tao-platform.ru'));
		$this->assertTrue(tao_helpers_Uri::isValidAsCookieDomain('http://www.mytaoplatform.com'));
		$this->assertTrue(tao_helpers_Uri::isValidAsCookieDomain('http://www.-my-tao-platform.ru'));
		$this->assertTrue(tao_helpers_Uri::isValidAsCookieDomain('http://my.taoplatform.com'));
		$this->assertTrue(tao_helpers_Uri::isValidAsCookieDomain('http://my.tao.platform.qc.ca'));
		$this->assertTrue(tao_helpers_Uri::isValidAsCookieDomain('http://my.TAO.plAtfOrm.qc.cA'));
		$this->assertFalse(tao_helpers_Uri::isValidAsCookieDomain('http://.my.tao.platform.qc.ca'));		
	}
}
?>