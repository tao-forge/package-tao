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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               2012-2016 Open Assessment Technologies SA
 * 
 */
namespace oat\tao\test;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Memory\MemoryAdapter;
use oat\generis\test\GenerisPhpUnitTestRunner;
use oat\oatbox\filesystem\Directory;
use oat\oatbox\filesystem\FileSystemService;
use oat\oatbox\service\ServiceManager;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Help you to run the test into the TAO Context
 * @package tao
 
 */
abstract class  TaoPhpUnitTestRunner extends GenerisPhpUnitTestRunner{
	
	const SESSION_KEY = 'TAO_TEST_SESSION';
    /**
     *
     * @var boolean
     */
    private static $connected = false;

    /**
     * Temp fly directory
     * @var Directory
     */
    protected $tempDirectory;

    /**
     * FileSystem id of temp fileSystem
     * @var string
     */
    protected $tempFileSystemId;


    /**
     * shared methods for test initialization
     */
    public static function initTest(){
        //connect the API
        if(!self::$connected){
            \common_session_SessionManager::startSession(new \common_test_TestUserSession());
            self::$connected = true;
        }
    }

    /**
     * At tear down,
     *  - Remove temporary file system created to testing
     */
    public function tearDown()
    {
        $this->removeTempFileSystem();
    }

    /**
     * Get a temp driectory used for testing purpose
     * If not exists, directory filesystem will created (memory if available, or Local)
     *
     * @return Directory
     */
    protected function getTempDirectory()
    {
        if (! $this->tempDirectory) {
            /** @var FileSystemService $fileSystemService */
            $fileSystemService = $this->getServiceManager()->get(FileSystemService::SERVICE_ID);
            $this->tempFileSystemId = 'unit-test-' . uniqid();

            $adapters = $fileSystemService->getOption(FileSystemService::OPTION_ADAPTERS);
            if (class_exists('League\Flysystem\Memory\MemoryAdapter')) {
                $adapters[$this->tempFileSystemId] = array(
                    'class' => MemoryAdapter::class
                );
            } else {
                $adapters[$this->tempFileSystemId] = array(
                    'class' => FileSystemService::FLYSYSTEM_LOCAL_ADAPTER,
                    'options' => array('root' => '/tmp/testing')
                );
            }
            $fileSystemService->setOption(FileSystemService::OPTION_ADAPTERS, $adapters);
            $fileSystemService->setOption(FileSystemService::OPTION_FILE_PATH, '/tmp/testing');

            $smProphecy = $this->prophesize(ServiceLocatorInterface::class);
            $smProphecy->get(FileSystemService::SERVICE_ID)->willReturn($fileSystemService);
            $fileSystemService->setServiceLocator($smProphecy->reveal());

            $this->tempDirectory = $fileSystemService->getDirectory($this->tempFileSystemId);
        }
        return $this->tempDirectory;
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object $object    Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    public function invokeProtectedMethod($object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    /**
     * return inaccessible property value
     * @param type $object
     * @param type $propertyName
     * @return mixed
     */
    protected function getInaccessibleProperty($object , $propertyName) {
        $property = new \ReflectionProperty(get_class($object) , $propertyName);
        $property->setAccessible(true);
        $value = $property->getValue($object);
        $property->setAccessible(false);
        return $value;
    }
    /**
     * set inaccessible property value
     * @param type $object
     * @param type $propertyName
     * @param type $value
     * @return \oat\tao\test\TaoPhpUnitTestRunner
     */
    protected function setInaccessibleProperty($object , $propertyName, $value) {
        $property = new \ReflectionProperty(get_class($object) , $propertyName);
        $property->setAccessible(true);
        $property->setValue($object, $value);
        $property->setAccessible(false);
        return $this;
    }

    /**
     * At tearDown, unregister tempFileSystem & clean directory
     */
    protected function removeTempFileSystem()
    {
        if ($this->tempDirectory) {
            /** @var FileSystemService $fileSystemService */
            $fileSystemService = $this->getServiceManager()
                ->get(FileSystemService::SERVICE_ID);

            $tempAdapter = $fileSystemService
                ->getFileSystem($this->tempFileSystemId)
                ->getAdapter();

            if ($tempAdapter instanceof Local) {
                $localPath = $this->getInaccessibleProperty($tempAdapter, 'pathPrefix');
                $this->rrmdir($localPath);
            }
        }
    }

    /**
     * Remove a local directory recursively
     *
     * @param $dir
     */
    protected function rrmdir($dir)
    {
        foreach(glob($dir . '/*') as $file) {
            if(is_dir($file)) {
                $this->rrmdir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dir);
    }

    protected function getServiceManager()
    {
        return ServiceManager::getServiceManager();
    }
}
