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
 * Copyright (c) 2018 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\taoTests\models;

use oat\tao\model\service\ServiceFileStorage;
use oat\taoTests\models\pack\Packable;
use core_kernel_classes_Resource;

/**
 * Interface to implement by test models
 *
 * @package taoTests
 * @author Joel Bout <joel@taotesting.com>
 */
interface TestModel
{
    /**
     * Prepare the content of the test,
     * using the provided items if possible
     *
     * @param core_kernel_classes_Resource $test
     * @param array $items an array of item resources
     */
    public function prepareContent(core_kernel_classes_Resource $test, $items = []);
    
    /**
     * Delete the content of the test
     *
     * @param Resource $test
     */
    public function deleteContent(core_kernel_classes_Resource $test);
    
    /**
     * Returns all the items potenially used within the test
     *
     * @param Resource $test
     * @return array an array of item resources
     */
    public function getItems(core_kernel_classes_Resource $test);
    
    /**
     * returns the test authoring url
     *
     * @param core_kernel_classes_Resource $test the test instance
     * @return string the authoring url
     */
    public function getAuthoringUrl(core_kernel_classes_Resource $test);
    
    /**
     * Clones the content of one test to another test,
     * assumes that other test has already been cleaned (using deleteContent())
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param core_kernel_classes_Resource $source
     * @param core_kernel_classes_Resource $destination
     */
    public function cloneContent(core_kernel_classes_Resource $source, core_kernel_classes_Resource $destination);
    
    /**
     * Returns the compiler class of the test
     *
     * @return string
     */
    public function getCompilerClass();
    
    /**
     * Return the Packable implementation for the given test model.
     * Packing is an alternative to Compilation. A Packer generates the
     * data needed to run a test where the compiler creates a stand alone
     * test.
     *
     * @return Packable the packer class to instantiate
     */
    public function getPackerClass();

    /**
     * Returns a compiler instance for a given test
     * @param \core_kernel_classes_Resource $test
     * @param ServiceFileStorage $storage
     * @return \oat\tao\model\Compiler
     */
    public function getCompiler(\core_kernel_classes_Resource $test, ServiceFileStorage $storage);
}
