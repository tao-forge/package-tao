<?php
/**
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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *
 *
 */
use oat\taoDeliveryRdf\install\RegisterDeliveryFactoryService;
use oat\taoDeliveryRdf\install\RegisterDeliveryPublishingService;
use oat\taoDeliveryRdf\scripts\install\OverrideRuntime;
use oat\taoDeliveryRdf\scripts\install\RegisterDeliveryAssemblyWrapperService;
use oat\taoDeliveryRdf\scripts\install\SetUpQueueTasks;

return array(
  'name'        => 'taoDeliveryRdf',
  'label'       => 'Delivery Management',
  'description' => 'Manages deliveries using the ontology',
  'license'     => 'GPL-2.0',
  'version'     => '4.11.0',
	'author'      => 'Open Assessment Technologies SA',
	'requires'    => array(
	    'generis'     => '>=6.14.0',
        'tao'         => '>=17.9.0',
        'taoGroups'   => '>=2.7.1',
        'taoTests'    => '>=3.5.0',
        'taoQtiTest'  => '>=18.6.0',
        'taoDelivery' => '>=9.2.0',
        'taoTaskQueue' => '>=0.14.0',
        'taoResultServer' => '>=6.1.0'
    ),
    'managementRole' => 'http://www.tao.lu/Ontologies/generis.rdf#taoDeliveryRdfManager',
    'acl' => array(
        array('grant', 'http://www.tao.lu/Ontologies/generis.rdf#taoDeliveryRdfManager', array('ext'=>'taoDeliveryRdf')),
        array('grant', \oat\tao\model\user\TaoRoles::REST_PUBLISHER, array('ext'=>'taoDeliveryRdf', 'mod' => 'RestDelivery')),
        array('grant', \oat\tao\model\user\TaoRoles::REST_PUBLISHER, array('ext'=>'taoDeliveryRdf', 'mod' => 'RestTest')),
        array('grant', 'http://www.tao.lu/Ontologies/generis.rdf#AnonymousRole','oat\taoDeliveryRdf\controller\Guest@guest'),
    ),
    'install' => array(
        'rdf' => array(
            __DIR__.DIRECTORY_SEPARATOR."install".DIRECTORY_SEPARATOR.'ontology'.DIRECTORY_SEPARATOR.'taodelivery.rdf',
            __DIR__.DIRECTORY_SEPARATOR."install".DIRECTORY_SEPARATOR.'ontology'.DIRECTORY_SEPARATOR.'widgetdefinitions.rdf'
        ),
        'php' => array(
            __DIR__.DIRECTORY_SEPARATOR."install".DIRECTORY_SEPARATOR.'registerAssignment.php',
            'oat\\taoDeliveryRdf\\install\\RegisterDeliveryContainerService',
            'oat\\taoDeliveryRdf\\scripts\\RegisterEvents',
            RegisterDeliveryFactoryService::class,
            OverrideRuntime::class,
            SetUpQueueTasks::class,
            RegisterDeliveryAssemblyWrapperService::class
        )
    ),
    //'uninstall' => array(),
    'update' => 'oat\\taoDeliveryRdf\\scripts\\update\\Updater',
    'routes' => array(
        '/taoDeliveryRdf' => 'oat\\taoDeliveryRdf\\controller'
    ),
	'constants' => array(
	    # views directory
	    "DIR_VIEWS" => dirname(__FILE__).DIRECTORY_SEPARATOR."views".DIRECTORY_SEPARATOR,

		#BASE URL (usually the domain root)
		'BASE_URL' => ROOT_URL.'taoDeliveryRdf/',
	),
    'extra' => array(
        'structures' => dirname(__FILE__).DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.'structures.xml',
    )
);
