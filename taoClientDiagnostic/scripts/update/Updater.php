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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\taoClientDiagnostic\scripts\update;


class Updater extends \common_ext_ExtensionUpdater 
{

	/**
     * 
     * @param string $currentVersion
     * @return string $versionUpdatedTo
     */
    public function update($initialVersion) {
        
        $currentVersion = $initialVersion;
		if ($currentVersion == '1.0') {
			$currentVersion = '1.0.1';
		}

        if ($currentVersion == '1.0.1') {
            $accessService = \funcAcl_models_classes_AccessService::singleton();
            //grant access to item author
            $anonymous = new \core_kernel_classes_Resource('http://www.tao.lu/Ontologies/generis.rdf#AnonymousRole');
            $subject = new \core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAO.rdf#DeliveryRole');
            $accessService->revokeExtensionAccess($anonymous, 'taoClientDiagnostic');
            $accessService->grantModuleAccess($subject, 'taoClientDiagnostic', 'CompatibilityChecker');

            $currentVersion = '1.1.0';
        }

		return $currentVersion;
	}
}