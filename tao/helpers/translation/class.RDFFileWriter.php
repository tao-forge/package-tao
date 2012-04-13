<?php

error_reporting(E_ALL);

/**
 * A FileWriter aiming at writing RDF files.
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package tao
 * @subpackage helpers_translation
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * A Writing class for TranslationFiles. Must be implemented by a concrete class
 * a given Translation Format such as XLIFF, PO, ... The write method must be
 * by subclasses.
 *
 * @author Jerome Bogaerts
 * @since 2.2
 * @version 1.0
 */
require_once('tao/helpers/translation/class.TranslationFileWriter.php');

/* user defined includes */
// section 10-13-1-85--345dcc7e:13543c6ca3a:-8000:0000000000003A84-includes begin
// section 10-13-1-85--345dcc7e:13543c6ca3a:-8000:0000000000003A84-includes end

/* user defined constants */
// section 10-13-1-85--345dcc7e:13543c6ca3a:-8000:0000000000003A84-constants begin
// section 10-13-1-85--345dcc7e:13543c6ca3a:-8000:0000000000003A84-constants end

/**
 * A FileWriter aiming at writing RDF files.
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package tao
 * @subpackage helpers_translation
 */
class tao_helpers_translation_RDFFileWriter
    extends tao_helpers_translation_TranslationFileWriter
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Writes the RDF file on the file system.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return mixed
     */
    public function write()
    {
        // section 10-13-1-85--345dcc7e:13543c6ca3a:-8000:0000000000003A86 begin
        $targetPath = $this->getFilePath();
        $file = $this->getTranslationFile();
        $semanticNamespaces = array('rdf' => 'http://www.w3.org/1999/02/22-rdf-syntax-ns#',
                                    'rdfs' => 'http://www.w3.org/2000/01/rdf-schema#');
        
        $xmlNS = 'http://www.w3.org/XML/1998/namespace';
        $xmlnsNS = 'http://www.w3.org/2000/xmlns/';
        
        try {
            $targetFile = new DOMDocument('1.0', 'UTF-8');
            $targetFile->formatOutput = true;
            $rdfNode = $targetFile->createElementNS($semanticNamespaces['rdf'], 'rdf:RDF');
            $targetFile->appendChild($rdfNode);
            
            $rdfNode->setAttributeNS($xmlNS, 'xml:base', $file->getBase());
            $rdfNode->setAttributeNS($xmlnsNS, 'xmlns:rdfs', $semanticNamespaces['rdfs']);
            
            $xPath = new DOMXPath($targetFile);
            $xPath->registerNamespace($semanticNamespaces['rdf'], 'rdf');
            $xPath->registerNamespace($semanticNamespaces['rdfs'], 'rdfs');
            
            foreach ($file->getTranslationUnits() as $tu){
                // Look if the predicate is a semantic namespace.
                $uri = explode('#', $tu->getPredicate());
                if (count($uri) == 2) {
                    $namespace = $uri[0] . '#';
                    $qName = $uri[1];
                    if (($searchedNS = array_search($namespace, $semanticNamespaces)) !== false) {
                        $tuNode = $targetFile->createElement("${searchedNS}:${qName}");
                        $tuNode->setAttributeNS($xmlNS, 'xml:lang', $tu->getTargetLanguage());
                        $cdataValue = (($tu->getTarget() == '') ? $tu->getSource() : $tu->getTarget());
                        $tuNodeValue = $targetFile->createCDATASection($cdataValue);
                        
                        $tuNode->appendChild($tuNodeValue);
                        // Check if an rdf:Description exists for
                        // the target of the TranslationUnit.
                        $subject = $tu->getSubject();
                        $result = $xPath->query("//rdf:Description[@rdf:about='${subject}']");
                
                        if ($result->length > 0){
                            // Append to the existing rdf:Description.
                            $existingDescription = $result->item(0);
                            $existingDescription->appendChild($tuNode);
                        }
                        else {
                            // Append to a new rdf:Description node.
                            $descriptionNode = $targetFile->createElementNS($semanticNamespaces['rdf'], 'rdf:Description');
                            $descriptionNode->setAttributeNS($semanticNamespaces['rdf'], 'rdf:about', $subject);
                            $descriptionNode->appendChild($tuNode);
                            $rdfNode->appendChild($descriptionNode);
                        }
                    }
                }
            }

            $targetFile->save($targetPath);   
        }
        catch (DOMException $e) {
            throw new tao_helpers_translation_TranslationException("An error occured while writing the RDF File at '${targetPath}'.");
        }
        // section 10-13-1-85--345dcc7e:13543c6ca3a:-8000:0000000000003A86 end
    }

} /* end of class tao_helpers_translation_RDFFileWriter */

?>