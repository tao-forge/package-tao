<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>QTI Item <?=$identifier?></title>

	<!-- CSS -->
    <link rel="stylesheet" type="text/css" href="<?=$ctx_base_www?>js/QTI/css/qti.min.css" media="screen" />
	<!-- user CSS -->
	<?foreach($stylesheets as $stylesheet):?>
		<link rel="stylesheet" type="text/css" href="<?=$stylesheet['href']?>" media="<?=$stylesheet['media']?>" />
	<?endforeach?>
	
	<!-- LIB -->
	<script type="text/javascript" src="<?=$ctx_taobase_www?>js/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="<?=$ctx_taobase_www?>js/jquery-ui-1.8.4.custom.min.js"></script>
	<script type="text/javascript" src="<?=$ctx_taobase_www?>js/json.min.js"></script>
	<script type="text/javascript" src="<?=$ctx_taobase_www?>js/raphael.min.js"></script>
	
	<?if($hasUpload):?>
	<link rel="stylesheet" type="text/css" href="<?=$ctx_qti_base_www?>lib/jquery.uploadify/uploadify.css" media="screen" />
	<script type="text/javascript" src="<?=$ctx_qti_base_www?>lib/jquery.uploadify/jquery.uploadify.v2.1.0.min.js"></script>
	<script type="text/javascript" src="<?=$ctx_qti_base_www?>lib/jquery.uploadify/swfobject.js"></script>
	<script type="text/javascript" src="<?=$ctx_qti_base_www?>lib/AsyncFileUpload.js"></script>
	<?endif?>
	
	<script type="text/javascript">
		var qti_base_www = "<?=$ctx_qti_base_www?>";
		var root_url = "<?=$ctx_root_url?>";
		<?if($ctx_debug):?>	
		var qti_debug = true;
		<?endif?>
	</script>
	
	<!-- JS REQUIRED -->
	<script type="text/javascript" src="<?=$ctx_base_www?>js/taoApi/taoApi.min.js"></script>
	<?if(!$ctx_raw_preview):?>
	<script type="text/javascript" src="<?=$ctx_root_url?>/wfEngine/views/js/wfApi/wfApi.min.js"></script>
	<?endif?>
	<script type="text/javascript" src="<?=$ctx_base_www?>js/taoMatching/taoMatching.min.js"></script>
	<script type="text/javascript" src="<?=$ctx_base_www?>js/QTI/qti.min.js"></script>
</head>
<body>
	<div id='<?=$identifier?>' class="qti_item">
		<h1><?=$options['title']?></h1>
	
		<?=$data?>
		
		<!-- validation button -->
		<div class="qti_control">
		<?if($ctx_raw_preview):?>
			<a href="#" id="qti_validate" style="visibility:hidden;">Validate</a>
		<?else:?>
			<a href="#" id="qti_validate">Validate</a>
		<?endif?>
		</div>
	</div>
</body>
</html>
