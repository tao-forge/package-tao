<?php

error_reporting(E_ALL);

/**
 * TAO - tao\helpers\translation\class.POUtils.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 06.06.2012, 11:58:16 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package tao
 * @subpackage helpers_translation
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 10-13-1-85--20e08ece:13320c7798f:-8000:0000000000003545-includes begin
// section 10-13-1-85--20e08ece:13320c7798f:-8000:0000000000003545-includes end

/* user defined constants */
// section 10-13-1-85--20e08ece:13320c7798f:-8000:0000000000003545-constants begin
// section 10-13-1-85--20e08ece:13320c7798f:-8000:0000000000003545-constants end

/**
 * Short description of class tao_helpers_translation_POUtils
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package tao
 * @subpackage helpers_translation
 */
class tao_helpers_translation_POUtils
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method sanitize
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string string
     * @param  boolean reverse
     * @return string
     */
    public static function sanitize($string, $reverse = false)
    {
        $returnValue = (string) '';

        // section 10-13-1-85--20e08ece:13320c7798f:-8000:0000000000003546 begin
		if ($reverse) {
			$smap = array('"', "\n", "\t", "\r");
			$rmap = array('\\"', '\\n"' . "\n" . '"', '\\t', '\\r');
			$returnValue = (string) str_replace($smap, $rmap, $string);
		} else {
			$smap = array('/"\s+"/', '/\\\\n/', '/\\\\r/', '/\\\\t/', '/\\\"/');
			$rmap = array('', "\n", "\r", "\t", '"');
			$returnValue = (string) preg_replace($smap, $rmap, $string);
		}
        // section 10-13-1-85--20e08ece:13320c7798f:-8000:0000000000003546 end

        return (string) $returnValue;
    }

    /**
     * Unserialize PO message comments.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string annotations The PO message comments.
     * @return array
     */
    public static function unserializeAnnotations($annotations)
    {
        $returnValue = array();

        // section -64--88-56-1--6ccfbacb:137c11aa2dd:-8000:0000000000003C03 begin
        $matches = array();
        $e = TAO_DEFAULT_ENCODING;
        
        if (preg_match_all('/(#[\.\:,\|]{0,1}\s+(?:[^\\n]*))/', $annotations, $matches) !== false){
            if (isset($matches[1]) && count($matches[1] > 0)){
                foreach ($matches[1] as $match){
                    $match = trim($match);
                    $matchLen = mb_strlen($match, $e);
                    $annotationId = null;
                    $annotationValue = null;
                    
                    switch (mb_substr($match, 1, 1, $e)){
                        case "\t":
                        case ' ':
                            // Translator comment.
                            $annotationId = tao_helpers_translation_POTranslationUnit::TRANSLATOR_COMMENTS;
                            $annotationValue = mb_substr($match, 2, $matchLen - 2, $e);
                        break;
                        
                        case '.':
                            $annotationId = tao_helpers_translation_POTranslationUnit::EXTRACTED_COMMENTS;
                            $annotationValue = mb_substr($match, 3, $matchLen - 3, $e);
                        break;
                        
                        case ':':
                            $annotationId = tao_helpers_translation_POTranslationUnit::REFERENCE;
                            $annotationValue = mb_substr($match, 3, $matchLen - 3, $e);
                        break;
                        
                        case ',':
                            $annotationId = tao_helpers_translation_POTranslationUnit::FLAGS;
                            $annotationValue = mb_substr($match, 3, $matchLen - 3, $e);
                        break;
                        
                        case '|':
                            if (($pos = mb_strpos($match, 'msgid_plural', 0, $e)) !== false){
                                $pos += mb_strlen('msgid_plural', $e) + 1;
                                $annotationId = tao_helpers_translation_POTranslationUnit::PREVIOUS_MSGID_PLURAL;
                                $annotationValue = mb_substr($match, $pos, $matchLen - $pos, $e);
                            }
                            else if(($pos = mb_strpos($match, 'msgid', 0, $e)) !== false){
                                $pos += mb_strlen('msgid', $e) + 1;
                                $annotationId = tao_helpers_translation_POTranslationUnit::PREVIOUS_MSGID;
                                $annotationValue = mb_substr($match, $pos, $matchLen - $pos, $e);
                            }
                            else if(($pos = mb_strpos($match, 'msgctxt', 0, $e)) !== false){
                                $pos += mb_strlen('msgctxt', $e) + 1;
                                $annotationId = tao_helpers_translation_POTranslationUnit::PREVIOUS_MSGCTXT;
                                $annotationValue = mb_substr($match, $pos, $matchLen - $pos, $e);
                            }
                        break;
                    }
                    
                    if ($annotationId != null && $annotationValue != null){
                        if (!isset($returnValue[$annotationId])){
                            $returnValue[$annotationId] = $annotationValue;
                        }
                        else{
                            $returnValue[$annotationId] .= "\n${annotationValue}";
                        }
                    }
                }
            }
            
        }
        else{
            throw new tao_helpers_translation_TranslationException("An error occured while unserializing annotations '${annotations}'.");
        }
        // section -64--88-56-1--6ccfbacb:137c11aa2dd:-8000:0000000000003C03 end

        return (array) $returnValue;
    }

    /**
     * Serialize an array of annotations in a PO compliant comments format.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array annotations An array of annotations where keys are annotation identifiers and values are annotation values.
     * @return string
     */
    public static function serializeAnnotations($annotations)
    {
        $returnValue = (string) '';

        // section -64--88-56-1--6ccfbacb:137c11aa2dd:-8000:0000000000003C14 begin
        // section -64--88-56-1--6ccfbacb:137c11aa2dd:-8000:0000000000003C14 end

        return (string) $returnValue;
    }

    /**
     * Append a flag to an existing PO comment flag value.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string comment A PO flag comment value in which you have to add the new flag.
     * @param  string flag The flag to add to the existing $comment.
     * @return string
     */
    public static function addFlag($comment, $flag)
    {
        $returnValue = (string) '';

        // section -64--88-56-1--6ccfbacb:137c11aa2dd:-8000:0000000000003C1A begin
        // section -64--88-56-1--6ccfbacb:137c11aa2dd:-8000:0000000000003C1A end

        return (string) $returnValue;
    }

} /* end of class tao_helpers_translation_POUtils */

?>