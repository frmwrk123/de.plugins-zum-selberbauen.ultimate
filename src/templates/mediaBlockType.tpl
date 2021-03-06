<div id="block-{$blockID}" class="block media-block-type {$alignmentSelected}" data-id="{$blockID}">
	{if $mediaType == 'video'}
		{if $mediaSourceType == 'provider'}
			{@$mediaHTML}
		{else}
		<video controls="controls" preload="metadata" height="{$mediaHeight}" width="{$mediaWidth}">
			<source type="{$mediaMimeType->mimeType}" src="{@$mediaSource}" />
		</video>
		{/if}
	{/if}
	{if $mediaType == 'audio'}
		<audio controls="controls">
			<source type="{$mediaMimeType->mimeType}" src="{@$mediaSource}" />
		</audio>
	{/if}
	{if $mediaType == 'photo'}
		<img src="{@$mediaSource}" alt="" width="{$mediaWidth}" height="{$mediaHeight}" />
	{/if}
</div>