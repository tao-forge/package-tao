<script type="text/javascript">
var ctx_extension 	= "<?=get_data('extension')?>";
var ctx_module 		= "<?=get_data('module')?>";
var ctx_action 		= "<?=get_data('action')?>";
$(function(){
	
	<?if(get_data('reload') === true):?>	
		
		<?if(get_data('showNodeUri')):?>
			var showNode = "<?=get_data('showNodeUri')?>";
		<?else:?>
			var showNode = false;
		<?endif?>
		
	loadControls();
	
	<?else:?>
	
	initNavigation();
	
	<?endif?>
});
</script>