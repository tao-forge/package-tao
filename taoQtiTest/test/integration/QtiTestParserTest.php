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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\taoQtiTest\test\integration;

use common_report_Report;
use core_kernel_classes_Property;
use core_kernel_classes_Resource;
use oat\generis\test\GenerisPhpUnitTestRunner;
use oat\taoQtiTest\models\ManifestParser;
use oat\taoQtiTest\models\QtiTestCompiler;
use oat\tao\model\service\FileStorage;

/**
 * This test case focuses on testing the ManifestParser model.
 *
 * @author Aamir
 * @package taoQtiTest
 */
class QtiTestParserTest extends GenerisPhpUnitTestRunner
{

    public static function dataDir()
    {
        return dirname(__FILE__) . '/data/';
    }

    public static function samplesDir()
    {
        return dirname(__FILE__) . '/../samples/';
    }

    public function setUp(): void
    {
        parent::initTest();
    }

    /**
     *
     * @return mixed|null
     */
    public function testManifestParserObject()
    {
        $objParser = new ManifestParser($this->dataDir() . 'imsmanifest_mapping_1.xml');
        $this->assertNotNull($objParser);

        return $objParser;
    }

    /**
     * @depends testManifestParserObject
     *
     * @param $objParser
     * @return void
     */
    public function testManifestParserValidate($objParser)
    {
        $this->assertTrue($objParser->validate());
    }

    /**
     * @depends testManifestParserObject
     *
     * @param $objParser
     * @return void
     */
    public function testManifestParserGetResources($objParser)
    {
        $idResources = $objParser->getResources(null, ManifestParser::FILTER_RESOURCE_IDENTIFIER);
        $this->assertEquals(4, count($idResources));

        $typeResources = $objParser->getResources('imsqti_test_xmlv2p1', ManifestParser::FILTER_RESOURCE_TYPE);
        $this->assertEquals(1, count($typeResources));

        $typeResourcesDefault = $objParser->getResources('imsqti_test_xmlv2p1');
        $this->assertEquals(1, count($typeResourcesDefault));
    }

    /**
     * Initialize the compiler
     *
     * @return \oat\taoQtiTest\models\QtiTestCompiler
     */
    public function testQtiTestCreateCompiler()
    {
        $content = new core_kernel_classes_Resource($this->dataDir() . 'qtitest.xml');

        $storage = FileStorage::singleton();

        $this->assertIsA($content, core_kernel_classes_Resource::class);
        $this->assertIsA($storage, FileStorage::class);

        $compiler = new QtiTestCompiler($content, $storage);
        $this->assertIsA($compiler, QtiTestCompiler::class);

        return $compiler;
    }



    /**
     * @depends testQtiTestCreateCompiler
     *
     * @param \oat\taoQtiTest\models\QtiTestCompiler $compiler
     * @return void
     */
    public function testQtiTextCompilerCompile($compiler)
    {
        $compiler->setServiceLocator($this->getServiceLocatorMock([]));
        $report = $compiler->compile();
        $this->assertEquals($report->getType(), common_report_Report::TYPE_ERROR);
        $serviceCall = $report->getData();
        $this->assertNull($serviceCall);
    }
}
