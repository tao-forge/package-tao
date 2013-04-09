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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * Utility to display dates
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 * @subpackage helpers
 */
class tao_helpers_Date
{
	const FORMAT_LONG		= 0;
	const FORMAT_VERBOSE	= 1;
	
    /**
     * Dispalys a date/time
     * Should in theorie be dependant on the users locale and timezone
     *
     * @access public
     * @param  mixed timestamp
     * @param  int format
     * @return string
     */
    public static function displayeDate($timestamp, $format = self::FORMAT_LONG)
    {
    	$returnValue = '';
    	$ts = is_object($timestamp) && $timestamp instanceof core_kernel_classes_Literal ? $timestamp->__toString() : $timestamp;
    	$dateTime = new DateTime();
    	$dateTime->setTimestamp($ts);
    	switch ($format) {
    		case self::FORMAT_LONG :
    			$returnValue = $dateTime->format('d/m/Y H:i:s');
    			break;
    		case self::FORMAT_VERBOSE :
    			$returnValue = $dateTime->format('F j, Y, g:i:s a');
    			break;
    		default:
    			common_Logger::w('Unkown date format '.$format.' for '.__FUNCTION__, 'TAO');
    	}
    	return $returnValue;
    }

} /* end of class tao_helpers_Display */

?>