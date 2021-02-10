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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\taoMediaManager\test\unit\model\sharedStimulus\service;

use ErrorException;
use PHPUnit\Framework\MockObject\MockObject;
use common_report_Report;
use core_kernel_classes_Class;
use oat\generis\model\data\Ontology;
use oat\generis\test\TestCase;
use oat\taoMediaManager\model\MediaService;
use oat\taoMediaManager\model\SharedStimulusImporter;
use oat\taoMediaManager\model\sharedStimulus\CreateCommand;
use oat\taoMediaManager\model\sharedStimulus\SharedStimulus;
use oat\taoMediaManager\model\sharedStimulus\service\CreateService;
use oat\tao\model\FileNotFoundException;
use oat\tao\model\upload\UploadService;

class CreateServiceTest extends TestCase
{
    private const URI = 'uri';
    private const CLASS_URI = 'uri';
    private const LANGUAGE_URI = 'uri';
    private const NAME = 'name';
    private const USER_DIRECTORY_HASH = 'user_directory_hash';
    private const UPLOADED_FILE = 'uploaded.xml';

    /** @var CreateService */
    private $service;

    /** @var Ontology|MockObject */
    private $ontology;

    /** @var UploadService|MockObject */
    private $uploadService;

    /** @var SharedStimulusImporter|MockObject */
    private $sharedStimulusImporter;

    public function setUp(): void
    {
        $this->ontology = $this->createMock(Ontology::class);
        $this->uploadService = $this->createMock(UploadService::class);
        $this->sharedStimulusImporter = $this->createMock(SharedStimulusImporter::class);
        $this->service = new CreateService();
        $this->service->setServiceLocator(
            $this->getServiceLocatorMock(
                [
                    Ontology::SERVICE_ID => $this->ontology,
                    UploadService::SERVICE_ID => $this->uploadService,
                    SharedStimulusImporter::class => $this->sharedStimulusImporter
                ]
            )
        );
    }

    public function testCreateSharedStimulus(): void
    {
        $this->uploadService
            ->method('uploadFile')
            ->willReturn(
                [
                    'uploaded_file' => self::UPLOADED_FILE
                ]
            );

        $this->uploadService
            ->method('getUserDirectoryHash')
            ->willReturn(self::USER_DIRECTORY_HASH);

        $kernelClass = $this->createMock(core_kernel_classes_Class::class);

        $this->ontology
            ->method('getClass')
            ->willReturn($kernelClass);

        $childrenReport = $this->createMock(common_report_Report::class);
        $childrenReport->method('getData')
            ->willReturn(
                [
                    'uriResource' => self::URI
                ]
            );

        $importReport = $this->createMock(common_report_Report::class);
        $importReport->method('getChildren')
            ->willReturn([$childrenReport]);

        $this->sharedStimulusImporter
            ->method('import')
            ->with(
                $kernelClass,
                [
                    'lang' => self::LANGUAGE_URI,
                    'source' => [
                        'name' => self::NAME,
                        'type' => MediaService::SHARED_STIMULUS_MIME_TYPE,
                    ],
                    'uploaded_file' => DIRECTORY_SEPARATOR
                        . self::USER_DIRECTORY_HASH
                        . DIRECTORY_SEPARATOR
                        . self::UPLOADED_FILE
                ]
            )
            ->willReturn($importReport);

        $createdSharedStimulus = $this->service->create(
            new CreateCommand(
                self::CLASS_URI,
                self::NAME,
                self::LANGUAGE_URI
            )
        );

        $this->assertEquals(
            new SharedStimulus(
                self::URI,
                self::NAME,
                self::LANGUAGE_URI,
                '<?xml version="1.0" encoding="UTF-8"?>
<div xmlns="http://www.imsglobal.org/xsd/imsqti_v2p2">
</div>'
            ),
            $createdSharedStimulus
        );
    }

    public function testCannotCreateSharedStimulusWithInvalidTemporaryPath(): void
    {
        $this->service->setOption(CreateService::OPTION_TEMP_UPLOAD_PATH, 'invalid_path');

        $this->expectException(ErrorException::class);
        $this->expectExceptionMessage('Could not save Shared Stimulus to temporary path');

        $this->service->create(
            new CreateCommand(
                self::CLASS_URI,
                self::NAME,
                self::LANGUAGE_URI
            )
        );
    }

    public function testCannotCreateSharedStimulusWithInvalidTemplatePath(): void
    {
        $this->service->setOption(CreateService::OPTION_TEMPLATE_PATH, 'invalid_path');

        $this->expectException(FileNotFoundException::class);
        $this->expectExceptionMessage('Shared Stimulus template not found');

        $this->service->create(
            new CreateCommand(
                self::CLASS_URI,
                self::NAME,
                self::LANGUAGE_URI
            )
        );
    }
}
