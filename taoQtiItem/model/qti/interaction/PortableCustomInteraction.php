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
use oat\taoQtiItem\model\qti\interaction\CustomInteraction;
use \DOMElement;

/**
 * The QTI custom interaction is a subclass of the main QTI Interaction class
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10267

 */
class PortableCustomInteraction extends CustomInteraction
{
    
    protected $properties = array();
    protected $libraries = array();
    protected $typeIdentifier = '';
    protected $entryPoint = '';
    
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
        
        $returnValue['libraries'] = $this->libraries;
        $returnValue['properties'] = $this->properties;
        $returnValue['entryPoint'] = $this->entryPoint;
        $returnValue['typeIdentifier'] = $this->typeIdentifier;
        
        return $returnValue;
    }

    public static function getTemplateQti(){
        return static::getTemplatePath().'interactions/qti.customInteraction.tpl.php';
    }

    protected function getTemplateQtiVariables(){
        
        $variables = parent::getTemplateQtiVariables();
        
        $variables['libraries'] = $this->libraries;
        $variables['properties'] = $this->properties;
        $variables['entryPoint'] = $this->entryPoint;
        $variables['typeIdentifier'] = $this->typeIdentifier;
        
        return $variables;
    }
    
    public function feed(ParserFactory $parser, DOMElement $data){

        $ns = $parser->getPciNamespace();

        $pciNodes = $parser->queryXPathChildren(array('portableCustomInteraction'), $data, $ns);
        if($pciNodes->length){
            $typeIdentifier = $pciNodes->item(0)->getAttribute('customInteractionTypeIdentifier');
            $this->setTypeIdentifier($typeIdentifier);
            
            $entryPoint = $pciNodes->item(0)->getAttribute('entryPoint');
            $this->setEntryPoint($entryPoint);
        }

        $libNodes = $parser->queryXPathChildren(array('portableCustomInteraction', 'resources', 'libraries', 'lib'), $data, $ns);
        $libs = array();
        foreach($libNodes as $libNode){
            //@todo : get lib "href" from "id"
            $libs[$libNode->getAttribute('name')] = $libNode->getAttribute('id');
        }
        $this->setLibraries($libs);

        $propertyNodes = $parser->queryXPathChildren(array('portableCustomInteraction', 'properties'), $data, $ns);
        if($propertyNodes->length){
            $properties = $this->extractPciProperties($propertyNodes->item(0), $ns);
            $this->setProperties($properties);
        }

        $markupNodes = $parser->queryXPathChildren(array('portableCustomInteraction', 'markup'), $data, $ns);
        if($markupNodes->length){
            $markup = $parser->getBodyData($markupNodes->item(0), true);
            $this->setMarkup($markup);
        }

    }
    
    private function extractPciProperties(DOMElement $propertiesNode, $ns = ''){

        $properties = array();
        $ns = $ns ? $ns.':' : '';
        
        foreach($propertiesNode->childNodes as $prop){
            
            if($prop instanceof DOMElement){
                switch($prop->tagName){
                    case $ns.'entry':
                        $key = $prop->getAttribute('key');
                        $properties[$key] = $prop->nodeValue;
                        break;
                    case $ns.'properties':
                        $key = $prop->getAttribute('key');
                        $properties[$key] = $this->extractPciProperties($prop, $ns);
                        break;
                }
            }
        }

        return $properties;
    }
    
}