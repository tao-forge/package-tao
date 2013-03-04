<?php header("HTTP/1.0 500 Internal Server Error"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<title>Internal Server Error</title>
	<script type="text/javascript" src="<?= ROOT_URL ?>tao/views/js/jquery-1.8.0.min.js "></script>
	<script type="text/javascript" src="<?= ROOT_URL ?>tao/views/js/jquery-ui-1.8.23.custom.min.js"></script>

	<link rel="stylesheet" type="text/css" href="<?= ROOT_URL ?>tao/views/css/reset.css" />
	<link rel="stylesheet" type="text/css" href="<?= ROOT_URL ?>tao/views/css/custom-theme/jquery-ui-1.8.22.custom.css" />
	<link rel="stylesheet" type="text/css" href="<?= ROOT_URL ?>tao/views/css/errors.css" />
	<link rel="stylesheet" type="text/css" href="<?= ROOT_URL ?>tao/views/css/error500.css" />
</head>

<body>
	<div id="main" class="ui-widget-content ui-corner-all" style="background-image: url(<?= ROOT_URL ?>tao/views/img/errors/500.png);">
		<div id="content">
			<h1>Internal Server Error</h1>
			<p id="warning_msg">
				<img src="<?= ROOT_URL ?>tao/views/img/warning_error_tpl.png" alt="warning" class="embedWarning" />
				The <strong>page</strong> you requested <strong>generated an unexpecter error</strong> on this server. 
				Make sure <strong>the address</strong> you entered in your <strong>web browser</strong> is valid or try
				again later. If you are sure that the address is correct but this page is still displayed, 
				<strong>contact your TAO administrator</strong> to get support.
			</p>
			
			<? if (!empty($message)): ?>
			<p>
				<strong>Debug Message:</strong>
				
				<p>
					<?= nl2br($message) ?>
				</p>
			</p>
			<? endif; ?>
			
			<? if (!empty($trace)): ?>
			<p>
				<strong>Stack Trace:</strong>
				
				<p class="trace">
					<?= nl2br($trace) ?>
				</p>
			</p>
			<? endif; ?>
			<a href="<?= ROOT_URL ?>" id="go_to_tao_bt">Go to the TAO Platform</a>
		</div>
	</div>
</body>

</html>