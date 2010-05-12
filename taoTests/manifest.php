<?php

/*
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * 
 */
	return array(
		'name' => 'TAO Tests',
		'description' => 'TAO Tests extensions http://www.tao.lu',
		'additional' => array(
			'version' => '1.0',
			'author' => 'CRP Henri Tudor',
			'dependances' => array(),
			'install' => array( 
				'sql' => dirname(__FILE__). '/model/ontology/TAOTest.sql',
				'php' => dirname(__FILE__). '/install/install.php'
			),
			'model' => array('http://www.tao.lu/Ontologies/TAOTest.rdf', 
							'http://www.tao.lu/Ontologies/TAOItem.rdf'
			),
		

			'classLoaderPackages' => array( 
				dirname(__FILE__).'/actions/',
				dirname(__FILE__).'/helpers/'
			 )

			

				
			
		)
	);
?>