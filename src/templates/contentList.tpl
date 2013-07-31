{* default values *}
{if !$showLikeColumn|isset}{assign var='showLikeColumn' value=false}{/if}
{if !$showViewColumn|isset}{assign var='showViewColumn' value=false}{/if}

{foreach from=$objects item=content}
	<tr id="content{@$content->contentID}" class="ultimateContent jsClipboardObject" data-content-id="{@$content->contentID}" data-element-id="{@$content->contentID}">
		<td class="columnText columnSubject">
			<h3>
				<a href="{linkExtended application='ultimate' date=$content->publishDateObject->format('Y-m-d') contentSlug=$content->contentSlug}{/linkExtended}" class="messageGroupLink" data-content-id="{@$content->contentID}">{$content->__toString()|wordwrap:35}</a>
			</h3>
			
			<small>
				{if $content->authorID}<a href="{link controller='User' object=$content->author}{/link}" class="userLink" data-user-id="{@$content->authorID}">{$content->author->username}</a>{else}{$content->author->username}{/if}
				- {@$content->publishDate|time}
			</small>
			
			{event name='contentData'}
		</td>
		{if $showLikeColumn && MODULE_LIKE && $__wcf->getSession()->getPermission('user.like.canViewLike')}<td class="columnStatus columnLikes">{if $content->likes || $content->dislikes}<span class="likesBadge badge jsTooltip {if $content->cumulativeLikes > 0}green{elseif $content->cumulativeLikes < 0}red{/if}" title="{lang likes=$content->likes dislikes=$content->dislikes}wcf.like.tooltip{/lang}">{if $content->cumulativeLikes > 0}+{elseif $content->cumulativeLikes == 0}&plusmn;{/if}{#$content->cumulativeLikes}</span>{/if}</td>{/if}
		{if $showViewColumn}<td class="columnDigits columnViews">{#$content->views}</td>{/if}
		
		{event name='columns'}
	</tr>
{/foreach}