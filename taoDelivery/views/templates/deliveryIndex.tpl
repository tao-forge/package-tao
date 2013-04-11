<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=tao_helpers_I18n::getLangCode()?>" lang="<?=tao_helpers_I18n::getLangCode()?>">
	<head>
		<title><?php echo __("TAO - An Open and Versatile Computer-Based Assessment Platform"); ?></title>
		<script type="text/javascript">
			var root_url = '<?=ROOT_URL?>';
			var base_url = '<?=BASE_URL?>';
			var taobase_www = '<?=TAOBASE_WWW?>';
			var base_www = '<?=BASE_WWW?>';
			var base_lang = '<?=strtolower(tao_helpers_I18n::getLangCode())?>';
		</script>
		<script src="<?=TAOBASE_WWW?>js/require-jquery.js"></script>
		<script src="<?=TAOBASE_WWW?>js/main.js"></script>
		<link rel="stylesheet" type="text/css" href="<?=TAOBASE_WWW?>css/custom-theme/jquery-ui-1.8.22.custom.css" />
		<style media="screen">
			@import url(<?echo BASE_WWW; ?>css/main.css);
		</style>
	</head>

	<body>
		<div id="process_view"></div>

		<ul id="control">
        	<li>
        		<span id="connecteduser" class="icon"><?php echo __("User name:"); ?> <span id="username"><?php echo $login; ?></span> </span>
        		<span class="separator"></span>
        	</li>
         	<li>
         		<a class="action icon" id="logout" href="<?=_url('logout', 'DeliveryServerAuthentification')?>"><?php echo __("Logout"); ?></a>
         	</li>
		</ul>

		<div id="content" class='ui-corner-bottom'>
			<h1 id="welcome_message"><img src="<?=BASE_WWW?>/img/taoDelivery_medium.png" alt='delivery' />&nbsp;<?= __("TAO - An Open and Versatile Computer-Based Assessment Platform"); ?></h1>
			<div id="business">
				<h2 class="section_title"><?php echo __("Active Deliveries"); ?></h2>
			<?php if(!empty($processViewData)) : ?>
			<table id="active_processes">
				<thead>
					<tr>
						<th><?php echo __("Status"); ?></th>
						<th><?php echo __("Deliveries"); ?></th>
						<th><?php echo __("Start/Resume the test"); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($processViewData as $procData): ?>
					<tr>
						<td class="status"><img src="<?php echo BASE_WWW;?>/<?php echo wfEngine_helpers_GUIHelper::buildStatusImageURI($procData['status']); ?>"/></td>
						<td class="label"><?php echo wfEngine_helpers_GUIHelper::sanitizeGenerisString($procData['label']); ?></td>
						<td class="join">
							<?php if($procData['status'] instanceof core_kernel_classes_Resource && $procData['status']->uriResource != INSTANCE_PROCESSSTATUS_FINISHED): ?>
								<?php foreach ($procData['activities'] as $activity): ?>
									<?php if ($activity['may_participate']): ?>
									<a href="<?= _url('index', 'ProcessBrowser', null, array('processUri' => $procData['uri'], 'activityUri' => $activity['uri']));?>"><?php echo $activity['label']; ?></a>
									<?php else: ?>
									<span></span>
									<?php endif; ?>
								<?php endforeach; ?>
							<?php else: ?>
								<span><?php echo __("Finished Test"); ?></span>
							<?php endif; ?>
						</td>
					</tr>
					<?php endforeach;  ?>
				</tbody>
			</table>
			<?php else:?>
			<br/><br/>
			<?php endif; ?>

			<!-- End of Active Processes -->
			<?php if(!empty($availableProcessDefinition)) : ?>
				<h2 class="section_title"><?php echo __("Initialize new test"); ?></h2>
				<div id="new_process">
					<ul>
						<?php foreach($availableProcessDefinition as $procDef) : ?>
						<li>
							<a href="<?=_url('initDeliveryExecution', 'DeliveryServer', null, array('processDefinitionUri' => $procDef->getUri()))?>">
							<?php echo wfEngine_helpers_GUIHelper::sanitizeGenerisString($procDef->getLabel()); ?></a>
						</li>
						<?php endforeach;  ?>
					</ul>
				</div>
			<?php endif; ?>
			</div>

		</div>
		<!-- End of content -->
		<? include TAO_TPL_PATH .'footer/layout_footer_'.TAO_RELEASE_STATUS.'.tpl' ?>