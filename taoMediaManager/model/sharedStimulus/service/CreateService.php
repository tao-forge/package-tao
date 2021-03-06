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

namespace oat\taoMediaManager\model\sharedStimulus\service;

use common_Exception;
use common_exception_Error;
use core_kernel_classes_Class;
use ErrorException;
use oat\generis\model\data\Ontology;
use oat\oatbox\service\ConfigurableService;
use oat\tao\model\upload\UploadService;
use oat\taoMediaManager\model\MediaService;
use oat\taoMediaManager\model\sharedStimulus\CreateCommand;
use oat\taoMediaManager\model\sharedStimulus\SharedStimulus;
use oat\taoMediaManager\model\SharedStimulusImporter;
use tao_models_classes_FileNotFoundException;

class CreateService extends ConfigurableService
{
    public const DEFAULT_NAME = 'passage NEW';
    public const OPTION_TEMP_UPLOAD_PATH = 'temp_upload_path';
    public const OPTION_TEMPLATE_PATH = 'template_path';

    /**
     * @throws common_Exception
     * @throws common_exception_Error
     * @throws tao_models_classes_FileNotFoundException
     * @throws ErrorException
     */
    public function create(CreateCommand $command): SharedStimulus
    {
        $defaultContent = $this->getDefaultTemplateContent();
        $fileName = $this->getTempFileName();
        $filePath = $this->saveTemporaryFile($fileName, $defaultContent);

        $uploadService = $this->getUploadService();

        $uploadResponse = $uploadService
            ->uploadFile(
                [
                    'name' => $fileName,
                    'tmp_name' => $filePath
                ],
                DIRECTORY_SEPARATOR
            );

        $kernelClass = $this->getOntology()->getClass($command->getClassId());

        $sharedStimulusName = $this->getSharedStimulusName($command, $kernelClass);

        $importResponse = $this->getSharedStimulusImporter()
            ->import(
                $kernelClass,
                [
                    'lang' => $command->getLanguageId(),
                    'source' => [
                        'name' => $sharedStimulusName,
                        'type' => MediaService::SHARED_STIMULUS_MIME_TYPE,
                    ],
                    'uploaded_file' => DIRECTORY_SEPARATOR
                        . $uploadService->getUserDirectoryHash()
                        . DIRECTORY_SEPARATOR
                        . $uploadResponse['uploaded_file']
                ]
            );

        return new SharedStimulus(
            current($importResponse->getChildren())->getData()['uriResource'],
            $sharedStimulusName,
            $command->getLanguageId(),
            $defaultContent
        );
    }

    private function getSharedStimulusName(CreateCommand $command, core_kernel_classes_Class $kernelClass): string
    {
        if ($command->getName()) {
            return $command->getName();
        }

        $totalInstances = count($kernelClass->getInstances());

        $name = $totalInstances === 0 ? self::DEFAULT_NAME : (self::DEFAULT_NAME . ' ' . $totalInstances);
        return $name . '.xml';
    }

    private function getTempFileName(): string
    {
        return 'shared_stimulus_' . uniqid() . '.xml';
    }

    private function getOntology(): Ontology
    {
        return $this->getServiceLocator()->get(Ontology::SERVICE_ID);
    }

    private function getUploadService(): UploadService
    {
        return $this->getServiceLocator()->get(UploadService::SERVICE_ID);
    }

    private function getSharedStimulusImporter(): SharedStimulusImporter
    {
        return $this->getServiceLocator()->get(SharedStimulusImporter::class);
    }

    private function getTemplateFilePath(): string
    {
        return $this->getOption(self::OPTION_TEMPLATE_PATH) ?? __DIR__
            . DIRECTORY_SEPARATOR
            . '..'
            . DIRECTORY_SEPARATOR
            . '..'
            . DIRECTORY_SEPARATOR
            . '..'
            . DIRECTORY_SEPARATOR
            . 'views'
            . DIRECTORY_SEPARATOR
            . 'templates'
            . DIRECTORY_SEPARATOR
            . 'sharedStimulus'
            . DIRECTORY_SEPARATOR
            . 'empty_template.xml';
    }

    /**
     * @throws tao_models_classes_FileNotFoundException
     */
    private function getDefaultTemplateContent(): string
    {
        $templatePath = $this->getTemplateFilePath();

        if (!is_readable($templatePath)) {
            throw new tao_models_classes_FileNotFoundException(
                sprintf('Shared Stimulus template not found: %s', $templatePath)
            );
        }

        return file_get_contents($templatePath);
    }

    /**
     * @throws ErrorException
     */
    private function saveTemporaryFile(string $fileName, string $templateContent): string
    {
        $fileDirectory = $this->getOption(self::OPTION_TEMP_UPLOAD_PATH) ?? sys_get_temp_dir();

        $filePath = $fileDirectory . DIRECTORY_SEPARATOR . $fileName;

        if (!is_writable($fileDirectory) || file_put_contents($filePath, $templateContent) === false) {
            throw new ErrorException(
                sprintf(
                    'Could not save Shared Stimulus to temporary path %s',
                    $filePath
                )
            );
        }

        return $filePath;
    }
}
