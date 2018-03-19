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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 *
 */
namespace oat\taoTestTaker\models;
use oat\oatbox\service\ServiceManager;
use oat\tao\model\TaoOntology;
use oat\generis\model\GenerisRdf;

/**
 * A custom subject CSV importer
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoSubjects

 */
class CsvImporter extends \tao_models_classes_import_CsvImporter
{
    /**
     * (non-PHPdoc)
     * @see tao_models_classes_import_CsvImporter::getExludedProperties()
     */
    protected function getExludedProperties()
    {
       return array_merge(parent::getExludedProperties(), array(
           GenerisRdf::PROPERTY_USER_DEFLG,
           GenerisRdf::PROPERTY_USER_ROLES,
		   TaoOntology::PROPERTY_USER_LAST_EXTENSION,
		   TaoOntology::PROPERTY_USER_FIRST_TIME,
           GenerisRdf::PROPERTY_USER_TIMEZONE
       ));
    }

    /**
     * (non-PHPdoc)
     * @see tao_models_classes_import_CsvImporter::getStaticData()
     */
    protected function getStaticData()
    {
        $lang = \tao_helpers_I18n::getLangResourceByCode(DEFAULT_LANG)->getUri();

        return array(
            GenerisRdf::PROPERTY_USER_DEFLG => $lang,
            GenerisRdf::PROPERTY_USER_TIMEZONE => TIME_ZONE,
            GenerisRdf::PROPERTY_USER_ROLES => TaoOntology::PROPERTY_INSTANCE_ROLE_DELIVERY,
        );
    }

    /**
     * (non-PHPdoc)
     * @see tao_models_classes_import_CsvImporter::getAdditionAdapterOptions()
     * @throws \common_ext_ExtensionException
     */
    protected function getAdditionAdapterOptions()
    {
        /** @var \common_ext_ExtensionsManager $extManager */
        $extManager = ServiceManager::getServiceManager()->get(\common_ext_ExtensionsManager::SERVICE_ID);
        $taoTestTaker = $extManager->getExtensionById('taoTestTaker');
        $config = $taoTestTaker->getConfig('csvImporterCallbacks');

        if (empty($config['callbacks'])){
            $returnValue = array(
                'callbacks' => array(
                    '*' => array('trim'),
                    GenerisRdf::PROPERTY_USER_PASSWORD => array('oat\taoTestTaker\models\CsvImporter::taoSubjectsPasswordEncode')
                )
            );
        } else {
            $returnValue = array(
                'callbacks' => $config['callbacks']
            );
        }

        return $returnValue;
    }

    /**
     * Wrapper for password hash
     *
     * @param  string $value
     * @return string
     */
    public static function taoSubjectsPasswordEncode($value)
    {
        return \core_kernel_users_Service::getPasswordHash()->encrypt($value);
    }

}
