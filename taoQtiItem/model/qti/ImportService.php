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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

namespace oat\taoQtiItem\model\qti;

use oat\tao\model\media\MediaService;
use oat\taoQtiItem\model\qti\exception\ParsingException;
use oat\taoQtiItem\model\qti\exception\ExtractException;
use oat\taoQtiItem\helpers\Apip;
use oat\taoQtiItem\model\apip\ApipService;
use \tao_models_classes_GenerisService;
use \core_kernel_classes_Class;
use \core_kernel_classes_Resource;
use \core_kernel_versioning_Repository;
use \common_report_Report;
use \taoItems_models_classes_ItemsService;
use \common_exception_Error;
use \common_ext_ExtensionsManager;
use \core_kernel_classes_Property;
use \tao_models_classes_Parser;
use \tao_helpers_File;
use \helpers_File;
use \Exception;
use \DOMDocument;
use \common_exception_UserReadableException;
use \common_Logger;
use oat\taoQtiItem\model\ItemModel;
use oat\taoQtiItem\model\qti\parser\ValidationException;
use oat\taoItems\model\media\LocalItemSource;

/**
 * Short description of class oat\taoQtiItem\model\qti\ImportService
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoQTI
 
 */
class ImportService extends tao_models_classes_GenerisService
{

    /**
     * Short description of method importQTIFile
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param $qtiFile
     * @param core_kernel_classes_Class $itemClass
     * @param bool $validate
     * @param core_kernel_versioning_Repository $repository unused
     * @throws \common_Exception
     * @throws \common_ext_ExtensionException
     * @throws common_exception_Error
     * @return common_report_Report
     */
    public function importQTIFile($qtiFile, core_kernel_classes_Class $itemClass, $validate = true, core_kernel_versioning_Repository $repository = null, $extractApip = false)
    {
        $report = null;

        try {
            
            $qtiModel = $this->createQtiItemModel($qtiFile, $validate);
            
            $rdfItem = $this->createRdfItem($itemClass, $qtiModel);
            
            // If APIP content must be imported, just extract the apipAccessibility element
            // and store it along the qti.xml file.
            if ($extractApip === true) {
                $this->storeApip($qtiFile, $rdfItem);
            }
            
            $report = \common_report_Report::createSuccess(__('The IMS QTI Item was successfully imported.'), $rdfItem);
            
        } catch (ValidationException $ve) {
            $report = \common_report_Report::createFailure(__('The IMS QTI Item could not be imported.'));
            $report->add($ve->getReport());
        }

        return $report;
    }
    
    /**
     * 
     * @param core_kernel_classes_Class $itemClass
     * @param unknown $qtiModel
     * @throws common_exception_Error
     * @throws \common_Exception
     * @return unknown
     */
    protected function createRdfItem(core_kernel_classes_Class $itemClass, $qtiModel)
    {
        $itemService = taoItems_models_classes_ItemsService::singleton();
        $qtiService = Service::singleton();
        
        if (!$itemService->isItemClass($itemClass)) {
            throw new common_exception_Error('provided non Itemclass for '.__FUNCTION__);
        }
        
        $rdfItem = $itemService->createInstance($itemClass);
        
        //set the QTI type
        $itemService->setItemModel($rdfItem, new core_kernel_classes_Resource(ItemModel::MODEL_URI));
        
        //set the label
        $rdfItem->setLabel($qtiModel->getAttributeValue('title'));
        
        //save itemcontent
        if (!$qtiService->saveDataItemToRdfItem($qtiModel, $rdfItem)) {
            throw new \common_Exception('Unable to save item');
        }
        
        return $rdfItem;
    }
    
    protected function createQtiItemModel($qtiFile, $validate = true)
    {
        //validate the file to import
        $qtiParser = new Parser($qtiFile);
        
        if ($validate) {
            $basePath = common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiItem')->getDir();
            $this->validateMultiple($qtiParser, array(
                $basePath.'model/qti/data/qtiv2p1/imsqti_v2p1.xsd',
                $basePath.'model/qti/data/qtiv2p0/imsqti_v2p0.xsd',
                $basePath.'model/qti/data/apipv1p0/Core_Level/Package/apipv1p0_qtiitemv2p1_v1p0.xsd'
            ));
        
            if (!$qtiParser->isValid()) {
                $failedValidation = true;
                
                $eStrs = array();
                foreach ($qtiParser->getErrors() as $libXmlError) {
                    $eStrs[] = __('QTI-XML error at line %1$d "%2$s".', $libXmlError['line'], str_replace('[LibXMLError] ', '', trim($libXmlError['message'])));
                }
        
                // Make sure there are no duplicate...
                $eStrs = array_unique($eStrs);
        
                // Add sub-report.
                throw new ValidationException($qtiFile, $eStrs);
            }
        }
        
        $qtiItem = $qtiParser->load();
        return $qtiItem;
    }
    
