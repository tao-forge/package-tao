<?php

namespace oat\taoTests\models\runner\features;

use oat\oatbox\PhpSerializable;
use oat\taoTests\models\runner\plugins\TestPlugin;

/**
 * A test runner feature is a user feature that can be expressed by one or more test runner plugins.
 * They can be toggled at the delivery level.
 *
 * @author Christophe Noël <christophe@taotesting.com>
 */

abstract class TestRunnerFeature implements PhpSerializable
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string[] must match active test runner plugins Ids
     */
    private $pluginsIds;

    /**
     * @var bool Determine if the feature will be automatically enabled upon delivery creation
     */
    private $isEnabledByDefault;

    /**
     * @var $allPlugins Used to check the existence of plugins Ids
     */
    private $allPlugins;

    /**
     * @param string $id
     * @param string[] $pluginsIds
     * @param bool $isEnabledByDefault
     * @param TestPlugin[] $allPlugins
     */
    public function __construct(
        $id,
        $pluginsIds,
        $isEnabledByDefault,
        $allPlugins)
    {
        $this->id = $id;
        $this->pluginsIds = $pluginsIds;
        $this->isEnabledByDefault = $isEnabledByDefault;
        $this->allPlugins = $allPlugins;

        $this->checkData();
    }

    /**
     * Check the validity of user params
     * @throws \common_exception_InconsistentData
     */
    private function checkData() {
        if(! is_string($this->id) || empty($this->id)) {
            throw new \common_exception_InconsistentData('The test runner feature needs an id');
        }

        if(! is_array($this->pluginsIds) || empty($this->pluginsIds)) {
            throw new \common_exception_InconsistentData('The test runner feature needs some plugin id');
        }

        $this->checkPluginsIds();

        // also check that abstract methods have been implemented correctly
        if(! is_string($this->getLabel()) || empty($this->getLabel())) {
            throw new \common_exception_InconsistentData('The test runner feature needs a label');
        }

        if(! is_string($this->getDescription()) || empty($this->getDescription())) {
            throw new \common_exception_InconsistentData('The test runner feature needs a description');
        }
    }

    /**
     * Check that the content of $pluginsIds matches existing and active plugin Ids
     * @throws \common_exception_InconsistentData
     */
    private function checkPluginsIds() {
        $allPluginIds = [];
        $inactivePluginsIds = [];

        foreach ($this->allPlugins as $plugin) {
            $allPluginIds[] = $plugin->getId();
            if ($plugin->isActive() === false) {
                $inactivePluginsIds[] = $plugin->getId();
            }
        }
        foreach ($this->pluginsIds as $id) {
            if (! in_array($id, $allPluginIds)) {
                throw new \common_exception_InconsistentData('Invalid plugin Id ' . $id . ' for test runner feature ' . $this->id);
            }
            if (in_array($id, $inactivePluginsIds)) {
                throw new \common_exception_InconsistentData('Cannot include inactive plugin ' . $id . ' in test runner feature ' . $this->id);
            }
        }
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string[]
     */
    public function getPluginsIds()
    {
        return $this->pluginsIds;
    }

    /**
     * @return bool
     */
    public function isEnabledByDefault()
    {
        return $this->isEnabledByDefault;
    }

    /**
     * User-friendly localized label for the feature
     * @return string
     */
    abstract function getLabel();

    /**
     * User-friendly localized description for the feature
     * @return mixed
     */
    abstract public function getDescription();

    /**
     * (non-PHPdoc)
     * @see \oat\oatbox\PhpSerializable::__toPhpCode()
     */
    public function __toPhpCode()
    {
        return 'new '.get_class($this).'()';
    }

}