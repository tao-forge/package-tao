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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA
 *
 */
namespace oat\generis\model\kernel\persistence\file;

use oat\generis\model\data\Model;
use \common_ext_NamespaceManager;
use \common_exception_MissingParameter;
use \common_exception_Error;
use oat\generis\model\kernel\persistence\file\FileRdf;

/**
 * transitory model for the smooth sql implementation
 * 
 * @author joel bout <joel@taotesting.com>
 * @package generis
 */
class FileModel
    implements Model
{
    /**
     * Path to the rdf file
     *  
     * @var string
     */
    private $file;
    
    /**
     * Constructor of the smooth model, expects a persistence in the configuration
     * 
     * @param array $configuration
     * @throws common_exception_MissingParameter
     */
    public function __construct($configuration) {
        if (!isset($configuration['file'])) {
            throw new common_exception_MissingParameter('file', __CLASS__);
        }
        $this->file = $configuration['file']; 
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\Model::getConfig()
     */
    public function getConfig() {
        return array(
            'file' => $this->file
        );
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\Model::getRdfInterface()
     */
    public function getRdfInterface() {
        return new FileRdf($this->file);
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\Model::getRdfsInterface()
     */
    public function getRdfsInterface() {
        throw new \common_exception_NoImplementation('Rdfs interface not implemented for '.__CLASS__);
    }
    
    // helper
    
    /**
     * @param string $file
     * @throws common_exception_Error
     */
    public static function getModelIdFromXml($file) {
        $xml = simplexml_load_file($file);
        $attrs = $xml->attributes('xml', true);
        if(!isset($attrs['base']) || empty($attrs['base'])){
            throw new common_exception_Error('The namespace of '.$file.' has to be defined with the "xml:base" attribute of the ROOT node');
        }
        $namespaceUri = (string) $attrs['base'];
        $modelId = null;
        foreach (common_ext_NamespaceManager::singleton()->getAllNamespaces() as $namespace) {
            if ($namespace->getUri() == $namespaceUri) {
                $modelId = $namespace->getModelId();
            }
        }
        if (is_null($modelId)) {
            throw new common_exception_Error('The model corresponding to the namespace '.$namespaceUri.' is unknown');
        }
        return $modelId;
    }
}