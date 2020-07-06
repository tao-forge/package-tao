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
 * Copyright (c) 2015-2020 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

declare(strict_types=1);

namespace oat\taoItems\model\pack;

use \InvalidArgumentException;
use \JsonSerializable;
use oat\tao\model\media\MediaAsset;
use oat\tao\model\media\sourceStrategy\HttpSource;
use oat\taoItems\model\pack\encoders\Encoding;
use oat\taoMediaManager\model\MediaSource;
use LogicException;
use tao_models_classes_FileNotFoundException;

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
    private static $assetTypes = ['html', 'document', 'js', 'css', 'font', 'img', 'audio', 'video', 'xinclude', 'apip'];


    /**
     * The item type
     * @var string
     */
    private $type;

    /**
     * The item data as arrays. Can be anything, just be careful of cyclic refs.
     * @var array
     */
    private $data = [];

    /**
     * The item's required assets by type
     * @var array
     */
    private $assets = [];

    /**
     * Determines what type of assets should be packed as well as packer
     * @example array('css'=>'base64')
     * @var array
     */
    protected $assetEncoders = [
        'html'     => 'none',
        'document'     => 'none',
        'js'        => 'none',
        'css'       => 'none',
        'font'      => 'none',
        'img'       => 'none',
        'audio'     => 'none',
        'video'     => 'none',
        'xinclude'  => 'none',
        'apip' => 'none',
    ];

    /**
     * Should be @import or url() processed
     * @var bool
     */
    protected $nestedResourcesInclusion = true;

    /**
     * Creates an ItemPack with the required data.
     *
     * @param string $type the item type
     * @param array $data the item data
     *
     * @throw InvalidArgumentException
     */
    public function __construct($type, $data)
    {
        if (empty($type)) {
            throw new InvalidArgumentException('Please provide and item type');
        }
        if (!is_array($data)) {
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
     * @param array $assets the list of assets' URL to load
     *
     * @throw InvalidArgumentException
     */
    public function setAssets(string $type, $assets): void
    {
        if (!is_array($assets)) {
            throw new InvalidArgumentException('Assests should be an array, "' . gettype($assets) . '" given');
        }

        foreach ($assets as $asset) {
            $this->setAsset($type, $asset);
        }
    }

    /**
     * @param string $type
     * @param string|MediaAsset $asset
     *
     * @throw InvalidArgumentException
     */
    public function setAsset(string $type, $asset): void
    {
        if (!in_array($type, self::$assetTypes)) {
            throw new InvalidArgumentException('Unknow asset type "' . $type . '", it should be either ' . implode(', ', self::$assetTypes));
        }

        /** @var Encoding $encoder */
        $encoder = EncoderService::singleton()->get($this->assetEncoders[$type]);

        $assetKey = $this->getAssetKey($asset);

        $this->assets[$type][$assetKey] = $encoder->encode($asset);
    }

    /**
     * Get item's assets of a given type.
     *
     * @param string $type the assets type, one of those who are supported
     * @return string[] the list of assets' URL to load
     */
    public function getAssets($type)
    {
        if (!array_key_exists($type, $this->assets)) {
            return [];
        }
        return $this->assets[$type];
    }

    /**
     * How to serialize the pack in JSON.
     */
    public function JsonSerialize()
    {
        return [
            'type'      => $this->type,
            'data'      => $this->data,
            'assets'    => $this->assets
        ];
    }

    /**
     * @return array
     */
    public function getAssetEncoders()
    {
        return $this->assetEncoders;
    }

    /**
     * @param array $assetEncoders
     */
    public function setAssetEncoders($assetEncoders)
    {
        foreach ($assetEncoders as $type => $encoder) {
            if ($encoder == '') {
                $this->assetEncoders[$type] = 'none';
            } else {
                $this->assetEncoders[$type] = $encoder;
            }
        }
    }

    /**
     * @return boolean
     */
    public function isNestedResourcesInclusion()
    {
        return $this->nestedResourcesInclusion;
    }

    /**
     * @param boolean $nestedResourcesInclusion
     */
    public function setNestedResourcesInclusion($nestedResourcesInclusion)
    {
        $this->nestedResourcesInclusion = (bool)$nestedResourcesInclusion;
    }

    /**
     * @param string|MediaAsset $asset
     * @return string
     * @throws tao_models_classes_FileNotFoundException
     * @throws LogicException
     */
    private function getAssetKey($asset): string
    {
        if (!$asset instanceof MediaAsset) {
            if (!is_string($asset)) {
                throw new LogicException('Item pack can only pack assets as string url or MediaAsset');
            }
            return $asset;
        }

        $mediaSource = $asset->getMediaSource();

        if ($mediaSource instanceof MediaSource || $mediaSource instanceof HttpSource) {
            return $asset->getMediaIdentifier();
        }

        return $mediaSource->getBaseName($asset->getMediaIdentifier());
    }
}
