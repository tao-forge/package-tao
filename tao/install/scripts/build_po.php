<?php

if(PHP_SAPI != 'cli'){
	echo "please run me in command line!";
	exit(1);
}

chdir(dirname(__FILE__));

include 'poextraction/l10n_functions.php';

define('ROOT_PATH', '../../../');

define('MANIFEST_FILE_NAME', 'manifest.php');
define('LOCAL_DIR_NAME', 'locales');
define('PO_FILE_NAME', 'messages.po');

$extensions = array();

foreach(scandir(ROOT_PATH) as $file){
	
	$extDir = ROOT_PATH . $file;
	
	if(is_dir($extDir)){
		if(file_exists($extDir . '/' . MANIFEST_FILE_NAME)){
			
			$localDir = $extDir. '/' . LOCAL_DIR_NAME;
			
			$langs = array();
			foreach(scandir($localDir) as $localFile){
				if(is_dir($localDir . '/' . $localFile)){
					if(file_exists($localDir . '/' . $localFile . '/' . PO_FILE_NAME)){
						$langs[$localFile] = $localDir . '/' . $localFile;
					}
				}
			}
			
			$extensions[$file] = array(
				'path'	=> ROOT_PATH . $file,
				'langs' => $langs  
			);
		}		
	}
}

//common extensions
foreach($extensions as $extensionName => $extensionData){

	##init vars to run the poextraction script
	$directories	= array(
		$extensionData['path'] . '/actions/',
		$extensionData['path'] . '/helpers/',
		$extensionData['path'] . '/models/',
		$extensionData['path'] . '/views/'
	);
	$extension	= array('php', 'tpl', 'js', 'xml');
	$fichier	= PO_FILE_NAME;
	$empLoc		= $extensionData['path'] . '/' . LOCAL_DIR_NAME . '/';
	
	foreach(array_keys($extensionData['langs']) as $langue){
		
		echo "\n => Extract $langue for $extensionName\n";
		
		include 'poextraction/l10n_update.php';
		
		echo "\n------\n";
	}
}

echo "\n => Rebuild tao extension \n";
$taoConcats = array();
foreach($extensions as $extensionName => $extensionData){
	if(preg_match("/^tao/", $extensionName)){
	
		foreach(array_keys($extensionData['langs']) as $lang){
			if(!isset($taoConcats[$lang])){
				$taoConcats[$lang] = array();
			} 
			$poFile =  $extensionData['path'] . '/' . LOCAL_DIR_NAME . '/'.$lang.'/'.PO_FILE_NAME;
			if(file_exists($poFile)){
				$taoConcats[$lang] = array_merge($taoConcats[$lang], getPoFile($poFile));
			}
		}
	}
}
$taoPath = ROOT_PATH . 'tao/' . LOCAL_DIR_NAME . '/';
foreach($taoConcats as $lang => $strings){
	if(writePoFile($taoPath . $lang .'/' .PO_FILE_NAME, $strings)){
		echo $taoPath . $lang .'/' .PO_FILE_NAME. " rebuild\n";
	}
}
echo "\n------\n";

//UTR
$utrPath = ROOT_PATH .'taoResults/models/ext/utrv1';
if(file_exists($utrPath)){
	
	$directories	= array(
		$utrPath . '/classes/',
		$utrPath . '/view/'
	);
	$extension	= array('php', 'js');
	$fichier	= PO_FILE_NAME;
	$localDir	= $utrPath . '/view/' . LOCAL_DIR_NAME . '/';
	$empLoc 	= $localDir;
	$langs = array();
	foreach(scandir($localDir) as $localFile){
		if(is_dir($localDir . '/' . $localFile)){
			if(file_exists($localDir . '/' . $localFile . '/' . PO_FILE_NAME)){
				$langs[$localFile] = $localDir . '/' . $localFile;
			}
		}
	}
	foreach(array_keys($langs) as $langue){
		
		echo "\n => Extract $langue for UTR\n";
		
		include 'poextraction/l10n_update.php';
		
		echo "\n------\n";
	}
	
}

//WATER PHENIX
$wpPath =  ROOT_PATH .'taoItems/models/ext/itemAuthoring/waterphenix';
if(file_exists($wpPath)){
	
	$directories	= array(
		$wpPath . '/js/',
		$wpPath . '/view/'
	);
	$extension	= array('php', 'js', 'ejs');
	$fichier	= PO_FILE_NAME;
	$localDir	= $wpPath . '/' . LOCAL_DIR_NAME . '/';
	$empLoc 	= $localDir;
	$langs = array();
	foreach(scandir($localDir) as $localFile){
		if(is_dir($localDir . '/' . $localFile)){
			if(file_exists($localDir . '/' . $localFile . '/' . PO_FILE_NAME)){
				$langs[$localFile] = $localDir . '/' . $localFile;
			}
		}
	}
	foreach(array_keys($langs) as $langue){
		
		echo "\n => Extract $langue for WATER PHENIX\n";
		
		include 'poextraction/l10n_update.php';
		
		echo "\n------\n";
	}
	
}

?>