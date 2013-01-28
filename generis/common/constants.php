<?php
/**
 * Generis Object Oriented API - common\constants.php
 *
 * This file is part of Generis Object Oriented API.
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package common
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */

#RDF
define('RDF_TYPE',								'http://www.w3.org/1999/02/22-rdf-syntax-ns#type',true);
define('RDF_CLASS',								'http://www.w3.org/2000/01/rdf-schema#Class',true);
define('RDF_PROPERTY',							'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property',true);
define('RDF_SUBCLASSOF',						'http://www.w3.org/2000/01/rdf-schema#subClassOf',true);
define('RDF_DOMAIN',							'http://www.w3.org/2000/01/rdf-schema#domain',true);
define('RDF_VALUE',								'http://www.w3.org/1999/02/22-rdf-syntax-ns#value',true);
define('RDFS_RANGE',							'http://www.w3.org/2000/01/rdf-schema#range',true);
define('RDF_RESOURCE',							'http://www.w3.org/2000/01/rdf-schema#Resource',true);

define('RDF_FIRST',								'http://www.w3.org/1999/02/22-rdf-syntax-ns#first', true);
define('RDF_REST',								'http://www.w3.org/1999/02/22-rdf-syntax-ns#rest', true);
define('RDF_LIST',								'http://www.w3.org/1999/02/22-rdf-syntax-ns#List', true);
define('RDF_NIL',								'http://www.w3.org/1999/02/22-rdf-syntax-ns#nil', true);

#RDFS
define('RDFS_COMMENT',							'http://www.w3.org/2000/01/rdf-schema#comment',true);
define('RDFS_LABEL',							'http://www.w3.org/2000/01/rdf-schema#label', true);
define('RDFS_LITERAL',							'http://www.w3.org/2000/01/rdf-schema#Literal', true);
define('RDFS_SEEALSO', 							'http://www.w3.org/2000/01/rdf-schema#seeAlso', true);
define('RDFS_DATATYPE', 						'http://www.w3.org/2000/01/rdf-schema#Datatype', true);
define('RDFS_STATEMENT', 						'http://www.w3.org/1999/02/22-rdf-syntax-ns#Statement', true);
define('RDFS_CLASS',							'http://www.w3.org/2000/01/rdf-schema#Class', true);
define('RDFS_SUBCLASSOF',						'http://www.w3.org/2000/01/rdf-schema#subClassOf', true);
define('RDFS_DOMAIN',							'http://www.w3.org/2000/01/rdf-schema#domain', true);
define('RDFS_RESOURCE',							'http://www.w3.org/2000/01/rdf-schema#Resource', true);

#generis
define('GENERIS_NS',							'http://www.tao.lu/Ontologies/generis.rdf', true ) ;
define('GENERIS_BOOLEAN',				 		GENERIS_NS . '#Boolean', true);
define('GENERIS_TRUE',							GENERIS_NS . '#True' , true);
define('GENERIS_FALSE',							GENERIS_NS . '#False' , true);
define('PROPERTY_IS_LG_DEPENDENT',				GENERIS_NS . '#is_language_dependent' , true);
define('CLASS_GENERIS_USER' , 					GENERIS_NS . '#User' , true) ;
define('CLASS_GENERIS_RESOURCE' , 				GENERIS_NS . '#generis_Ressource' , true) ;
define('CLASS_GENERIS_FILE' , 				    GENERIS_NS . '#File' , true) ;
define('PROPERTY_FILE_FILENAME' , 				GENERIS_NS . '#FileName' , true) ;
define('PROPERTY_FILE_FILEPATH' , 				GENERIS_NS . '#FilePath' , true) ;
define('PROPERTY_MULTIPLE',						GENERIS_NS . '#Multiple' , true) ;

#versioned file
define('CLASS_GENERIS_VERSIONEDFILE' , 			GENERIS_NS . '#VersionedFile' , true) ;
define('PROPERTY_VERSIONEDFILE_VERSION' , 		GENERIS_NS . '#VersionedFileVersion' , true) ;
define('PROPERTY_VERSIONEDFILE_REPOSITORY' ,	GENERIS_NS . '#VersionedFileRepository' , true) ;
define('PROPERTY_VERSIONEDFILE_FILEPATH' , 		GENERIS_NS . '#VersionedFilePath' , true) ;

#Versioned Repository
define('CLASS_GENERIS_VERSIONEDREPOSITORY' ,				GENERIS_NS . '#VersionedRepository' , true) ;
define('PROPERTY_GENERIS_VERSIONEDREPOSITORY_URL' , 		GENERIS_NS . '#VersionedRepositoryUrl' , true) ;
define('PROPERTY_GENERIS_VERSIONEDREPOSITORY_PATH' , 		GENERIS_NS . '#VersionedRepositoryPath' , true) ;
define('PROPERTY_GENERIS_VERSIONEDREPOSITORY_TYPE' , 		GENERIS_NS . '#VersionedRepositoryType' , true) ;
define('PROPERTY_GENERIS_VERSIONEDREPOSITORY_LOGIN' , 		GENERIS_NS . '#VersionedRepositoryLogin' , true) ;
define('PROPERTY_GENERIS_VERSIONEDREPOSITORY_PASSWORD' , 	GENERIS_NS . '#VersionedRepositoryPassword' , true) ;
define('PROPERTY_GENERIS_VERSIONEDREPOSITORY_ENABLED' , 	GENERIS_NS . '#VersionedRepositoryEnabled' , true) ;
define('PROPERTY_GENERIS_VERSIONEDREPOSITORY_ROOTFILE' , 	GENERIS_NS . '#RepositoryRootFile' , true) ;

