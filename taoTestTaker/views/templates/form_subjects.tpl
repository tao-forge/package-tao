<?php
use oat\tao\helpers\Template;

Template::inc('form_context.tpl', 'tao');
?>

<?=get_data('groupForm')?>

<div class="main-container" data-tpl="taoSubjects/form_subjects.tpl">
	<div id="form-title" class="ui-widget-header ui-corner-top ui-state-default">
		<?=get_data('formTitle')?>
	</div>
	<div id="form-container" class="ui-widget-content ui-corner-bottom">
		<?=get_data('myForm')?>
	</div>
</div>

<?if(get_data('checkLogin')):?>
	<script">
	 require(['users'], function(user){
            user.checkLogin("<?=get_data('loginUri')?>", "<?=_url('checkLogin', 'Users', 'tao')?>");
	});
	</script>
<?endif?>