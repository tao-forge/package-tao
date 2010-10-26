<div id="formChoices_title" class="ui-widget-header ui-corner-top ui-state-default">
	<?=__('Choices editor:')?>
</div>
<?  $formChoices = get_data('formChoices');
	$groupSerials = get_data('groupSerials'); ?>
<div class="ui-widget-content ui-corner-bottom qti-authoring-form-container">
	<?foreach($groupSerials as $order => $groupSerial):?>
	<div id="formContainer_choices_container_<?=$groupSerial?>" class="formContainer_choices qti-authoring-form-container-column">
		<div id="formContainer_choices_<?=$groupSerial?>" class="qti-authoring-form-container">
		<?foreach($formChoices[$groupSerial] as $choiceId => $choiceForm):?>
			<div id='<?=$choiceId?>' class='formContainer_choice'>
				<?=$choiceForm?>
			</div>
		<?endforeach;?>
		</div>

		<div id="add_choice_button_<?=$groupSerial?>" class="add_choice_button">
			<a href="#"><img src="<?=ROOT_URL?>/tao/views/img/add.png"> Add a choice</a>
		</div>
	</div>
	<?endforeach;?>
	<div style="clear:both">
</div>	



<script type="text/javascript">
$(document).ready(function(){
	<?foreach($groupSerials as $order => $groupSerial):?>
	$('#add_choice_button_<?=$groupSerial?>').click(function(){
		//add a choice to the current interaction:
		myInteraction.addChoice($('#formContainer_choices_<?=$groupSerial?>'), 'formContainer_choice', '<?=$groupSerial?>');//need an extra param "groupSerial"
		return false;
	});
	<?endforeach;?>
	
	//add adv. & delete button
	myInteraction.initToggleChoiceOptions();
	
	//add move up and down button
	myInteraction.orderedChoices = [];//double dimension array:
	<?foreach(get_data('orderedChoices') as $groupSerial => $group):?>
		myInteraction.orderedChoices['<?=$groupSerial?>'] = [];
		<?foreach($group as $choice):?>
			myInteraction.orderedChoices['<?=$groupSerial?>'].push('<?=$choice->getSerial()?>');
		<?endforeach;?>
	<?endforeach;?>
	
	myInteraction.setOrderedMatchChoicesButtons(myInteraction.orderedChoices);
	
	
});
</script>
