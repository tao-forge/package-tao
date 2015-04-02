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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\tao\helpers;



use oat\tao\model\media\MediaManagement;
use oat\tao\model\media\MediaSource;

class MediaRetrieval{


    /**
     * get the identifier (if one) and the relPath of a media from a path
     *
     * @param $path
     * @return array ['identifier' => xxx, 'relPath' => yyy]
     */
    public static function getLinkAndIdentifier($path){
        $url = parse_url($path);
        $identifier = '';
        $link = '';
        if(isset($url['scheme']) && $url['scheme'] === 'taomedia'){
            $identifier = (isset($url['host']))? $url['host'] : '';
            $link = (isset($url['path']))? ltrim($url['path'],'/') : '';
        }
        else{
            $link = ltrim($path,'/');
        }
        return compact('identifier', 'link');

    }

    /**
     * Check if the identifier exists in the config
     * @param $identifier
     * @param $localIds array of ids specific to the call controller
     * @return bool
     */
    protected static function isIdentifierValid($identifier){

        $configs = MediaSource::getMediaBrowserSources();
        if(isset($configs[$identifier])){
            return true;
        }


        return false;
    }

    /**
     * @param $identifier
     * @param array $localImplementation
     * @return \oat\tao\model\media\MediaBrowser
     */
    public static function getBrowserImplementation($path, $options = array()){
        $mediaInfo = self::getLinkAndIdentifier($path);
        extract($mediaInfo);

        if(!self::isIdentifierValid($identifier)){
            return false;
        }

        $mediaBrowser = MediaSource::getMediaBrowserSource($identifier);
        return new $mediaBrowser($options);
    }

    /**
     * @param $identifier
     * @param array $localImplementation
     * @return \oat\tao\model\media\MediaManagement or false if the identifier has no MediaManagement implementation
     */
    public static function getManagementImplementation($path, $options = array()){

        $impl = self::getBrowserImplementation($path, $options);

        if($impl instanceof MediaManagement){
            return $impl;
        }

        return false;
    }
}
