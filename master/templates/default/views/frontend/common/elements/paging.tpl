{if isset($section) && $section != '' && isset($paging.$section)}
	{$paging=$paging.$section}
{/if}

{if $paging.pages}
<ul {if $ul_class}class="{$ul_class}"{/if}>
    {if $paging.prev}
    <li>
        {if $paging.prev.href}
            <a href="{$paging.prev.href}" class="{if $js_ajax} {$js_ajax}{/if}" title="{$paging.prev.text}">{$paging.prev.text}</a>
        {else}
            <span>{$paging.prev.text}</span>
        {/if}
    </li>
    <li><span>/</span></li>
    {/if}
    {if $paging.pages}
        {foreach from=$paging.pages item=page name=pages}
        <li>
            {if $page.href}
                <a href="{$page.href}" {if $js_ajax}class="{$js_ajax}"{/if} title="{$page.text}">{$page.text}</a>
            {else}
                <span>{$page.text}</span>
            {/if}
        </li>
        <li><span>/</span></li>
        {/foreach}
    {/if}
    {if $paging.next}
    <li>
        {if $paging.next.href}
            <a href="{$paging.next.href}" class="{if $js_ajax} {$js_ajax}{/if}" title="{$paging.next.text}">{$paging.next.text}</a>
        {else}
            <span>{$paging.next.text}</span>
        {/if}
    </li>
    {/if}
</ul>
{/if}