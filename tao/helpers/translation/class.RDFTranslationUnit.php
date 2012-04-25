<?php

error_reporting(E_ALL);

/**
 * TAO - tao\helpers\translation\class.RDFTranslationUnit.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 18.04.2012, 16:03:04 with ArgoUML PHP module 
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
 * A Translation Unit represents a single unit of translation of a software,
 * file, ... It has a source text in the original language and a target in which
 * text has to be translated.
 *
 * Example:
 * Source (English): The end is far away
 * Target (Yoda English): Far away the end is
 *
 * @author Jerome Bogaerts
 * @since 2.2
 * @version 1.0
 */
require_once('tao/helpers/translation/class.TranslationUnit.php');

/* user defined includes */
// section 10-13-1-85-6e73505d:1353d49e194:-8000:0000000000003A51-includes begin
// section 10-13-1-85-6e73505d:1353d49e194:-8000:0000000000003A51-includes end

/* user defined constants */
// section 10-13-1-85-6e73505d:1353d49e194:-8000:0000000000003A51-constants begin
// section 10-13-1-85-6e73505d:1353d49e194:-8000:0000000000003A51-constants end

/**
 * Short description of class tao_helpers_translation_RDFTranslationUnit
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 * @package tao
 * @subpackage helpers_translation
 */
class tao_helpers_translation_RDFTranslationUnit
    extends tao_helpers_translation_TranslationUnit
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute subject
     *
     * @access private
     * @var string
     */
    private $subject = '';

    /**
     * Short description of attribute predicate
     *
     * @access private
     * @var string
     */
    private $predicate = '';

    // --- OPERATIONS ---

    /**
     * Short description of method getSubject
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return string
     */
    public function getSubject()
    {
        $returnValue = (string) '';

        // section 10-13-1-85-6e73505d:1353d49e194:-8000:0000000000003A59 begin
        $returnValue = $this->subject;
        // section 10-13-1-85-6e73505d:1353d49e194:-8000:0000000000003A59 end

        return (string) $returnValue;
    }

    /**
     * Short description of method getPredicate
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return string
     */
    public function getPredicate()
    {
        $returnValue = (string) '';

        // section 10-13-1-85-6e73505d:1353d49e194:-8000:0000000000003A5B begin
        $returnValue = $this->predicate;
        // section 10-13-1-85-6e73505d:1353d49e194:-8000:0000000000003A5B end

        return (string) $returnValue;
    }

    /**
     * Short description of method setSubject
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string subject
     * @return mixed
     */
    public function setSubject($subject)
    {
        // section 10-13-1-85--1df98728:1353d86f548:-8000:0000000000003A60 begin
        $this->subject = $subject;
        $this->addAnnotation('subject', $subject);
        // section 10-13-1-85--1df98728:1353d86f548:-8000:0000000000003A60 end
    }

    /**
     * Short description of method setPredicate
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string predicate
     * @return mixed
     */
    public function setPredicate($predicate)
    {
        // section 10-13-1-85--1df98728:1353d86f548:-8000:0000000000003A63 begin
        $this->predicate = $predicate;
        $this->addAnnotation('predicate', $predicate);
        // section 10-13-1-85--1df98728:1353d86f548:-8000:0000000000003A63 end
    }

    /**
     * Checks whether or not a given RDFTranslationUnit has the same subject
     * value as the current instance.
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  RDFTranslationUnit translationUnit
     * @return boolean
     */
    public function hasSameTranslationUnitSubject( tao_helpers_translation_RDFTranslationUnit $translationUnit)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--60155064:1355488bb4c:-8000:0000000000003707 begin
        $returnValue = $this->getSubject() == $translationUnit->getSubject();
        // section 127-0-1-1--60155064:1355488bb4c:-8000:0000000000003707 end

        return (bool) $returnValue;
    }

    /**
     * Checks whether or not a given RDFTranslationUnit has the same predicate
     * value as the current instance.
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  RDFTranslationUnit translationUnit
     * @return boolean
     */
    public function hasSameTranslationUnitPredicate( tao_helpers_translation_RDFTranslationUnit $translationUnit)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--60155064:1355488bb4c:-8000:000000000000370E begin
        $returnValue = $this->getPredicate() == $translationUnit->getPredicate();
        // section 127-0-1-1--60155064:1355488bb4c:-8000:000000000000370E end

        return (bool) $returnValue;
    }

    /**
     * Checks wether or not that the current translation unit has the same
     * than another one. For RDFTranslationUnits, we consider that two
     * units have the same source if their source, subject, predicate and target
     * are identical.
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  TranslationUnit translationUnit A translation unit to compare.
     * @return boolean
     */
    public function hasSameTranslationUnitSource( tao_helpers_translation_TranslationUnit $translationUnit)
    {
        $returnValue = (bool) false;

        // section -64--88-56-1-f4cfebb:136c5b476f3:-8000:000000000000391A begin
        $returnValue = $this->hasSameTranslationUnitPredicate($translationUnit) &&
                       $this->hasSameTranslationUnitSubject($translationUnit) &&
                       $this->hasSameTranslationUnitTargetLanguage($translationUnit);
        // section -64--88-56-1-f4cfebb:136c5b476f3:-8000:000000000000391A end

        return (bool) $returnValue;
    }

} /* end of class tao_helpers_translation_RDFTranslationUnit */

?>