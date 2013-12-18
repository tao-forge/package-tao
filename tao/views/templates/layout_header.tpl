<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title><?=PRODUCT_NAME?> <?=TAO_VERSION?></title>
	<link rel="shortcut icon" href="<?=BASE_WWW?>img/favicon.ico" type="image/x-icon" />

        
	<?=tao_helpers_Scriptloader::render()?>

        <script id='amd-loader' 
                type="text/javascript" 
                src="<?=TAOBASE_WWW?>js/lib/require.js" 
                data-main="<?=TAOBASE_WWW?>js/main"
                data-config="<?=get_data('client_config_url')?>"></script>
	
	<!-- Error Handling -->
	<? include(TAO_TPL_PATH . 'errors.tpl') ?>
</head>
<body>
	<!-- AJAX Main Spinner Element -->
	<div id="ajax-loading" class="ui-widget-overlay"></div>