    protected function createQtiManifest($manifestFile, $validate = true)
    {
        //load and validate the manifest
        $qtiManifestParser = new ManifestParser($manifestFile);
        
        if ($validate) {
            $basePath = common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiItem')->getDir();
            $this->validateMultiple($qtiManifestParser, array(
                $basePath.'model/qti/data/imscp_v1p1.xsd',
                $basePath.'model/qti/data/apipv1p0/Core_Level/Package/apipv1p0_imscpv1p2_v1p0.xsd'
            ));
        
            if(!$qtiManifestParser->isValid()) {
                tao_helpers_File::delTree($folder);
        
                $eStrs = array();
                foreach ($qtiManifestParser->getErrors() as $libXmlError) {
                    $eStrs[] = __('XML error at line %1$d "%2$s".', $libXmlError['line'], str_replace('[LibXMLError] ', '', trim($libXmlError['message'])));
                }

                // Add sub-report.
                throw new ValidationException($qtiFile, $eStrs);
            }
        }
        
        return $qtiManifestParser->load();
    }
    
    
    private function extractFiles($qtiModel)
    {
        $files = array();
        if(count($qtiModel->getBody()->getComposingElements('oat\taoQtiItem\model\qti\Xinclude')) > 0){
            //extract shared stimulus to store them into the first registered media manager
            /** @var  \oat\taoQtiItem\model\qti\Xinclude $xinclude */
            foreach($qtiModel->getBody()->getComposingElements('oat\taoQtiItem\model\qti\Xinclude') as $xinclude){
                $files[$xinclude->attr('href')] = array('md5' => md5_file(dirname($qtiFile).'/'.$xinclude->attr('href')), 'file' => dirname($qtiFile).'/'.$xinclude->attr('href'));
            }
        }
        return $files;
    }
    
    private function storeApip($qtiFile, $rdfItem)
    {
        $originalDoc = new DOMDocument('1.0', 'UTF-8');
        $originalDoc->load($qtiFile);
        
        $apipService = ApipService::singleton();
        $apipService->storeApipAccessibilityContent($rdfItem, $originalDoc);
    }

    /**
     * Excecute parser validation and stops at the first valid one, and returns the identified schema
     * 
     * @param tao_models_classes_Parser $parser
     * @param array $xsds
     * @return string
     */
    public function validateMultiple(tao_models_classes_Parser $parser, $xsds = array())
    {
        $returnValue = '';

        foreach ($xsds as $xsd) {
            $parser->validate($xsd);
            if ($parser->isValid()) {
                $returnValue = $xsd;
                break;
            }
        }

        return $returnValue;
    }

