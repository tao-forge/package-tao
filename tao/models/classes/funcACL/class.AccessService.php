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
 * Copyright (c) 2009-2012 (original work) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               
 * 
 */

/**
 * mother class for access operations
 *
 * @author Jehan Bihin
 * @package tao
 * @since 2.2
 * @subpackage models_classes_funcACL
 */
class tao_models_classes_funcACL_AccessService extends tao_models_classes_GenerisService
{

    /**
     * Short description of method makeEMAUri
     *
     * @access public
     * @author Jehan Bihin, <jehan.bihin@tudor.lu>
     * @param string ext
     * @param string mod
     * @param string act
     * @return string
     */
    public function makeEMAUri($ext, $mod = null, $act = null)
    {
        $returnValue = (string) '';
        
        $returnValue = FUNCACL_NS . '#';
        if (! is_null($act)) {
            $type = 'a';
        } else 
            if (! is_null($mod)) {
                $type = 'm';
            } else {
                $type = 'e';
            }
        $returnValue .= $type . '_' . $ext;
        if (! is_null($mod)) {
            $returnValue .= '_' . $mod;
        }
        if (! is_null($act)) {
            $returnValue .= '_' . $act;
        }
        return (string) $returnValue;
    }
}

?>