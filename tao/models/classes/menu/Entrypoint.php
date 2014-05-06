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

namespace oat\tao\model\menu;

use oat\oatbox\PhpSerializable;
use tao_models_classes_accessControl_AclProxy;

class Entrypoint  implements PhpSerializable
{
    const SERIAL_VERSION = 1392821334;
    
    private $data = array();
    
    public static function fromSimpleXMLElement(\SimpleXMLElement $node) {
        $replaced = array();
        foreach ($node->xpath("replace") as $replacedNode) {
            $replaced[] = (string) $replacedNode['id'];
        }
        return new static(array(
            'id' => (string) $node['id'],
            'title' => (string) $node['title'],
            'label' => (string) $node['label'],
            'desc' => (string) $node->description,
            'url' => (string) $node['url'],
            'replace' => $replaced
        ));
    }
    
    public function __construct($data, $version = self::SERIAL_VERSION) {
        $this->data = $data;
    }
    
    public function getId() {
        return $this->data['id'];
    }
    
    public function getTitle() {
        return $this->data['title'];
    }
    
    public function getLabel() {
        return $this->data['label'];
    }
    
    public function getDescription() {
        return $this->data['desc'];
    }
    
    public function getUrl() {
        return ROOT_URL.trim($this->data['url'], '/');
    }
    
    public function getReplacedIds() {
        return $this->data['replace'];
    }
    
    public function hasAccess() {
        list($ext, $mod, $act) = explode('/', trim($this->data['url'], '/'));
        return tao_models_classes_accessControl_AclProxy::hasAccess($act, $mod, $ext);
    }    
    
    public function __toPhpCode() {
        return "new ".__CLASS__."("
            .\common_Utils::toPHPVariableString($this->data).','
            .\common_Utils::toPHPVariableString(self::SERIAL_VERSION)
        .")";
    }
}