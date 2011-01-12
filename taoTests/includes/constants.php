<?php
/**
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 *
 */
//include constants for the wfEngine:
include_once ROOT_PATH . '/wfEngine/includes/constants.php';

include_once ROOT_PATH . '/tao/includes/constants.php';

$todefine = array(
	'TEST_TESTCONTENT_PROP' 			=> 'http://www.tao.lu/Ontologies/TAOTest.rdf#TestContent',
	
	'TEST_ACTIVE_PROP' 	=> 'http://www.tao.lu/Ontologies/TAOTest.rdf#active',
	
	'TAO_TEST_AUTHORINGMODE_PROP' => 'http://www.tao.lu/Ontologies/TAOTest.rdf#AuthoringMode',
	'TAO_TEST_SIMPLEMODE' => 'http://www.tao.lu/Ontologies/TAOTest.rdf#i1268049036038811802',
	'TAO_TEST_ADVANCEDMODE' => 'http://www.tao.lu/Ontologies/TAOTest.rdf#i1268049036038811803'
);
foreach($todefine as $constName => $constValue){
	if(!defined($constName)){
		define($constName, $constValue);
	}
}
unset($todefine);
?>