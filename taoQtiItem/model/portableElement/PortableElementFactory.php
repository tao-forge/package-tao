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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 *
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\taoQtiItem\model\portableElement;

use oat\oatbox\service\ConfigurableService;
use oat\taoQtiItem\model\portableElement\common\exception\PortableElementInconsistencyModelException;
use oat\taoQtiItem\model\portableElement\common\parser\implementation\PortableElementDirectoryParser;
use oat\taoQtiItem\model\portableElement\common\parser\itemParser\PortableElementItemParserInterface;

/**
 * Factory to create components implementation based on PortableElementModel
 *
 * Class PortableElementFactory
 * @package oat\qtiItemPci\model\common
 */
class PortableElementFactory extends ConfigurableService
{
    const SERVICE_ID = 'taoQtiItem/PortableElementFactory';

    /**
     * Return model associated to the given string value
     *
     * @param string $modelId
     * @return PortableElement
     * @throws PortableElementInconsistencyModelException
     */
    public function getModel($modelId)
    {
        if ($this->hasOption($modelId)
            && ($implementation = $this->getOption($modelId)) instanceof PortableElement
        ) {
            return $implementation;
        }

        throw new PortableElementInconsistencyModelException('Portable element model "' . $modelId . '" not found. '.
            'Required extension might be missing');
    }

    /**
     * Return all configured models
     *
     * @return PortableElement[]
     * @throws PortableElementInconsistencyModelException
     */
    public function getModels()
    {
        $models = $this->getOptions();

        foreach ($models as $key => $model) {
            if (! $model instanceof PortableElement) {
                throw new PortableElementInconsistencyModelException('Configured model '.$key.' does not implement PortableElement');
            }
        }
        return $models;
    }

    /**
     * Return all directory parsers from configuration
     *
     * @return PortableElementDirectoryParser[]
     */
    public function getDirectoryParsers()
    {
        $parsers = array();
        $models = $this->getModels();
        foreach ($models as $key => $model) {
            if ($model->getDirectoryParser() instanceof PortableElementDirectoryParser) {
                $parsers[] = $model->getDirectoryParser();
            } else {
                \common_Logger::e('Invalid DirectoryParser for model '.$key);
            }
        }
        return $parsers;
    }

    /**
     * Return all item parsers from configuration
     *
     * @return PortableElementItemParserInterface[]
     */
    public function getItemParsers()
    {
        $parsers = array();
        $models = $this->getModels();
        foreach ($models as $model) {
            if ($model->getItemParser() instanceof PortableElementItemParserInterface) {
                $parsers[] = $model->getItemParser();
            } else {
                \common_Logger::e('Invalid ItemParser for model '.$key);
            }
        }
        return $parsers;
    }
}