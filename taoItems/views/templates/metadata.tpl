<?if(get_data('metadata')):?>
<div id="meta-title" class="ui-widget-header ui-corner-top ui-state-default">
	Meta Data
</div>
<div id="meta-content" class="ui-widget-content ui-state-default">
	<table>
		<thead>
			<tr>
				<th>Date</th>
				<th>User</th>
				<th>Comment</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><?=get_data('date')?></td>
				<td><?=get_data('user')?></td>
				<td>
					<span id="comment-field"><?=get_data('comment')?></span>
					<a href="#" id="comment-editor">
						<img src="../views/img/edit.png" />
					</a>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<span id="comment-form-container-title" style="display:none;"><?=__("Edit item comment")?></span>
<div id="comment-form-container" style="display:none;">
	<form method="post" id="comment-form">
		<textarea name="comment" rows="4" cols="30"><?=get_data('comment')?></textarea><br />
		<input type="hidden" name="uri" value="<?=get_data('uri')?>" />
		<input type="hidden" name="classUri" value="<?=get_data('classUri')?>" />
		<input id="comment-saver" type="button" value="<?=__('Save')?>" />
	</form>
</div>
<?endif?>