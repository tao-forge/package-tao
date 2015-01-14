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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

namespace oat\taoQtiItem\model\pack;

use oat\taoItems\model\pack\Packable;
use oat\taoItems\model\pack\ItemPack;
use oat\taoQtiItem\model\qti\Parser as QtiParser;
use oat\taoQtiItem\model\qti\Item;
use oat\taoQtiItem\model\qti\container\Container;
use oat\taoQtiItem\model\qti\Object as QtiObject; 
use \core_kernel_classes_Resource;
use \InvalidArgumentException;
use \common_Exception;

/**
 * This class pack a QTI Item. Packing instead of compiling, aims 
 * to extract the only data of an item. Those data are used by the 
 * item runner to render the item.
 *
 * @package taoQtiItem
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
class QtiItemPacker implements Packable
{

    /**
     * The item type identifier
     * @var string
     */
    private static $itemType = 'qti';

    private $images = array();
    private $audios = array();
    private $videos = array();

    /**
     * packItem implementation for QTI
     * @see {@link Packable}
     * @throws InvalidArgumentException
     * @throws common_Exception
     */
    public function packItem(core_kernel_classes_Resource $item, $content)
    {
        $itemPack = null;

        if(!is_string($content)){
            throw new InvalidArgumentException('Item content should be a string, "'. gettype($content) .'" given' ); 
        }

        //use the QtiParser to transform the QTI XML into an assoc array representation
        try {
                
            //load content
            $qtiParser = new QtiParser($content);
            
            //validate it
            $qtiParser->validate();
            if(!$qtiParser->isValid()){
                throw new common_Exception('Invalid QTI content : ' . $qtiParser->displayErrors(false));
            }

            //parse
            $qtiItem  = $qtiParser->load();
        
            //then build the ItemPack from the parsed data
            if(!is_null($qtiItem)){
                $itemPack = new ItemPack(self::$itemType, $qtiItem->toArray()); 

                $this->loadAssets($qtiItem);

                $itemPack->setAssets('img', $this->images);
                $itemPack->setAssets('audio', $this->audios);
                $itemPack->setAssets('video', $this->videos);
            }

        } catch(common_Exception $e){
            throw new common_Exception('Unable to pack item '. $item->getUri() . ' : ' . $e->getMessage());
        }

        return $itemPack;
    }

    private function loadAssets(Item $qtiItem){

        foreach($qtiItem->getComposingElements() as $element){
            if($element instanceof Container){

                foreach($element->getElements('oat\taoQtiItem\model\qti\Img') as $img){
                    $this->addAsset('images', $img->attr('src'));
                }
        
                foreach($element->getElements('oat\taoQtiItem\model\qti\Object') as $object){
                    $this->loadObjectAssets($object);
                }
            }
            if($element instanceof QtiObject){
                $this->loadObjectAssets($element);
            }
        }
    }

    private function loadObjectAssets(QtiObject $object){

        $type = $object->attr('type');
        
        if(strpos($type, "image") !== false){
            $this->addAsset('images', $object->attr('data'));
        } 
        else if (strpos($type, "video") !== false  || strpos($type, "ogg") !== false){
            $this->addAsset('videos', $object->attr('data'));
        } 
        else if (strpos($type, "audio") !== false){
            $this->addAsset('audios', $object->attr('data'));
        }
    }

    private function addAsset($type, $uri){
        if(is_array($this->{$type}) && !empty($uri) && !in_array($uri, $this->{$type})){
            $this->{$type}[] = $uri;
        }
    }

}
