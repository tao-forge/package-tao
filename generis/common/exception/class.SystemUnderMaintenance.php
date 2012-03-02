<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 29.02.2012, 15:14:32 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package common
 * @subpackage exception
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include common_Exception
 *
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('common/class.Exception.php');

/* user defined includes */
// section 127-0-1-1--4a441dc4:135c963290f:-8000:000000000000195E-includes begin
// section 127-0-1-1--4a441dc4:135c963290f:-8000:000000000000195E-includes end

/* user defined constants */
// section 127-0-1-1--4a441dc4:135c963290f:-8000:000000000000195E-constants begin
// section 127-0-1-1--4a441dc4:135c963290f:-8000:000000000000195E-constants end

/**
 * Short description of class common_exception_SystemUnderMaintenance
 *
 * @access public
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package common
 * @subpackage exception
 */
class common_exception_SystemUnderMaintenance
    extends common_Exception
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return mixed
     */
    public function __construct()
    {
        // section 127-0-1-1--4a441dc4:135c963290f:-8000:0000000000001961 begin
        parent::__construct(__('TAO is under maintenance'));
        // section 127-0-1-1--4a441dc4:135c963290f:-8000:0000000000001961 end
    }

} /* end of class common_exception_SystemUnderMaintenance */

?>