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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

/**
 * Im- and export a compiled delivery 
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoDelivery
 * @subpackage models_classes
 */
class taoDelivery_models_classes_import_Assembler
{
    const MANIFEST_FILE = 'manifest.json';
    
    public static function importDelivery(core_kernel_classes_Class $deliveryClass, $archiveFile) {
        
        $folder = tao_helpers_File::createTempDir();
        $zip = new ZipArchive();
        if ($zip->open($archiveFile) === true) {
            if($zip->extractTo($folder)){
                $returnValue = $folder;
            }
            $zip->close();
        }
        $manifestPath = $folder.self::MANIFEST_FILE;
        if (!file_exists($manifestPath)) {
            return common_report_Report::createFailure(__('Manifest not found in assembly'));
        }
        $manifest = json_decode(file_get_contents($manifestPath), true);

        $label          = $manifest['label'];
        $serviceCall    = tao_models_classes_service_ServiceCall::fromString(base64_decode($manifest['runtime']));
        $dirs           = $manifest['dir'];
        
        $delivery = $deliveryClass->createInstance($label);
        try {
            foreach ($dirs as $id => $relPath) {
                tao_models_classes_service_FileStorage::singleton()->import($id, $folder.$relPath);
            }
            
            $contentClass = new core_kernel_classes_Class(CLASS_DELIVERY_CONTENT_ASSEMBLY);
            taoDelivery_models_classes_DeliveryService::singleton()->createContent($delivery, $contentClass);
            
            $compilationClass = new core_kernel_classes_Class(CLASS_COMPILEDDELIVERY);
            $compilationInstance = $compilationClass->createInstanceWithProperties(array(
                RDFS_LABEL                          => $label,
                PROPERTY_COMPILEDDELIVERY_DELIVERY  => $delivery,
                PROPERTY_COMPILEDDELIVERY_DIRECTORY => array_keys($dirs),
                PROPERTY_COMPILEDDELIVERY_TIME      => time(),
                PROPERTY_COMPILEDDELIVERY_RUNTIME   => $serviceCall->toOntology()
            ));
            $delivery->editPropertyValues(new core_kernel_classes_Property(PROPERTY_DELIVERY_ACTIVE_COMPILATION), $compilationInstance);
            $report = common_report_Report::createSuccess(__('Delivery "%s" successfully imported',$label), $delivery);
        } catch (Exception $e) {
            $delivery->delete();
            $report = common_report_Report::createFailure(__('Unkown error during impoort'));
        }
        return $report;
    }
    
    /**
     * export a compiled delivery into an archive
     * 
     * @param core_kernel_classes_Resource $compiledDelivery
     * @throws Exception
     * @return string
     */
    public static function exportCompiledDelivery(core_kernel_classes_Resource $compiledDelivery) {
        
        $fileName = 'assembly_'.substr(md5($compiledDelivery->getUri()), 0, 5).'.zip';
        $path = tao_helpers_File::concat(array(tao_helpers_Export::getExportPath(), $fileName));
        if(!tao_helpers_File::securityCheck($path, true)){
            throw new Exception('Unauthorized file name');
        }
        
        $zipArchive = new ZipArchive();
        if($zipArchive->open($path, ZipArchive::CREATE) !== true){
            throw new Exception('Unable to create archive at '.$path);
        }
        
        $data = array(
        	'dir' => array(),
            'label' => $compiledDelivery->getLabel()
        );
        $directories = $compiledDelivery->getPropertyValues(new core_kernel_classes_Property(PROPERTY_COMPILEDDELIVERY_DIRECTORY));
        foreach ($directories as $id) {
            $directory = tao_models_classes_service_FileStorage::singleton()->getDirectoryById($id);
            tao_helpers_File::addFilesToZip($zipArchive, $directory->getPath(), $directory->getRelativePath());
            $data['dir'][$id] = $directory->getRelativePath();
        }
        
        $runtime = $compiledDelivery->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_COMPILEDDELIVERY_RUNTIME));
        $serviceCall = tao_models_classes_service_ServiceCall::fromResource($runtime);
        $data['runtime'] = base64_encode($serviceCall->serializeToString());
        
        $content = json_encode($data);//'<?php return '.common_Utils::toPHPVariableString($data).";";
        if (!$zipArchive->addFromString(self::MANIFEST_FILE, $content)) {
            $zipArchive->close();
            unlink($path);
            throw common_Exception('Unable to add manifest to exported delivery assembly');
        }
        $zipArchive->close();
        return $path;
    }
}