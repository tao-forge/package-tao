<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/QTI/class.Service.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 19.08.2010, 17:27:28 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include tao_models_classes_Service
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('tao/models/classes/class.Service.php');

/* user defined includes */
// section 127-0-1-1-25600304:12a5c17a5ca:-8000:00000000000024A8-includes begin
// section 127-0-1-1-25600304:12a5c17a5ca:-8000:00000000000024A8-includes end

/* user defined constants */
// section 127-0-1-1-25600304:12a5c17a5ca:-8000:00000000000024A8-constants begin
// section 127-0-1-1-25600304:12a5c17a5ca:-8000:00000000000024A8-constants end

/**
 * Short description of class taoItems_models_classes_QTI_Service
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI
 */
class taoItems_models_classes_QTI_Service
    extends tao_models_classes_Service
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method getItemById
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string id
     * @return taoItems_models_classes_QTI_Item
     */
    public function getItemById($id)
    {
        $returnValue = null;

        // section 127-0-1-1-25600304:12a5c17a5ca:-8000:00000000000024A9 begin
        // section 127-0-1-1-25600304:12a5c17a5ca:-8000:00000000000024A9 end

        return $returnValue;
    }

    /**
     * Short description of method getInteractionById
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string id
     * @return taoItems_models_classes_QTI_Interaction
     */
    public function getInteractionById($id)
    {
        $returnValue = null;

        // section 127-0-1-1-25600304:12a5c17a5ca:-8000:00000000000024C3 begin
        // section 127-0-1-1-25600304:12a5c17a5ca:-8000:00000000000024C3 end

        return $returnValue;
    }

    /**
     * Short description of method getResponseById
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string id
     * @return taoItems_models_classes_QTI_Response
     */
    public function getResponseById($id)
    {
        $returnValue = null;

        // section 127-0-1-1--272f4da0:12a899718bf:-8000:00000000000024D1 begin
        // section 127-0-1-1--272f4da0:12a899718bf:-8000:00000000000024D1 end

        return $returnValue;
    }

    /**
     * Short description of method getDataById
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string id
     * @param  string type
     * @return taoItems_models_classes_QTI_Data
     */
    public function getDataById($id, $type = '')
    {
        $returnValue = null;

        // section 127-0-1-1--272f4da0:12a899718bf:-8000:00000000000024E1 begin
        // section 127-0-1-1--272f4da0:12a899718bf:-8000:00000000000024E1 end

        return $returnValue;
    }

    /**
     * Short description of method remove
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Data data
     * @return boolean
     */
    public function remove( taoItems_models_classes_QTI_Data $data)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--272f4da0:12a899718bf:-8000:00000000000024E8 begin
        
        $tmpId = $data->getId();
        $data->setPersitance(false);
        unset($data);
        
        $returnValue = (!Session::hasAttribute($tmpId));
        
        // section 127-0-1-1--272f4da0:12a899718bf:-8000:00000000000024E8 end

        return (bool) $returnValue;
    }

} /* end of class taoItems_models_classes_QTI_Service */

?>