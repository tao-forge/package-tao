<?php

error_reporting(E_ALL);

/**
 * TAO - taoResults\models\classes\EventAdvancedResult\class.SymbolFactory.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 24.01.2011, 11:45:52 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Younes Djaghloul, <younes.djaghloul@tudor.lu>
 * @package taoResults
 * @subpackage models_classes_EventAdvancedResult
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include taoResults_models_classes_EventAdvancedResult_MatchingScoringToolBox
 *
 * @author Younes Djaghloul, <younes.djaghloul@tudor.lu>
 */
require_once('taoResults/models/classes/EventAdvancedResult/class.MatchingScoringToolBox.php');

/**
 * include taoResults_models_classes_EventAdvancedResult_SymbolDescription
 *
 * @author Younes Djaghloul, <younes.djaghloul@tudor.lu>
 */
require_once('taoResults/models/classes/EventAdvancedResult/class.SymbolDescription.php');

/* user defined includes */
// section 10-13-1--65--6ef728ed:12db72853fd:-8000:0000000000002B81-includes begin
// section 10-13-1--65--6ef728ed:12db72853fd:-8000:0000000000002B81-includes end

/* user defined constants */
// section 10-13-1--65--6ef728ed:12db72853fd:-8000:0000000000002B81-constants begin
// section 10-13-1--65--6ef728ed:12db72853fd:-8000:0000000000002B81-constants end

/**
 * Short description of class
 *
 * @access public
 * @author Younes Djaghloul, <younes.djaghloul@tudor.lu>
 * @package taoResults
 * @subpackage models_classes_EventAdvancedResult
 */
class taoResults_models_classes_EventAdvancedResult_SymbolFactory
{
    // --- ASSOCIATIONS ---
    // generateAssociationEnd : n    // generateAssociationEnd :     // generateAssociationEnd : 

    // --- ATTRIBUTES ---

    /**
     * Short description of attribute symbolOfPatternCollection
     *
     * @access private
     * @var array
     */
    private $symbolOfPatternCollection = array(array);

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Younes Djaghloul, <younes.djaghloul@tudor.lu>
     * @return mixed
     */
    public function __construct()
    {
        // section 10-13-1--65--6ef728ed:12db72853fd:-8000:0000000000002BB5 begin
        // section 10-13-1--65--6ef728ed:12db72853fd:-8000:0000000000002BB5 end
    }

    /**
     * create the symbol, without using SymbolDescription class
     *
     * @access public
     * @author Younes Djaghloul, <younes.djaghloul@tudor.lu>
     * @param  string symbolLetter
     * @param  string patternQuery
     * @param  string symbolComment
     * @return taoResults_models_classes_EventAdvancedResult_SymbolDescription
     */
    public static function create($symbolLetter, $patternQuery, $symbolComment = 'This symbol is ...')
    {
        $returnValue = null;

        // section 10-13-1--65--6ef728ed:12db72853fd:-8000:0000000000002BB7 begin
        // section 10-13-1--65--6ef728ed:12db72853fd:-8000:0000000000002BB7 end

        return $returnValue;
    }

    /**
     * add symbol the the list of symbols
     *
     * @access public
     * @author Younes Djaghloul, <younes.djaghloul@tudor.lu>
     * @param  SymbolDescription symbol
     * @return array
     */
    public function addSymbol( taoResults_models_classes_EventAdvancedResult_SymbolDescription $symbol)
    {
        $returnValue = array();

        // section 10-13-1--65--6ef728ed:12db72853fd:-8000:0000000000002BBD begin
        // section 10-13-1--65--6ef728ed:12db72853fd:-8000:0000000000002BBD end

        return (array) $returnValue;
    }

    /**
     * get the current collection
     *
     * @access public
     * @author Younes Djaghloul, <younes.djaghloul@tudor.lu>
     * @return array
     */
    public function getSymbolDescription()
    {
        $returnValue = array();

        // section 10-13-1--65--6ef728ed:12db72853fd:-8000:0000000000002BC0 begin
        // section 10-13-1--65--6ef728ed:12db72853fd:-8000:0000000000002BC0 end

        return (array) $returnValue;
    }

} /* end of class taoResults_models_classes_EventAdvancedResult_SymbolFactory */

?>