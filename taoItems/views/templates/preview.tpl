
<div class="ui-widget ui-state-default ui-widget-header ui-corner-top container-title" >
	<?=get_data('previewTitle')?>
</div>
<div class="ui-widget ui-widget-content">
	<?if(get_data('preview')):?>
	<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="700" height="600" id="tao_item" align="middle">
		<param name="allowScriptAccess" value="sameDomain" />
		<param name="movie" value="<?=get_data('swf')?>?localXmlFile=<?=get_data('contentUrl')?>&instance=<?=get_data('instanceUri')?>" />
		<param name="quality" value="high" />
		<param name="bgcolor" value="#ffffff" />
		<embed src="<?=get_data('swf')?>?localXmlFile=<?=get_data('contentUrl')?>&instance=<?=get_data('instanceUri')?>" quality="high" bgcolor="#ffffff"  width="700" height="600" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
	</object>
	<?else:?>
		<span style="font-size:28px;">
		<?=__('PREVIEW BOX')?><br /><br />
		<?=get_data('previewMsg')?>
		</span>
	<?endif?>
</div>
