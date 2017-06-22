
<!-- open 'item' block -->
<div class="item level{$comment.level}{if !empty($comment.children)} children{/if}{if !empty($is_new)} is_new{/if}" id="comment_item_{$comment.id}">
	{if !$comment.status}
		<div class="error">Комментарий удален</div>
	{else}
		<div class="header">
			<div class="user">
				<b>{$comment.user.name|escape:'html'}</b>
				<a href="mailto:{$comment.user.email|escape:'html'}">{$comment.user.email|escape:'html'}</a>
			</div>
			<div class="date">
				{$comment.date}
			</div>
		</div>
		<div class="data">
			{include file="Comment/text.tpl" comment=$comment}
		</div>
	{/if}
	
	{if (!$comment.parent_id && !empty($comment.children)) || $comment.status}
		<div class="footer">
			<div class="expander">
				{if !$comment.parent_id}
					<a href="#" data-expand="{$comment.id}">Развернуть &darr;</a>
				{/if}
			</div>
			{if $comment.status}
				<div class="links">
					<a href="#" data-reply="{$comment.id}">Ответить</a>
					{if $comment.editable}
						<a href="#" data-edit="{$comment.id}">Редактировать</a>
						<a href="#" data-remove="{$comment.id}">Удалить</a>
					{/if}
				</div>
			{/if}
		</div>
	{/if}
</div>
<!-- close 'item' block -->

{if !empty($is_new)}
	<!-- open level {$comment.level+1} -->
	<div class="tree level level{$comment.level+1}" id="comments_for_{$comment.id}"></div>
	<!-- close level {$comment.level+1} -->
{/if}