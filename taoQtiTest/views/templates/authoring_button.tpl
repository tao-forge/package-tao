<div id="item-container" class="data-container" >
	<div class="ui-widget ui-state-default ui-widget-header ui-corner-top container-title" >
		<?=__('QTI Test: %s', get_data('label'))?>
	</div>
    <div class="ui-widget ui-widget-content container-content">
		<ul class="contentList">
           <li id='authoringButton' class="contentButton"><?=__('Author QTI test')?></li>
           <li id='importAction' class="contentButton"><?=__('Import QTI test')?></li>
        </ul>
	</div>	
	<div class="emptyContentFooter ui-widget ui-widget-content ui-state-default ui-corner-bottom" style="text-align:center; padding:4px;">
	</div>
</div>
<script type="text/javascript">
    require(['jquery', 'helpers'], function($, helpers) {

            $('#authoringButton').click(function(e) {
                    e.preventDefault();
                    var uri = '<?=_url('index', 'Creator', 'taoQtiTest', array('uri' => get_data('uri')))?>';
                    if($("div#tabs ul.ui-tabs-nav li a").length > 1){
                         helpers.closeTab(1);
                    }
                    helpers.openTab('<?=__('Authoring %s', get_data('label'))?>', uri);
            });

            $('#importAction').click(function(e) {
                    e.preventDefault();
                    var uri = '<?=_url('index', 'Import', 'taoQtiTest', array('uri' => get_data('uri')))?>';
                    if($("div#tabs ul.ui-tabs-nav li a").length > 1){
                         helpers.closeTab(1);
                    }

                    helpers.openTab('<?=__('Import into %s', get_data('label'))?>', uri);
            });
});
</script>
