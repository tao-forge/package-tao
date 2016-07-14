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
 * */

namespace oat\pciSamples\scripts\install;

use common_ext_action_InstallAction;
use oat\oatbox\service\ServiceManager;
use oat\taoQtiItem\model\portableElement\PortableElementService;

class RegisterPci extends common_ext_action_InstallAction
{
    public function __invoke($params)
    {
        $service = new PortableElementService();
        $service->setServiceLocator(ServiceManager::getServiceManager());

        $viewDir = \common_ext_ExtensionsManager::singleton()->getExtensionById('pciSamples')->getConstant('DIR_VIEWS');

        $sourceTextReader = $viewDir.implode(DIRECTORY_SEPARATOR, ['js', 'pciCreator', 'dev', 'textReaderInteraction']).DIRECTORY_SEPARATOR;
        var_dump($sourceTextReader);
        $service->registerFromDirectorySource($sourceTextReader);

        return new \common_report_Report(\common_report_Report::TYPE_SUCCESS, 'Portable Element Plugins for Qti Creator added to Tao Qti Item extension');
    }
}