define('PROPERTY_GENERIS_VCS_TYPE_SUBVERSION' ,				GENERIS_NS . '#VCSTypeSubversion' , true) ;
define('PROPERTY_GENERIS_VCS_TYPE_SUBVERSION_WIN' ,			GENERIS_NS . '#VCSTypeSubversionWindows' , true) ;
define('PROPERTY_GENERIS_VCS_TYPE_CVS' ,	 				GENERIS_NS . '#VCSTypeCvs' , true) ;
define('INSTANCE_GENERIS_VCS_TYPE_LOCAL' , 					GENERIS_NS . '#VCSLocalDirectory' , true) ;

#user
define('CLASS_ROLE',							GENERIS_NS . '#ClassRole' ,true);
define('PROPERTY_ROLE_ISSYSTEM',				GENERIS_NS . '#isSystem' , true);
define('PROPERTY_ROLE_INCLUDESROLE',			GENERIS_NS . '#includesRole' , true);
define('PROPERTY_USER_LOGIN' ,					GENERIS_NS . '#login' , true);
define('PROPERTY_USER_PASSWORD' , 				GENERIS_NS . '#password' , true);
define('PROPERTY_USER_UILG' ,					GENERIS_NS . '#userUILg' , true);
define('PROPERTY_USER_DEFLG' ,					GENERIS_NS . '#userDefLg' , true);
define('PROPERTY_USER_MAIL' ,					GENERIS_NS . '#userMail' , true);
define('PROPERTY_USER_FIRTNAME' , 				GENERIS_NS . '#userFirstName' , true);
define('PROPERTY_USER_FIRSTNAME' , 				GENERIS_NS . '#userFirstName' , true) ;
define('PROPERTY_USER_LASTNAME' , 				GENERIS_NS . '#userLastName' , true);
define('PROPERTY_USER_ROLES',					GENERIS_NS . '#userRoles' , true);
define('INSTANCE_ROLE_GENERIS',					GENERIS_NS . '#GenerisRole' , true);

define('FUNCACL_NS',							'http://www.tao.lu/Ontologies/taoFuncACL.rdf', true);
define('INSTANCE_ROLE_BASEACCESS',				FUNCACL_NS.'#BaseAccessRole', true) ;

define('CLASS_ACL_MODULE',						FUNCACL_NS.'#Module', true);
define('PROPERTY_ACL_MODULE_ID',				FUNCACL_NS.'#moduleIdentifier', true);
define('PROPERTY_ACL_MODULE_EXTENSION',			FUNCACL_NS.'#moduleExtension', true);
define('PROPERTY_ACL_MODULE_GRANTACCESS',		FUNCACL_NS.'#grantAccessModule', true);

define('CLASS_ACL_ACTION',						FUNCACL_NS.'#Action', true);
define('PROPERTY_ACL_ACTION_ID',				FUNCACL_NS.'#actionIdentifier', true);
define('PROPERTY_ACL_ACTION_MEMBEROF',			FUNCACL_NS.'#actionMemberOf', true);
define('PROPERTY_ACL_ACTION_GRANTACCESS',		FUNCACL_NS.'#grantAccessAction', true);

define('CLASS_SUBCRIPTION',						GENERIS_NS . '#Subscription' , true) ;
define('PROPERTY_SUBCRIPTION_URL', 				GENERIS_NS . '#SubscriptionUrl' , true) ;
define('PROPERTY_SUBCRIPTION_MASK', 			GENERIS_NS . '#SubscriptionMask' , true) ;

define('CLASS_MASK',							GENERIS_NS . '#Mask' , true) ;
define('PROPERTY_MASK_SUBJECT',					GENERIS_NS . '#MaskSubject' , true) ;
define('PROPERTY_MASK_PREDICATE', 				GENERIS_NS . '#MaskPredicate' , true) ;
define('PROPERTY_MASK_OBJECT', 				    GENERIS_NS . '#MaskObject' , true) ;


#widget
define('CLASS_WIDGET',							'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#WidgetClass',true);
define('PROPERTY_WIDGET',						'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget',true);
define('WIDGET_RADIO',							'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#RadioBox',true);
define('WIDGET_COMBO',							'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#ComboBox',true);
define('WIDGET_CHECK',							'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#CheckBox',true);
define('WIDGET_FTE',							'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TextBox',true);
define('WIDGET_TIMER',							'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#Timer',true);
define('WIDGET_TREEVIEW',						'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TreeView',true);
define('WIDGET_LABEL',							'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#Label',true);
define('WIDGET_CONSTRAINT_TYPE',				'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#rangeConstraintTypes',true);

