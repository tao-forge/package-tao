<?php
/**
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 *
 */
#TAO version number
define('TAO_VERSION', '2.4-alpha');

#TAO version label
define('TAO_VERSION_NAME', 'v2.4-alpha');

#the name to display
define('PRODUCT_NAME', 'TAO');

#TAO release status, use to add specific footer to TAO, available alpha, beta, demo, stable
define('TAO_RELEASE_STATUS', 'alpha');

#the temporary suffix of property URIs at CSV import.
define('TEMP_SUFFIX_CSV', '-taocsvdef');

#TAO default character encoding (mainly used with multi-byte string functions).
define('TAO_DEFAULT_ENCODING', 'UTF-8');

$todefine = array(
	'TAO_OBJECT_CLASS' 				=> 'http://www.tao.lu/Ontologies/TAO.rdf#TAOObject',
	'TAO_GROUP_CLASS' 				=> 'http://www.tao.lu/Ontologies/TAOGroup.rdf#Group',
	'TAO_ITEM_CLASS' 				=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#Item',
	'TAO_ITEM_MODEL_CLASS' 			=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModels',
	'TAO_RESULT_CLASS' 				=> 'http://www.tao.lu/Ontologies/TAOResult.rdf#Result',
	'TAO_SUBJECT_CLASS' 			=> 'http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject',
	'TAO_TEST_CLASS' 				=> 'http://www.tao.lu/Ontologies/TAOTest.rdf#Test',
	'TAO_DELIVERY_CLASS' 			=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Delivery',
	'TAO_DELIVERY_CAMPAIGN_CLASS'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryCampaign',	
	'TAO_DELIVERY_RESULTSERVER_CLASS'=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryResultServer',
	'TAO_DELIVERY_HISTORY_CLASS'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#History',
	'TAO_GROUP_MEMBERS_PROP'		=> 'http://www.tao.lu/Ontologies/TAOGroup.rdf#Members',
	'RDFS_LABEL'					=> 'http://www.w3.org/2000/01/rdf-schema#label',
	'RDFS_CLASS'					=> 'http://www.w3.org/2000/01/rdf-schema#Class',
	'RDFS_TYPE'						=> 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type',
	'GENERIS_RESOURCE'				=> 'http://www.tao.lu/Ontologies/generis.rdf#generis_Ressource',
	'GENERIS_BOOLEAN'				=> 'http://www.tao.lu/Ontologies/generis.rdf#Boolean',
	'INSTANCE_BOOLEAN_TRUE'			=> 'http://www.tao.lu/Ontologies/generis.rdf#True',
	'INSTANCE_BOOLEAN_FALSE'		=> 'http://www.tao.lu/Ontologies/generis.rdf#False',
	'TAO_LIST_CLASS'				=> 'http://www.tao.lu/Ontologies/TAO.rdf#List',
	'TAO_LIST_LEVEL_PROP'			=> 'http://www.tao.lu/Ontologies/TAO.rdf#level',
	'TAO_GUIORDER_PROP'				=> 'http://www.tao.lu/Ontologies/TAO.rdf#TAOGUIOrder',
	'CLASS_LANGUAGES'				=> 'http://www.tao.lu/Ontologies/TAO.rdf#Languages',
	'INSTANCE_ROLE_TAOMANAGER'		=> 'http://www.tao.lu/Ontologies/TAO.rdf#TaoManagerRole',
	'INSTANCE_ROLE_BACKOFFICE'		=> 'http://www.tao.lu/Ontologies/TAO.rdf#BackOfficeRole',
	'INSTANCE_ROLE_FRONTOFFICE'		=> 'http://www.tao.lu/Ontologies/TAO.rdf#FrontOfficeRole',
	'INSTANCE_ROLE_SERVICE'			=> 'http://www.tao.lu/Ontologies/TAO.rdf#ServiceRole',
	'INSTANCE_ROLE_WORKFLOW'  		=> 'http://www.tao.lu/Ontologies/TAO.rdf#WorkflowRole',
	'INSTANCE_ROLE_DELIVERY'		=> 'http://www.tao.lu/Ontologies/TAO.rdf#DeliveryRole',
	'CLASS_WORKFLOWUSER' 			=> 'http://www.tao.lu/Ontologies/TAO.rdf#WorkflowUser',
	'TAO_INSTALLATOR'  				=> 'http://www.tao.lu/Ontologies/TAO.rdf#installator',
	'PROPERTY_WIDGET_CALENDAR'		=> 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#Calendar',
	'PROPERTY_WIDGET_TEXTBOX'		=> 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TextBox',
	'PROPERTY_WIDGET_TEXTAREA'		=> 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TextArea',
	'PROPERTY_WIDGET_HTMLAREA'		=> 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#HTMLArea',
	'PROPERTY_WIDGET_PASSWORD'		=> 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#Password',
	'PROPERTY_WIDGET_HIDDENBOX'		=> 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#HiddenBox',
	'PROPERTY_WIDGET_RADIOBOX'		=> 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#RadioBox',
	'PROPERTY_WIDGET_COMBOBOX'		=> 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#ComboBox',
	'PROPERTY_WIDGET_CHECKBOX'		=> 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#CheckBox',
	'PROPERTY_WIDGET_FILE'			=> 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#AsyncFile',
	'PROPERTY_WIDGET_VERSIONEDFILE'	=> 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#VersionedFile',
	'PROPERTY_TAO_PROPERTY'			=> 'http://www.tao.lu/Ontologies/TAO.rdf#TAOProperty',
	'PROPERTY_LANGUAGE_USAGES'		=> 'http://www.tao.lu/Ontologies/TAO.rdf#LanguageUsages',
	'CLASS_LANGUAGES_USAGES'		=> 'http://www.tao.lu/Ontologies/TAO.rdf#LanguagesUsages',
	'INSTANCE_LANGUAGE_USAGE_GUI'	=> 'http://www.tao.lu/Ontologies/TAO.rdf#LanguageUsageGUI',
	'INSTANCE_LANGUAGE_USAGE_DATA'	=> 'http://www.tao.lu/Ontologies/TAO.rdf#LanguageUsageData',
    'CLASS_PROCESS_EXECUTIONS'		=> 'http://www.tao.lu/middleware/taoqual.rdf#i119010455660544'
	
);
?>
