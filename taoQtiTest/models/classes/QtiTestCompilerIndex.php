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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA ;
 */
/**
 * @author Jean-Sébastien Conan <jean-sebastien@taotesting.com>
 */

namespace oat\taoQtiTest\models;

use oat\taoItems\model\ItemCompilerIndex;

/**
 * Class QtiTestCompilerIndex
 *
 * @package oat\taoQtiTest\models
 */
class QtiTestCompilerIndex implements ItemCompilerIndex
{
    /**
     * @var array
     */
    private $index = [];

    /**
     * @param string $id
     * @param string $language
     * @param mixed $data
     * @return $this
     */
    public function setItem($id, $language, $data)
    {
        $this->index[$language][$id] = $data;
        return $this;
    }

    /**
     * @param string $id
     * @param string $language
     * @return mixed
     */
    public function getItem($id, $language)
    {
        if (isset($this->index[$language]) && isset($this->index[$language][$id])) {
            return $this->index[$language][$id];
        }
        return null;
    }

    /**
     * @param string $data
     * @param string $language
     * @throws \common_exception_InconsistentData
     */
    public function unserialize($data, $language = null)
    {
        if (!is_string($data)) {
            throw new \common_exception_InconsistentData('The encoded index data should be provided as a string');
        }
        
        $index = json_decode($data, true);
        
        if (!is_array($index)) {
            throw new \common_exception_InconsistentData('The decoded index data should be an array');
        }
        
        if ($language) {
            $this->index[$language] = $index;
        } else {
            $this->index = $index;
        }
    }

    /**
     * @param string $language
     * @return string
     */
    public function serialize($language = null)
    {
        if ($language) {
            if (isset($this->index[$language])) {
                return json_encode($this->index[$language]);
            } else {
                return json_encode([]);
            }
        } else {
            return json_encode($this->index);
        }
    }
}
