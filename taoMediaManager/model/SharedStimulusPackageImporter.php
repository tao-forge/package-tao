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
 *
 *
 */

namespace oat\taoMediaManager\model;

use core_kernel_classes_Class;
use Jig\Utils\FsUtils;
use qtism\data\storage\xml\XmlDocument;
use tao_helpers_form_Form;

/**
 * Service methods to manage the Media
 *
 * @access public
 * @package taoMediaManager

 */
class SharedStimulusPackageImporter extends ZipImporter
{

    /**
     * @var string $xmlFile
     */
    private $xmlFile;

    public function __construct($xmlFile = '', $directory = ''){
        parent::__construct($directory);
        $this->xmlFile = $xmlFile;
    }


    /**
     * Starts the import based on the form
     *
     * @param \core_kernel_classes_Class $class
     * @param \tao_helpers_form_Form $form
     * @return \common_report_Report
     */
    public function import($class, $form)
    {
        //as upload may be called multiple times, we remove the session lock as soon as possible
        session_write_close();

        try{
            $file = $form->getValue('source');

            $tmpDir = \tao_helpers_File::createTempDir();
            $fileName = \tao_helpers_File::getSafeFileName($file['name']);
            $filePath = $tmpDir . '/' . $fileName;
            if (!rename($file['uploaded_file'], $filePath)) {
                return \common_report_Report::createFailure(__('Unable to move uploaded file'));
            }

            // unzip the file

            if (!$this->extractArchive($filePath)) {
                $report = \common_report_Report::createFailure($this->getDirectory());
                return $report;
            }

            //get the xml file that represents the shared stimulus
            if($this->getSharedStimulusFile() === false){
                $report = \common_report_Report::createFailure('Unable to find an xml file in you package');
                return $report;
            }

            $report = $this->validateAndStoreSharedStimulus($class, \tao_helpers_Uri::decode($form->getValue('lang')), $tmpDir);

            return $report;

        } catch(\Exception $e){
            $report = \common_report_Report::createFailure($e->getMessage());
            return $report;
        }
    }


    /**
     * @param core_kernel_classes_Class $instance
     * @param \tao_helpers_form_Form$form
     * @return \common_report_Report
     */
    public function edit($instance, $form){

        //as upload may be called multiple times, we remove the session lock as soon as possible
        session_write_close();

        try{
            $file = $form->getValue('source');

            $tmpDir = \tao_helpers_File::createTempDir();
            $fileName = \tao_helpers_File::getSafeFileName($file['name']);
            $filePath = $tmpDir . '/' . $fileName;
            if (!rename($file['uploaded_file'], $filePath)) {
                return \common_report_Report::createFailure(__('Unable to move uploaded file'));
            }

            // unzip the file
            if (!$this->extractArchive($filePath)) {
                $report = \common_report_Report::createFailure($this->getDirectory());
                return $report;
            }

            //get the xml file that represents the shared stimulus
            if($this->getSharedStimulusFile() === false){
                $report = \common_report_Report::createFailure('Unable to find an xml file in you package');
                return $report;
            }

            $report = $this->validateAndEditSharedStimulus($instance, \tao_helpers_Uri::decode($form->getValue('lang')), $tmpDir);

            return $report;

        } catch(\Exception $e){
            $report = \common_report_Report::createFailure($e->getMessage());
            return $report;
        }
    }


    /**
     * Validate an xml file, convert file linked inside and store it into media manager
     * @param \core_kernel_classes_Resource $class the class under which we will store the shared stimulus (can be an item)
     * @param string $lang language of the shared stimulus
     * @param string $tmpDir (optional)
     * @return \common_report_Report
     */
    public function validateAndStoreSharedStimulus($class, $lang, $tmpDir = ''){
        //create tmp dir if it does not exist
        if($tmpDir === ''){
            $tmpDir = \tao_helpers_File::createTempDir();
        }


        //if the class does not belong to media classes create a new one with its name (for items)
        $mediaClass = new core_kernel_classes_Class(MEDIA_URI);
        if(!$class->isClass() && !$class->isInstanceOf($mediaClass)){
            $mediaClass->createSubClass($class->getLabel());
        }

        $service = MediaService::singleton();
        $filename = $this->validateXmlFile($tmpDir);
        if(!$service->createMediaInstance($filename, $class->getUri(), $lang, basename($this->xmlFile))){
            $report = \common_report_Report::createFailure(__('Fail to import Shared Stimulus'));
        }
        else{
            $report = \common_report_Report::createSuccess(__('Shared Stimulus imported successfully'));
        }

        return $report;
    }

