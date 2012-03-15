<!--<div id="main-container" class="main-container">-->
<div class="main-container">
	<div id="monitoring-processes-container">
		<table id="monitoring-processes-grid" />
	</div>	
</div>

<script type="text/javascript">

var monitoringGrid = null;
var monitoringFilter = null;
 
//load the monitoring interface functions of the parameter filter
function loadMonitoring(filter)
{
    monitoringFilter = filter;
	$.getJSON(root_url+'/tao/SaSUsers/getGridData'
		,{
			'filter':filter
		}
		, function (DATA) {
            //clean the grid
            monitoringGrid.empty();
            //add the new data
			monitoringGrid.add(DATA);
			selectedProcessId = null;
		}
	);
}

$(function(){

	model = <?=$model?>;
	var monitoringGridOptions = {
		'height'        : 769
		, 'title'       : 'TAO Users'
	};
	monitoringGrid = new TaoGridClass('#monitoring-processes-grid', model, '', monitoringGridOptions);
	
	//load monitoring grid
	loadMonitoring();

});
</script>
