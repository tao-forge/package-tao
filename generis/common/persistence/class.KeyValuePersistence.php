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
 * Copyright (c) 2013-2017 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Lionel Lecaque  <lionel@taotesting.com>
 * @author Camille Moyon  <camille@taotesting.com>
 * @license GPLv2
 * @package generis
 
 *
 */
class common_persistence_KeyValuePersistence extends common_persistence_Persistence
{
    const MAX_VALUE_SIZE = 'max_value_size';
    const MAP_IDENTIFIER = 'map_identifier';

    const START_MAP_DELIMITER = 'start_map_delimiter';
    const END_MAP_DELIMITER = 'end_map_delimiter';
    const MAPPED_KEY_SEPARATOR = '%%';
    const LEVEL_SEPARATOR = '-';

    const DEFAULT_MAP_IDENTIFIER = '<<<<mapped>>>>';
    const DEFAULT_START_MAP_DELIMITER = '<<<<mappedKey>>>>';
    const DEFAULT_END_MAP_DELIMITER = '<<<</mappedKey>>>>';


    /**
     * @var int The maximum size allowed for the value
     */
    protected $size = false;

    /**
     * Set a $key with a $value
     * If $value is too large, it is split into multiple $mappedKey.
     * These new keys are serialized and stored into actual $key
     *
     * @param $key
     * @param $value
     * @param null $ttl
     * @return bool
     * @throws common_Exception If size is misconfigured
     */
    public function set($key, $value, $ttl = null)
    {
        if ($this->isLarge($value)) {
            common_Logger::t('Large value detected into KeyValue persistence. Splitting value for key : ' . $key);
            $value = $this->setLargeValue($key, $value);
        }
        return $this->getDriver()->set($key, $value, $ttl);
    }

    /**
     * Get $key from driver. If $key is split, all mapped values are retrieved and join to restore original value
     *
     * @param string $key
     * @return bool|int|null|string
     */
    public function get($key)
    {
        $value = $this->getDriver()->get($key);
        if ($this->isSplit($value)) {
            common_Logger::t('Large value detected into KeyValue persistence. Joining value for key : ' . $key);
            $value =  $this->join($key, $value);
        }
        return $value;
    }

    /**
     * Check if a key exists
     * Return false if $key is a mappedKey
     *
     * @param $key
     * @return bool
     */
    public function exists($key)
    {
        if ($this->isMappedKey($key)) {
            return false;
        } else {
            return $this->getDriver()->exists($key);
        }
    }

    /**
     * Delete a key. If key is split, all associated mapped key are deleted too
     *
     * @param $key
     * @return bool
     */
    public function del($key)
    {
        if ($this->isMappedKey($key)) {
            return false;
        }

        $success = true;
        if ($this->isSplit($key)) {
            foreach ($this->unSerializeMap($this->get($key)) as $mappedKey) {
                $success = $success && $this->getDriver()->del($mappedKey);
            }
        }
        return $success && $this->getDriver()->del($key);
    }

    /**
     * Purge the Driver if it implements common_persistence_Purgable
     * Otherwise throws common_exception_NotImplemented
     *
     * @return mixed
     * @throws common_exception_NotImplemented
     */
    public function purge()
    {
        if ($this->getDriver() instanceof common_persistence_Purgable) {
            return $this->getDriver()->purge();
        } else {
            throw new common_exception_NotImplemented("purge not implemented ");
        }
    }

    /**
     * Set a large value recursively.
     * Create a map of value (split by size range) and store the serialize map as current value
     *
     * @param $key
     * @param $value
     * @param int $level
     * @return bool
     * @throws common_Exception If size is misconfigured
     */
    protected function setLargeValue($key, $value, $level = 0, $flush = true, $toTransform = true)
    {
        if ($level > 0) {
            $key = $key . self::LEVEL_SEPARATOR . $level;
        }

        if (! $this->isLarge($value)) {
            if ($flush) {
                $this->set($key, $value);
            }
            return $value;
        }

        $map = $this->createMap($key, $value);
        foreach ($map as $mappedKey => $valuePart) {
            if ($toTransform) {
                $this->set($this->transformReferenceToMappedKey($mappedKey), $valuePart);
            } else {
                $this->set($mappedKey, $valuePart);
            }
        }

        return $this->setLargeValue($key, $this->serializeMap($map), $level + 1, $flush);
    }

    /**
     * Check if the given $value is larger than $this max size
     *
     * @param $value
     * @return bool
     * @throws common_Exception If size is misconfigured
     */
    protected function isLarge($value)
    {

        $size = $this->getSize();
        if (!$size) {
            return false;
        }
        return strlen($value) > $size;
    }

    /**
     * Cut a string into an array with $size option
     *
     * @param $value
     * @return array
     * @throws common_Exception If size is misconfigured
     */
    protected function split($value)
    {
        return str_split($value, $this->getSize());
    }

