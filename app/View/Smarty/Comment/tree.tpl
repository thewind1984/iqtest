
{assign var="level" value=0}
{assign var="opened_levels" value="0"}
{assign var="comment_id" value=0}

{if empty($parent_id)}
	<div class="tree level level{$level}" id="comments_for_0">
{/if}

{foreach from=$tree_items item="comment" name="tree"}
	{if $level && $comment.level <= $level}
		{for $l=0 to ($level - $comment.level)}
			</div>
		{/for}
	{/if}
	
	{if $comment.level > $level}
		{math equation="x+y" x=$opened_levels y=1 assign="opened_levels"}
	{/if}
	
	{assign var="level" value=$comment.level}
	
	{include file="Comment/item.tpl"}
	
	{assign var="comment_id" value=$comment.id}
	
	<div class="tree level level{$comment.level+1}" id="comments_for_{$comment.id}">
{/foreach}

{for $l=0 to $opened_levels}
	</div><!-- close level -->
{/for}
