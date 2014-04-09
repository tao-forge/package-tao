<div id="<?=get_data('id')?>-container" class="data-container">
	<div class="ui-widget ui-state-default ui-widget-header ui-corner-top container-title" >
		<?=get_data('title')?>
	</div>
	<div class="ui-widget ui-widget-content container-content">
		<div id="<?=get_data('id')?>-tree"></div>
		<div class="breaker"></div>
	</div>
	<div class="ui-widget ui-widget-content ui-state-default ui-corner-bottom" style="text-align:center; padding:4px;">
		<button id="saver-action-<?=get_data('id')?>" class="btn-info small" type="button" ><?=tao_helpers_Icon::iconSave().__('Save')?></button>
	</div>
</div>

<script type="text/javascript">
require(['jquery', 'generis.tree.select', 'helpers'], function($, GenerisTreeSelectClass, helpers) {
        new GenerisTreeSelectClass('#<?=get_data('id')?>-tree', '<?=get_data('dataUrl')?>', {
                actionId: '<?=get_data('id')?>',
                saveUrl: '<?=get_data('saveUrl')?>',
                saveData: {
                        resourceUri: '<?=get_data('resourceUri')?>',
                        propertyUri: '<?=get_data('propertyUri')?>'
                },
                checkedNodes: <?=json_encode(tao_helpers_Uri::encodeArray(get_data('values')))?>,
                serverParameters: {
                        openNodes: <?=json_encode(get_data('openNodes'))?>,
                        rootNode: <?=json_encode(get_data('rootNode'))?>
                },
                saveCallback: function() {
					helpers._load(
							helpers.getMainContainerSelector()
							,helpers._url('editDelivery', 'Delivery', 'taoDelivery')
							,{uri: '<?=get_data('resourceUri')?>'}
						);
                },
                paginate: 10
        });
});
</script>