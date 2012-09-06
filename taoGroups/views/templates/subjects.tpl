<div id="subject-container" class="data-container">
	<div class="ui-widget ui-state-default ui-widget-header ui-corner-top container-title" >
		<?=__('Select group test takers')?>
	</div>
	<div class="ui-widget ui-widget-content container-content">
		<div id="subject-tree"></div>
		<div class="breaker"></div>
	</div>
	<div class="ui-widget ui-widget-content ui-state-default ui-corner-bottom" style="text-align:center; padding:4px;">
		<input id="saver-action-subject" type="button" value="<?=__('Save')?>" />
	</div>
</div>
<?if(!get_data('myForm')):?>
	<input type='hidden' name='uri' value="<?=get_data('uri')?>" />
	<input type='hidden' name='classUri' value="<?=get_data('classUri')?>" />
<?endif?>
<script type="text/javascript">
$(function(){
	require(['require', 'jquery', 'generis.tree.select'], function(req, $, GenerisTreeSelectClass) {
		if (ctx_extension) {
			url = root_url + '/' + ctx_extension + '/' + ctx_module + '/';
		}

		new GenerisTreeSelectClass('#subject-tree', url + 'getMembers', {
			actionId: 'subject',
			saveUrl: url + 'saveMembers',
			checkedNodes: <?=get_data('relatedSubjects')?>,
			paginate: 10
		});
	});
});
</script>
