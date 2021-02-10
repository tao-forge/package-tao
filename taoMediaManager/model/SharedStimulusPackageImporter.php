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
 * Copyright (c) 2014-2020 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\taoMediaManager\model;

use Exception;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use common_Exception;
use common_exception_Error;
use common_exception_UserReadableException;
use common_report_Report as Report;
use core_kernel_classes_Class;
use core_kernel_classes_Resource as Resource;
use helpers_File;
use helpers_TimeOutHelper;
use oat\oatbox\filesystem\File;
use oat\tao\model\FileNotFoundException;
use oat\tao\model\import\InvalidSourcePathException;
use qtism\data\content\xhtml\Img;
use qtism\data\content\xhtml\QtiObject;
use qtism\data\storage\xml\XmlDocument;
use qtism\data\storage\xml\XmlStorageException;
use tao_helpers_File;
use tao_helpers_Uri;
use tao_helpers_form_Form as Form;

/**
 * Service methods to manage the Media
 *
 * @access public
 * @package taoMediaManager
 */
class SharedStimulusPackageImporter extends ZipImporter
{
    /**
     * Starts the import based on the form
     *
     * @param core_kernel_classes_Class $class
     * @param Form|array $form
     * @param string|null $userId owner of the resource
     * @return Report
     */
    public function import($class, $form, $userId = null)
    {
        try {
            $uploadedFile = $this->fetchUploadedFile($form);

            $xmlFile = $this->getSharedStimulusFile($uploadedFile);

            $this->getUploadService()->remove($uploadedFile);

            // throws an exception of invalid
            SharedStimulusImporter::isValidSharedStimulus($xmlFile);

            $embeddedFile = static::embedAssets($xmlFile);

            $report = Report::createSuccess(__('Shared Stimulus imported successfully'));

            $subReport = $this->storeSharedStimulus($class, $this->getDecodedUri($form), $embeddedFile, $userId);

            $report->add($subReport);
        } catch (Exception $e) {
            $message = $e instanceof common_exception_UserReadableException
                ? $e->getUserMessage()
                : __('An error has occurred. Please contact your administrator.');
            $report = Report::createFailure($message);
            $this->logError($e->getMessage());
        }

        return $report;
    }

    /**
     * Edit a shared stimulus package
     *
     * @param Resource $instance
     * @param Form|array $form
     * @param null|string $userId
     * @return Report
     */
    public function edit(Resource $instance, $form, $userId = null)
    {
        try {
            $uploadedFile = $this->fetchUploadedFile($form);

            $xmlFile = $this->getSharedStimulusFile($uploadedFile);

            $this->getUploadService()->remove($uploadedFile);

            // throws an exception of invalid
            SharedStimulusImporter::isValidSharedStimulus($xmlFile);

            $embeddedFile = static::embedAssets($xmlFile);

            $report = $this->replaceSharedStimulus($instance, $this->getDecodedUri($form), $embeddedFile, $userId);
        } catch (Exception $e) {
            $message = $e instanceof common_exception_UserReadableException
                ? $e->getUserMessage()
                : __('An error has occurred. Please contact your administrator.');
            $report = Report::createFailure($message);
            $this->logError($e->getMessage());
            $report->setData(['uriResource' => '']);
        }

        return $report;
    }

    /**
     * Embed external resources into the XML
     *
     * @param $originalXml
     *
     * @return string
     * @throws InvalidSourcePathException
     * @throws common_exception_Error
     * @throws XmlStorageException
     * @throws oat\tao\model\FileNotFoundException
     */
    public static function embedAssets($originalXml)
    {
        $basedir = dirname($originalXml) . DIRECTORY_SEPARATOR;

        $xmlDocument = new XmlDocument();
        $xmlDocument->load($originalXml, true);

        //get images and object to base64 their src/data
        $images = $xmlDocument->getDocumentComponent()->getComponentsByClassName('img');
        $objects = $xmlDocument->getDocumentComponent()->getComponentsByClassName('object');

        /** @var $image Img */
        foreach ($images as $image) {
            $source = $image->getSrc();
            static::validateSource($basedir, $source);
            $image->setSrc(self::secureEncode($basedir, $source));
        }

        /** @var $object QtiObject */
        foreach ($objects as $object) {
            $data = $object->getData();
            static::validateSource($basedir, $data);
            $object->setData(self::secureEncode($basedir, $data));
        }

        // save the document to a tempfile
        $newXml = tempnam(sys_get_temp_dir(), 'sharedStimulus_') . '.xml';
        $xmlDocument->save($newXml);
        return $newXml;
    }

