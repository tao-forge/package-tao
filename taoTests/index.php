<?php

/*
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * 
 */

	require_once dirname(__FILE__) . '/../generis/common/inc.extension.php';
	require_once dirname(__FILE__). '/includes/common.php';

	// internationalisation
	l10n::init();
	l10n::set(dirname(__FILE__).'/locales/'.$GLOBALS['lang'].'/messages');
	
	// helpers
	// Here are imported all core helpers
	require_once DIR_CORE_HELPERS . 'Core.php';

	try {
		$re		= new HttpRequest();
		$fc		= new AdvancedFC($re);
		$fc->loadModule();
	} catch (Exception $e) {
		$message	= $e->getMessage();
		require_once DIR_VIEWS . $GLOBALS['dir_theme'] . 'error404.tpl';
	}

?>