    /**
     * imports a qti package and
     * returns the number of items imported
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param $file
     * @param core_kernel_classes_Class $itemClass
     * @param bool $validate
     * @param core_kernel_versioning_Repository $repository
     * @param bool $rollbackOnError
     * @param bool $rollbackOnWarning
     * @throws Exception
     * @throws ExtractException
     * @throws ParsingException
     * @throws \common_Exception
     * @throws \common_ext_ExtensionException
     * @throws common_exception_Error
     * @return common_report_Report
     */
    public function importQTIPACKFile($file, core_kernel_classes_Class $itemClass, $validate = true, core_kernel_versioning_Repository $repository = null, $rollbackOnError = false, $rollbackOnWarning = false, $extractApip = false)
    {

        $report = new common_report_Report(common_report_Report::TYPE_SUCCESS, '');
        
        //load and validate the package
        $qtiPackageParser = new PackageParser($file);

        if ($validate) {
            $qtiPackageParser->validate();
            if (!$qtiPackageParser->isValid()) {
                throw new ParsingException('Invalid QTI package format');
            }
        }

        //extract the package
        $folder = $qtiPackageParser->extract();
        if (!is_dir($folder)) {
            throw new ExtractException();
        }

        try {
            //load the information about resources in the manifest 
            $qtiItemResources = $this->createQtiManifest($folder.'imsmanifest.xml');
            $itemService = taoItems_models_classes_ItemsService::singleton();
            $qtiService = Service::singleton();
            
            // The metadata import feature needs a DOM representation of the manifest.
            $domManifest = new DOMDocument('1.0', 'UTF-8');
            $domManifest->load($folder.'imsmanifest.xml');
            $metadataMapping = $qtiService->getMetadataRegistry()->getMapping();
            $metadataInjectors = array();
            $metadataValues = array();
            
            foreach ($metadataMapping['injectors'] as $injector) {
                $metadataInjectors[] = new $injector();
            }
            
            foreach ($metadataMapping['extractors'] as $extractor) {
                $metadataExtractor = new $extractor();
                $metadataValues = array_merge($metadataValues, $metadataExtractor->extract($domManifest));
            }
            
            $successItems = array();
            $successCount = 0;
            $itemCount = 0;
            
            $name = basename($file, '.zip');
            $name = preg_replace('/[^_]+_/', '',$name, 1);
            $sources = MediaService::singleton()->getWritableSources();
            /** @var \oat\tao\model\media\MediaManagement $source */
            $sharedStorage = array_shift($sources);
            $files = array();
            $items = array();
            foreach ($qtiItemResources as $qtiItemResource) {

                $itemCount++;

                try {
                    $qtiFile = $folder . $qtiItemResource->getFile();
                    
                    $qtiModel = $this->createQtiItemModel($qtiFile);
                    $rdfItem = $this->createRdfItem($itemClass, $qtiModel);
                    if ($extractApip) {
                        $this->storeApip($qtiFile, $rdfItem);
                    }
                    
                    $files = array_merge($files,$this->extractFiles($qtiModel));
                    
                    $local = new LocalItemSource(array('item' => $rdfItem));
                    foreach ($qtiItemResource->getAuxiliaryFiles() as $auxResource) {
                        
                        // shared storage?
                        
                        $filePath = $folder.str_replace('/', DIRECTORY_SEPARATOR, $auxResource);
                        $relPath = helpers_File::getRelPath($qtiFile, $filePath);

                        //$local->add($filePath, $fileName, $parent);
                    }

                    if ($rdfItem) {
                        $itemPath = taoItems_models_classes_ItemsService::singleton()->getItemFolder($rdfItem);
                        $itemContent = $itemService->getItemContent($rdfItem);
                        $items[] = $rdfItem;
                        foreach ($qtiItemResource->getAuxiliaryFiles() as $auxResource) {
                            // $auxResource is a relativ URL, so we need to replace the slashes with directory separators
                            $auxPath = $folder.str_replace('/', DIRECTORY_SEPARATOR, $auxResource);
                            $relPath = helpers_File::getRelPath($qtiFile, $auxPath);
                            if(!array_key_exists($relPath,$files) || is_null($sharedStorage)){

                                //prevent directory traversal:
                                $relPathSafe = str_replace('..'.DIRECTORY_SEPARATOR, '', $relPath, $count);
                                if($count){
                                    $itemContent = str_replace($relPath, $relPathSafe, $itemContent);
                                }
                        
                                $destPath = $itemPath.$relPathSafe;
                                tao_helpers_File::copy($auxPath, $destPath, true);
                                \common_Logger::i("Auxiliary file '${relPathSafe}' copied.");

                            } else {
                                \common_Logger::w('yay');
                            }
                        }
                        
                        // Finally, import metadata.
                        $this->importItemMetadata($metadataValues, $qtiItemResource, $rdfItem, $metadataInjectors);
                        
                        $itemService->setItemContent($rdfItem, $itemContent);
                        $successItems[$qtiItemResource->getIdentifier()] = $rdfItem;
                        $successCount++;
                    }

                    $msg = __('The IMS QTI Item referenced as "%s" in the IMS Manifest file was successfully imported.', $qtiItemResource->getIdentifier());
                    $report->add(common_report_Report::createSuccess($msg, $rdfItem));
                    
                } catch (ParsingException $e) {
                    $report->add(new common_report_Report(common_report_Report::TYPE_ERROR, $e->getUserMessage()));
                } catch (Exception $e) {
                    // an error occured during a specific item
                    $report->add(new common_report_Report(common_report_Report::TYPE_ERROR, __("An unknown error occured while importing the IMS QTI Package.")));
                    common_Logger::e($e->getMessage());
                }
            }

            if(!is_null($sharedStorage)){
                try{
                    $files = $this->addSharedStimulus($sharedStorage, $name, $files);

                    //modify item body with new link to shared stimulus
                    /** @var  \core_kernel_classes_Resource $item */
                    foreach ($items as $item) {

                        $itemContent = $itemService->getItemContent($item);
                        $qtiParser = new Parser($itemContent);
                        $qtiItem = $qtiParser->load();
                        /** @var  \oat\taoQtiItem\model\qti\Xinclude $xinclude */
                        foreach($qtiItem->getBody()->getComposingElements('oat\taoQtiItem\model\qti\Xinclude') as $xinclude){
                            //modify the href to link to the imported one
                            if(isset($files[$xinclude->attr('href')])){
                                $xinclude->attr('href', $files[$xinclude->attr('href')]);
                            }
                        }
                        $itemService->setItemContent($item, $qtiItem->toXML());
                    }
                }
                catch(\common_Exception $e){
                    $report->add(common_report_Report::createFailure($e->getMessage()));
                }
            }
            else{
                common_Logger::i("No media source found");
            }
            
            if ($successCount > 0) {
                // Some items were imported from the package.
                $report->setMessage(__('%d Item(s) of %d imported from the given IMS QTI Package.', $successCount, $itemCount));
                
                if ($successCount !== $itemCount) {
                    $report->setType(common_report_Report::TYPE_WARNING);
                }
            }
            else {
                $report->setMessage(__('No Items could be imported from the given IMS QTI package.'));
                $report->setType(common_report_Report::TYPE_ERROR);
            }
            
            if ($rollbackOnError === true) {
                if ($report->getType() === common_report_Report::TYPE_ERROR || $report->contains(common_report_Report::TYPE_ERROR)) {
                    $this->rollback($successItems, $report);
                }
            } elseif ($rollbackOnWarning === true) {
                if ($report->getType() === common_report_Report::TYPE_WARNING || $report->contains(common_report_Report::TYPE_WARNING)) {
                    $this->rollback($successItems, $report);
                }
            }
        } catch (ValidationException $ve) {
            tao_helpers_File::delTree($folder);
            $validationReport = \common_report_Report::createFailure("The IMS Manifest file could not be validated");
            $validationReport->add($ve->getReport());
            $report->setMessage(__("No Items could be imported from the given IMS QTI package."));
            $report->setType(common_report_Report::TYPE_ERROR);
            $report->add($validationReport);
        } catch (common_exception_UserReadableException $e) {
            $report = new common_report_Report(common_report_Report::TYPE_ERROR, __($e->getUserMessage()));
            $report->add($e);
        }

        // cleanup
        tao_helpers_File::delTree($folder);

        return $report;
    }

