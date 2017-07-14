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
 * @author Mikhail Kamarouski, <kamarouski@1pt.com>
 */
namespace oat\taoItems\model\pack\encoders;

use oat\tao\model\media\MediaAsset;
use oat\taoItems\model\pack\ExceptionMissingAsset;

/**
 * Class Base64Encoder
 * Helper, encode file for embedding  using base64 algorithm
 * @package oat\taoItems\model\pack\encoders
 */
class Base64Encoder implements Encoding
{
    public function __construct()
    {
    }

    /**
     * @param string|MediaAsset $data content to encode
     *
     * @return string
     * @throws ExceptionMissingAsset
     */
    public function encode($data)
    {
        if ($data instanceof MediaAsset) {
            $mediaSource = $data->getMediaSource();
            $data = $mediaSource->getBaseName($data->getMediaIdentifier());
        }

        if (is_string($data)) {
            return base64_encode($data);
        }
        throw new ExceptionMissingAsset('Incorrect asset type - cann\'t be encoded ' . $data);
    }
}