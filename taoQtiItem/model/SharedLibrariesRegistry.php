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
namespace oat\taoQtiItem\model;

use common_ext_Extension;
use common_ext_ExtensionsManager;
use DOMDocument;
use DOMXPath;

class SharedLibrariesRegistry
{
    
    const CONFIG_ID = 'local_shared_libraries';
    
    private $basePath;
    
    private $baseUrl;
    
    private $extension;
    
    /**
     * Create a new LocalSharedLibraries object.
     * 
     * @param string $basePath The path of the main directory to store library files.
     * @param string $baseUrl The base URL to serve these libraries.
     */
    public function __construct($basePath, $baseUrl)
    {
        $this->setBasePath($basePath);
        $this->setBaseUrl($baseUrl);
        $this->setExtension(common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiItem'));
    }
    
    protected function setBasePath($basePath)
    {
        $this->basePath = rtrim($basePath, "\\/");
    }
    
    public function getBasePath()
    {
        return $this->basePath;
    }
    
    protected function setBaseUrl($baseUrl)
    {
        $this->baseUrl = rtrim($baseUrl, "\\/");
    }
    
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }
    
    protected function setExtension(common_ext_Extension $extension)
    {
        $this->extension = $extension;
    }
    
    protected function getExtension()
    {
        return $this->extension;
    }
    
    public function getMapping()
    {
        $mapping = $this->getExtension()->getConfig(self::CONFIG_ID);
        return is_array($mapping) ? $mapping : array();
    }
    
    protected function setMapping(array $mapping)
    {
        $this->getExtension()->setConfig(self::CONFIG_ID, $mapping);
    }
    
    public function isRegistered($id)
    {
        return array_key_exists($id, $this->getMapping());
    }
    
    public function registerFromFile($id, $path)
    {
        $basePath = $this->getBasePath();
        $baseUrl = $this->getBaseUrl();
        $finalPath = "${basePath}/${id}";
    
        $dirName = pathinfo($finalPath, PATHINFO_DIRNAME);
        $dirName = str_replace(array('css!', 'tpl!'), '', $dirName);
    
        if (is_dir($dirName) === false) {
            mkdir($dirName, 0777, true);
        }
    
        $fileBaseName = pathinfo($path, PATHINFO_BASENAME);
        $fileName = pathinfo($path, PATHINFO_FILENAME);
        $destination = "${dirName}/${fileBaseName}";
    
        // Subtract eventual css!, tpl! prefixes.
        copy($path, $destination);
    
        // Subtract $basePath from final destination.
        $mappingPath = str_replace($basePath . '/', '', $destination);
    
        $map = self::getMapping();
        $map[$id] = "${baseUrl}/${mappingPath}";
    
        $this->setMapping($map);
    }
    
    public function registerFromItem($path)
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->load($path);
        $basePath = pathinfo($dom->documentURI, PATHINFO_DIRNAME);
        
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('pci', 'http://www.imsglobal.org/xsd/portableCustomInteraction');
        $libElts = $xpath->query('//pci:lib');
        
        for ($i = 0; $i < $libElts->length; $i++) {
            $libElt = $libElts->item($i);
            
            if (($name = $libElt->getAttribute('name')) !== '') {
                // Is the library already registered?
                if ($this->isRegistered($name) === false) {
                    // So we consider to find the library at item's $basePath . $name
                    $expectedLibLocation = "${basePath}/". str_replace(array('tpl!', 'css!'), '', $name);
                    /*
                     * @todo if something goes wrong, throw an exception.
                     */ 
                    $this->registerFromFile($name, $expectedLibLocation);
                }
            }
        }
    }
}