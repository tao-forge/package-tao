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

require_once dirname(__FILE__) . '/GenerisTestRunner.php';

class FileSourceLocalTestCase extends UnitTestCase {
    
    /**
     * @var core_kernel_versioning_Repository
     */
    private static $repository = null;
    
	public function __construct()
	{
		parent::__construct();
	}
	
    public function setUp()
    {
	    GenerisTestRunner::initTest();
		self::$repository = core_kernel_fileSystem_FileSystemFactory::createFileSystem(
			new core_kernel_classes_Resource(INSTANCE_GENERIS_VCS_TYPE_LOCAL),
			'', '', '', sys_get_temp_dir().DIRECTORY_SEPARATOR."testrepo", 'UnitTestRepository', true
		);
    }
	
    public function tearDown()
    {
	    self::$repository->delete();
	    parent::tearDown();
	}

	protected function getTestRepository () {
		return self::$repository;
	}

    public function testRepository() {
    	$this->assertIsA($this->getTestRepository(), 'core_kernel_versioning_Repository');
    }
	
}