#Rules
define('RULES_NS', 								'http://www.tao.lu/middleware/Rules.rdf',true);

define('PROPERTY_OPERATION_FIRST_OP',			RULES_NS.'#FirstOperand',true);
define('PROPERTY_OPERATION_SECND_OP',			RULES_NS.'#SecondOperand',true);
define('PROPERTY_OPERATION_OPERATOR',			RULES_NS.'#HasOperator',true);
define('PROPERTY_RULE_IF' , 					RULES_NS.'#If', true);

define('CLASS_TERM_X_PREDICATE_OBJECT',			RULES_NS.'#XPredicateObject',true);
define('PROPERTY_TERM_XPO_OBJECT',				RULES_NS.'#Object', true);
define('PROPERTY_TERM_XPO_PREDICATE',			RULES_NS.'#Predicate',true);

define('INSTANCE_OPERATOR_ADD',					RULES_NS.'#Plus',true);
define('INSTANCE_OPERATOR_MINUS',				RULES_NS.'#Minus', true);
define('INSTANCE_OPERATOR_DIVISION',			RULES_NS.'#Division',true);
define('INSTANCE_OPERATOR_MULTIPLY',			RULES_NS.'#Multiply',true);
define('INSTANCE_OPERATOR_CONCAT',				RULES_NS.'#Concat', true);
define('INSTANCE_OPERATOR_UNION',				RULES_NS.'#Union',true);
define('INSTANCE_OPERATOR_INTERSECT',			RULES_NS.'#Intersect',true);

define('CLASS_CONSTRUCTED_SET',					RULES_NS.'#ConstrcuctedSet',true);
define('PROPERTY_SET_OPERATOR',					RULES_NS.'#HasSetOperator',true);
define('PROPERTY_SUBSET',						RULES_NS.'#SubSets',true);

define('PROPERTY_ASSIGNMENT_VARIABLE', 			RULES_NS.'#Variable',true);
define('PROPERTY_ASSIGNMENT_VALUE', 			RULES_NS.'#Value',true);
define('CLASS_ASSIGNMENT',						RULES_NS.'#Assignment',true);

define('CLASS_EXPRESSION',						RULES_NS.'#Expression',true);
define('PROPERTY_FIRST_EXPRESSION',				RULES_NS.'#FirstExpression',true);
define('PROPERTY_SECOND_EXPRESSION',			RULES_NS.'#SecondExpression',true);
define('PROPERTY_HASLOGICALOPERATOR',			RULES_NS.'#HasLogicalOperator',true);
define('INSTANCE_OR_OPERATOR' , 				RULES_NS.'#Or', true);
define('INSTANCE_AND_OPERATOR' , 				RULES_NS.'#And', true);

define('INSTANCE_EXPRESSION_TRUE' , 			RULES_NS.'#TrueExpression', true);
define('INSTANCE_EXPRESSION_FALSE' , 			RULES_NS.'#FalseExpression', true);

define('PROPERTY_TERMINAL_EXPRESSION' , 		RULES_NS.'#TerminalExpression', true);
define('CLASS_DYNAMICTEXT',						RULES_NS.'#DynamicText',true);
define('CLASS_RULE',							RULES_NS.'#Rule',true);
define('CLASS_TERM',							RULES_NS.'#Term',true);
define('CLASS_TERM_CONST',						RULES_NS.'#Const',true);
define('CLASS_OPERATION',						RULES_NS.'#Operation',true);
define('CLASS_TERM_SUJET_PREDICATE_X',			RULES_NS.'#SubjectPredicateX',true);
define('PROPERTY_TERM_SPX_SUBJET',				RULES_NS.'#Subject',true);
define('PROPERTY_TERM_SPX_PREDICATE',			RULES_NS.'#Predicate',true);
define('PROPERTY_TERM_VALUE',					RULES_NS.'#TermValue',true);
define('INSTANCE_EXISTS_OPERATOR_URI' , 		RULES_NS.'#Exists', true);
define('INSTANCE_EQUALS_OPERATOR_URI' , 		RULES_NS.'#Equal', true);
define('INSTANCE_DIFFERENT_OPERATOR_URI' , 		RULES_NS.'#NotEqual', true);
define('INSTANCE_SUP_EQ_OPERATOR_URI' , 		RULES_NS.'#GreaterThanOrEqual', true);
define('INSTANCE_INF_EQ_OPERATOR_URI' , 		RULES_NS.'#LessThanOrEqual', true);
define('INSTANCE_SUP_OPERATOR_URI' , 			RULES_NS.'#GreaterThan', true);
define('INSTANCE_INF_OPERATOR_URI' , 			RULES_NS.'#LessThan', true);
define('INSTANCE_EMPTY_TERM_URI' , 				RULES_NS.'#Empty', true);
define('INSTANCE_TERM_IS_NULL' ,	 			RULES_NS.'#IsNull', true);

define('PERSISTENCE_SMOOTH' ,					"smoothsql");
define('PERSISTENCE_HARD' ,						"hardsql");
define('PERSISTENCE_VIRTUOSO' ,					"virtuoso");
define('PERSISTENCE_SUBSCRIPTION' ,				"subscription");

?>