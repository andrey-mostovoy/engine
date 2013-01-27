<td>
	{if $content.title}<a href="{$url.domain}/page/{if $content.url}{$content.url}{else}show/id/{$content.id}{/if}" target="_blank" title="{$content.title}">{$content.title}</a>{else}&nbsp;{/if}
</td>
{if $content.parent_id != 0}
<td>
	{if $content.url}{$content.url}{else}&nbsp;{/if}
</td>
{/if}