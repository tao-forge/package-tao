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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */
namespace oat\taoQtiItem\model\qti\response;

use oat\taoQtiItem\model\qti\response\SetOutcomeVariable;
use oat\taoQtiItem\model\qti\response\ResponseRule;
use oat\taoQtiItem\model\qti\expression\Expression;

/**
 * Short description of class
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoQTI
 
 */
class SetOutcomeVariable
    extends ResponseRule
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute outcomeVariableIdentifier
     *
     * @access public
     * @var string
     */
    public $outcomeVariableIdentifier = '';

    /**
     * Short description of attribute expression
     *
     * @access public
     * @var Expression
     */
    public $expression = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string identifier
     * @param  Expression expression
     * @return mixed
     */
    public function __construct($identifier,  Expression $expression)
    {
        // section 127-0-1-1-dbb9044:134e695b13f:-8000:00000000000062AE begin
        $this->outcomeVariableIdentifier	= $identifier;
        $this->expression					= $expression;
        // section 127-0-1-1-dbb9044:134e695b13f:-8000:00000000000062AE end
    }

    /**
     * Short description of method getRule
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getRule()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-dbb9044:134e695b13f:-8000:00000000000062A2 begin
		$returnValue = 'setOutcomeValue("'.$this->outcomeVariableIdentifier.'", '.$this->expression->getRule().');';
        // section 127-0-1-1-dbb9044:134e695b13f:-8000:00000000000062A2 end

        return (string) $returnValue;
    }

} /* end of class oat\taoQtiItem\model\qti\response\SetOutcomeVariable */

?>