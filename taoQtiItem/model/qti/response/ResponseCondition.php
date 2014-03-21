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
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */
namespace oat\taoQtiItem\model\qti\response;

use oat\taoQtiItem\model\qti\response\ResponseCondition;
use oat\taoQtiItem\model\qti\response\ResponseRule;
use oat\taoQtiItem\model\qti\response\Rule;
use oat\taoQtiItem\model\qti\expression\Expression;
use oat\taoQtiItem\model\qti\response\ConditionalExpression;

/**
 * Short description of class
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoQTI
 * @subpackage models_classes_QTI_response
 */
class ResponseCondition
    extends ResponseRule
        implements Rule
{
    // --- ASSOCIATIONS ---
    // generateAssociationEnd :     // generateAssociationEnd :     // generateAssociationEnd : 0    // generateAssociationEnd : 

    // --- ATTRIBUTES ---

    /**
     * Short description of attribute responseIfs
     *
     * @access protected
     * @var array
     */
    protected $responseIfs = array();

    /**
     * Short description of attribute responseElse
     *
     * @access public
     * @var ResponseRule
     */
    public $responseElse = null;

    // --- OPERATIONS ---

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

        // section 127-0-1-1-3397f61e:12c15e8566c:-8000:0000000000002AFF begin
        
        // Get the if / elseif conditions and the associated actions
        foreach ($this->responseIfs as $responseElseIf){
            $returnValue .= (empty($returnValue) ? '' : ' else ').$responseElseIf->getRule();
        }
        
        // Get the else actions
        if (!empty($this->responseElse)){
            $returnValue .= 'else {';
            foreach ($this->responseElse as $actions){
                $returnValue .= $actions->getRule();
            }
            $returnValue .= '}';
        }
        
        // section 127-0-1-1-3397f61e:12c15e8566c:-8000:0000000000002AFF end

        return (string) $returnValue;
    }

    /**
     * Short description of method addResponseIf
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Expression condition
     * @param  array actions
     * @return mixed
     */
    public function addResponseIf( Expression $condition, $actions)
    {
        // section 127-0-1-1-2d3ac2b0:12c120718cc:-8000:0000000000002AE0 begin
        $this->responseIfs[] = new ConditionalExpression($condition, $actions);
        // section 127-0-1-1-2d3ac2b0:12c120718cc:-8000:0000000000002AE0 end
    }

    /**
     * Short description of method setResponseElse
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  array actions
     * @return mixed
     */
    public function setResponseElse($actions)
    {
        // section 127-0-1-1-2d3ac2b0:12c120718cc:-8000:0000000000002AE6 begin
        $this->responseElse = $actions;
        // section 127-0-1-1-2d3ac2b0:12c120718cc:-8000:0000000000002AE6 end
    }

} /* end of class oat\taoQtiItem\model\qti\response\ResponseCondition */

?>