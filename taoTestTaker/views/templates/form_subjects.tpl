<?include(TAO_TPL_PATH . 'header.tpl')?>

<?include('groups.tpl')?>

<div class="main-container">
	<div id="form-title" class="ui-widget-header ui-corner-top ui-state-default">
		<?=get_data('formTitle')?>
	</div>
	<div id="form-container" class="ui-widget-content ui-corner-bottom">
		<?=get_data('myForm')?>
	</div>
</div>

<?if(get_data('checkLogin')):?>
	<script type="text/javascript">
		$(document).ready(function(){
			checkLogin("<?=get_data('loginUri')?>", "<?=_url('checkLogin', 'Users', 'tao')?>");
		});
	</script>
<?endif?>

<?include(TAO_TPL_PATH . 'footer.tpl');?>
