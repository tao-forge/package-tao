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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA;
 *
 */
namespace oat\taoTests\models\runner\plugins;

use JsonSerializable;
use common_exception_InconsistentData;

/**
 * A pojo that reprensents a test runner plugin.
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
class TestPlugin implements JsonSerializable
{

    /**
     * @var string $id the plugin identifier
     */
    private $id;

    /**
     * @var string $module the plugin AMD module
     */
    private $module;

    /**
     * @var string|null $bundle the bundle the plugin belongs to
     */
    private $bundle;

    /**
     * @var string|int|null $position to sort plugings together
     */
    private $position;

    /**
     * @var string $description describes what the plugins is doing
     */
    private $description = '';

    /**
     * @var string $name a human readable plugin name
     */
    private $name = '';

    /**
     * @var boolean $active if the plugin is activated
     */
    private $active = true;

    /**
     * @var string $category the plugin belongs to a category, to group them
     */
    private $category;

    /**
     * @var string[] $tags tags to add labels to plugins
     */
    private $tags = [];


    /**
     * Create a test plugin
     * @param string $id the plugin identifier
     * @param string $module the plugin AMD module
     * @param string $category the category the plugin belongs to
     * @param array $data optionnal other properties
     */
    public function __construct ($id, $module, $category, $data = [] )
    {

        self::validateRequiredData($id, $module, $category);

        $this->id          = (string)  $id;
        $this->module      = (string)  $module;
        $this->category    = (string)  $category;

        if(isset($data['bundle'])) {
            $this->bundle  = (string)  $data['bundle'];
        }
        if(isset($data['position'])) {
            $this->position  = $data['position'];
        }
        if(isset($data['description'])) {
            $this->description  = (string) $data['description'];
        }
        if(isset($data['name'])) {
            $this->name  = (string) $data['name'];
        }
        if(isset($data['active'])) {
            $this->active = (boolean) $data['active'];
        }
        if(isset($data['tags'])) {
            $this->tags = (array) $data['tags'];
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getModule()
    {
        return $this->module;
    }

    public function getBundle()
    {
        return $this->bundle;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function isActive()
    {
        return $this->active;
    }

    public function setActive($active)
    {
        $this->active = (boolean) $active;
    }

    public function getTags()
    {
        return $this->tags;
    }

    public function hasTag($tag)
    {
        return in_array($this->tags, $tag);
    }

    /**
     * @see JsonSerializable::jsonSerialize
     */
    public function jsonSerialize()
    {
        return [
            'id'          => $this->id,
            'module'      => $this->module,
            'bundle'      => $this->bundle,
            'position'    => $this->position,
            'name'        => $this->name,
            'description' => $this->description,
            'category'    => $this->category,
            'active'      => $this->active,
            'tags'        => $this->tags
        ];
    }

    /**
     * Create a test plugin from an assoc array
     * @param array $data
     * @return TestPlugin the new instance
     * @throws common_exception_InconsistentData
     */
    public static function fromArray( array $data )
    {

        if( !isset($data['id']) || !isset($data['module']) || !isset($data['category']) ) {
            throw new common_exception_InconsistentData('The plugin requires an id, a module and a category');
        }
        if(self::validateRequiredData($data['id'], $data['module'], $data['category'])){
            return new self($data['id'], $data['module'], $data['category'], $data);
        }
        return null;
    }

    /**
     * Validate required data to construct a plugin
     * @param mixed $id
     * @param mixed $module
     * @param mixed $category
     * @return boolean true
     * @throws common_exception_InconsistentData
     */
    private static function validateRequiredData($id, $module, $category)
    {

        if(! is_string($id) || empty($id)) {
            throw new common_exception_InconsistentData('The plugin needs an id');
        }
        if(! is_string($module) || empty($module)) {
            throw new common_exception_InconsistentData('The plugin needs a module');
        }
        if(! is_string($category) || empty($category)) {
            throw new common_exception_InconsistentData('The plugin needs a category');
        }

        return true;
    }
}
