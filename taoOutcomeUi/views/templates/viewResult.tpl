<link rel="stylesheet" type="text/css" href="<?= ROOT_URL ?>taoResults/views/css/result.css" />
<div id="form-title" class="ui-widget-header ui-corner-top ui-state-default"><?=__('View result')?> - <?=get_data('deliveryResultLabel')?></div>
<div class="ui-widget-content ui-corner-bottom">

<script type="text/javascript">
	var data;
	data.uri = '<?=get_data("uri")?>';
	data.classUri = '<?=get_data("classUri")?>';
	
</script>
<script src="<?=BASE_WWW?>js/viewResult.js"></script>

    <div id="content">

	<span id="TestTakerIdentificationBox"><strong>&nbsp;<img src="/tao/views/js/jsTree/themes/custom/subject.png" /><span id="testTakerHeader"><?=__('Test Taker')?></span></strong>
	    <table class="mini">
		<tr><td class="field"><?=__('Login:')?></td><td class="fieldValue"><?=get_data('userLogin')?></td></tr>
		<tr><td class="field"><?=__('Label:')?></td><td class="fieldValue"><?=get_data('userLabel')?></td></tr>
		<tr><td class="field"><?=__('Last Name:')?></td><td class="fieldValue"><?=get_data('userLastName')?></td></tr>
		<tr><td class="field"><?=__('First Name:')?></td><td class="fieldValue"><?=get_data('userFirstName')?></td></tr>
		<tr><td class="field"><?=__('Email:')?></td><td class="fieldValue userMail"><?=get_data('userEmail')?></td></tr>
	    </table>
	</span>
	<span id="ScoresSummaryBox">
		
			<?=__('Filter values:')?>
			<select id="filter">
			    <option  value="all"><?=__('All collected values')?></option>
			    <option  value="firstSubmitted"><?=__('First submitted responses only')?></option>
			    <option  value="lastSubmitted"><?=__('Last submitted responses only')?></option>
			</select>

		<br/>
		<br/>
		<b><?=__('Responses Evaluation')?></b>
		<table id="respEval">
		    <tr><td><span class="valid"><?=__('Correct')?>: </span></td><td><?=get_data("nbCorrectResponses")?>/<?=get_data('nbResponses')?></td> <td><img src="/taoResults/views/img/correct.png" /></td></tr>
		    <tr><td><span class="invalid"><?=__('Incorrect')?>: </span></td><td><?=get_data("nbIncorrectResponses")?>/<?=get_data('nbResponses')?></td><td><img src="/taoResults/views/img/incorrect.png" /></td></tr>
		    <tr><td><span class="uneval"><?=__('Not Evaluated')?>: </span></td><td><?=get_data("nbUnscoredResponses")?>/<?=get_data('nbResponses')?></td><td><img src="/taoResults/views/img/non-evaluated.png" /></td></tr>
		 </table>
		
		<br/>
		
	</span>

	    <span id="resultsBox">

	    <table class="resultsTable" border="1">
	    <?  foreach (get_data('variables') as $item){ ?>
	    <tr >
		    <td class="headerRow" colspan="4"><span class="itemName"><?=__('Item')?>: <?=$item['label']?></span> <span class="itemModel">(<?=$item['itemModel']?>)</span></td>
	    </tr>
	    <!--<tr><td class="headerColumn"><?=__('Variable Name')?></td><td class="headerColumn"><?=__('Collected Value')?></td><td class="headerColumn"><?=__('Correctness')?></td><td class="headerColumn"><?=__('Timestamp')?></td></tr>!-->

	     <? if (isset($item['sortedVars'][CLASS_RESPONSE_VARIABLE])) {?>
	    <tr ><td class="subHeaderRow" colspan="4"><b><?=__('Responses')?> </b></td></tr>
	    <?

		    foreach ($item['sortedVars'][CLASS_RESPONSE_VARIABLE] as $variableIdentifier  => $observations){
			$rowspan = 'rowspan="'.count($observations).'"';
			foreach ($observations as $key=>$observation) {
	    ?>

		    <tr >
		    <? if ($key === key($observations)) {?>
			 <td <?=$rowspan?>><?=$variableIdentifier?></td>
		    <?}?>
		    <td class="dataResult" colspan="2"><?=nl2br(array_pop($observation[RDF_VALUE]))?>
		    <span class="<?=$observation['isCorrect']?>" />
		    </td>
		    <td class="epoch"><?=array_pop($observation["epoch"])?></td>
		    </tr>
	    <?
			}
		    }
	    ?>
	<? } ?>
	     <? if (isset($item['sortedVars'][CLASS_OUTCOME_VARIABLE])) {?>
	    <tr> <td class="subHeaderRow" colspan="4"><b><?=__('Grades')?></b></td></tr>
	    <?

		    foreach ($item['sortedVars'][CLASS_OUTCOME_VARIABLE] as $variableIdentifier  => $observations){
		       $rowspan = 'rowspan="'.count($observations).'"';
			foreach ($observations as $observation) {
	    ?>

		    <tr>
		    <td ><?=$variableIdentifier?></td>
		    <td colspan="2" class="dataResult"><?=nl2br(array_pop($observation[RDF_VALUE]))?></td>
		
		    <td class="epoch"><?=array_pop($observation["epoch"])?></td>
		    </tr>
	    <?
			}
		    }
	    ?>
	<?} ?>
	    <? if (isset($item['sortedVars'][CLASS_TRACE_VARIABLE])) {?>
	    <tr> <td class="subHeaderRow" colspan="4"><b><?=__('Traces')?></b></td></tr>
	    <?

		    foreach ($item['sortedVars'][CLASS_TRACE_VARIABLE] as $variableIdentifier  => $observations){
		       $rowspan = 'rowspan="'.count($observations).'"';
			foreach ($observations as $observation) {
	    ?>

		    <tr>
		    <td ><?=$variableIdentifier?></td>
		    <td colspan="2" class="dataResult"><button class="traceDownload" value="<?=$observation["uri"]?>"><?=__('download')?></button></td>
		    
		    <td class="epoch"><?=array_pop($observation["epoch"])?></td>
		    </tr>
	    <?
			}
		    }
	    ?>
	    <?} ?>
	    <? } ?></table>
		</span>
    </div>

	


</div>
<div id="form-container" >

	<?if(get_data('errorMessage')):?>
		<fieldset class='ui-state-error'>
			<legend><strong><?=__('Error')?></strong></legend>
			<?=get_data('errorMessage')?>
		</fieldset>
	<?endif?>
		
</div>
<?include(TAO_TPL_PATH . 'footer.tpl')?>
