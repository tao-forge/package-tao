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
 * Copyright (c) 2009-2012 (original work) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 27.12.2012, 14:55:59 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package core
 * @subpackage kernel_persistence_virtuoso
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include core_kernel_persistence_PersistenceImpl
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 */
require_once('core/kernel/persistence/class.PersistenceImpl.php');

/**
 * include core_kernel_persistence_PropertyInterface
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 */
require_once('core/kernel/persistence/interface.PropertyInterface.php');

/* user defined includes */
// section 127-0-1-1--3a4c22:13104bcfe8d:-8000:00000000000022F2-includes begin
// section 127-0-1-1--3a4c22:13104bcfe8d:-8000:00000000000022F2-includes end

/* user defined constants */
// section 127-0-1-1--3a4c22:13104bcfe8d:-8000:00000000000022F2-constants begin
// section 127-0-1-1--3a4c22:13104bcfe8d:-8000:00000000000022F2-constants end

/**
 * Short description of class core_kernel_persistence_virtuoso_Property
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package core
 * @subpackage kernel_persistence_virtuoso
 */
class core_kernel_persistence_virtuoso_Property
    extends core_kernel_persistence_PersistenceImpl
        implements core_kernel_persistence_PropertyInterface
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute instance
     *
     * @access public
     * @var Resource
     */
    public static $instance = null;

    // --- OPERATIONS ---

    /**
     * Short description of method getSubProperties
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @param  boolean recursive
     * @return array
     */
    public function getSubProperties( core_kernel_classes_Resource $resource, $recursive = false)
    {
        $returnValue = array();

        // section 127-0-1-1-7b8668ff:12f77d22c39:-8000:000000000000144D begin
        
        throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
        
        // section 127-0-1-1-7b8668ff:12f77d22c39:-8000:000000000000144D end

        return (array) $returnValue;
    }

    /**
     * Short description of method isLgDependent
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @return boolean
     */
    public function isLgDependent( core_kernel_classes_Resource $resource)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--bedeb7e:12fb15494a5:-8000:00000000000014DB begin
        
        throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
        
        // section 127-0-1-1--bedeb7e:12fb15494a5:-8000:00000000000014DB end

        return (bool) $returnValue;
    }

    /**
     * Short description of method isMultiple
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @return boolean
     */
    public function isMultiple( core_kernel_classes_Resource $resource)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--bedeb7e:12fb15494a5:-8000:00000000000014DD begin
        
        throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
        
        // section 127-0-1-1--bedeb7e:12fb15494a5:-8000:00000000000014DD end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getRange
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @return core_kernel_classes_Class
     */
    public function getRange( core_kernel_classes_Resource $resource)
    {
        $returnValue = null;

        // section 127-0-1-1-7a0c731b:12fbfab7535:-8000:0000000000001539 begin
        
        throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
        
        // section 127-0-1-1-7a0c731b:12fbfab7535:-8000:0000000000001539 end

        return $returnValue;
    }

    /**
     * Short description of method delete
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @param  boolean deleteReference
     * @return boolean
     */
    public function delete( core_kernel_classes_Resource $resource, $deleteReference = false)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--330ca9de:1318ac7ca9f:-8000:0000000000001641 begin
        
        throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
        
        // section 127-0-1-1--330ca9de:1318ac7ca9f:-8000:0000000000001641 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method setRange
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @param  Class class
     * @return core_kernel_classes_Class
     */
    public function setRange( core_kernel_classes_Resource $resource,  core_kernel_classes_Class $class)
    {
        $returnValue = null;

        // section 10-13-1-85-36aaae10:13bad44a267:-8000:0000000000001E25 begin
        throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
        // section 10-13-1-85-36aaae10:13bad44a267:-8000:0000000000001E25 end

        return $returnValue;
    }

    /**
     * Short description of method setMultiple
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @param  boolean isMultiple
     * @return void
     */
    public function setMultiple( core_kernel_classes_Resource $resource, $isMultiple)
    {
        // section 10-13-1-85-71dc1cdd:13bade8452c:-8000:0000000000001E32 begin
        throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
        // section 10-13-1-85-71dc1cdd:13bade8452c:-8000:0000000000001E32 end
    }

    /**
     * Short description of method setLgDependent
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @param  boolean isLgDependent
     * @return void
     */
    public function setLgDependent( core_kernel_classes_Resource $resource, $isLgDependent)
    {
        // section 10-13-1-85-4a20f448:13bdca46e9a:-8000:0000000000001E45 begin
        throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
        // section 10-13-1-85-4a20f448:13bdca46e9a:-8000:0000000000001E45 end
    }

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return core_kernel_classes_Resource
     */
    public static function singleton()
    {
        $returnValue = null;

        // section 127-0-1-1--3a4c22:13104bcfe8d:-8000:00000000000022F6 begin
        
        if (core_kernel_persistence_virtuoso_Property::$instance == null){
                core_kernel_persistence_virtuoso_Property::$instance = new core_kernel_persistence_virtuoso_Property();
        }
        $returnValue = core_kernel_persistence_virtuoso_Property::$instance;
        
        // section 127-0-1-1--3a4c22:13104bcfe8d:-8000:00000000000022F6 end

        return $returnValue;
    }

    /**
     * Short description of method isValidContext
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @return boolean
     */
    public function isValidContext( core_kernel_classes_Resource $resource)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--3a4c22:13104bcfe8d:-8000:00000000000022FA begin
        list($NS, $id) = explode('#', $resource->uriResource);
        if(isset($id) && !empty($id)){
                
                $virtuoso = core_kernel_persistence_virtuoso_VirtuosoDataStore::singleton();
                
                $query = 'PREFIX resourceNS: <'.$NS.'#> ASK {resourceNS:'.$id.' ?p ?o}';
                
                $returnValue = $virtuoso->execQuery($query, 'Boolean');
        }
        // section 127-0-1-1--3a4c22:13104bcfe8d:-8000:00000000000022FA end

        return (bool) $returnValue;
    }

} /* end of class core_kernel_persistence_virtuoso_Property */

?>