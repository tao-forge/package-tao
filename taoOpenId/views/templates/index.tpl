<?php
use oat\tao\helpers\Template;
?>
<link rel="stylesheet" href="<?= Template::css('openid.css') ?>"/>
<div class="main-container">
	<div class="ext-home-container ui-state-highlight">
		<h1><?=__('Open ID')?></h1>
		<p><?=__('The Open ID Provider library.')?></p>
	</div>
</div>

<?php
Template::inc('footer.tpl', 'tao');
?>