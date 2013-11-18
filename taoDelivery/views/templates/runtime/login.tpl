<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?= __("TAO Delivery Server"); ?></title>
	 	
		<link rel="stylesheet" type="text/css" media="screen" href="<?=TAOBASE_WWW?>css/custom-theme/jquery-ui-1.8.22.custom.css"/>
		<link rel="stylesheet" type="text/css" media="screen" href="<?=TAOBASE_WWW?>css/style.css"/>
		<link rel="stylesheet" type="text/css" media="screen" href="<?=TAOBASE_WWW?>css/layout.css"/>
		<link rel="stylesheet" type="text/css" media="screen" href="<?=TAOBASE_WWW?>css/form.css"/>
		<link rel="stylesheet" type="text/css" href="<?=TAOBASE_WWW?>css/portal.css" />
		<link rel="stylesheet" type="text/css" href="<?=TAOBASE_WWW?>css/login.css" />
		<link rel="stylesheet" type="text/css" href="<?=BASE_WWW?>css/login.css" />
		<style media="screen">
			@import url(<?echo BASE_WWW; ?>css/login.css);
		</style>
		<script type="text/javascript">
			var root_url = '<?=ROOT_URL?>';
			var base_url = '<?=BASE_URL?>';
			var taobase_www = '<?=TAOBASE_WWW?>';
			var base_www = '<?=BASE_WWW?>';
			var base_lang = '<?=strtolower(tao_helpers_I18n::getLangCode())?>';
		</script>
		<script src="<?=TAOBASE_WWW?>js/require-jquery.js" data-main="<?=TAOBASE_WWW?>js/main"></script>
		<script type="text/javascript" src="<?=TAOBASE_WWW?>js/login.js"></script>
	</head>
	<body>
	  <div id="portal-box" class="ui-tabs ui-widget ui-widget-content ui-corner-all">
		<span class="loginHeader">
		    <span class="Title">
			<?=__('Test Takers')?>
		    </span>
		    <span class="hintMsg">
			    <?=__('Login to the TAO Delivery Server')?>&nbsp;&nbsp;<a href="<?echo ROOT_URL; ?>"><?=__('Change...')?></a>
		    </span>
		    <span class=hintLink>
		    </span>
		</span>
		<span class="loginBox">
			<?if(get_data('errorMessage')):?>
				<div class="ui-widget ui-corner-all ui-state-error error-message">
					<?=urldecode(get_data('errorMessage'))?>
				</div>

			<?endif?>
			<div id="login-form" >
				<?=get_data('form')?>
			</div>
		</span>
	    </div>
<? include TAO_TPL_PATH .'layout_footer.tpl';?>
	    
