<? include TAO_TPL_PATH .'header.tpl' ?>

<div class="main-container">
	<table id="user-list"></table>
	<div id="user-list-pager"></div>
</div>
<script type="text/javascript">
require(['require', 'jquery', 'grid/tao.grid'], function(req, $) {
	function editUser(uri){
		index = helpers.getTabIndexByName('edit_user');
		if(index && uri){
			editUrl = "<?=_url('edit', 'Users', 'tao')?>" + '?uri=' + uri;
			uiBootstrap.tabs.tabs('url', index, editUrl);
			uiBootstrap.tabs.tabs('enable', index);
			helpers.selectTabByName('edit_user');
		}
	}
	function removeUser(uri){
		if(confirm("<?=__('Please confirm user deletion')?>")){
			window.location = "<?=_url('delete', 'Users', 'tao')?>" + '?uri=' + uri;
		}
	}
	$(function(){
		uiBootstrap.tabs.tabs('disable', helpers.getTabIndexByName('edit_user'));
		var myGrid = $("#user-list").jqGrid({
			url: "<?=_url('data', 'Users', 'tao')?>",
			datatype: "json",
			colNames:[ __('Login'), __('Name'), __('Mail'), __('Roles'), __('Data Language'), __('Interface Language'), __('Actions')],
			colModel:[
				{name:'login',index:'login'},
				{name:'name',index:'name'},
				{name:'email',index:'email', width: '200'},
				{name:'roles',index:'roles'},
				{name:'deflg',index:'deflg', align:"center"},
				{name:'uilg',index:'uilg', align:"center"},
				{name:'actions',index:'actions', align:"center", sortable: false}
			],
			rowNum:20,
			height:350,
			width: (parseInt($("#user-list").width()) - 2),
			pager: '#user-list-pager',
			sortname: 'login',
			viewrecords: false,
			sortorder: "asc",
			caption: __("Users"),
			gridComplete: function(){
				$.each(myGrid.getDataIDs(), function(index, elt){
					myGrid.setRowData(elt, {
						actions: "<a id='user_editor_"+elt+"' href='#' class='user_editor nd' ><img class='icon' src='<?=BASE_WWW?>img/pencil.png' alt='<?=__('Edit user')?>' /><?=__('Edit')?></a> | " +
						"<a id='user_deletor_"+elt+"' href='#' class='user_deletor nd' ><img class='icon' src='<?=BASE_WWW?>img/delete.png' alt='<?=__('Delete user')?>' /><?=__('Delete')?></a>"
					});
				});
				$(".user_editor").click(function(){
					editUser(this.id.replace('user_editor_', ''));
				});
				$(".user_deletor").click(function(){
					removeUser(this.id.replace('user_deletor_', ''));
				});
				$(window).unbind('resize').bind('resize', function(){
					myGrid.jqGrid('setGridWidth', (parseInt($("#user-list").width()) - 2));
				});
			}
		});
		myGrid.navGrid('#user-list-pager',{edit:false, add:false, del:false});

		helpers._autoFx();
	});
});
</script>