    /**
     * @param string $basePath
     * @param string $sourcePath
     *
     * @throws InvalidSourcePathException
     */
    private static function validateSource(string $basePath, string $sourcePath): void
    {
        $urlData = parse_url($sourcePath);

        if (!empty($urlData['scheme'])) {
            return;
        }

        if (!helpers_File::isFileInsideDirectory($sourcePath, $basePath)) {
            throw new InvalidSourcePathException($basePath, $sourcePath);
        }
    }

    /**
     * Get the shared stimulus file with assets from the zip
     *
     * @param string|File $filePath path of the zip file
     *
     * @return string|false path to the xml
     *
     * @throws common_Exception
     */
    private function getSharedStimulusFile($filePath)
    {
        helpers_TimeOutHelper::setTimeOutLimit(helpers_TimeOutHelper::LONG);
        $extractPath = $this->extractArchive($filePath);
        helpers_TimeOutHelper::reset();

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($extractPath),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        /** @var $file SplFileInfo */
        foreach ($iterator as $file) {
            //check each file to see if it can be the shared stimulus file
            if ($file->isFile()) {
                if (preg_match('/^[\w]/', $file->getFilename()) === 1 && $file->getExtension() === 'xml') {
                    return $file->getRealPath();
                }
            }
        }

        throw new common_Exception('XML not found in the package');
    }

    /**
     * Validate an xml file, convert file linked inside and store it into media manager
     *
     * @throws XmlStorageException
     */
    protected function storeSharedStimulus(
        Resource $class,
        string $lang,
        string $xmlFile,
        string $userId = null
    ): Report
    {
        SharedStimulusImporter::isValidSharedStimulus($xmlFile);

        $mediaResourceUri = $this->getMediaService()->createMediaInstance(
            $xmlFile,
            $class->getUri(),
            $lang,
            basename($xmlFile),
            MediaService::SHARED_STIMULUS_MIME_TYPE,
            $userId
        );

        if ($mediaResourceUri !== false) {
            $report = Report::createSuccess(__('Imported %s', basename($xmlFile)));
            $report->setData(['uriResource' => $mediaResourceUri]);
        } else {
            $report = Report::createFailure(__('Fail to import Shared Stimulus'));
            $report->setData(['uriResource' => '']);
        }

        return $report;
    }

    /**
     * Validate an xml file, convert file linked inside and store it into media manager
     *
     * @throws common_exception_Error
     * @throws XmlStorageException
     */
    protected function replaceSharedStimulus(
        Resource $instance,
        string $lang,
        string $xmlFile,
        string $userId = null
    ): Report
    {
        //if the class does not belong to media classes create a new one with its name (for items)
        $mediaClass = new core_kernel_classes_Class(MediaService::ROOT_CLASS_URI);
        if (!$instance->isInstanceOf($mediaClass)) {
            $report = Report::createFailure(
                'The instance ' . $instance->getUri() . ' is not a Media instance'
            );
            $report->setData(['uriResource' => '']);
            return $report;
        }

        SharedStimulusImporter::isValidSharedStimulus($xmlFile);
        $name = basename($xmlFile, '.xml');
        $name .= '.xhtml';
        $filepath = dirname($xmlFile) . '/' . $name;
        tao_helpers_File::copy($xmlFile, $filepath);

        if (!$this->getMediaService()->editMediaInstance($filepath, $instance->getUri(), $lang, $userId)) {
            $report = Report::createFailure(__('Fail to edit Shared Stimulus'));
        } else {
            $report = Report::createSuccess(__('Shared Stimulus edited successfully'));
            $report->add(
                Report::createSuccess(
                    __('Edited %s', $instance->getLabel()),
                    [
                        'uriResource' => $instance->getUri()
                    ]
                )
            );
        }

        $report->setData(['uriResource' => $instance->getUri()]);

        return $report;
    }

    /**
     * Verify paths and encode the file
     *
     * @throws oat\tao\model\FileNotFoundException
     * @throws common_exception_Error
     */
    protected static function secureEncode(string $basedir, string $source): string
    {
        $components = parse_url($source);

        if (!isset($components['scheme'])) {
            if (tao_helpers_File::securityCheck($source, false)) {
                if (file_exists($basedir . $source)) {
                    return 'data:' . tao_helpers_File::getMimeType($basedir . $source) . ';'
                        . 'base64,' . base64_encode(file_get_contents($basedir . $source));
                }

                throw new FileNotFoundException($source);
            }

            throw new common_exception_Error('Invalid source path "' . $source . '"');
        }

        return $source;
    }

    /**
     * @param array|Form $form
     */
    private function getDecodedUri($form): string
    {
        return tao_helpers_Uri::decode($form instanceof Form ? $form->getValue('lang') : $form['lang']);
    }

    private function getMediaService(): MediaService
    {
        return $this->getServiceLocator()->get(MediaService::class);
    }
}
