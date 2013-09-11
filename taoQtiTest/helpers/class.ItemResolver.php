<?php

use qtism\data\storage\FileResolver;
use qtism\common\ResolutionException;

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

require_once dirname(__FILE__) . '/../lib/qtism/qtism.php';

/**
 * The ItemResolver class implements the logic to resolve TAO Item URIs to
 * paths to the related QTI-XML files.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class taoQtiTest_helpers_ItemResolver extends FileResolver {
    
    /**
     * Resolve the given TAO Item URI in the path to
     * the related QTI-XML file.
     * 
     * @param string $url The URI of the TAO Item to resolve.
     * @return string The path to the related QTI-XML file.
	 * @throws ResolutionException If an error occurs during the resolution of $url.
     */
    public function resolve($url) {
        
        $taoItem = new core_kernel_classes_Resource($url);
        if ($taoItem->exists() === false) {
            $msg = "The QTI Item with URI '${url}' cannot be found.";
            throw new ResolutionException($msg);
        }
        
        // The item is retrieved from the database.
        // We can try to reach the QTI-XML file by detecting
        // where it is supposed to be located.
        $itemContentPropertyUri = TAO_ITEM_CONTENT_PROPERTY;
        $itemContentProperty = new core_kernel_classes_Property($itemContentPropertyUri);
        $itemLocation = $taoItem->getUniquePropertyValue($itemContentProperty);
        $itemLocation = new core_kernel_file_File($itemLocation->getUri());
        
        return $itemLocation->getAbsolutePath();
    }
    
}