<?php
define('NS_TAOQUAL', 'http://www.tao.lu/middleware/taoqual.rdf');

define('PROCESS_VARIABLES',								NS_TAOQUAL . '#i118589204618246');
define('CURRENT_TOKEN',									NS_TAOQUAL . '#119011853150574');
define('STATUS',										NS_TAOQUAL . '#119011843917578');
define('STATUS_FINISHED',								NS_TAOQUAL . '#119011840560280');
define('EXECUTION_OF',									NS_TAOQUAL . '#119010459643422');
define('PREC_ACTIVITIES',								NS_TAOQUAL . '#i118589245545368');
define('NEXT_ACTIVITIES',								NS_TAOQUAL . '#i118589252058280');
define('USER_ROLE',										NS_TAOQUAL . '#119012169222836');
define('ACTIVITY_ROLE',									NS_TAOQUAL . '#i118588925461850');
define('CLASS_WORKFLOW_USER',							NS_TAOQUAL . '#i11859665003194');
define('PROPERTY_USER_ROLE', 							NS_TAOQUAL . '#i121930698442742');
define('PROPERTY_USER_LOGIN',							NS_TAOQUAL . '#119012256329986');

define('PROCESS_ACTIVITIES',							NS_TAOQUAL . '#i118735548956256');
define('CLASS_PROCESS',									NS_TAOQUAL . '#i118588753722590');
define('CLASS_PROCESS_EXECUTIONS',						NS_TAOQUAL . '#119010455660544');
define('CLASS_CONSISTENCY_RULES',						NS_TAOQUAL . '#i122206969324866');
define('ACTIVITIES_IS_INITIAL',							NS_TAOQUAL . '#119018447833116');
define('PROPERTY_PROCESS_VARIABLE',						NS_TAOQUAL . '#i118589204618246');
define('PROPERTY_PROCESS_ACTIVITIES',					NS_TAOQUAL . '#i118735548956256');
define('PROPERTY_ACTIVITIES_ISINITIAL',					NS_TAOQUAL . '#119018447833116');
define('PROPERTY_CONNECTORS_PRECACTIVITIES',			NS_TAOQUAL . '#i118589245545368');
define('PROPERTY_ACTIVITIES_ISERVICES',					NS_TAOQUAL . '#i118588789618848');
define('PROPERTY_CONENCTORS_NEXTACTIVITIES',			NS_TAOQUAL . '#i118589252058280');
define('PROPERTY_CONENCTORS_TYPEOF',					NS_TAOQUAL . '#i118595164231830');
define('PROPERTY_PINSTANCES_STATUS',					NS_TAOQUAL . '#119011838314196');
define('PROPERTY_PINSTANCES_EXECUTIONOF',				NS_TAOQUAL . '#119010459643422');
define('PROPERTY_PINSTANCES_TOKEN',						NS_TAOQUAL . '#119011853150574');
define('PROPERTY_PINSTANCES_PROCESSPATH',				NS_TAOQUAL . '#processPath');
define('PROPERTY_PINSTANCES_FULLPROCESSPATH',			NS_TAOQUAL . '#processFullPath');

