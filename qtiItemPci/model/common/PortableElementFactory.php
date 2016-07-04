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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
02110-1301, USA.
 *
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\qtiItemPci\model\common;

use oat\oatbox\service\ConfigurableService;
use oat\oatbox\service\ServiceManager;
use oat\qtiItemPci\model\common\model\PortableElementModel;
use oat\qtiItemPci\model\common\parser\PortableElementDirectoryParser;
use oat\qtiItemPci\model\common\parser\PortableElementPackageParser;
use oat\qtiItemPci\model\common\validator\PortableElementModelValidator;
use oat\qtiItemPci\model\pci\model\PciModel;
use oat\qtiItemPci\model\pci\validator\PciValidator;
use oat\qtiItemPci\model\pic\model\PicModel;
use oat\qtiItemPci\model\pic\validator\PicValidator;

class PortableElementFactory extends ConfigurableService
{
    const SERVICE_ID = 'qtiItemPci/portableElementRegistry';

    const PCI_IMPLEMENTATION = 'pciRegistry';
    const PIC_IMPLEMENTATION = 'picRegistry';

    public function getRegistry(PortableElementModel $model)
    {
        switch (get_class($model)) {
            case PciModel::class :
                $registry = $this->getOption(self::PCI_IMPLEMENTATION);
                break;
            case PicModel::class :
                $registry = $this->getOption(self::PIC_IMPLEMENTATION);
                break;
            default:
                throw new \common_Exception('Unable to load registry implementation for model ' . get_class($model));
                break;
        }
        $registry->setServiceLocator(ServiceManager::getServiceManager());
        return $registry;
    }

    public function getPciRegistry()
    {
        $registry = $this->getOption(self::PCI_IMPLEMENTATION);
        $registry->setServiceLocator($this->getServiceManager());
        return $registry;
    }

    public function getPicRegistry()
    {
        $registry = $this->getOption(self::PIC_IMPLEMENTATION);
        $registry->setServiceLocator($this->getServiceManager());
        return $registry;
    }

    static protected function getAvailableModels()
    {
        return [
            new PciModel(),
            new PicModel()
        ];
    }

    static public function getPackageParser($source, PortableElementModel $forceModel=null)
    {
        $parser = new PortableElementPackageParser($source);
        $models = self::getAvailableModels();

        if ($forceModel) {
            return $parser->hasValidModel($forceModel);
        }

        foreach ($models as $model) {
            if ($parser->hasValidModel($model)) {
                return $parser;
            }
        }

        throw new \common_Exception('This zip source is not compatible neither with PCI or PIC model. Manifest and/or engine file are missing.');
    }

    static public function getDirectoryParser($source, PortableElementModel $forceModel=null)
    {
        $parser = new PortableElementDirectoryParser($source);
        $models = self::getAvailableModels();

        if ($forceModel) {
            return $parser->hasValidModel($forceModel);
        }

        foreach ($models as $model) {
            if ($parser->hasValidModel($model)) {
                return $parser;
            }
        }

        throw new \common_Exception('This directory source is not compatible neither with PCI or PIC model. Manifest and/or engine file are missing.');
    }

    /**
     * @param PortableElementModel $model
     * @return PortableElementModelValidator|PciValidator|PicValidator
     */
    static public function getValidator(PortableElementModel $model)
    {
        switch (get_class($model)) {
            case PciModel::class :
                $validator = new PciValidator();
                break;
            case PicModel::class :
                $validator = new PicValidator();
                break;
            default:
                $validator = new PortableElementModelValidator();
                break;
        }
        $validator->setModel($model);
        return $validator;
    }
}