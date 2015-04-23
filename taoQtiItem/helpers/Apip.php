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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

namespace oat\taoQtiItem\helpers;

/**
 * APIP Utility class.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class Apip
{
    /**
     * Extract the apipAccessibility element from a document.
     * 
     * The returned DOMDocument will have the apipAccessibility element as its document element.
     * If no apipAccessibility element can be extracted, null is returned.
     * 
     * @param \DOMDocument $doc
     * @return null|\DOMDocument
     */
    static public function extractApipAccessibility(\DOMDocument $doc)
    {
        $apipDoc = null;
        
        $accessibilityElts = $doc->getElementsByTagName('apipAccessibility');
        if ($accessibilityElts->length > 0) {
            $apipDoc = new \DOMDocument('1.0', 'UTF-8');
            $accessibilityElt = $accessibilityElts->item(0);
            
            $newNode = $apipDoc->importNode($accessibilityElt, true);
            $apipDoc->appendChild($newNode);
        }
        
        return $apipDoc;
    }
}