define('PROPERTY_ACTIVITIES_HYPERCLASSES',				NS_TAOQUAL . '#i12193017041224');
define('PROPERTY_ACTIVITIES_INFERENCERULE',				NS_TAOQUAL . '#activityInferenceRule');
define('PROPERTY_ACTIVITIES_ONBEFOREINFERENCERULE',		NS_TAOQUAL . '#activityOnBeforeInferenceRule');
define('PROPERTY_CALLOFSERVICES_SERVICEDEFINITION',		NS_TAOQUAL . '#i11859509039346');
define('PROPERTY_CALLOFSERVICES_ACTUALPARAMETERIN',		NS_TAOQUAL . '#i118595099928140');
define('PROPERTY_CALLOFSERVICES_ACTUALPARAMETEROUT',	NS_TAOQUAL . '#i118596586150000');
define('PROPERTY_SERVICEDEFINITIONS_URL',				NS_TAOQUAL . '#i11858886911216');
define('PROPERTY_ACTUALPARAMETER_PROCESSVARIABLE',		NS_TAOQUAL . '#i11858901499008');
define('PROPERTY_ACTUALPARAMETER_QUALITYMETRIC',		NS_TAOQUAL . '#i118589023027962');
define('PROPERTY_ACTUALPARAMETER_CONSTANTVALUE',		NS_TAOQUAL . '#i1185890127346');
define('PROPERTY_ACTUALPARAMETER_FORMALPARAMETER',		NS_TAOQUAL . '#i118588973457282');
define('PROPERTY_PROCESSINSTANCE_CURRENTPATHITEM',		NS_TAOQUAL . '#i122182453333834');
define('PROPERTY_PATHITEM_ACTIVITY',					NS_TAOQUAL . '#i122182427816968');
define('PROPERTY_PATHITEM_NEXTPATHITEM',				NS_TAOQUAL . '#i122182434950916');
define('PROPERTY_CONNECTOR_TYPEOFCONNECTOR',			NS_TAOQUAL . '#i118595164231830');
define('PROPERTY_CONNECTOR_TRANSITIONRULE',				NS_TAOQUAL . '#i122207114241798');
define('PROPERTY_TRANSITIONRULES_THEN',					NS_TAOQUAL . '#i122207070428322');
define('PROPERTY_TRANSITIONRULES_ELSE',					NS_TAOQUAL . '#i122207096147834');
define('PROPERTY_RULE_THEN',							NS_TAOQUAL . '#i122207070428322');
define('PROPERTY_RULE_ELSE',							NS_TAOQUAL . '#i122207096147834');
define('PROPERTY_ACTIVITIES_ISHIDDEN',					NS_TAOQUAL . '#isHiddenActivity');
define('CLASS_CONSISTENCYRULES',						NS_TAOQUAL . '#i122346531841048');
define('CLASS_CALLOFSERVICES',							NS_TAOQUAL . '#i118595077025536');
define('CLASS_DEFINITIONOFSUPPORTSERVICES',				NS_TAOQUAL . '#i118588779325312');
define('PROPERTY_CONSISTENCYRULES_INVOLVEDACTIVITIES',	NS_TAOQUAL . '#i122346533932400');
define('PROPERTY_CONSISTENCYRULES_SUPPRESSABLE',		NS_TAOQUAL . '#i122347196613578');
define('PROPERTY_ACTIVITIES_CONSISTENCYRULE',			NS_TAOQUAL . '#i122346640532066');
define('CLASS_PATHITEM',								NS_TAOQUAL . '#i122182410160922');
define('RESOURCE_PROCESSSTATUS_RESUMED',				NS_TAOQUAL . '#119011839432700');
define('RESOURCE_PROCESSSTATUS_STARTED',				NS_TAOQUAL . '#119011838314196');
define('RESOURCE_PROCESSSTATUS_FINISHED',				NS_TAOQUAL . '#119011840560280');
define('RESOURCE_PROCESSSTATUS_PAUSED',					NS_TAOQUAL . '#119011862341174');
define('CLASS_TRANSITIONRULES',							NS_TAOQUAL . '#i122206969324866');
define('CLASS_CONNECTORS',								NS_TAOQUAL . '#i118589215756172');
define('CLASS_ACTIVITIES',								NS_TAOQUAL . '#i118588757437650');
define('CLASS_EXITCODE',								NS_TAOQUAL . '#exitCode');
define('CLASS_ACTIONCODE',								NS_TAOQUAL . '#actionCode');
define('PROPERTY_PROCESSINSTANCE_EXITCODE',				NS_TAOQUAL . '#procExitCode');
define('PROPERTY_PROCESSINSTANCE_ACTIONCODE',			NS_TAOQUAL . '#procActionCode');
define("CONNECTOR_SEQ",									NS_TAOQUAL . '#i118589243226718');
define("CONNECTOR_SPLIT",								NS_TAOQUAL . '#i118589220353990');
define("CONNECTOR_LIST",								NS_TAOQUAL . '#i11858924147584');
define('CONNECTOR_LIST_UP',								NS_TAOQUAL.'#i1246279182028411900');


define("INTERVIEWER_ROLE",								NS_TAOQUAL . '#i121930698442742');
define('CLASS_INFERENCERULES',							NS_TAOQUAL.'#inferenceRule');
define('PROPERTY_INFERENCERULES_THEN',					NS_TAOQUAL.'#inferenceRuleThen');
define('PROPERTY_INFERENCERULES_ELSE',					NS_TAOQUAL.'#inferenceRuleElse');
define('PROPERTY_ACTIVITIES_DISPLAYCALENDAR',			NS_TAOQUAL.'#i123383820311354');
define('PROPERTY_ACTIVITIES_LIST',						NS_TAOQUAL.'#i124326462812324');
define('PROPERTY_ACTIVITIES_SELECTOR',					NS_TAOQUAL.'#i124299860710746');

