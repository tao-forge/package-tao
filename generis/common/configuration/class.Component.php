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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * Short description of class common_configuration_Component
 *
 * @abstract
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package generis
 
 */
abstract class common_configuration_Component
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute optional
     *
     * @access private
     * @var boolean
     */
    private $optional = false;

    /**
     * Short description of attribute name
     *
     * @access private
     * @var string
     */
    private $name = '';

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string name
     * @param  boolean optional
     * @return mixed
     */
    public function __construct($name = 'unknown', $optional = false)
    {
        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001A8B begin
        $this->setName($name);
        $this->setOptional($optional);
        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001A8B end
    }

    /**
     * Short description of method check
     *
     * @abstract
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return common_configuration_Report
     */
    public abstract function check();

    /**
     * Short description of method isOptional
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return boolean
     */
    public function isOptional()
    {
        $returnValue = (bool) false;

        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001AA1 begin
        $returnValue = $this->optional;
        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001AA1 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method setOptional
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  boolean optional
     * @return void
     */
    public function setOptional($optional)
    {
        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001AA3 begin
        $this->optional = $optional;
        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001AA3 end
    }

    /**
     * Short description of method getName
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getName()
    {
        $returnValue = (string) '';

        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001AA6 begin
        $returnValue = $this->name;
        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001AA6 end

        return (string) $returnValue;
    }

    /**
     * Short description of method setName
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string name
     * @return void
     */
    public function setName($name)
    {
        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001AA8 begin
        $this->name = $name;
        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001AA8 end
    }

} /* end of abstract class common_configuration_Component */

?>