<?php
/*
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * 
 */
 if(isset($_GET['session_id'])){
 	session_start($_GET['session_id']);
 }
 else{
 	session_start();
 }


require_once dirname(__FILE__). '/config.php';
require_once dirname(__FILE__). '/constants.php';

set_include_path(get_include_path() . PATH_SEPARATOR . GENERIS_BASE_PATH.'/..');

require_once 'tao/helpers/class.Uri.php';
include_once 'tao/includes/prepend.php';
?>
