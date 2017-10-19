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

/**
 * @author Christophe Noël <christophe@taotesting.com>
 */

namespace oat\taoQtiItem\model\qti;

class Tooltip extends Element
{

    protected static $qtiTagName = '_tooltip';
    protected $content = '';

    public function getUsedAttributes(){
        return array();
    }

    public function getSerial() {
        return '_' . parent::getSerial();
    }

    public function getContent(){
        return $this->content;
    }

    public function setContent($content){
        if(empty($content)){
            $content = strval($content);
        }
        if(is_string($content)){
            $this->content = $content;

        }else{
            throw new InvalidArgumentException('a Tooltip content can only be text');
        }
    }

    public function toArray($filterVariableContent = false, &$filtered = array()){
        $returnValue = parent::toArray($filterVariableContent, $filtered);
        $returnValue['content'] = (string) $this->content;
        return $returnValue;
    }

}