define('PROPERTY_ACTIVITIES_LIST_PARENT',						NS_TAOQUAL.'#i124748211635858');



define('RESOURCE_ACTIVITIES_SELECTOR_SEQ',				NS_TAOQUAL.'#i12429977703072');
define('RESOURCE_ACTIVITIES_SELECTOR_RAND',				NS_TAOQUAL.'#i124299784247410');
define('RESOURCE_ACTIVITIES_SELECTOR_RAND_1',			NS_TAOQUAL.'#i124653558221396');

define('RESOURCE_ACTIVITIES_SELECTOR_DICO',				NS_TAOQUAL.'#i124299788641244');

define('CLASS_ACTIVITIES_LIST',							NS_TAOQUAL.'#i12429950482224');
define('CLASS_ACTIVITIES_LIST_EXECUTION',				NS_TAOQUAL.'#i12445408585808');

define('CURRENT_TOKEN_EXECUTION',						NS_TAOQUAL.'#i124454126218994');
define('ACTIVITY_EXECUTION_HISTORY',					NS_TAOQUAL.'#i124653558221396');
define('PROPERTY_ACTIVITIES_LIST_EXECUTION_UP',			NS_TAOQUAL.'#i124705207723679');
define('PROPERTY_ACTIVITIES_LIST_EXECUTION_ISFINISHED',		NS_TAOQUAL.'#i124705207723677');



define('PROPERTY_REMAINING_ACTIVITIES',					NS_TAOQUAL.'#i124705207723678');

define('PROPERTY_REMAINING_ACTIVITIES_ARRAY',					NS_TAOQUAL.'#i124705207523877');

define('PROPERTY_FINISHED_ACTIVITIES',					NS_TAOQUAL.'#i12525951541482');