    /**
     * @param \oat\tao\model\MediaManagement $sharedStorage
     * @param string $name the class into which to import
     * @param array $files [ 'relPath' => ['md5' => 12fedl34, 'file' => absolute path]]
     * @return array ['relPath' => link]
     */
    private function addSharedStimulus($sharedStorage, $name, $files = array()){

        $similar = array();
        //detect similar files
        foreach($files as $file => $info){
            if(!isset($similar[$info['md5']]) || !in_array($file, $similar[$info['md5']])){
                $similar[$info['md5']][] = $file;
            }
        }

        foreach($similar as $md5 => $similars){
            //import first
            $fileInfo = $sharedStorage->add($files[$similars[0]]['file'], basename($files[$similars[0]]['file']), $name, true);
            //update all
            foreach($similars as $file){
                $files[$file] = $fileInfo['uri'];
            }
        }

        return $files;
    }
    
    /**
     * Import metadata to a given QTI Item.
     * 
     * @param oat\taoQtiItem\model\qti\metadata\MetadataValue[] $metadataValues An array of MetadataValue objects.
     * @param Resource $qtiResource The object representing the QTI Resource, from an IMS Manifest perspective.
     * @param core_kernel_classes_Resource $resource The object representing the target QTI Item in the Ontology.
     * @param oat\taoQtiItem\model\qti\metadata\MetadataInjector[] $ontologyInjectors Implementations of MetadataInjector that will take care to inject the metadata values in the appropriate Ontology Resource Properties.
     * @throws oat\taoQtiItem\model\qti\metadata\MetadataInjectionException If an error occurs while importing the metadata. 
     */
    protected function importItemMetadata(array $metadataValues, Resource $qtiResource, core_kernel_classes_Resource $resource, array $ontologyInjectors = array())
    {
        // Filter metadata values for this given item.
        $identifier = $qtiResource->getIdentifier();
        if (isset($metadataValues[$identifier]) === true) {
            
            $values = $metadataValues[$identifier];
            
            foreach ($ontologyInjectors as $injector) {
                $injector->inject($resource, array($identifier => $values));
            }
        }
    }

    /**
     * @param array $items
     * @param common_report_Report $report
     * @throws common_exception_Error
     */
    protected function rollback(array $items, common_report_Report $report) 
    {
        foreach ($items as $id => $item) {
            @taoItems_models_classes_ItemsService::singleton()->deleteItem($item);
            $report->add(new common_report_Report(common_report_Report::TYPE_WARNING, __('The IMS QTI Item referenced as "%s" in the IMS Manifest was successfully rolled back.', $id)));
        }
    }
}