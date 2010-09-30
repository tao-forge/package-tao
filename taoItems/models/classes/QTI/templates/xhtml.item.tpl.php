<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>QTI Item <?=$identifier?></title>

	<!-- CSS -->
	<link rel="stylesheet" type="text/css" href="css/reset.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="css/main.css" media="screen" />
	<!-- LIB -->
	<script type="text/javascript" src="lib/jquery/jquery.js"></script>
	<!-- LIB (required for sort item interaction) -->
	<script type="text/javascript" src="lib/jquery-ui-1.8.4.custom.min.js"></script>
	<!-- JS REQUIRED -->
	<script type="text/javascript" src="js/main.js"></script>
	<script type="text/javascript">
		var data_container = new Object();
		var qti_initParam  = new Object();
		data_container.init =qti_initParam;
		$(document).ready(function(){
			qti_init();	
		});
	</script>

</head>                                             
	<body>
	
	<?=$data?>
	
	<!-- validation button -->
	<!-- <a href="#" id="qti_validate">Validation</a> -->
	</body>
 </html>