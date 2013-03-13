<?php
/*  
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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * TAO - tao\helpers\translation\class.ManifestExtractor.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 25.10.2011, 02:01:25 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author firstname and lastname of author, <author@example.org>
 * @package tao
 * @subpackage helpers_translation
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * A TranslationExtractor instance extracts TranslationUnits from a given source
 * as an Item, source code, ...
 *
 * @author Jerome Bogaerts
 * @since 2.2
 * @version 1.0
 */
require_once('tao/helpers/translation/class.TranslationExtractor.php');

/* user defined includes */
// section -64--88-1-7-508c7beb:133385c71af:-8000:000000000000323F-includes begin
// section -64--88-1-7-508c7beb:133385c71af:-8000:000000000000323F-includes end

/* user defined constants */
// section -64--88-1-7-508c7beb:133385c71af:-8000:000000000000323F-constants begin
// section -64--88-1-7-508c7beb:133385c71af:-8000:000000000000323F-constants end

/**
 * Short description of class tao_helpers_translation_ManifestExtractor
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 * @package tao
 * @subpackage helpers_translation
 */
class tao_helpers_translation_ManifestExtractor
    extends tao_helpers_translation_TranslationExtractor
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method extract
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return mixed
     */
    public function extract()
    {
        // section -64--88-1-7-508c7beb:133385c71af:-8000:0000000000003241 begin
        $paths = $this->getPaths();
        $translationUnits = array();
        
        foreach ($paths as $path) {
        	// Search for a filename containing 'structure.xml'.
        	if (is_dir($path)) {
        		$files = scandir($path);
        		
        		foreach ($files as $file) {
        			$fullPath = $path . '/' . $file;
        			if (is_file($fullPath) && mb_strpos($file, 'structures.xml') !== false) {
						// Translations must be extracted from this tao manifest file.
						try{
							$xml = new SimpleXMLElement(trim(file_get_contents($fullPath)));
							if ($xml instanceof SimpleXMLElement){
								// look up for "name" attributes of structure elements.
								$nodes = $xml->xpath("//structure[@name]|//section[@name]");
								foreach ($nodes as $node) {
									if (isset($node['name'])) {
										$nodeName = (string)$node['name'];
                                        $newTranslationUnit = new tao_helpers_translation_POTranslationUnit();
                                        $newTranslationUnit->setSource($nodeName);
                                        $newTranslationUnit->addFlag('tao-public');
										$translationUnits[$nodeName] = $newTranslationUnit;
									}
								}
                                
                                // look up for "name" attributes of action elements.
                                $nodes = $xml->xpath("//action[@name]|//tree[@name]");
                                foreach ($nodes as $node) {
                                    if (isset($node['name'])) {
                                        $nodeName = (string)$node['name'];
                                        $newTranslationUnit = new tao_helpers_translation_POTranslationUnit();
                                        $newTranslationUnit->setSource($nodeName);
                                        $translationUnits[$nodeName] = $newTranslationUnit;
                                    }
                                }
								
								// look up for "description" elements.
								$nodes = $xml->xpath("//description");
								foreach ($nodes as $node) {
									if ((string)$node != '') {
									    $newTranslationUnit = new tao_helpers_translation_POTranslationUnit();
                                        $newTranslationUnit->setSource((string)$node);
                                        $newTranslationUnit->addFlag('tao-public');
										$translationUnits[(string)$node] = $newTranslationUnit;
									}
								}
							}
						}
						catch(Exception $e){}
        			}
        		}
        	} else {
        		throw new tao_helpers_translation_TranslationException("'${path}' is not a directory.");
        	}
        }
        
        $this->setTranslationUnits(array_values($translationUnits));
        // section -64--88-1-7-508c7beb:133385c71af:-8000:0000000000003241 end
    }

} /* end of class tao_helpers_translation_ManifestExtractor */

?>