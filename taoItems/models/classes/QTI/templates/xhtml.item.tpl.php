<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>QTI Item <?=$identifier?></title>

	<!-- CSS -->
	<link rel="stylesheet" type="text/css" href="<?=$rtPath?>css/reset.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="<?=$rtPath?>css/qti.css" media="screen" />
	
	<!-- LIB -->
	<script type="text/javascript" src="<?=$rtPath?>lib/jquery/jquery.js"></script>
	
	<!-- LIB (required for sort item interaction) -->
	<script type="text/javascript" src="<?=$rtPath?>lib/jquery-ui-1.8.4.custom.min.js"></script>
	
	<!-- JS REQUIRED -->
	<script type="text/javascript" src="<?=$rtPath?>src/Widget.js"></script>
	<script type="text/javascript" src="<?=$rtPath?>src/ResultCollector.js"></script>
	<script type="text/javascript" src="<?=$rtPath?>src/init.js"></script>
	<script type="text/javascript" src="<?=$rtPath?>../taoMatching/taoMatching.min.js"></script>
	<script type="text/javascript">
		var qti_initParam  = new Object();
		var matching_param = {"mode":"client", "outcomes":[], "corrects":[], "maps":[], "rule":""};
		matching_param.rule = '<?=$rule?>';
		matching_param.outcomes = <?=$outcomes?>;

		$(document).ready(function(){
			qti_init(qti_initParam);
			matching_init (matching_param);	
		});
	</script>
</head>                                             
<body>
	<div class="qti_item">
		<h1><?=$options['title']?></h1>
	
		<?=$data?>
		
		<!-- validation button -->
		<a href="#" id="qti_validate">Validate</a>
	</div>
</body>
</html>
