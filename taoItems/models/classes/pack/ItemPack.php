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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

namespace oat\taoItems\model\pack;

use \InvalidArgumentException;
use \JsonSerializable;

/**
 * The Item Pack represents the item package data produced by the compilation.
 *
 * @package taoItems
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
class ItemPack implements JsonSerializable 
{

    /**
     * The supported assets types
     * @var string[]
     */
    private static $assetTypes = array('js', 'css', 'font', 'img');

    
    /**
     * The item type
     * @var string
     */
    private $type;

    /**
     * The item data as arrays. Can be anything, just be careful of cyclic refs.
     * @var array
     */
    private $data = array();

    /**
     * The item's required assets by type
     * @var array
     */
    private $assets = array();
  
    /**
     * Creates an ItemPack with the required data.
     * 
     * @param string $type the item type
     * @param array $data the item data
     * @throw InvalidArgumentException
     */
    public function __construct($type, $data)
    {
        if(empty($type)){
            throw new InvalidArgumentException('Please provide and item type');
        }
        if(!is_array($data)){
            throw new InvalidArgumentException('Please provide the item data as an array');
        }
        $this->type = $type;
        $this->data = $data;
    }

    /**
     * Get the item type
     * @return string the type
     */
    public function getType()
    {
        return $this->type;   
    } 
    
    /**
     * Get the item data
     * @return array the data
     */
    public function getData()
    {
        return $this->data;   
    }

    /**
     * Set item's assets of a given type to the pack.
     * 
     * @param string $type the assets type, one of those who are supported. 
     * @param string[] $assets the list of assets' URL to load
     * @throw InvalidArgumentException
     */
    public function setAssets($type, $assets)
    {
        if(!in_array($type, self::$assetTypes)){
            throw new InvalidArgumentException('Unknow asset type "' . $type . '", it should be either ' . implode(', ', self::$assetTypes));
        }
        if(!is_array($assets)){
            throw new InvalidArgumentException('Assests should be an array, "' . gettype($assets) . '" given');
        }

        $this->assets[$type] = $assets;
    }
 
    /**
     * Get item's assets of a given type.
     * 
     * @param string $type the assets type, one of those who are supported 
     * @return string[] the list of assets' URL to load
     */
    public function getAssets($type)
    {
        if(!array_key_exists($type, $this->assets)){
            return array();
        }
        return $this->assets[$type];
    }

    /**
     * How to serialize the pack in JSON.
     */
    public function JsonSerialize()
    {
        return array(
            'type'      => $this->type,
            'data'      => $this->data,
            'assets'    => $this->assets
        );
    }
}
?>
