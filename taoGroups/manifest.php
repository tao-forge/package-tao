<?php
/*
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * 
 */
return array(
	'name' => 'taoGroups',
	'description' => 'TAO Groups extension',
	'additional' => array(
		'version' => '2.0',
		'author' => 'CRP Henri Tudor',
		'dependances' => array('tao'),
		'models' => 'http://www.tao.lu/Ontologies/TAOGroup.rdf',
		'install' => array( 
			'php' => dirname(__FILE__). '/install/install.php',
			'rdf' => dirname(__FILE__). '/models/ontology/taogroup.rdf'
		),
		'classLoaderPackages' => array( 
			dirname(__FILE__).'/actions/',
			dirname(__FILE__).'/helpers/'
		 )
	)
);
?>