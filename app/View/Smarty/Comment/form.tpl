
<div class="form-box">
	<div class="box-inner">
		<form action="/ajax/comment/add">
			
			<input type="hidden" name="parent_id" id="reply_to" value="0" />
			
			<h1>Новый комментарий</h1>
			<div>
				<input type="text" name="user_name" value="" placeholder="Ваше имя" required />
				<input type="email" name="user_email" value="" placeholder="Ваш email" required />
			</div>
			<p>Если email уже был зарегистрирован, то будет использовано ранее выбранное имя.</p>
			<div>
				<textarea name="comment" placeholder="Несколько слов о том, что вы думаете..."></textarea>
			</div>
			<div>
				<button type="submit">Отправить комментарий</button>
			</div>
			
		</form>
		
		<div>
			<h1>Для тестирования</h1>
			
			<a href="#" data-clean>Очистить базу комментариев и пользователей</a>
		</div>
	</div>
</div>