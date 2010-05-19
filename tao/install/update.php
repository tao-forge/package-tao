<?php
   /*
    * @todo implement this script in a real update process
    * 1. go to update dir
    * 2. checkout the right version id
    * 3. execute the php scripts and run the sql instructions from the last version to the current version
    */
	
if(PHP_SAPI == 'cli'){
	$_SERVER['HTTP_HOST'] = 'http://localhost';
	$_SERVER['DOCUMENT_ROOT'] = dirname(__FILE__).'/../..';
	if(isset($_SERVER['argv'][1])){
		$version = $_SERVER['argv'][1];
	}
}

if(isset($_GET['version'])){
	$version = $_GET['version'];
}
	
require_once dirname(__FILE__).'/../../generis/common/inc.extension.php';	
require_once dirname(__FILE__).'/../includes/common.php';
require_once dirname(__FILE__).'/utils.php';

$dbWrapper = core_kernel_classes_DbWrapper::singleton(DATABASE_NAME);

$updateFiles = array();
foreach(glob(dirname(__FILE__).'/update/'.$version.'/*') as $path){
		$updateFiles[basename($path)] = $path;
}
ksort($updateFiles);	
foreach($updateFiles as $file => $path){
	if(preg_match("/\.php$/", $file)){
		include $path;
	}
	if(preg_match("/\.sql$/", $file)){
		loadSql($path, $dbWrapper->dbConnector);
	}
}
?>