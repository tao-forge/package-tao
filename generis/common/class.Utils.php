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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *
 */

/**
 *
 * Generis Object Oriented API - common/class.Utils.php
 *
 * Utility functions
 *
 * This file is part of Generis Object Oriented API.
 *
 * @author lionel.lecaque@tudor.lu
 * @package generis
 * @subpackage common
 * @see @license  GNU General Public (GPL) Version 2 http://www.opensource.org/licenses/gpl-2.0.php
 */

class common_Utils
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Check if the given string is a proper uri
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  string strarg
     * @return boolean
     */
    public static function isUri($strarg)
    {
        $returnValue = (bool) false;
        $uri = trim($strarg);
        if(!empty($uri)){
        	if( (preg_match("/^(http|https|file|ftp):\/\/[\/:.A-Za-z0-9_-]+#[A-Za-z0-9_-]+$/", $uri) && strpos($uri,'#')>0) || strpos($uri,"#")===0){
        		$returnValue = true;
        	}
        }
        return (bool) $returnValue;
    }

    /**
     * Removes starting/ending spaces, strip html tags out, remove any \r and \n
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  string strarg
     * @return string
     */
    public static function fullTrim($strarg)
    {
        return strip_tags(trim($strarg));
    }


    /**
     * Short description of method getNewUri
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return string
     */
    public static function getNewUri()
    {

		$uriProviderClassName = 'common_uri_' . GENERIS_URI_PROVIDER;
		$uriProvider = new $uriProviderClassName(SGBD_DRIVER);
		$returnValue = $uriProvider->provide();
        return (string) $returnValue;
    }

    /**
     * Returns the php code, that if evaluated
     * would return the value provided
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  value
     * @return string
     */
    public static function toPHPVariableString($value)
    {
		switch (gettype($value)) {
        	case "string" :
        		// replace \ by \\ and then ' by \'
        		$returnValue =  '\''.str_replace('\'', '\\\'', str_replace('\\', '\\\\', $value)).'\'';
        		break;
        	case "boolean" :
        		$returnValue = $value ? 'true' : 'false';
        		break;
        	case "integer" :
        	case "double" :
        		$returnValue = $value;
        		break;
        	case "array" :
				$string = "";
				foreach ($value as $key => $val) {
					$string .= self::toPHPVariableString($key)." => ".self::toPHPVariableString($val).",";
				}
				$returnValue = "array(".substr($string, 0, -1).")";
				break;
        	case "NULL" :
        		$returnValue = 'null';
				break;
        	case "object" :
        		$returnValue = 'unserialize(\''.serialize($value).'\')';
        		break;
        	default:
    			// resource and unexpected types
        		throw new common_exception_Error("Could not convert variable of type ".gettype($value)." to PHP variable string");
        }

        return (string) $returnValue;
    }



} /* end of class common_Utils */

?>