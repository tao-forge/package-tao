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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA
 *
 */

namespace oat\taoQtiItem\model\qti\metadata\imsManifest\classificationMetadata;

use oat\taoQtiItem\model\qti\metadata\simple\SimpleMetadataValue;

class ClassificationSourceMetadataValue extends SimpleMetadataValue implements ClassificationValue
{
    /**
     * ClassificationSourceMetadataValue constructor.
     *
     * @param string $resourceIdentifier
     * @param string $value
     * @param string $language
     */
    public function __construct($resourceIdentifier, $value, $language = DEFAULT_LANG)
    {
        parent::__construct($resourceIdentifier, $this->getDefaultPath(), $value, $language);
    }

    /**
     * Get the default classification source path
     */
    protected function getDefaultPath()
    {
        return [
            'http://ltsc.ieee.org/xsd/LOM#lom',
            'http://ltsc.ieee.org/xsd/LOM#classification',
            'http://ltsc.ieee.org/xsd/LOM#taxonPath',
            'http://ltsc.ieee.org/xsd/LOM#source',
            'http://ltsc.ieee.org/xsd/LOM#string'
        ];
    }

}