    /**
     * Join different values referenced into a map recursively
     *
     * @param $key
     * @param $value
     * @param int $level
     * @return string
     */
    protected function join($key, $value, $level = 0)
    {
        if ($level > 0) {
            $key = $key . self::LEVEL_SEPARATOR . $level;
        }

        $valueParts = [];
        foreach ($this->unSerializeMap($value) as $mappedKey) {
            $mappedKey = $this->transformReferenceToMappedKey($mappedKey);
            $valueParts[$this->getMappedKeyIndex($mappedKey, $key)] = $this->getDriver()->get($mappedKey);
        }

        uksort($valueParts, 'strnatcmp');
        $value = implode('', $valueParts);
        if ($this->isSplit($value)) {
            $value = $this->join($key, $value, $level + 1);
        }
        return $value;
    }

    /**
     * Split a large value to an array with value size lesser than required max size
     * Construct the array with index of value
     *
     * @param $key
     * @param $value
     * @return array
     * @throws common_Exception If size is misconfigured
     */
    protected function createMap($key, $value)
    {
        $splitValue = $this->split($value);
        $map = [];
        foreach ($splitValue as $index => $part) {
            $map[$key . self::MAPPED_KEY_SEPARATOR . $index] = $part;
        }
        return $map;
    }

    /**
     * Transform a map reference to an identifiable $key
     *
     * @param $key
     * @return string
     */
    protected function transformReferenceToMappedKey($key)
    {
        return $this->getStartMapDelimiter() . $key . $this->getEndMapDelimiter();
    }

    /**
     * Check if current $key is part of a map
     *
     * @param $key
     * @return bool
     */
    protected function isMappedKey($key)
    {
        return substr($key, 0, strlen($this->getStartMapDelimiter())) == $this->getStartMapDelimiter()
            && substr($key, -strlen($this->getEndMapDelimiter())) == $this->getEndMapDelimiter();
    }

    /**
     *  Get the mapped key index of a mappedKey
     *
     * @param $mappedKey
     * @param $key
     * @return bool|string
     */
    protected function getMappedKeyIndex($mappedKey, $key)
    {
        $startSize = strlen($this->getStartMapDelimiter()) - 1;
        $key = substr($key, $startSize, strrpos($key, $this->getEndMapDelimiter())-$startSize);
        return substr($mappedKey, strlen($key . self::MAPPED_KEY_SEPARATOR));
    }

    /**
     * Serialize a map to set it as a value
     *
     * @param array $map
     * @return string
     */
    protected function serializeMap(array $map)
    {
        return $this->getMapIdentifier() . json_encode(array_keys($map));
    }

    /**
     * Unserialize a map that contains references to mapped keys
     *
     * @param $map
     * @return mixed
     */
    protected function unSerializeMap($map)
    {
        return json_decode(substr_replace($map, '', 0, strlen($this->getMapIdentifier())), true);
    }

    /**
     * Check if the value was split into couple of values.
     * Identifiable by the map identifier at beginning
     *
     * @param $value
     * @return bool
     */
    protected function isSplit($value)
    {
        if (!is_string($value)) {
            return false;
        }
        return strpos($value, $this->getMapIdentifier()) === 0;
    }

    /**
     * Get the current maximum allowed size for a value
     *
     * @return int
     * @throws common_Exception If size is set
     */
    protected function getSize()
    {
        if (! $this->size) {
            $size = $this->getParam(self::MAX_VALUE_SIZE);
            if ($size !== false) {
                if (!is_int($size)) {
                    throw new common_Exception('Persistence max value size has to be an integer');
                }
                $this->size = $size - strlen($this->getMapIdentifier());
            }
        }
        return $this->size;
    }

    /**
     * Get the identifier to identify a value as split. Should be no used into beginning of none mapped key
     *
     * @return string
     */
    protected function getMapIdentifier()
    {
        return $this->getParam(self::MAP_IDENTIFIER)
            ?: self::DEFAULT_MAP_IDENTIFIER;
    }

    /**
     * Get the start-map delimiter from config, otherwise fallback to default value
     *
     * @return string
     */
    protected function getStartMapDelimiter()
    {
        return $this->getParam(self::START_MAP_DELIMITER)
            ?: self::DEFAULT_START_MAP_DELIMITER;
    }

    /**
     * Get the end-map delimiter from config, otherwise fallback to default value
     *
     * @return string
     */
    protected function getEndMapDelimiter()
    {
        return $this->getParam(self::END_MAP_DELIMITER)
            ?: self::DEFAULT_END_MAP_DELIMITER;
    }

    /**
     * Get the requested param from current parameters, otherwise throws exception
     *
     * @param $param
     * @return mixed
     */
    protected function getParam($param)
    {
        $params = $this->getParams();
        if (! isset($params[$param])) {
            return false;
        }
        return $params[$param];
    }
}