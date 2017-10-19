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

namespace oat\taoQtiItem\model\qti\interaction;

use oat\taoQtiItem\model\qti\ParserFactory;
use oat\taoQtiItem\model\qti\exception\QtiModelException;
use \DOMElement;
use oat\taoQtiItem\model\qti\PortableElementTrait;
use oat\taoQtiItem\model\qti\QtiNamespace;

/**
 * The PortableCustomInteraction is the class of the OAT specific PCI implementation
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10267

 */
class PortableCustomInteraction extends CustomInteraction
{
    use PortableElementTrait;

    const NS_NAME = 'pci';
    const NS_URI = 'http://www.imsglobal.org/xsd/portableCustomInteraction';

    protected $markupNs = 'html5';
    protected $properties = array();
    protected $libraries = array();
    protected $stylesheets = array();
    protected $mediaFiles = array();
    protected $typeIdentifier = '';
    protected $entryPoint = '';
    protected $version = '0.0.0';
    
    public function setTypeIdentifier($typeIdentifier){
        $this->typeIdentifier = $typeIdentifier;
    }
    
    public function setEntryPoint($entryPoint){
        $this->entryPoint = $entryPoint;
    }
    
    public function getTypeIdentifier(){
        return $this->typeIdentifier;
    }
    
    public function getEntryPoint(){
        return $this->entryPoint;
    }
    
    public function getProperties(){
        return $this->properties;
    }

    public function setProperties($properties){
        if(is_array($properties)){
            $this->properties = $properties;
        }else{
            throw new InvalidArgumentException('properties should be an array');
        }
    }

    public function getStylesheets(){
        return $this->stylesheets;
    }
    
    public function setStylesheets($stylesheets){
        $this->stylesheets = $stylesheets;
    }
    
    public function getMediaFiles(){
        return $this->mediaFiles;
    }
    
    public function setMediaFiles($mediaFiles){
        $this->mediaFiles = $mediaFiles;
    }
    
    public function getVersion(){
        return $this->version;
    }

    public function setVersion($version){
        return $this->version = $version;
    }

    public function getLibraries(){
        return $this->libraries;
    }

    public function setLibraries($libraries){
        if(is_array($libraries)){
            $this->libraries = $libraries;
        }else{
            throw new InvalidArgumentException('libraries should be an array');
        }
    }

    public function toArray($filterVariableContent = false, &$filtered = array()){
        
        $returnValue = parent::toArray($filterVariableContent, $filtered);

        $returnValue['typeIdentifier'] = $this->typeIdentifier;
        $returnValue['version'] = $this->version;
        $returnValue['entryPoint'] = $this->entryPoint;
        $returnValue['libraries'] = $this->libraries;
        $returnValue['stylesheets'] = $this->stylesheets;
        $returnValue['mediaFiles'] = $this->mediaFiles;
        $returnValue['properties'] = $this->getArraySerializedPrimitiveCollection($this->getProperties(), $filterVariableContent, $filtered);
        $returnValue['xmlns']  = $this->getNamespace()->getUri();

        return $returnValue;
    }

    public static function getTemplateQti(){
        return static::getTemplatePath().'interactions/qti.portableCustomInteraction.tpl.php';
    }
    
    protected function getTemplateQtiVariables(){

        $variables = parent::getTemplateQtiVariables();
        $variables['libraries'] = $this->libraries;
        $variables['stylesheets'] = $this->stylesheets;
        $variables['mediaFiles'] = $this->mediaFiles;
        $variables['serializedProperties'] = $this->serializePortableProperties($this->properties);
        $variables['entryPoint'] = $this->entryPoint;
        $variables['typeIdentifier'] = $this->typeIdentifier;
        $variables['markup'] = preg_replace('/<(\/)?([^!])/', '<$1'.$this->markupNs.':$2', $variables['markup']);
        $this->getRelatedItem()->addNamespace($this->markupNs, $this->markupNs);
        return $variables;
    }
    
    /**
     * Feed the pci instance with data provided in the pci dom node
     *
     * @param ParserFactory $parser
     * @param DOMElement $data
     * @throws InvalidArgumentException
     * @throws QtiModelException
     */
    public function feed(ParserFactory $parser, DOMElement $data, QtiNamespace $xmlns = null){

        $this->setNamespace($xmlns);
        $xmlnsName = $xmlns->getName();

        $pciNodes = $parser->queryXPathChildren(array('portableCustomInteraction'), $data, $xmlnsName);
        if(!$pciNodes->length) {
            $xmlnsName = '';//even if a namespace has been defined, it may not be used
            $pciNodes = $parser->queryXPathChildren(array('portableCustomInteraction'), $data, $xmlnsName);
        }
        if(!$pciNodes->length) {
            throw new QtiModelException('no oat portableCustomInteraction node found');
        }

        $typeIdentifier = $pciNodes->item(0)->getAttribute('customInteractionTypeIdentifier');
        if(empty($typeIdentifier)){
            throw new QtiModelException('the type identifier of the pci is missing');
        }else{
            $this->setTypeIdentifier($typeIdentifier);
        }
        $this->setEntryPoint($pciNodes->item(0)->getAttribute('hook'));

        $version = $pciNodes->item(0)->getAttribute('version');
        if($version){
            $this->setVersion($version);
        }

        $libNodes = $parser->queryXPathChildren(array('portableCustomInteraction', 'resources', 'libraries', 'lib'), $data, $xmlnsName);
        $libs = array();
        foreach($libNodes as $libNode){
            $libs[] = $libNode->getAttribute('id');
        }
        $this->setLibraries($libs);

        $stylesheetNodes = $parser->queryXPathChildren(array('portableCustomInteraction', 'resources', 'stylesheets', 'link'), $data, $xmlnsName);
        $stylesheets = array();
        foreach($stylesheetNodes as $styleNode){
            $stylesheets[] = $styleNode->getAttribute('href');
        }
        $this->setStylesheets($stylesheets);

        $mediaNodes = $parser->queryXPathChildren(array('portableCustomInteraction', 'resources', 'mediaFiles', 'file'), $data, $xmlnsName);
        $media = array();
        foreach($mediaNodes as $mediaNode){
            $media[] = $mediaNode->getAttribute('src');
        }
        $this->setMediaFiles($media);

        $propertyNodes = $parser->queryXPathChildren(array('portableCustomInteraction', 'properties'), $data, $xmlnsName);
        if($propertyNodes->length){
            $properties = $this->extractProperties($propertyNodes->item(0), $xmlnsName);
            $this->setProperties($properties);
        }

        $markupNodes = $parser->queryXPathChildren(array('portableCustomInteraction', 'markup'), $data, $xmlnsName);
        if($markupNodes->length){
            $markup = $parser->getBodyData($markupNodes->item(0), true, true);
            $this->setMarkup($markup);
        }
    }
}
