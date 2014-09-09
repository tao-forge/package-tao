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
 *
 */

namespace oat\qtiItemPci\controller;

use \tao_actions_CommonModule;
use \common_ext_ExtensionsManager;
use \common_exception_Error;
use \tao_helpers_File;
use \tao_helpers_Http;
use \FileUploadException;
use oat\qtiItemPci\model\CreatorRegistry;
use oat\qtiItemPci\model\CreatorPackageParser;

class PciManager extends tao_actions_CommonModule
{

    public function __construct(){
        parent::__construct();
        $this->registry = CreatorRegistry::singleton();
    }

    public function getRegisteredInteractions(){

        $returnValue = array();

        $all = $this->registry->getRegisteredInteractions();

        foreach($all as $pci){
            $returnValue[$pci['typeIdentifier']] = $this->filterInteractionData($pci);
        }

        $this->returnJson($returnValue);
    }

    protected function filterInteractionData($rawInteractionData){

        return array(
            'typeIdentifier' => $rawInteractionData['typeIdentifier'],
            'label' => $rawInteractionData['label']
        );
    }

    /**
     * Service to check if the uploaded file archive is a valid and non-existing one
     * 
     * @todo call this from the client side
     */
    public function verify(){

        $result = array(
            'valid' => false,
            'exists' => false
        );

        $file = tao_helpers_Http::getUploadedFile('content');

        $creatorPackageParser = new CreatorPackageParser($file['tmp_name']);
        $creatorPackageParser->validate();
        if($creatorPackageParser->isValid()){

            $result['valid'] = true;

            $manifest = $creatorPackageParser->getManifest();

            $result['typeIdentifier'] = $manifest['typeIdentifier'];
            $result['label'] = $manifest['label'];
            $interaction = $this->registry->get($manifest['typeIdentifier']);

            if(!is_null($interaction)){
                $result['exists'] = true;
            }
        }else{
            $result['package'] = $creatorPackageParser->getErrors();
        }

        $this->returnJson($result);
    }

    public function add(){

        //as upload may be called multiple times, we remove the session lock as soon as possible
        session_write_close();

        try{

            $file = tao_helpers_Http::getUploadedFile('content');
            $newInteraction = $this->registry->add($file['tmp_name']);

            $this->returnJson($this->filterInteractionData($newInteraction));
        }catch(FileUploadException $fe){

            $this->returnJson(array('error' => $fe->getMessage()));
        }
    }

    public function delete(){

        $typeIdentifier = $this->getRequestParameter('typeIdentifier');
        $this->registry->remove($typeIdentifier);
        $ok = true;

        $this->returnJson(array(
            'success' => $ok
        ));
    }

    public function getFile(){

        if($this->hasRequestParameter('file')){
            $file = urldecode($this->getRequestParameter('file'));
            $filePathTokens = explode('/', $file);
            $pciTypeIdentifier = array_shift($filePathTokens);
            $relPath = implode(DIRECTORY_SEPARATOR, $filePathTokens);
            $this->renderFile($pciTypeIdentifier, $relPath);
        }
    }

    private function renderFile($pciTypeIdentifier, $relPath){

        $pci = $this->registry->get($pciTypeIdentifier);
        if(is_null($pci)){
            $base = common_ext_ExtensionsManager::singleton()->getExtensionById('qtiItemPci')->getConstant('DIR_VIEWS');
            $folder = $base.'js'.DIRECTORY_SEPARATOR.'pciCreator'.DIRECTORY_SEPARATOR.$pciTypeIdentifier.DIRECTORY_SEPARATOR;
            echo '/*source from views/js/pciCreator*/'.PHP_EOL;
        }else{
            $folder = $pci['directory'];
        }

        if(tao_helpers_File::securityCheck($relPath, true)){
            $filename = $folder.$relPath;
            tao_helpers_Http::returnFile($filename);
        }else{
            throw new common_exception_Error('invalid item preview file path');
        }
    }

}