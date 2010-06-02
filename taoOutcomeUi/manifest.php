<?php

/*
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * 
 */
	return array(
		'name' => 'TAO Results',
		'description' => 'TAO Results extensions http://www.tao.lu',
		'additional' => array(
			'version' => '1.2',
			'author' => 'CRP Henri Tudor',
			'dependances' => array(),
			'install' => array( 
				'sql' => dirname(__FILE__). '/model/ontology/TAOResult.sql',
				'php' => dirname(__FILE__). '/install/install.php'
			),

			'model' => array('http://www.tao.lu/Ontologies/TAOResult.rdf'),
			'classLoaderPackages' => array( 
				dirname(__FILE__).'/actions/',
				dirname(__FILE__).'/helpers/'
			 )

				
			
		)
	);
?>