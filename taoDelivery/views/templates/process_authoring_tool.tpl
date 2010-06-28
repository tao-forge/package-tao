<?include('header.tpl')?>



<?if(get_data('error')):?>

	<div class="main-container">
		<div class="ui-state-error ui-corner-all" style="padding:5px;">
			<?=__('Please select a delivery before authoring it!')?>
			<br/>
			<?=get_data('errorMessage')?>
		</div>
		<br />
		<span class="ui-widget ui-state-default ui-corner-all" style="padding:5px;">
			<a href="#" onclick="selectTabByName('manage_deliveries');"><?=__('Back')?></a>
		</span>
	</div>
	
<?else:?>
	<style type="text/css">
	#draggable {padding: 0.5em;width:auto; }
	#draggable1 {padding: 0.5em;width:auto;}
	
	#accordion_container_1 {position:absolute;left:0%;top:0%;width:30%;height:100%;z-index:1000;background-color:#fff;}
	#accordion_container_2 {position:relative;left:30%;top:0%;width:70%;height:100%;z-index:1000;background-color:#fff;}

	#process_diagram_container {position:absolute;left:0%;top:0%;width:75%;height:100%;}
	
	#demo {position:absolute;left:27%;top:1%;width:50%;height=auto;}
	#process {position:absolute;left:78%;top:1%;width:21%;height=auto;}
	#main {width:1000px;height:700px;}
	
	</style>

	<script type="text/javascript">
		$(function(){
			
		});
	</script>

	<div class="main-container" style="display:none;"></div>
	<div id="authoring-container" class="ui-helper-reset">
	<div id="process_diagram_container"></div>
	
	<div id="accordion_container_1">
		<div id="accordion1" style="font-size:0.8em;">
			<h3><a href="#"><?=__('Service Definition')?></a></h3>
			<div>
				<div id="serviceDefinition_tree"/>
				<div id="serviceDefinition_form"/>
			</div>
			<h3><a href="#"><?=__('Formal Parameter')?></a></h3>
			<div>
				<div id="formalParameter_tree"/>
				<div id="formalParameter_form"/>
			</div>
			<h3><a href="#"><?=__('Role')?></a></h3>
			<div>
				<div id="role_tree"/>
				<div id="role_form"/>
			</div>
			<h3><a href="#"><?=__('Process Variables')?></a></h3>
			<div>
				<div id="variable_tree"/>
				<div id="variable_form"/>
			</div>
		</div><!--end accordion -->
	</div>
	
	<div id="accordion_container_2">
	<div id="accordion2" style="font-size:0.8em;">
		<h3><a href="#"><?=__('Activity Editor')?></a></h3>
		<div>
			<div id="activity_menu">
				<a href="#" id="activity_menu_addActivity">Add Activity</a><br/><br/>
			</div>
			<div id="activity_tree"/>
			<div id="activity_form"/>
		</div>
		<h3><a href="#"><?=__('Process Property')?></a></h3>
		<div>
			<div id="process_form"><?=__('loading...')?></div>
		</div>
		<h3><a href="#"><?=__('Compilation')?></a></h3>
		<div>
			<div id="compile_info"><?=__('loading...')?></div>
			<div id="compile_form"></div>
		</div>
	</div><!--end accordion -->
	</div><!--end accordion_container_2 -->
	
	<div style="clear:both;"></div>
	
	</div><!--end authoring-container -->
	
	<script type="text/javascript" src="/<?=get_data('extension')?>/views/js/authoringConfig.js"></script>
	<script type="text/javascript" src="/<?=get_data('extension')?>/views/js/activity.tree.js"></script>
	
	<script type="text/javascript">
	var processUri = "<?=get_data("processUri")?>";
	
	$(function(){
		EventMgr.unbind('activityAdded');
		
		EventMgr.bind('activityAdded', function(event, response){
			console.log("added from menu");
		});
		
		$("#activity_menu_addActivity").click(function(event){
			event.preventDefault();
			GatewayProcessAuthoring.addActivity(authoringControllerPath+"addActivity", processUri);
		});
		
		$("#accordion1").accordion({
			fillSpace: false,
			autoHeight: false,
			collapsible: false,
			active: 0,
			icons: { 'header': 'ui-icon-plus', 'headerSelected': 'ui-icon-minus' }
		});
		
		//load activity tree:
		loadActivityTree();
		
		
		//load the trees:
		loadSectionTree("serviceDefinition");//use get_value instead to get the uriResource of the service definition class and make
		loadSectionTree("formalParameter");
		loadSectionTree("role");
		loadSectionTree("variable");
		
		processProperty();
		
		loadCompilationForm();
		
	});
	
	$(function(){
		$("#accordion2").accordion({
			fillSpace: false,
			autoHeight: false,
			collapsible: false,
			
			icons: { 'header': 'ui-icon-plus', 'headerSelected': 'ui-icon-minus' }
		});
		
		//load the trees:
		
	});
	
	function processProperty(){
		_load("#process_form", 
			authoringControllerPath+"editProcessProperty", 
			{processUri: processUri}
		);
	}
	
	function loadSectionTree(section){
	//section in [serviceDefinition, formalParameter, role]
		$.ajax({
			url: authoringControllerPath+'getSectionTrees',
			type: "POST",
			data: {section: section},
			dataType: 'html',
			success: function(response){
				$('#'+section+'_tree').html(response);
			}
		});
	}
	
	function loadActivityTree(){
		$.ajax({
			url: authoringControllerPath+'getActivityTree',
			type: "POST",
			data: {section: "activity"},
			dataType: 'html',
			success: function(response){
				$('#activity_tree').html(response);
			}
		});
	}
	
	function loadCompilationForm(){
		$.ajax({
			url: authoringControllerPath+'compileView',
			type: "POST",
			data: {processUri: processUri},
			dataType: 'html',
			success: function(response){
				$('#compile_info').html(response);
			}
		});
	}
	</script>
	
<?endif?>

<?include('footer.tpl')?>