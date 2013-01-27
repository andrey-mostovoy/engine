{if isset($section) && $section != '' && isset($paging.$section)}
	{$paging=$paging.$section}
{/if}

{if $paging.pages}
    {if $paging.next && $paging.next.href}
        <a class="more-games{if $js_ajax} {$js_ajax}{/if}" href="{$paging.next.href}" title="{lang}paging.show_more{/lang}">{lang}paging.show_more{/lang}</a>
    {/if}
{/if}