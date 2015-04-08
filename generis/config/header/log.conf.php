<?php
/**
 * Log config
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package generis
 
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */

#	 trace_level 	= 0;
#	 debug_level 	= 1;
#	 info_level 	= 2;
#	 warning_level 	= 3;
#	 error_level	= 4;
#	 fatal_level 	= 5;

/* Example of a Single File Appender
 array(
    'class'			=> 'SingleFileAppender',
    'threshold'		=> 4 ,
    'file'			=> dirname(__FILE__).'/../../log/error.txt',
    'format'		=> '%m'
)
*/

/* Example of a Multiple File Appender with archiving
array(
    'class'			=> 'ArchiveFileAppender',
    'mask'			=> 62 , // 111110
    'tags'			=> array('GENERIS', 'TAO')
    'file'			=> '/var/log/tao/debug.txt',
    'directory'		=> '/var/log/tao/',
    'max_file_size'	=> 10000000
)
*/

/* Example of a UDP Appender
array(
    'class'			=> 'UDPAppender',
    'host'			=> '127.0.0.1',
    'port'			=> 5775,
    'threshold'		=> 1
)
*/
