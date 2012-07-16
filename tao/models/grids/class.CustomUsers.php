<?php

error_reporting(E_ALL);

/**
 * Extend the default users grid model here
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package tao
 * @subpackage models_grids
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include tao_models_grids_Users
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 */
require_once('tao/models/grids/class.Users.php');

/* user defined includes */
// section 127-0-1-1--2e12219e:1360c8283db:-8000:000000000000387B-includes begin
// section 127-0-1-1--2e12219e:1360c8283db:-8000:000000000000387B-includes end

/* user defined constants */
// section 127-0-1-1--2e12219e:1360c8283db:-8000:000000000000387B-constants begin
// section 127-0-1-1--2e12219e:1360c8283db:-8000:000000000000387B-constants end

/**
 * Extend the default users grid model here
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package tao
 * @subpackage models_grids
 */
class tao_models_grids_CustomUsers
    extends tao_models_grids_Users
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method initColumns
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return boolean
     */
    public function initColumns()
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--2e12219e:1360c8283db:-8000:000000000000387C begin
		$returnValue = parent::initColumns();
		if (!in_array('country', $this->excludedProperties)) {
			$this->grid->addColumn('country', __('Country'));
			$returnValue &= $this->grid->setColumnsAdapter(
				'country',
				new tao_models_grids_adaptors_UserAdditionalProperties()
			);
		}
        // section 127-0-1-1--2e12219e:1360c8283db:-8000:000000000000387C end

        return (bool) $returnValue;
    }

} /* end of class tao_models_grids_CustomUsers */

?>