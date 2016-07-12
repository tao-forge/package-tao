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

namespace oat\taoQtiItem\model\portableElement\common\parser;

use oat\taoQtiItem\model\portableElement\common\model\PortableElementModel;
use oat\taoQtiItem\model\portableElement\common\PortableElementModelTrait;
use oat\taoQtiItem\model\qti\exception\ExtractException;
use oat\taoQtiItem\model\qti\PackageParser;
use oat\taoQtiItem\helpers\QtiPackage;
use common_Exception;
use \ZipArchive;

/**
 * Parser of a QTI PCI package
 * A PCI package must contain a manifest pciCreator.json in the root as well as a pciCreator.js creator file
 *
 * @package qtiItemPci
 */
class PortableElementPackageParser extends PackageParser
{
    use PortableElementModelTrait;

    /**
     * Validate the zip package
     *
     * @param string $schema
     * @return bool
     * @throws common_Exception
     */
    public function validate($schema = '')
    {
        if (!QtiPackage::isValidZip($this->source)) {
            throw new common_Exception('Source package is not a valid zip.');
        }

        $zip = new ZipArchive();
        $zip->open($this->source, ZIPARCHIVE::CHECKCONS);

        $definitionFiles = $this->getModel()->getDefinitionFiles();
        foreach ($definitionFiles as $file) {
            if ($zip->locateName($file) === false) {
                throw new common_Exception('A portable element package must contains a "' . $file . '" file at the root of the archive');
            }
        }

        $zip->close();

        $this->setModel($this->getModel()->exchangeArray($this->getManifestContent()));
        return true;
    }

    /**
     * Extract zip package into temp directory
     *
     * @return string Name of source directory
     * @throws ExtractException
     * @throws \common_exception_Error
     */
    public function extract()
    {
        $source = parent::extract();
        if(!is_dir($source)){
            throw new ExtractException('Unable to find a valid directory of extracted package.');
        }
        return $source;
    }

    public function getManifestContent()
    {
        /** get Manifest */
        if (($handle = fopen('zip://' . $this->source . '#' . $this->getModel()->getManifestName(), 'r')) === false) {
            throw new common_Exception('Unable to open the ZIP file located at: ' . $this->source);
        }

        $content = '';
        while(!feof($handle)){
            $content .= fread($handle, 8192);
        }
        fclose($handle);

        $content = json_decode($content, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            return $content;
        }
        throw new common_Exception('Portable element manifest is not a valid json file.');
    }

    public function hasValidModel(PortableElementModel $model)
    {
        $this->setModel($model);
        try {
            if ($this->validate()) {
                return true;
            }
        } catch (\common_Exception $e) {}
        return false;
    }
}
