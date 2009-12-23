<?php
$todefine = array(
	'TEST_CLASS' 				=> 'http://www.tao.lu/Ontologies/TAOTest.rdf#Test',
	'TEST_RELATED_ITEMS_PROP' 	=> 'http://www.tao.lu/Ontologies/TAOTest.rdf#RelatedItems',
	'TEST_TESTCONTENT_PROP' 	=> 'http://www.tao.lu/Ontologies/TAOTest.rdf#TestContent',
	'TAO_ITEM_CLASS' 			=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#Item',
	'TAO_OBJECT_CLASS' 			=> 'http://www.tao.lu/Ontologies/TAO.rdf#TAOObject',
	'TAO_GROUP_CLASS' 			=> 'http://www.tao.lu/Ontologies/TAOGroup.rdf#Group',
	'TAO_ITEM_CLASS' 			=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#Item',
	'TAO_ITEM_MODEL_CLASS' 		=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModels',
	'TAO_RESULT_CLASS' 			=> 'http://www.tao.lu/Ontologies/TAOResult.rdf#Result',
	'TAO_SUBJECT_CLASS' 		=> 'http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject',
	'TAO_TEST_CLASS' 			=> 'http://www.tao.lu/Ontologies/TAOTest.rdf#Test',
	'RDFS_LABEL'				=> 'http://www.w3.org/2000/01/rdf-schema#label',
	'RDFS_TYPE'							=> 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type',
	'GENERIS_BOOLEAN'			=> 'http://www.tao.lu/Ontologies/generis.rdf#Boolean'
);
foreach($todefine as $constName => $constValue){
	if(!defined($constName)){
		define($constName, $constValue);
	}
}
unset($todefine);
?>