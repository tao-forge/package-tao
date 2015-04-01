<?php
/**
 * Created by Antoine on 31/03/15
 * at 15:03
 */

namespace oat\taoItems\model;


use oat\tao\helpers\MediaRetrieval;

class ItemMediaRetrieval extends MediaRetrieval{


    public static function getBrowserImplementation($path, $options = array()){
        $browser =  parent::getBrowserImplementation($path, $options);
        if($browser === false){
            $mediaInfo = self::getLinkAndIdentifier($path);
            extract($mediaInfo);
            if(self::isIdentifierValid($identifier)){
                return new \taoItems_helpers_ResourceManager($options);
            }
        }
        return $browser;
    }

    public static function getManagementImplementation($path, $options = array()){
        $impl = self::getBrowserImplementation($path, $options);

        if(in_array('oat\tao\model\media\MediaManagement', class_implements($impl))){
            return $impl;
        }

        return false;
    }

    public static function isIdentifierValid($identifier){

        if($identifier === ''){
            return true;
        }
        return parent::isIdentifierValid($identifier);
    }

} 