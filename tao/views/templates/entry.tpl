<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>TAO</title>
	<link rel="stylesheet" type="text/css" media="screen" href="<?=BASE_WWW?>css/custom-theme/jquery-ui-1.8.22.custom.css"/>
	<link rel="stylesheet" type="text/css" media="screen" href="<?=BASE_WWW?>css/style.css"/>
	<link rel="stylesheet" type="text/css" media="screen" href="<?=BASE_WWW?>css/layout.css"/>
	<link rel="stylesheet" type="text/css" media="screen" href="<?=BASE_WWW?>css/portal.css"/>
	 <script src="<?=BASE_WWW?>js/jquery-1.8.0.min.js"></script>
</head>
    <script type="text/javascript">
	$( document ).ready(function(){
	    $('.tile').mouseover(function() {
		$(this).addClass("tileSelected");
		jQuery(".portalOperation", this).addClass("tileLabelSelected");
		jQuery(".Title", this).addClass("TitleSelected");
	    });
	    $('.tile').mouseleave(function() {
		$(this).removeClass("tileSelected");
		jQuery(".portalOperation", this).removeClass("tileLabelSelected");
		jQuery(".Title", this).removeClass("TitleSelected");
	    });
	});
    </script>

<body>
	<div id="content">
		<div id="portal-box" class="ui-tabs ui-widget ui-widget-content ui-corner-all">
		<!--<span class="portalInfo"><h1><?=__('Welcome to TAO!')?></h1></span>!-->

		<a href="<?=_url('login','Main','tao')?>" >
		    <span class="tile">
			    <span class="Title"><?=__('Test Developers and Test Administrators')?></span>
			    <span class="hintMsg">
				<?=__('Create items, manage item banks and tests, organize cohorts and schedule test deliveries.')?>
			    </span>
			    <span class="portalOperation">
				<?=__('TAO Back Office')?>
			    </span>

		    </span>
		</a>
		
		<a href="<?=_url('index','DeliveryServerAuthentification','taoDelivery')?>" >
		<span class="tile">
			<span class="Title"><?=__('Test Takers')?></span>
			<span class="hintMsg">
			    <?=__('Login here to take a test.')?>
			</span>
			<span class="portalOperation">
			    <?=__('TAO Delivery Server')?>
			</span>
			
		</span>
		</a>
		<a href="<?=_url('index','Authentication','wfEngine')?>"  >
		<span class="tile">
			<span class="Title"><?=__('Advanced Users')?></span>
			<span class="hintMsg">
			    <?=__('Check pending tasks for assessment preparation.')?>
			</span>
			<span class="portalOperation">
			   <?=__('TAO Process Engine')?>
			</span>
			</a>
		</span>
		</a>
		
		

		</div>
	</div>
	 <div id="footer">
		TAO<sup>&reg;</sup> - <?=TAO_VERSION_NAME?> - <?=TAO_RELEASE_STATUS?> - <?=date('Y')?> - A joint initiative of CRP Henri Tudor and the University of Luxembourg
	    </div>
</body>
</html>