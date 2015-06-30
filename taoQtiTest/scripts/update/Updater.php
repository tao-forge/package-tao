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
 */

namespace oat\taoQtiTest\scripts\update;

/**
 *
 * @author Jean-S�bastien Conan <jean-sebastien.conan@vesperiagroup.com>
 */
class Updater extends \common_ext_ExtensionUpdater {
    
    /**
     * 
     * @param string $initialVersion
     * @return string $versionUpdatedTo
     */
    public function update($initialVersion) {

        $currentVersion = $initialVersion;
        
        // add testrunner config
        if ($currentVersion == '2.6') {

            \common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiTest')->setConfig('testRunner', array(
                'progress-indicator' => 'percentage',
                'timerWarning' => array(
                    'assessmentItemRef' => null,
                    'assessmentSection' => 300,
                    'testPart' => null
                )
            ));

            $currentVersion = '2.6.1';
        }
        
        // add testrunner review screen config
        if ($currentVersion == '2.6.1') {
            $extension = \common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiTest');
            $config = $extension->getConfig('testRunner');
            $extension->setConfig('testRunner', array_merge($config, array(
                'test-taker-review' => true,
                'test-taker-review-region' => 'left',
                'test-taker-review-section-only' => false,
                'test-taker-review-prevents-unseen' => false,
            )));

            $currentVersion = '2.6.2';
        }
   
        return $currentVersion;
    }
}
