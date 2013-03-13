<?php
/*  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);\n *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php
/**
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 *
 */
$todefine = array(
	'RESULT_ONTOLOGY'		=> 'http://www.tao.lu/Ontologies/TAOResult.rdf',
	'ITEM_ONTOLOGY'			=> 'http://www.tao.lu/Ontologies/TAOItem.rdf',
	'GROUP_ONTOLOGY'		=> 'http://www.tao.lu/Ontologies/TAOGroup.rdf',
	'TEST_ONTOLOGY'			=> 'http://www.tao.lu/Ontologies/TAOTest.rdf',
	'SUBJECT_ONTOLOGY'		=> 'http://www.tao.lu/Ontologies/TAOSubject.rdf',

	'SCORE_ID'					=> 'SCORE',
	'SCORE_MIN_ID'				=> 'SCORE_MIN',
	'SCORE_MAX_ID'				=> 'SCORE_MAX',
	'ENDORSMENT_ID'				=> 'ENDORSMENT',
	'ANSWERED_VALUES_ID'		=> 'ANSWERED_VALUES',

	// defined in tao
	//'TAO_RESULT_CLASS'			=> 'http://www.tao.lu/Ontologies/TAOResult.rdf#Result',
	
	'TAO_DELIVERY_RESULT'			=> 'http://www.tao.lu/Ontologies/TAOResult.rdf#DeliveryResult',
	'PROPERTY_RESULT_OF_SUBJECT'	=> 'http://www.tao.lu/Ontologies/TAOResult.rdf#resultOfSubject',
	'PROPERTY_RESULT_OF_DELIVERY'	=> 'http://www.tao.lu/Ontologies/TAOResult.rdf#resultOfDelivery',
	'PROPERTY_RESULT_OF_PROCESS'	=> 'http://www.tao.lu/Ontologies/TAOResult.rdf#resultOfProcess',
	'TAO_RESULT_VARIABLE'			=> 'http://www.tao.lu/Ontologies/TAOResult.rdf#Variable',
	'TAO_RESULT_RESPONSE'			=> 'http://www.tao.lu/Ontologies/TAOResult.rdf#ResponseVariable',
	'TAO_RESULT_GRADE'				=> 'http://www.tao.lu/Ontologies/TAOResult.rdf#GradeVariable',
	'PROPERTY_MEMBER_OF_RESULT'		=> 'http://www.tao.lu/Ontologies/TAOResult.rdf#memberOfDeliveryResult',
	'PROPERTY_VARIABLE_ORIGIN'		=> 'http://www.tao.lu/Ontologies/TAOResult.rdf#variableOrigin',
	'PROPERTY_VARIABLE_IDENTIFIER'	=> 'http://www.tao.lu/Ontologies/TAOResult.rdf#variableIdentifier',
	'PROPERTY_VARIABLE_DERIVATED'	=> 'http://www.tao.lu/Ontologies/TAOResult.rdf#derivatedFrom',
	'PROPERTY_VARIABLE_AUTHOR'	=> 'http://www.tao.lu/Ontologies/TAOResult.rdf#authoredBy',
	'PROPERTY_GRADE_FINAL'			=> 'http://www.tao.lu/Ontologies/TAOResult.rdf#gradeFinal',

	// old
	'TAO_ITEM_RESULTS_CLASS'=> 'http://www.tao.lu/Ontologies/TAOResult.rdf#TAO_ITEM_RESULTS',
	'PROP_RESULT_PROCESS_EXEC_ID'	=> 'http://www.tao.lu/Ontologies/TAOResult.rdf#TAO_PROCESS_EXEC_ID',
	'PROP_RESULT_DELIVERY_ID'		=> 'http://www.tao.lu/Ontologies/TAOResult.rdf#TAO_DELIVERY_ID',
	'PROP_RESULT_TEST_ID'			=> 'http://www.tao.lu/Ontologies/TAOResult.rdf#TAO_TEST_ID',
	'PROP_RESULT_ITEM_ID'			=> 'http://www.tao.lu/Ontologies/TAOResult.rdf#TAO_ITEM_ID',
	'PROP_RESULT_SUBJECT_ID'		=> 'http://www.tao.lu/Ontologies/TAOResult.rdf#TAO_SUBJECT_ID',
	'PROP_RESULT_ITEM_VARIABLE_ID'	=> 'http://www.tao.lu/Ontologies/TAOResult.rdf#TAO_ITEM_VARIABLE_ID',
	'PROP_RESULT_ITEM_VARIABLE_VALUE' => 'http://www.tao.lu/Ontologies/TAOResult.rdf#TAO_ITEM_VARIABLE_VALUE'
);
?>