    /**
     * Validate an xml file, convert file linked inside and store it into media manager
     * @param \core_kernel_classes_Resource $instance the instance to edit
     * @param string $lang language of the shared stimulus
     * @param string $tmpDir (optional)
     * @return \common_report_Report
     */
    public function validateAndEditSharedStimulus($instance, $lang, $tmpDir = ''){
        //create tmp dir if it does not exist
        if($tmpDir === ''){
            $tmpDir = \tao_helpers_File::createTempDir();
        }


        //if the class does not belong to media classes create a new one with its name (for items)
        $mediaClass = new core_kernel_classes_Class(MEDIA_URI);
        if(!$instance->isInstanceOf($mediaClass)){
            $report = \common_report_Report::createFailure('The instance ' . $instance->getUri() . ' is not a Media instance');
            return $report;
        }

        $service = MediaService::singleton();
        $filename = $this->validateXmlFile($tmpDir);
        if(!$service->editMediaInstance($filename, $instance->getUri(), $lang)){
            $report = \common_report_Report::createFailure(__('Fail to edit Shared Stimulus'));
        }
        else{
            $report = \common_report_Report::createSuccess(__('Shared Stimulus edited successfully'));
        }

        return $report;
    }


    /**
     * @param XmlDocument $xmlDocument
     * @return XmlDocument
     */
    public function convertEmbeddedFiles(XmlDocument $xmlDocument){
        //get images and object to base64 their src/data
        $images = $xmlDocument->getDocumentComponent()->getComponentsByClassName('img');
        $objects = $xmlDocument->getDocumentComponent()->getComponentsByClassName('object');

        /** @var $image \qtism\data\content\xhtml\Img */
        foreach($images as $image) {
            $source = $image->getSrc();
            if(file_exists($this->getDirectory() . '/' . $source)){
                $base64 = 'data:' . FsUtils::getMimeType($this->getDirectory() . '/' . $source) . ';'
                    . 'base64,' . base64_encode(file_get_contents($this->getDirectory() . '/' . $source));
                $image->setSrc($base64);
            }
        }

        /** @var $object \qtism\data\content\xhtml\Object */
        foreach($objects as $object) {
            $data = $object->getData();
            if(file_exists($this->getDirectory() . '/' . $data)){
                $base64 = 'data:' . FsUtils::getMimeType($this->getDirectory() . '/' . $data) . ';'
                    . 'base64,' . base64_encode(file_get_contents($this->getDirectory() . '/' . $data));
                $object->setData($base64);
            }
        }
        return $xmlDocument;
    }

    /**
     * @param $tmpDir
     * @return string filename
     * @throws \Exception
     */
    private function validateXmlFile($tmpDir){
        //validate the xml file
        $xmlDocument = SharedStimulusImporter::isValidSharedStimulus($this->xmlFile);

        //parse the xml file to modify it with base64 files
        if(($xmlDocument = $this->convertEmbeddedFiles($xmlDocument)) === false){
            throw new \Exception('Unable to convert embedded files');
        }

        $filename = $tmpDir . '/sharedStimulus.xml';
        $xmlDocument->save($filename);

        return $filename;
    }


    /**
     * Search in a directory to find the xml file
     * @return bool|string the xml filename or false on failure
     */
    private function getSharedStimulusFile(){
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->getDirectory()),
            \RecursiveIteratorIterator::LEAVES_ONLY);

        /** @var $file \SplFileInfo */
        foreach($iterator as $file) {
                //check each file to see if it can be the shared stimulus file
                if($file->isFile()){
                    if(preg_match('/^[\w]/', $file->getFilename()) === 1 && $file->getExtension() === 'xml'){
                        $this->xmlFile = $file->getRealPath();
                        return $this->xmlFile;
                    }
                }
        }

        return false;
    }
}