$todefine = array(
	'TAO_OBJECT_CLASS' 		=> 'http://www.tao.lu/Ontologies/TAO.rdf#TAOObject',
	'TAO_DELIVERY_CLASS' 		=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Delivery',
	'TAO_SUBJECT_CLASS' 	=> 'http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject',
	'TAO_GROUP_CLASS' => 'http://www.tao.lu/Ontologies/TAOGroup.rdf#Group',
	'TAO_TEST_CLASS' 		=> 'http://www.tao.lu/Ontologies/TAOTest.rdf#Test',
	'TAO_ITEM_MODEL_CLASS' 	=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModels',
	'TAO_RESULT_CLASS' 		=> 'http://www.tao.lu/Ontologies/TAOResult.rdf#Result',
	'TEST_RELATED_ITEMS_PROP' 	=> 'http://www.tao.lu/Ontologies/TAOTest.rdf#RelatedItems',
	'TEST_TESTCONTENT_PROP' 	=> 'http://www.tao.lu/Ontologies/TAOTest.rdf#TestContent',
	'TEST_COMPILED_PROP' 	=> 'http://www.tao.lu/Ontologies/TAOTest.rdf#compiled',
	'TEST_ACTIVE_PROP' 	=> 'http://www.tao.lu/Ontologies/TAOTest.rdf#active',
	'ITEM_ITEMCONTENT_PROP' 	=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemContent',
	'ITEM_ITEMMODEL_PROP' 	=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModel',
	'ITEM_MODEL_RUNTIME_PROP' 	=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#SWFFile', 
	'SUBJECT_LOGIN_PROP' => 'http://www.tao.lu/Ontologies/TAOSubject.rdf#Login',
	'SUBJECT_PASSWORD_PROP' => 'http://www.tao.lu/Ontologies/TAOSubject.rdf#Password',
	
	'TAO_DELIVERY_EXCLUDEDSUBJECTS_PROP'=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ExcludedSubjects',
	'TAO_DELIVERY_TESTS_PROP'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Tests',
	
	'TAO_ITEM_MODEL_PROPERTY' 			=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModel', 
	'TAO_ITEM_MODEL_WATERPHENIX'		=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#i125933161031263',
	'TAO_ITEM_CLASS' 					=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#Item',
	'TAO_ITEM_AUTHORING_BASE_URI' 		=> $_SERVER['DOCUMENT_ROOT'].'/taoItems/data',
	'TAO_ITEM_AUTHORING_TPL_FILE' 		=> $_SERVER['DOCUMENT_ROOT'].'/taoItems/data/black_ref.xml',
	
	'GENERIS_BOOLEAN'		=> 'http://www.tao.lu/Ontologies/generis.rdf#Boolean',
	'RDFS_LABEL'			=> 'http://www.w3.org/2000/01/rdf-schema#label',
	'RDFS_TYPE'				=> 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type',
	
	'TAO_DELIVERY_COMPILED_PROP'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Compiled',
	
	'TAO_DELIVERY_CAMPAIGN_CLASS'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryCampaign',	
	'TAO_DELIVERY_CAMPAIGN_PROP'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Campaign',
	'TAO_DELIVERY_RESULTSERVER_CLASS'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryResultServer',	
	'TAO_DELIVERY_RESULTSERVER_PROP'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ResultServer',
	'TAO_DELIVERY_HISTORY_CLASS'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#History',	
	'TAO_DELIVERY_HISTORY_SUBJECT_PROP'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#HistorySubject',
	'TAO_DELIVERY_HISTORY_DELIVERY_PROP'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#HistoryDelivery',
	'TAO_DELIVERY_HISTORY_TIMESTAMP_PROP'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#HistoryTimestamp',
	'TAO_DELIVERY_DELIVERYCONTENT'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryContent',
	'TAO_DELIVERY_MAXEXEC_PROP' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#MaxExec',
	
	'CLASS_PROCESS' => NS_TAOQUAL . '#i118588753722590',
	'CLASS_CALLOFSERVICES' => NS_TAOQUAL . '#i118595077025536',
	'CLASS_TRANSITIONRULES' => NS_TAOQUAL . '#i122206969324866',
	'CLASS_CONNECTORS' => NS_TAOQUAL . '#i118589215756172',
	'CLASS_ACTIVITIES' => NS_TAOQUAL . '#i118588757437650',
	'CLASS_SERVICESDEFINITION' => NS_TAOQUAL . '#i118588759532084',
	'CLASS_ROLE' => NS_TAOQUAL . '#i118588820437156',
	'CLASS_FORMALPARAMETER' => NS_TAOQUAL . '#i118588904546812',
	'CLASS_WEBSERVICES' => NS_TAOQUAL . '#i118588763446870',
	'CLASS_SUPPORTSERVICES' => NS_TAOQUAL . '#i118588779325312',
	'CLASS_ACTUALPARAMETER' =>  NS_TAOQUAL . '#i118588960462136',
	'CLASS_PROCESSVARIABLES' => NS_TAOQUAL . '#i118589004639950',
	'CLASS_PROCESSINSTANCE' => NS_TAOQUAL . '#119010455660544',
	'PROPERTY_PROCESS_ACTIVITIES' => NS_TAOQUAL . '#i118735548956256',
	'PROPERTY_CALLOFSERVICES_SERVICEDEFINITION' => NS_TAOQUAL . '#i11859509039346',
	'PROPERTY_CALLOFSERVICES_ACTUALPARAMOUT' => NS_TAOQUAL . '#i118596586150000',//Used for saving the param value
	'PROPERTY_CALLOFSERVICES_ACTUALPARAMIN' => NS_TAOQUAL . '#i118595099928140',//Used for saving the param value
	'PROPERTY_SERVICESDEFINITION_FORMALPARAMOUT' => NS_TAOQUAL . '#i118588897651172',
	'PROPERTY_SERVICESDEFINITION_FORMALPARAMIN' => NS_TAOQUAL . '#i118588892919658',
	'PROPERTY_ACTUALPARAM_FORMALPARAM' => NS_TAOQUAL . '#i118588973457282',
	'PROPERTY_ACTUALPARAM_CONSTANTVALUE' => NS_TAOQUAL . '#i1185890127346',
	'PROPERTY_ACTUALPARAM_PROCESSVARIABLE' => NS_TAOQUAL . '#i11858901499008',
	'PROPERTY_ACTUALPARAM_QUALITYMETRIC' => NS_TAOQUAL . '#i118589023027962',
	'PROPERTY_FORMALPARAM_DEFAULTVALUE' => NS_TAOQUAL . '#i118588964565322',
	'PROPERTY_CONNECTORS_TYPE' => NS_TAOQUAL . '#i118595164231830',
	'PROPERTY_CONNECTORS_TRANSITIONRULE' => NS_TAOQUAL . '#i122207114241798',
	'PROPERTY_CONNECTORS_PRECACTIVITIES' => NS_TAOQUAL . '#i118589245545368',
	'PROPERTY_CONNECTORS_NEXTACTIVITIES' => NS_TAOQUAL . '#i118589252058280',
	// 'PROPERTY_TRANSITIONRULE_THEN' => NS_TAOQUAL . '#i122207070428322',
	// 'PROPERTY_TRANSITIONRULE_ELSE' => NS_TAOQUAL . '#i122207096147834',
	'PROPERTY_ACTIVITIES_INTERACTIVESERVICES' => NS_TAOQUAL . '#i118588789618848',
	'PROPERTY_ACTIVITIES_CONSISTENCYRULE' => NS_TAOQUAL . '#i122346640532066',
	'PROPERTY_ACTIVITIES_ONAFTERINFERENCERULE' => NS_TAOQUAL . '#activityInferenceRule',
	'PROPERTY_ACTIVITIES_ONBEFOREINFERENCERULE' => NS_TAOQUAL . 'activityOnBeforeInferenceRule',
	'PROPERTY_CONNECTORS_ACTIVITYREFERENCE' => NS_TAOQUAL . '#activityReference',
	'CLASS_TYPEOFCONNECTORS' => NS_TAOQUAL . '#i118589088163970',
	// 'TYPEOFCONNECTORS_SEQUENCE' => NS_TAOQUAL . '#i118589243226718',
	// 'TYPEOFCONNECTORS_SPLIT' => NS_TAOQUAL . '#i118589220353990',
	'PROPERTY_TRANSITIONRULES_THEN' => NS_TAOQUAL . '#i122207070428322',
	'PROPERTY_TRANSITIONRULES_ELSE' => NS_TAOQUAL . '#i122207096147834',
	// 'VAR_PROCESSINSTANCE_URI' => NS_TAOQUAL . '#var_processinstance',
	'PROPERTY_CODE' => NS_TAOQUAL . '#code',
	'INSTANCE_TYPEOFCONNECTORS_SPLIT' => NS_TAOQUAL . '#i118589220353990',
	'INSTANCE_TYPEOFCONNECTORS_SEQUENCE' => NS_TAOQUAL . '#i118589243226718',
	'PROPERTY_SUPPORTSERVICES_URL' => NS_TAOQUAL .'#i11858886911216',
	'VAR_SUBJECTURI' => 'http://127.0.0.1/middleware/demo.rdf#i1267544223024059900',
	'VAR_WSDL' => 'http://127.0.0.1/middleware/demo.rdf#i1267544266063085400'
	/*
	CLASS_EXPRESSION
	PROPERTY_FIRST_EXPRESSION
	PROPERTY_SECOND_EXPRESSION
	PROPERTY_HASLOGICALOPERATOR
	PROPERTY_TERMINAL_EXPRESSION
	PROPERTY_TERM_VALUE
	
	INSTANCE_AND_OPERATOR
	INSTANCE_OR_OPERATOR
	// INSTANCE_NOT_OPERATOR //doesn't exist in genesis constants file
	INSTANCE_EMPTY_TERM_URI
	INSTANCE_EXISTS_OPERATOR_URI
	INSTANCE_EQUALS_OPERATOR_URI
	INSTANCE_DIFFERENT_OPERATOR_URI
	INSTANCE_SUP_EQ_OPERATOR_URI
	INSTANCE_INF_EQ_OPERATOR_URI
	INSTANCE_SUP_OPERATOR_URI
	INSTANCE_INF_OPERATOR_URI
	CLASS_TERM_SUJET_PREDICATE_X
	PROPERTY_TERM_SPX_SUBJET
	PROPERTY_TERM_SPX_PREDICATE
	INSTANCE_TERM_IS_NULL
	CLASS_TERM_CONST
	PROPERTY_OPERATION_OPERATOR
	CLASS_OPERATION
	INSTANCE_OPERATOR_ADD
	INSTANCE_OPERATOR_MINUS
	INSTANCE_OPERATOR_MULTIPLY
	INSTANCE_OPERATOR_DIVISION
	INSTANCE_OPERATOR_CONCAT
	PROPERTY_OPERATION_FIRST_OP
	PROPERTY_OPERATION_SECND_OP*/
);

foreach($todefine as $constName => $constValue){
	if(!defined($constName)){
		define($constName, $constValue);
	}
}
unset($todefine);

define('API_LOGIN','generis');
define('API_PASSWORD',md5('g3n3r1s'));

?>
