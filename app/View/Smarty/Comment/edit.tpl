
	<form action="/ajax/comment/edit">
		
		<input type="hidden" name="id" value="{$comment.id}" />
		<div>
			<textarea name="comment" placeholder="Несколько слов о том, что вы думаете...">{$comment.text}</textarea>
		</div>
		<div>
			<button type="submit">Сохранить</button>
			<button type="button" data-edit-cancel>Отмена</button>
		</div>
		
	</form>