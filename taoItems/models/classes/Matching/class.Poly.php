<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/Matching/class.Poly.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 15.12.2010, 14:07:05 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_Matching
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include taoItems_models_classes_Matching_Shape
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('taoItems/models/classes/Matching/class.Shape.php');

/* user defined includes */
// section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002CBA-includes begin
// section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002CBA-includes end

/* user defined constants */
// section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002CBA-constants begin
// section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002CBA-constants end

/**
 * Short description of class taoItems_models_classes_Matching_Poly
 *
 * @access public
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_Matching
 */
class taoItems_models_classes_Matching_Poly
    extends taoItems_models_classes_Matching_Shape
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute points
     *
     * @access protected
     * @var array
     */
    protected $points = array();

    // --- OPERATIONS ---

    /**
     * Short description of method contains
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Variable var
     * @return boolean
     */
    public function contains( taoItems_models_classes_Matching_Variable $var)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002CBE begin
        
        list ($point_x, $point_y) = $var->getValue();

        // Algorithm from http://jsfromhell.com/math/is-point-in-poly
        for($c = false, $i = -1, $l = count($this->points), $j = $l - 1; ++$i < $l; $j = $i) {
            list ($point_i_x, $point_i_y) = $this->points[$i]->getValue();
            list ($point_j_x, $point_j_y) = $this->points[$j]->getValue();

            ( (($point_i_y->getValue() <= $point_y->getValue()) && ($point_y->getValue() < $point_j_y->getValue()))
                || (($point_j_y->getValue() <= $point_y->getValue() ) && ($point_y->getValue() < $point_i_y->getValue()))
            )
            && ($point_x->getValue() < ( ($point_j_x->getValue() - $point_i_x->getValue()) * ($point_y->getValue() - $point_i_y->getValue()) / ($point_j_y->getValue() - $point_i_y->getValue()) + $point_i_x->getValue()))
            && ($c = !$c);
        }
        
        $returnValue = $c;
        
        // section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002CBE end

        return (bool) $returnValue;
    }

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  data
     * @return mixed
     */
    public function __construct(   $data)
    {
        // section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002CC4 begin
                
        if (isset($data->type)){
            $this->setType ($data->type);
        }
        
        foreach ($data->points as $point){
            array_push( $this->points, taoItems_models_classes_Matching_VariableFactory::create($point));
        }
        
        // section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002CC4 end
    }

    /**
     * Short description of method getPoints
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return array
     */
    public function getPoints()
    {
        $returnValue = array();

        // section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002CD4 begin
        
        return $this->points;
        
        // section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002CD4 end

        return (array) $returnValue;
    }

} /* end of class taoItems_models_classes_Matching_Poly */

?>