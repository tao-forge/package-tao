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

namespace oat\taoQtiItem\model\qti\metadata\importer;

use oat\taoQtiItem\model\qti\metadata\AbstractMetadataService;
use oat\taoQtiItem\model\qti\metadata\MetadataClassLookup;
use oat\taoQtiItem\model\qti\metadata\MetadataClassLookupClassCreator;
use oat\taoQtiItem\model\qti\metadata\MetadataGuardian;
use oat\taoQtiItem\model\qti\metadata\MetadataService;

class MetadataImporter extends AbstractMetadataService
{
    /**
     * Config key to store guardians classes
     */
    const GUARDIAN_KEY     = 'guardians';

    /**
     * Config key to store classLookup classes
     */
    const CLASS_LOOKUP_KEY = 'classLookups';

    /**
     * Guard a metadata identifier at import
     *
     * Guard a metadata identifier by calling guard method of each guardians
     * If guardians have no reason to stop process, true is returned
     * If a guardian does not allow the import, the target guardian is returned
     *
     * @param $identifier
     * @return bool
     */
    public function guard($identifier)
    {
        foreach ($this->getGuardians() as $guardian) {
            if ($this->hasMetadataValue($identifier)) {
                \common_Logger::i('Guard for resource "' . $identifier . '" ...');
                if (($guard = $guardian->guard($this->getMetadataValue($identifier))) !== false) {
                    return $guard;
                }
            }
        }
        return false;
    }

    /**
     * Lookup classes for a metadata identifier at import
     *
     * Lookup classes for a metadata identifier by calling lookup method of each classLookup
     * If no lookup has been triggered, false is returned
     * If a lookup has been triggered, classLookup could apply his own process
     * Specific should be applied here, like get created classes
     * CreatedClasses params could be updated
     *
     * @param $identifier
     * @param $createdClasses
     * @return bool
     */
    public function classLookUp($identifier, &$createdClasses)
    {
        $targetClass = false;
        foreach ($this->getClassLookUp() as $classLookup) {
            if ($this->hasMetadataValue($identifier)) {
                \common_Logger::i('Target Class Lookup for resource "' . $identifier . '" ...');
                if (($targetClass = $classLookup->lookup($this->getMetadataValue($identifier))) !== false) {
                    \common_Logger::i('Class Lookup Successful. Resource "' . $identifier . '" will be stored in RDFS Class "' . $targetClass->getUri() . '".');

                    if ($classLookup instanceof MetadataClassLookupClassCreator) {
                        $createdClasses = $classLookup->createdClasses();
                    }

                    break;
                }
            }
        }
        return $targetClass;
    }

    /**
     * Register an importer instance
     *
     * Register an instance e.q. Injectors, Extractors, Guardians or LooUpClass
     * Respective interface is checked
     * Throw exception if call if not correctly formed
     *
     * @param $key
     * @param $name
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function register($key, $name)
    {
        if (empty($key) || empty($name)) {
            throw new \InvalidArgumentException(__('Register method expects $key and $name parameters'));
        }

        if (is_object($name)) {
            $name = get_class($name);
        }

        switch ($key) {
            case self::GUARDIAN_KEY:
                $this->registerInstance(self::GUARDIAN_KEY, $name, MetadataGuardian::class);
                return true;
                break;
            case self::CLASS_LOOKUP_KEY:
                $this->registerInstance(self::CLASS_LOOKUP_KEY, $name, MetadataClassLookup::class);
                return true;
                break;
        }
        return parent::register($key, $name);
    }

    public function unregister($key, $name)
    {
        if (empty($key) || empty($name)) {
            throw new \common_Exception();
        }

        if (is_object($name)) {
            $name = get_class($name);
        }

        switch ($key) {
            case self::GUARDIAN_KEY:
                $this->unregisterInstance(self::GUARDIAN_KEY, $name);
                return true;
                break;
            case self::CLASS_LOOKUP_KEY:
                $this->unregisterInstance(self::CLASS_LOOKUP_KEY, $name);
                return true;
                break;
        }
        return parent::unregister($key, $name);
    }

    /**
     * Allow to register, into the config, the current importer service
     */
    protected function registerService()
    {
        if ($this->getServiceLocator()->has(MetadataService::SERVICE_ID)) {
            $metadataService = $this->getServiceLocator()->get(MetadataService::SERVICE_ID);
        } else {
            $metadataService = $this->getServiceManager()->build(MetadataService::class);
        }
        $metadataService->setOption(MetadataService::IMPORTER_KEY, $this);
        $this->getServiceManager()->register(MetadataService::SERVICE_ID, $metadataService);
    }

    /**
     * Return all guardians stored into config
     *
     * @return MetadataGuardian[]
     */
    protected function getGuardians()
    {
        return $this->getInstances(self::GUARDIAN_KEY, MetadataGuardian::class);
    }

    /**
     * Return all classLookup stored into config
     *
     * @return MetadataClassLookup[]
     */
    protected function getClassLookUp()
    {
        return $this->getInstances(self::CLASS_LOOKUP_KEY, MetadataClassLookup::class);
    }
}