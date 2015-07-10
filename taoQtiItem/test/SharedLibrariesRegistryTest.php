<?php
use oat\tao\test\TaoPhpUnitTestRunner;
use oat\taoQtiItem\model\SharedLibrariesRegistry;
use oat\tao\model\ClientLibRegistry;
use \helpers_File;
use \common_ext_ExtensionsManager;

include_once dirname(__FILE__) . '/../includes/raw_start.php';

class LocalSharedLibrariesTest extends TaoPhpUnitTestRunner
{
    private $registry;
    
    private $initialMapping;
    
    protected function getBasePath()
    {
        $tmp = sys_get_temp_dir();
        return "${tmp}/tao_shared_libs_test";
    }
    
    protected function getBaseUrl()
    {
        return \common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiItem')->getConstant('BASE_WWW');
    }
    
    protected function getSamplesDir()
    {
        return dirname(__FILE__) . '/samples/sharedLibraries/';
    }
    
    protected function setRegistry(SharedLibrariesRegistry $registry)
    {
        $this->registry = $registry;
    }

    protected function getRegistry()
    {
        return $this->registry;
    }

    
    public function setUp()
    {
        parent::setUp();
        
        // Save installation original mapping for restitution in tearDown.
        @mkdir($this->getBasePath());
        $this->setRegistry(new SharedLibrariesRegistry($this->getBasePath(), $this->getBaseUrl()));

        $this->initialMapping = $this->getRegistry()->getMap();
    }
    
    public function tearDown()
    {
        parent::tearDown();
        helpers_File::remove($this->getBasePath());

        //unregister all
        $registry = $this->getRegistry();
        $ids = array_keys( array_diff($registry->getMap(), $this->initialMapping));

        foreach ($ids as $id) {
            $registry->remove($id);
        }
    }
    
    /**
     * @dataProvider registerFromFileProvider
     */
    public function testRegisterFromFile($id, $path, $expected)
    {
        $originalId = $id;
        $registry = $this->getRegistry();
        $registry->registerFromFile($id, $path);
        
        // A correct entry must be found in the mapping provided by the registry.
        $mapping = $registry->getMap();
        $this->assertTrue(isset($mapping[$id]), "No mapping found for '${id}'.");
        $this->assertEquals($expected, $mapping[$id], "Expected mapping entry '${expected}' not for id '${id}'.");
        
        $parts = explode('/', $id);
        $id = implode('/', array_slice($parts, 0, count($parts) - 1));
        $expectedLocation = str_replace(array('css!', 'tpl!'), '', $this->getBasePath() . '/' . $id . '/' . basename($path));
        
        // A file must be placed at "$this->getBasePath()/id/basename($path)"
        $this->assertTrue(file_exists($expectedLocation), "No library at location '${expectedLocation}'.");
    }
    
    public function registerFromFileProvider()
    {
        return array(
            array('OAT/Jacky/Julietta', $this->getSamplesDir() . 'julietta.js', $this->getBaseUrl() . 'OAT/Jacky/julietta.js'),
            array('OAT/Jacky/Julietta/julietta.js', $this->getSamplesDir() . 'julietta.js', $this->getBaseUrl() . 'OAT/Jacky/Julietta/julietta.js'),
            array('css!OAT/Jacky/Julietta/julietta.css', $this->getSamplesDir() . 'julietta.css', $this->getBaseUrl() . 'OAT/Jacky/Julietta/julietta.css'),
        );
    }
    
    /**
     * @dataProvider registerFromItemProvider
     * @depends testRegisterFromFile
     * 
     * @param string $itemPath The path where to find the item XML definition.
     * @param array $mappingDiff The expected difference between the original mapping and the one after registration.
     */
    public function testRegisterFromItem($itemPath, array $mappingDiff)
    {
        $registry = $this->getRegistry();
        // Register official libraries...
        self::registerOfficialLibraries($registry);
        
        // Save it for latter diff...
        $officialMapping = $registry->getMap();

        // Register the item libraries!
        $registry->registerFromItem($itemPath);

        $diff = array_diff($registry->getMap(), $officialMapping);
        $this->assertEquals($mappingDiff, $diff);
    }
    
    public function registerFromItemProvider()
    {
        $dir = $this->getSamplesDir();
        
        return array(
            array(
                "${dir}/registry_official_only/item.xml", 
                array()
            ),
            array(
                "${dir}/registry_with_unofficial/item.xml",
                array(
                    'OAT/shapes/collisions.js' => $this->getBaseUrl() . 'OAT/shapes/collisions.js',
                    'tpl!OAT/shapes/shapes.tpl' => $this->getBaseUrl() . 'OAT/shapes/shapes.tpl',
                    'css!OAT/shapes/shapes.css' => $this->getBaseUrl() . 'OAT/shapes/shapes.css'
                )
            )
        );
    }
    
    /**
     * @dataProvider isRegisteredProvider
     * @depends testRegisterFromFile
     * 
     * @param string $id The identifier of the library
     * @param boolean $expected The expected return value for $id.
     */
    public function testIsRegistered($id, $expected)
    {
        $registry = $this->getRegistry();
        self::registerOfficialLibraries($registry);
        
        $this->assertSame($expected, $registry->isRegistered($id));
    }
    
    public function isRegisteredProvider()
    {
        return array(
            array('IMSGlobal/jquery_2_1_1', true),
            array('IMSGlobal/gaga', false)
        );
    }
    
    public function testRegisterFromItemNotFound()
    {
        $dir = $this->getSamplesDir();
        
        $this->setExpectedException('oat\\taoQtiItem\\model\\SharedLibraryNotFoundException');
        $registry = $this->getRegistry();
        $registry->registerFromItem("${dir}/registry_notfound/item.xml");
    }
    
    // --- Test Utility Methods.
    static private function registerOfficialLibraries(SharedLibrariesRegistry $registry) 
    {
        $registry->registerFromFile('IMSGlobal/jquery_2_1_1', dirname(__FILE__) . '/../install/scripts/portableSharedLibraries/IMSGlobal/jquery_2_1_1.js');
        $registry->registerFromFile('OAT/lodash', dirname(__FILE__) . '/../install/scripts/portableSharedLibraries/OAT/lodash.js');
    }

    /**
     * @dataProvider registerFromFileProvider
     * @depends testRegisterFromFile
     */
    public function testRemove($id, $path)
    {
        $registry = $this->getRegistry();

        $registry->registerFromFile($id, $path);
        $registry->remove($id);

        $mapSharedLib = $registry->getMap();
        $mapClientLibRegistry = ClientLibRegistry::getRegistry()->getMap();

        $this->assertArrayNotHasKey($id, $mapSharedLib);
        $this->assertArrayNotHasKey($id, $mapClientLibRegistry);
    }
}