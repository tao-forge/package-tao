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
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php
//define specific constants to delivery extension:
$todefine = array(
	'TAO_DELIVERY_EXCLUDEDSUBJECTS_PROP'=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ExcludedSubjects',

    'TAO_DELIVERY_RESULTSERVER_PROP'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryResultServer',
	//todo ppl avoid dependency
	'TAO_DELIVERY_DEFAULT_RESULT_SERVER' => 'http://www.tao.lu/Ontologies/TAOResultServer.rdf#taoResultServer',
   
	'TAO_DELIVERY_ACTIVE_PROP'				=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#active',
    'TAO_DELIVERY_MAXEXEC_PROP' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Maxexec',
	'TAO_DELIVERY_START_PROP' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#PeriodStart',
	'TAO_DELIVERY_END_PROP' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#PeriodEnd',
	
	'PROPERTY_DELIVERY_CONTENT'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryContent',
    'PROPERTY_DELIVERY_COMPILED'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryCompiledDelivery',
    
    'CLASS_ABSTRACT_DELIVERYCONTENT'       => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#AbstractDeliveryContent',
    'PROPERTY_CONTENTCLASS_IMPLEMENTATION' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryContentImplementation',
     
    'CLASS_COMPILEDDELIVERY'               => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#CompiledDelivery',
    'PROPERTY_COMPILEDDELIVERY_TIME'       => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#CompiledDeliveryCompilationTime',
    'PROPERTY_COMPILEDDELIVERY_RUNTIME'    => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#CompiledDeliveryRuntime',
    'PROPERTY_COMPILEDDELIVERY_FOLDER'     => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#CompiledDeliveryCompilationFolder',
    
    'CLASS_DELVIERYEXECUTION'              => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryExecution',
    'PROPERTY_DELVIERYEXECUTION_DELIVERY'  => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryExecutionDelivery',
    'PROPERTY_DELVIERYEXECUTION_SUBJECT'   => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryExecutionSubject',
    'PROPERTY_DELVIERYEXECUTION_START'     => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryExecutionStart',
    'PROPERTY_DELVIERYEXECUTION_END'       => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryExecutionStart',
    'PROPERTY_DELVIERYEXECUTION_STATUS'    => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#StatusOfDeliveryExecution',
    
    'INSTANCE_DELIVERYEXEC_ACTIVE'         => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryExecutionStatusActive',
    'INSTANCE_DELIVERYEXEC_FINISHED'       => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryExecutionStatusFinished',
    
    
    
    // @todo currently defined in group, should be defined in delivery to mirror the dependencies
	//'TAO_GROUP_DELIVERIES_PROP'	=> 'http://www.tao.lu/Ontologies/TAOGroup.rdf#Deliveries',
	
	// Coding
	'TAO_DELIVERY_CODINGMETHOD_PROP'		=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryCodingMethode',
	'TAO_DELIVERY_CODINGMETHOD_AUTOMATED'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#CodingMethodeAutomated',
	'TAO_DELIVERY_CODINGMETHOD_MANUAL'		=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#CodingMethodeManual',

	'TAO_DELIVERY_CODINGSTATUS_PROP'			=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryCodingStatus',
	'TAO_DELIVERY_CODINGSTATUS_GRADING'			=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#CodingStatusGrading',
	'TAO_DELIVERY_CODINGSTATUS_CONCILIATION'	=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#CodingStatusConciliation',
	'TAO_DELIVERY_CODINGSTATUS_COMMITED'		=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#CodingStatusCommited',
	
);