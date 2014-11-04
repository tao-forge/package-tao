<?php
use oat\tao\helpers\Template;
?>
<link rel="stylesheet" type="text/css" href="<?= ROOT_URL ?>taoOutcomeUi/views/css/result.css" />

<header class="section-header flex-container-full">
    <h2><?=__('View result')?> - <?=get_data('deliveryResultLabel')?></h2>
</header>
<div class="main-container flex-container-full">

    <div id="view-result">
        <div id="resultsViewTools">
            <select class="result-filter">
                <option  value="all" ><?=__('All collected variables')?></option>
                <option  value="firstSubmitted" ><?=__('First submitted variable only')?></option>
                <option  value="lastSubmitted" ><?=__('Last submitted variable only')?></option>
            </select>
            <button class="btn-info small result-filter-btn"><?=__('Filter');?></button>
        </div>
        <div id="resultsHeader">
            <div class="tile testtaker">
                <strong>
                    <span class="icon-test-taker"/>
                    <?=__('Test Taker')?>
                </strong>
                <table class="mini">
                    <tr><td class="field"><?=__('Login:')?></td><td class="fieldValue"><?=get_data('userLogin')?></td></tr>
                    <tr><td class="field"><?=__('Label:')?></td><td class="fieldValue"><?=get_data('userLabel')?></td></tr>
                    <tr><td class="field"><?=__('Last Name:')?></td><td class="fieldValue"><?=get_data('userLastName')?></td></tr>
                    <tr><td class="field"><?=__('First Name:')?></td><td class="fieldValue"><?=get_data('userFirstName')?></td></tr>
                    <tr><td class="field"><?=__('Email:')?></td><td class="fieldValue userMail"><?=get_data('userEmail')?></td></tr>
                </table>
            </div>
            <div class="tile statistics">
                <strong><span class="icon-result"/>
                    <?=__('Responses Evaluation')?>
                </strong>
                <table class="mini">
                    <tr>
                        <td><span class="valid"><?=__('Correct')?>: </span></td>
                        <td><?=get_data("nbCorrectResponses")?>/<?=get_data('nbResponses')?></td>
                        <td><span class="icon-result-ok"/></td>
                    </tr>
                    <tr>
                        <td><span class="invalid"><?=__('Incorrect')?>: </span></td><td><?=get_data("nbIncorrectResponses")?>/<?=get_data('nbResponses')?></td>
                        <td><span class="icon-result-nok"/></td>
                    </tr>
                    <tr>
                        <td><span class="uneval"><?=__('Not Evaluated')?>: </span></td><td><?=get_data("nbUnscoredResponses")?>/<?=get_data('nbResponses')?></td>
                        <td><span class="icon-not-evaluated"/></td>
                    </tr>
                </table>
            </div>
        </div>
        <div id="resultsBox">
            <table class="matrix">
                <thead>
                <tr >
                    <th class="headerRow" colspan="4">
                        <span class="itemName">
                            <?=__('Test Variables')?> (<?=count(get_data("deliveryVariables"))?>)
                        </span>
                    </th>
                </tr>
                </thead>
                <?php foreach (get_data("deliveryVariables") as $testVariable){
                $baseType = $testVariable->getBaseType();
                $cardinality = $testVariable->getCardinality();
                ?>
                <tbody>
                <tr>
                    <td><?=$testVariable->getIdentifier()?></td>
                    <td><?=$testVariable->getValue()?></td>
                    <td> 
                        <?php 
                        echo $cardinality;
                        ?>
                    </td>
                    <td> 
                        <?php 
                        echo $baseType;
                        ?>
                    </td>
                </tr>
                </tbody>
                <?php
                }
                ?>
            </table>
            <?php  foreach (get_data('variables') as $itemUri => $item){
           ?>
           
            <table class="matrix">
                <thead>
                    <tr >
                        <th colspan="5" class="bold">
                            <b>
                                <?=$item['label']?>
                                (<?=$item['itemModel']?>)
                            </b>
                        </th>
                        <th>
                            <a href="<?=_url(
                               'fullScreenPreview', 'Items', 'taoItems',
                               array(
                                    'uri' => tao_helpers_Uri::encode($itemUri),
                                    'fullScreen' => true
                                    )
                                    )?>" target="preview">
                                <?=__('Preview')?>
                            </a>
                            
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($item['sortedVars'][CLASS_RESPONSE_VARIABLE])) {?>
                    <tr>
                        <th colspan="6" class="italic">
                            <i><?=__('Responses')?> (<?=count($item['sortedVars'][CLASS_RESPONSE_VARIABLE]) ?>)</i>
                        </th>
                    </tr>
                <?php
		foreach ($item['sortedVars'][CLASS_RESPONSE_VARIABLE] as $variableIdentifier  => $observations){
		    $rowspan = 'rowspan="'.count($observations).'"';
		    foreach ($observations as $key=>$observation) {
                    $variable = $observation["var"];
        	?>
		<tr>
		<?php if ($key === key($observations)) {?>
		     <td <?=$rowspan?> class="variableIdentifierField"><?=$variableIdentifier?></td>
		<?php }?>
		<td class="dataResult" colspan="2">
            <?php
            if ($variable->getBaseType()=="file") {
                    echo '<button class="download" value="'.$observation["uri"].'">'.__('download').'</button>';
            }
            else{
            ?>
		    <?php
                        $rdfValue = $variable->getValue();
                        if (is_array($rdfValue)) {
                            echo "<OL>";
                            foreach ($rdfValue as $value) {
                                echo "<LI>";
                                    echo tao_helpers_Display::htmlEscape(nl2br($value));
                                echo "</LI>";
                            }
                            echo "</OL>";
                        } elseif (is_string($rdfValue)) {
                            echo tao_helpers_Display::htmlEscape(nl2br($rdfValue));
                        } else {
                            echo tao_helpers_Display::htmlEscape($rdfValue);
                        }
                    }
                    ?>

                <span class="    
                      <?php
                      switch ($observation['isCorrect']){
                          case "correct":{ echo "icon-result-ok";break;}
                          case "incorrect":{ echo "icon-result-nok"; break;}
                          default: { echo "icon-not-evaluated";break;}
                          }
                          ?>
                          rgt" />
                          </td>
                          <td> 
                              <?php 
                              echo $variable->getCardinality();
                              ?>
                          </td>
                          <td> 
                              <?php 
                              echo $variable->getBaseType();
                              ?>
                          </td>

                          <td class="epoch"><?=$variable->getEpoch()?></td>
                          </tr>
                          <?php
                          }
                          }
                          ?>
                          <?php } ?>
                          <?php if (isset($item['sortedVars'][CLASS_OUTCOME_VARIABLE])) {?>
                <tr>
                    <th colspan="6" class="italic">
                        <i><?=__('Grades')?>  (<?=count($item['sortedVars'][CLASS_OUTCOME_VARIABLE]) ?>)</i>
                    </th>
                </tr>
                <?php
		foreach ($item['sortedVars'][CLASS_OUTCOME_VARIABLE] as $variableIdentifier  => $observations){
		   $rowspan = 'rowspan="'.count($observations).'"';
		    foreach ($observations as $key=>$observation) {
                    $variable = $observation["var"];
        	?>
		<tr>
		<?php if ($key === key($observations)) {?>
		     <td <?=$rowspan?> class="variableIdentifierField"><?=$variableIdentifier?></td>
		<?php }?>
		<td colspan="2" class="dataResult">
                    <?=tao_helpers_Display::htmlEscape(nl2br($variable->getValue()))?>
                    <?php
                        if ($variable->getBaseType()=="file") {
                        echo '<button class="download" value="'.$observation["uri"].'">'.__('download').'</button>';
                          }
                          ?>
                          </td>
                          <td> 
                              <?php 
                              echo $variable->getCardinality();
                              ?>
                          </td>
                          <td> 
                              <?php 
                              echo $variable->getBaseType();
                              ?>
                          </td>
                          <td class="epoch"><?=$variable->getEpoch()?></td>
                          </tr>
                          <?php
                          }
                          }
                          ?>

                          <?php } ?>
                          </tbody>
                </table>
                <br />
                <?php } ?>
            </div>
        </div>
    </div>
<div id="form-container" >

    <?php if(get_data('errorMessage')):?>
    <fieldset class='ui-state-error'>
        <legend><strong><?=__('Error')?></strong></legend>
        <?=get_data('errorMessage')?>
    </fieldset>
    <?php endif?>

</div>

<script type="text/javascript">
    requirejs.config({
        config: {
            'taoOutcomeUi/controller/viewResult': {
                uri: '<?=get_data("uri")?>',
                classUri: '<?=get_data("classUri")?>',
                filter: '<?=get_data("filter")?>'
            }
        }
    });
</script>

<?php
Template::inc('footer.tpl', 'tao');
?>
