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

class Section extends MenuElement implements PhpSerializable
{

    const SERIAL_VERSION = 1392821334;
    
    const POLICY_MERGE = 'merge';
    
    const POLICY_OVERRIDE = 'override';

    private $data = array();

    private $trees = array();

    private $actions = array();

    public static function fromSimpleXMLElement(\SimpleXMLElement $node)
    {
        $data = array(
            'id'   => (string)$node['id'],
            'name' => (string)$node['name'],
            'url'  => (string)$node['url'],
            'policy' => isset($node['policy']) ? (string)$node['policy'] : self::POLICY_MERGE,
        );

        $trees = array();
        foreach ($node->xpath("trees/tree") as $treeNode) {
            $trees[] = Tree::fromSimpleXMLElement($treeNode);
        }

        $actions = array();
        foreach ($node->xpath("actions/action") as $actionNode) {
            $actions[] = Action::fromSimpleXMLElement($actionNode);
        }



        return new static($data, $trees, $actions);
    }

    public function __construct($data, $trees, $actions, $version = self::SERIAL_VERSION)
    {
        parent::__construct($data['id'], $version);
        $this->data    = $data;
        $this->trees   = $trees;
        $this->actions = $actions;

        $this->migrateDataFromLegacyFormat();
    }

    public function getUrl()
    {
        return $this->data['url'];
    }

    public function getName()
    {
        return $this->data['name'];
    }
    
    /**
     * Policy on how to deal with existing structures
     * 
     * Only merge or override are currently supported
     * 
     * @return string
     */
    public function getPolicy()
    {
        return $this->data['policy'];
    }
    
    public function getTrees()
    {
        return $this->trees;
    }

    public function addTree(Tree $tree)
    {
        $this->trees[] = $tree;
    }

    public function getActions()
    {
        return $this->actions;
    }

    public function addAction(Action $action)
    {
        $this->actions[] = $action;
    }

    /**
     * @param string $groupId
     * @return array
     */
    public function getActionsByGroup($groupId)
    {
        $actions = array();
        foreach ($this->getActions() as $action) {
            if ($action->getGroup() === $groupId) {
                $actions[] = $action;
            }
        };
        return $actions;
    }

    /**
     * Enables sections to be backward compatible with the structure format 
     * (before some actions were missing as they were defined in the tree part).
     * legacy format : tao <= 2.6.x
     * this method should be deprecated from 2.8.0 / 3.0.0
     */
    private function migrateDataFromLegacyFormat(){

        if(count($this->trees) > 0){

            //tree attributes to be migrated.
           $mapping = array(
              'editClassUrl' => array(
                'attr'   => 'selectClass',
                'action' => array(
                    'name'    => 'edit class',
                    'group'   => 'none',
                    'context' => 'class',
                    'binding' => 'load'
                ) 
              ),
              'editInstanceUrl' => array(
                'attr'   => 'selectInstance',
                'action' => array(
                    'name'    => 'edit instance',
                    'group'   => 'none',
                    'context' => 'instance',
                    'binding' => 'load'
                ) 
              ),
              'addInstanceUrl'  => array(
                'attr'   => 'addInstance',
                'action' => array(
                    'name'    => 'add instance',
                    'group'   => 'none',
                    'context' => 'instance',
                    'binding' => 'instantiate'
                ) 
              ),
              'addSubClassUrl'  => array(
                'attr'   => 'addClass',
                'action' => array(
                    'name'    => 'add class',
                    'group'   => 'none',
                    'context' => 'class',
                    'binding' => 'subClass'
                ) 
              ),
              'deleteUrl' => array(
                'attr'   => 'addClass',
                'action' => array(
                    'name'    => 'add class',
                    'group'   => 'none',
                    'context' => 'class',
                    'binding' => 'subClass'
                ) 
              ),
              'moveInstanceUrl' => array(
                'attr'   => 'moveInstance',
                'action' => array(
                    'name'    => 'move',
                    'group'   => 'none',
                    'context' => 'instance',
                    'binding' => 'moveNode'
                ) 
              )
           );

           foreach($this->trees as $index => $tree){
               $needMigration = false;
               $treeAttributes = $tree->getAttributes();
            
               //check if this attribute needs a migration
               foreach($treeAttributes as $attr){
                    if(array_key_exists($attr, $mapping)){
                        $needMigration = true;
                        break;
                    } 
               }
               if($needMigration){
                    $newData = array();

                    //migrate the tree
                    foreach($treeAttributes as $attr){
                        //if the attribute belongs to the mapping
                        if(array_key_exists($attr, $mapping)){
                            $url = $tree->get($attr);
                            $actionName = false;
    
                            //try to find an action with the same url
                            foreach($this->actions as $action){
                                if($action->getUrl() == $url){
                                    $actionName = $action->getName();
                                    break;
                                }
                            }
                            if($actionName){
                                $newData[$mapping[$attr]['attr']] = $actionName;
                            } else {
    
                                //otherwise create a new action from the mapping
                                $newData[$mapping[$attr]['attr']] = $mapping[$attr]['action']['name'];
                                $actionData = $mapping[$attr]['action'];
                                $actionData['url'] = $url;
                                $this->actions[] = new Action($actionData); 
                            }
                        } else {
                            $newData[$attr] = $tree->get($attr);
                        }
                    }
    
                    //the tree is replaced
                    $this->trees[$index] = new Tree($newData);
               }
           }
        }
    }


    public function __toPhpCode()
    {
        return "new " . __CLASS__ . "("
        . \common_Utils::toPHPVariableString($this->data) . ','
        . \common_Utils::toPHPVariableString($this->trees) . ','
        . \common_Utils::toPHPVariableString($this->actions) . ','
        . \common_Utils::toPHPVariableString(self::SERIAL_VERSION)
        . ")";
    }
}
