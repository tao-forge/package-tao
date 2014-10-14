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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 */

namespace oat\taoQtiItem\test;

use \common_report_Report;
use oat\tao\test\TaoPhpUnitTestRunner;
use oat\taoQtiItem\model\Export\QTIPackedItemExporter;
use oat\taoQtiItem\model\qti\ImportService;
use \taoItems_models_classes_ItemsService;
use \tao_models_classes_service_FileStorage;
use \taoItems_models_classes_ItemCompiler;
use \ZipArchive;
use oat\taoQtiItem\model\Export;

include_once dirname(__FILE__) . '/../includes/raw_start.php';
/**
 * test the item content access
 *
 */
class ItemImportTest extends TaoPhpUnitTestRunner
{
    /**
     * @var ImportService
     */
    protected $importService;
    /**
     * @var taoItems_models_classes_ItemsService
     */
    protected $itemService;
    /**
     * @var array
     */
    protected $exportedZips = array();

    /**
     * tests initialization
     * load qti service
     */
    public function setUp()
    {
        TaoPhpUnitTestRunner::initTest();
        $this->importService = ImportService::singleton();
        $this->itemService = taoItems_models_classes_ItemsService::singleton();
    }

    /**
     * @return string
     */
    protected function getSampleDir()
    {
        return dirname(__FILE__) . '/samples';
    }


    /**
     * @expectedException \common_exception_Error
     * 
     */
    public function testWrongPackage()
    {
        $itemClass = $this->itemService->getRootClass();
        $report = $this->importService->importQTIPACKFile($this->getSampleDir() . '/package/wrong/invalidArchive.zip',
            $itemClass);
        
    }

    /**
     * @expectedException \oat\taoQtiItem\model\qti\exception\ParsingException
     */
    public function testWrongClass()
    {
        $itemClass = new \core_kernel_classes_Class(TAO_ITEM_MODEL_PROPERTY);
        $report = $this->importService->importQTIPACKFile($this->getSampleDir() . '/package/wrong/package.zip',
            $itemClass);

    }

    /**
     * @expectedException \oat\taoQtiItem\model\qti\exception\ParsingException
     */
    public function testWrongFormatClass()
    {
        $itemClass = $this->itemService->getRootClass();

        $report = $this->importService->importQTIPACKFile($this->getSampleDir() . '/package/wrong/MalformedItemXml.zip',
            $itemClass, true, null, true);
        $this->assertEquals(\common_report_Report::TYPE_ERROR, $report->getType());
    }


    /**
     * @expectedException \oat\taoQtiItem\model\qti\exception\ParsingException
     */
    public function testWrongFormatXmlClass()
    {
        $itemClass = $this->itemService->getRootClass();

        $report = $this->importService->importQTIPACKFile($this->getSampleDir() . '/package/wrong/MalformedItemInTheMiddleXml.zip',
            $itemClass, true, null, false, true);
        $this->assertEquals(\common_report_Report::TYPE_WARNING, $report->getType());

    }

    /**
     * @expectedException \oat\taoQtiItem\model\qti\exception\ParsingException
     */
    public function testWrongManifest()
    {
        $itemClass = $this->itemService->getRootClass();


        $report = $this->importService->importQTIPACKFile($this->getSampleDir() . '/package/wrong/MalformedManifest.zip',
            $itemClass, true, null, true);
        $this->assertEquals(\common_report_Report::TYPE_ERROR, $report->getType());


    }
    /**
     * @expectedException \oat\taoQtiItem\model\qti\exception\ParsingException
     */
    public function testWrongXml()
    {

        $itemClass = $this->itemService->getRootClass();

        $report = $this->importService->importQTIPACKFile($this->getSampleDir() . '/package/wrong/WrongManifestFileItemHref.zip',
            $itemClass, true, null, true);
        $this->assertEquals(\common_report_Report::TYPE_ERROR, $report->getType());
    }


