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

namespace oat\taoQtiItem\model\portableElement\common;

use oat\oatbox\service\ConfigurableService;
use oat\oatbox\service\ServiceManager;
use oat\taoQtiItem\model\portableElement\common\model\PortableElementModel;
use oat\taoQtiItem\model\portableElement\common\parser\PortableElementDirectoryParser;
use oat\taoQtiItem\model\portableElement\common\parser\PortableElementPackageParser;
use oat\taoQtiItem\model\portableElement\common\validator\PortableElementModelValidator;
use oat\taoQtiItem\model\portableElement\pci\model\PciModel;
use oat\taoQtiItem\model\portableElement\pci\validator\PciValidator;
use oat\taoQtiItem\model\portableElement\pic\model\PicModel;
use oat\taoQtiItem\model\portableElement\pic\validator\PicValidator;
use oat\taoQtiItem\model\portableElement\PortableElementRegistry;

/**
 * Factory to create components implementation based on PortableElementModel
 *
 * Class PortableElementFactory
 * @package oat\qtiItemPci\model\common
 */
class PortableElementFactory extends ConfigurableService
{
    const SERVICE_ID = 'taoQtiItem/portableElementRegistry';

    const PCI_IMPLEMENTATION = 'pciRegistry';
    const PIC_IMPLEMENTATION = 'picRegistry';

    /**
     * Get a list of available $model implementation
     * @return array
     */
    static protected function getAvailableModels()
    {
        return [
            new PciModel(),
            new PicModel()
        ];
    }

    /**
     * Get the registry associated to $model, from config
     *
     * @param PortableElementModel $model
     * @return mixed
     * @throws \common_Exception
     */
    static function getRegistry(PortableElementModel $model)
    {
        $registry = new PortableElementRegistry();
        $registry->setModel($model);
        $registry->setServiceLocator(ServiceManager::getServiceManager());
        return $registry;
    }

    /**
     * Get packageParser related to $source e.q. zipfile
     * $forceModel try only to apply a given PortableElementModel
     *
     * @param $source
     * @param PortableElementModel|null $forceModel
     * @return bool|PortableElementPackageParser
     * @throws \common_Exception
     */
    static public function getPackageParser($source, PortableElementModel $forceModel=null)
    {
        $parser = new PortableElementPackageParser($source);

        if ($forceModel) {
            if ($parser->hasValidModel($forceModel)) {
                return $parser;
            }
        } else {
            $models = self::getAvailableModels();
            foreach ($models as $model) {
                if ($parser->hasValidModel($model)) {
                    return $parser;
                }
            }
        }

        throw new \common_Exception('This zip source is not compatible neither with PCI or PIC model. Manifest and/or engine file are missing.');
    }

    /**
     * Get directoryParser related to $source e.q. directory
     *
     * @param $source
     * @param PortableElementModel|null $forceModel
     * @return bool|PortableElementDirectoryParser
     * @throws \common_Exception
     */
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
     * Get validator component associated to a model
     *
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