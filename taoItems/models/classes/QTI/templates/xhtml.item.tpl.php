<?php
    // Temporary add item path to the matching vars
    // This vars is useless during a real test, it will exist in the test context
    // to simulate the 'pseudo' real behavior this var is in the ctx 
    if (isset($matching_params)){
        $matching_params = json_decode ($matching_params, true);
        $matching_params['tmp_item_path'] = $ctx_tmp_item_path;
        $matching_params = json_encode ($matching_params);
    }
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>QTI Item <?=$identifier?></title>

	<!-- CSS -->
	<link rel="stylesheet" type="text/css" href="<?=$ctx_base_www?>js/QTI/css/reset.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="<?=$ctx_base_www?>js/QTI/css/qti.css" media="screen" />
	
	<!-- user CSS -->
	<?foreach($stylesheets as $stylesheet):?>
		<link rel="stylesheet" type="text/css" href="<?=$stylesheet['href']?>" media="<?=$stylesheet['media']?>" />
	<?endforeach?>
	
	<!-- LIB -->
	<script type="text/javascript" src="<?=$ctx_taobase_www?>js/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="<?=$ctx_taobase_www?>js/jquery-ui-1.8.custom.min.js"></script>
	
	<!-- JS REQUIRED -->
	
	<script type="text/javascript" src="<?=$ctx_base_www?>js/taoApi/taoApi.min.js"></script>
    <script type="text/javascript" src="<?=$ctx_base_www?>js/taoMatching/src/class.Matching.js"></script>
    <script type="text/javascript" src="<?=$ctx_base_www?>js/taoMatching/src/class.MatchingRemote.js"></script>
    <script type="text/javascript" src="<?=$ctx_base_www?>js/taoMatching/src/class.VariableFactory.js"></script>
    <script type="text/javascript" src="<?=$ctx_base_www?>js/taoMatching/src/class.Variable.js"></script>
    <script type="text/javascript" src="<?=$ctx_base_www?>js/taoMatching/src/class.BaseTypeVariable.js"></script>
    <script type="text/javascript" src="<?=$ctx_base_www?>js/taoMatching/src/class.Collection.js"></script>
    <script type="text/javascript" src="<?=$ctx_base_www?>js/taoMatching/src/class.List.js"></script>
    <script type="text/javascript" src="<?=$ctx_base_www?>js/taoMatching/src/class.Tuple.js"></script>
    <script type="text/javascript" src="<?=$ctx_base_www?>js/taoMatching/src/class.Map.js"></script>
    <script type="text/javascript" src="<?=$ctx_base_www?>js/taoMatching/src/matching_constant.js"></script>
    <script type="text/javascript" src="<?=$ctx_base_www?>js/taoMatching/src/matching_api.js"></script>
	<!--<script type="text/javascript" src="<?=$ctx_base_www?>js/taoMatching/taoMatching.min.js"></script>-->
	<script type="text/javascript" src="<?=$ctx_base_www?>js/QTI/qti.min.js"></script>
	<script type="text/javascript">

		var myEvaluateCallbackFunction = function () {
			// Get the ouctomes
			var outcomes = matching_getOutcomes();
			console.log ('THE OUTCOME VALUE SCORE IS : '  + outcomes['SCORE']['value']);
		}
	
		var qti_initParam  = new Object();
		var matching_param = {
<?php if ($ctx_delivery_server_mode) { ?>
			"url" : "<?=isset($matching_url)?$ctx_base_www.'../../'.$matching_url:'null'?>"
			, "params" : <?=isset($matching_params)?$matching_params:'null'?>
<?php } else { ?>
			"data" : {
				"outcomes" : <?=isset($matching_outcomes)?$matching_outcomes:'[]'?>
				, "corrects" : <?=isset($matching_corrects)?$matching_corrects:'[]'?>
				, "maps" : <?=isset($matching_maps)?$matching_maps:'[]'?>
				, "rule" : '<?=isset($matching_rule)?$matching_rule:'""'?>'
			}
<?php } ?>
			, "options" : {
				"evaluateCallback" : function () {
					myEvaluateCallbackFunction ();
				}
			}
			, "format" : "json"
		};

		$(document).ready(function(){
			qti_init(qti_initParam);
			matching_init(matching_param);	
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
