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
 * @author Cédric Alfonsi, <taosupport@tudor.lu>
 * @package tao
 * @subpackage test
 */
class FileHelperTestCase extends UnitTestCase {
	
    protected $deep = 3;
    protected $fileCount = 5;
    
    public function __construct()
    {
        $this->tmpPath = sys_get_temp_dir();
        $this->envName = 'ROOT_DIR';
        $this->envPath = $this->tmpPath.'/'.$this->envName;
    }
    
    public function setUp()
    {		
        parent::setUp();
		TaoTestRunner::initTest();
        $this->initEnv($this->tmpPath, $this->envName, $this->deep, $this->fileCount);
	}
    
    public function tearDown() {
        parent::tearDown();
        tao_helpers_File::remove($this->envPath, true);
        $this->assertFalse(is_dir($this->envPath));
    }
    
    private function initEnv($root, $name, $deep, $n)
    {
        $envPath = $root.'/'.$name;
        mkdir($envPath);
        $this->assertTrue(is_dir($envPath));
        for($i=0;$i<$n;$i++){
            $tempnam = tempnam($envPath, '');
            $this->assertTrue(is_file($tempnam));
        }
        if($deep > 0){
            $this->initEnv($envPath, 'DIR_'.$deep, $deep-1, $n);
        }
    }
    
    public function testScanDir()
    {
        $this->assertEqual(count(tao_helpers_File::scanDir($this->envPath, array('recursive'=>true))), 23);
        $this->assertEqual(count(tao_helpers_File::scanDir($this->envPath, array('only'=>tao_helpers_File::$DIR, 'recursive'=>true))), 3);
        $this->assertEqual(count(tao_helpers_File::scanDir($this->envPath, array('only'=>tao_helpers_File::$FILE, 'recursive'=>true))), 20);
    }
    
    public function testTempDir()
    {
    	$path1 = tao_helpers_File::createTempDir();
    	$path2 = tao_helpers_File::createTempDir();
    	$this->assertTrue(is_dir($path1));
    	$this->assertTrue(is_dir($path2));
    	$this->assertNotEqual($path1, $path2);
    	
    	$tempnam1 = tempnam($path1, '');
        $this->assertTrue(is_file($tempnam1));
        
    	$subdir2 = $path2.DIRECTORY_SEPARATOR.'testdir';
    	$this->assertTrue(mkdir($subdir2));
    	$this->assertTrue(is_dir($subdir2));
    	$tempnam2 = tempnam($subdir2, '');
        $this->assertTrue(is_file($tempnam2));
    	
        $this->assertTrue(tao_helpers_File::delTree($path1));
        $this->assertFalse(is_dir($path1));
        $this->assertFalse(is_file($tempnam1));
        
        $this->assertTrue(tao_helpers_File::delTree($path2));
        $this->assertFalse(is_dir($path2));
        $this->assertFalse(is_dir($subdir2));
        $this->assertFalse(is_file($tempnam2));
    }
}
?>