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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\taoOutcomeUi\helper;

use \taoResultServer_models_classes_ResponseVariable as ResponseVariable;

class ResponseVariableFormatter {

    static private function trimIdentifier($identifierString){
        return trim($identifierString, " \t\n\r\0\x0B'\"");
    }

    static private function formatStringValue($baseType, $stringValue){

        switch(strtolower($baseType)){
            case 'string':
            case 'duration':
                return $stringValue;
            case 'identifier':
                return self::trimIdentifier($stringValue);
            case 'integer':
                return intval($stringValue);
            case 'float':
                return floatval($stringValue);
            case 'boolean':
                return (trim($stringValue) === 'true' || $stringValue === true) ? true : false;
            case 'pair':
            case 'directedpair':
                $pair = explode(' ', trim($stringValue));
                if(count($pair) != 2){
                    throw new \common_Exception('invalid pair string');
                }
                return [self::trimIdentifier($pair[0]), self::trimIdentifier($pair[1])];
            case 'point':
                $pair = explode(' ', trim($stringValue));
                if(count($pair) != 2){
                    throw new \common_Exception('invalid point string');
                }
                return [intval($pair[0]), intval($pair[1])];
            default:
                throw new \common_Exception('unknown basetype');
        }
    }

    static public function formatVariableToPci(ResponseVariable $var) {
        $value = base64_decode($var->getValue());
        switch($var->getCardinality()){
            case 'single':
                if(strlen($value) === 0){
                    $formatted = ['base' => null];
                }else{
                    $formatted = ['base' => [$var->getBaseType() => self::formatStringValue($var->getBaseType(), $value)]];
                }
                break;
            case 'ordered':
            case 'multiple':
                if(strlen($value) === 0){
                    $formatted = ['list' => [$var->getBaseType() => []]];
                }else{
                    preg_match_all('/([^\[\];<>]+)/', $value, $matches);
                    $list = [];
                    foreach($matches[1] as $valueString){
                        if(empty(trim($valueString))){
                            continue;
                        }
                        $list[] = self::formatStringValue($var->getBaseType(), $valueString);
                    }
                    $formatted = ['list' => [$var->getBaseType() => $list]];
                }
                break;
            default:
                throw new \common_Exception('unknown response cardinality');
        }
        return $formatted;
    }
}