    public function testImport()
    {
        $itemClass = $this->itemService->getRootClass();
        $report = $this->importService->importQTIPACKFile($this->getSampleDir() . '/package/QTI/package.zip',
            $itemClass);

        $items = array();
        foreach ($report as $itemReport) {
            $data = $itemReport->getData();
            if (!is_null($data)) {
                $items[] = $data;
            }
        }
        $this->assertEquals(1, count($items));

        $item = current($items);
        $this->assertInstanceOf('\core_kernel_classes_Resource', $item);
        $this->assertTrue($item->exists());

        $data = \taoItems_helpers_ResourceManager::buildDirectory($item, DEFAULT_LANG);
        $this->assertTrue(is_array($data));
        $this->assertTrue(isset($data['path']));
        $this->assertEquals('/', $data['path']);

        $this->assertTrue(isset($data['children']));
        $children = $data['children'];
        $this->assertEquals(2, count($children));

        $file = null;
        $dir = null;
        foreach ($children as $child) {
            if (isset($child['path'])) {
                $dir = $child;
            }
            if (isset($child['name'])) {
                $file = $child;
            }
        }

        $this->assertEquals("qti.xml", $file['name']);
        $this->assertEquals(ROOT_URL, substr($file['url'], 0, strlen(ROOT_URL)));
        $this->assertEquals("application/xml", $file['mime']);
        $this->assertTrue($file['size'] > 0);

        $this->assertEquals("/images", $dir['path']);
        $this->assertEquals(ROOT_URL, substr($dir['url'], 0, strlen(ROOT_URL)));


        return $item;
    }

    /**
     * @depends testImport
     * @param $item
     */
    public function testCompile($item)
    {
        $storage = tao_models_classes_service_FileStorage::singleton();
        $compiler = new taoItems_models_classes_ItemCompiler($item, $storage);
        $report = $compiler->compile();
        $this->assertEquals($report->getType(), common_report_Report::TYPE_SUCCESS);
        $serviceCall = $report->getData();
        $this->assertNotNull($serviceCall);
        $this->assertInstanceOf('\tao_models_classes_service_ServiceCall', $serviceCall);
    }

    /**
     * @depends testImport
     * @param $item
     * @throws Exception
     * @throws \oat\taoQtiItem\model\qti\exception\ExtractException
     * @throws \oat\taoQtiItem\model\qti\exception\ParsingException
     * @return mixed
     */
    public function testExport($item)
    {
        $itemClass = $this->itemService->getRootClass();

        list($path, $manifest) = $this->createZipArchive($item);

        $report = $this->importService->importQTIPACKFile($path, $itemClass);
        $this->assertEquals(\common_report_Report::TYPE_SUCCESS, $report->getType());
        $items = array();
        foreach ($report as $itemReport) {
            $data = $itemReport->getData();
            if (!is_null($data)) {
                $items[] = $data;
            }
        }
        $this->assertEquals(1, count($items));
        $item2 = current($items);
        $this->assertInstanceOf('\core_kernel_classes_Resource', $item);
        $this->assertTrue($item->exists());

        $this->assertEquals($item->getLabel(), $item2->getLabel());

        $this->removeItem($item2);

        return $manifest;
    }

    /**
     * @depends testImport
     * @depends testExport
     * @param $item
     * @param $manifest
     * @return mixed
     */
    public function testExportWithManifest($item, $manifest)
    {
        list($path, $manifest2) = $this->createZipArchive($item, $manifest);
        $this->assertSame($manifest, $manifest2);

    }

    /**
     * @depends testImport
     */
    public function testRemoveItem()
    {
        foreach (func_get_args() as $item) {
            $this->removeItem($item);
        }
    }


    private function removeItem($item)
    {
        $this->itemService->deleteItem($item);
        $this->assertFalse($item->exists());
    }

    public function tearDown()
    {

        foreach ($this->exportedZips as $path) {
            if (file_exists($path)) {
                $this->assertTrue(unlink($path));
            }
        }
    }

    /**
     * @param $item
     * @param $manifest
     * @return array
     * @throws Exception
     */
    private function createZipArchive($item, $manifest = null)
    {
        $path = sys_get_temp_dir() . DIRECTORY_SEPARATOR. uniqid('test_') . '.zip';
        $zipArchive = new ZipArchive();
        if ($zipArchive->open($path, ZipArchive::CREATE) !== true) {
            throw new Exception('Unable to create archive at ' . $path);
        }

        if ($this->itemService->hasItemModel($item, array(TAO_ITEM_MODEL_QTI))) {
            $exporter = new QTIPackedItemExporter($item, $zipArchive, $manifest);
            $exporter->export();
            $manifest = $exporter->getManifest();
        }

        $this->assertTrue($this->itemService->hasItemModel($item, array(TAO_ITEM_MODEL_QTI)));

        $this->assertNotNull($manifest);

        $this->assertEquals(ZipArchive::ER_OK, $zipArchive->status, $zipArchive->getStatusString());

        $zipArchive->close();
        $this->assertTrue(file_exists($path),'could not find path ' . $path);
        $this->exportedZips[] = $path;
        return array($path, $manifest);
    }

}