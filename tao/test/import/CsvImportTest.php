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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

use oat\tao\test\TaoPhpUnitTestRunner;

include_once dirname(__FILE__) . '/../../includes/raw_start.php';

class CsvImportTest extends TaoPhpUnitTestRunner {

	const CSV_FILE_USERS_HEADER_UNICODE = '/../samples/csv/users1-header.csv';
	const CSV_FILE_USERS_NO_HEADER_UNICODE = '/../samples/csv/users1-no-header.csv';
	
	public function testImport(){
		$importer = new \oat\tao\model\import\CSVBasicImporter();

		$staticMap = array();
		//copy file because it should be removed
		$path = dirname(__FILE__) . self::CSV_FILE_USERS_HEADER_UNICODE;
		$file = tao_helpers_File::createTempDir().'/copy.csv';
		tao_helpers_File::copy($path,$file);
		$this->assertFileExists($file);
		$map = array();
		$resource = $this->prophesize('\core_kernel_classes_Resource');
		$class = $this->prophesize('\core_kernel_classes_Class');
		$class->createInstanceWithProperties($staticMap)->willReturn($resource->reveal());

		$options = array('file' => $file, 'map' => $map, 'staticMap' => $staticMap);
		$report = $importer->import($class->reveal(), $options);

		$this->assertInstanceOf('common_report_Report',$report);
		$this->assertEquals('Data imported',$report->getMessage());
		$this->assertEquals(common_report_Report::TYPE_SUCCESS,$report->getType());
		$this->assertFileNotExists($file);

	}

	public function testCsvMapping(){
		$importer = new \oat\tao\model\import\CSVBasicImporter();

		$expectedHeaderMap = array('label','First Name','Last Name','Login','Mail','password','UserUILg');

		$property1 = $this->prophesize('\core_kernel_classes_Property');
		$property1->getUri()->willReturn('uriproperty1');
		$property1->getLabel()->willReturn('label');

		$property2 = $this->prophesize('\core_kernel_classes_Property');
		$property2->getUri()->willReturn('uriproperty2');
		$property2->getLabel()->willReturn('Login');

		$property3 = $this->prophesize('\core_kernel_classes_Property');
		$property3->getUri()->willReturn('uriproperty3');
		$property3->getLabel()->willReturn('labelproperty3');

		$properties = array($property1->reveal(), $property2->reveal(), $property3->reveal());
		$class = $this->prophesize('\core_kernel_classes_Class');
		$class->getProperties()->willReturn($properties);

		//copy file because it should be removed
		$path = dirname(__FILE__) . self::CSV_FILE_USERS_HEADER_UNICODE;

		$options[\tao_helpers_data_CsvFile::FIRST_ROW_COLUMN_NAMES] = true;
		$map = $importer->getCsvMapping($class->reveal(), $path, $options);

		$this->assertArrayHasKey('classProperties',$map);
		$this->assertArrayHasKey('headerList',$map);
		$this->assertArrayHasKey('mapping',$map);
		$this->assertEquals($expectedHeaderMap,$map['headerList']);
		$this->assertCount(3,$map['classProperties']);
		$this->assertArrayHasKey('uriproperty1',$map['classProperties']);
		$this->assertArrayHasKey('uriproperty2',$map['classProperties']);
		$this->assertArrayHasKey('uriproperty3',$map['classProperties']);
		$this->assertEquals('label',$map['classProperties']['uriproperty1']);
		$this->assertEquals('Login',$map['classProperties']['uriproperty2']);
		$this->assertEquals('labelproperty3',$map['classProperties']['uriproperty3']);
		$this->assertCount(2,$map['mapping']);
		$this->assertEquals(0,$map['mapping']['uriproperty1']);
		$this->assertEquals(3,$map['mapping']['uriproperty2']);
		$this->assertArrayNotHasKey('uriproperty3',$map['mapping']);

	}


	public function testGetDataSample(){
		$importer = new \oat\tao\model\import\CSVBasicImporter();

		$path = dirname(__FILE__) . self::CSV_FILE_USERS_HEADER_UNICODE;
		$expectedKeys = array('label','First Name','Last Name','Login','Mail','password','UserUILg');
		$expectedLogin = array('jbogaerts','pplichart','rjadoul','chenri','ijars');

		$sample = $importer->getDataSample($path);

		$this->assertCount(5,$sample);
		foreach($sample as $i => $row){
			$this->assertCount(7,$row);
			$this->assertEquals($expectedKeys, array_keys($row));
			$this->assertEquals($expectedLogin[$i], $row['Login']);
		}

		$expectedKeys = array(0,1,2,3,4,5,6);
		$sample = $importer->getDataSample($path, array(), 20, false);

		$this->assertCount(16,$sample);
		foreach($sample as $i => $row){
			$this->assertCount(7,$row);
			$this->assertEquals($expectedKeys, array_keys($row));
		}


	}

}
