<?php
/**
 * RAW Bootstraping
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
if(PHP_SAPI == 'cli'){
	$_SERVER['HTTP_HOST'] = 'http://localhost';
	$_SERVER['DOCUMENT_ROOT'] = dirname(__FILE__).'/../../..';
}
require_once dirname(__FILE__).'/../../tao/includes/class.Bootstrap.php';

$options = array();
//need to load additional constants
$options['constants'] = array('taoTests', 'taoItems', 'taoResults', 'taoGroups');

$bootStrap = new BootStrap('taoDelivery', $options);
$bootStrap->start();
?>