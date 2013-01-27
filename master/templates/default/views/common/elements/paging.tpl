{if isset($section) && $section ne ''}
	{assign var=paging value=$paging.$section}
{/if}
{if $paging.pages}
    <ul>
        {if $paging.prev}
        <li>
            {if $paging.prev.href}
                <a href="{$paging.prev.href}">{$paging.prev.text}</a>
            {else}
                {$paging.prev.text}
            {/if}
        </li>
        {/if}
        {if $paging.pages}
            {foreach from=$paging.pages item=page name=pages}
            <li>
                {if $page.href}
                    <a href="{$page.href}">{$page.text}</a>
                {else}
                    {$page.text}
                {/if}
            </li>
            {/foreach}
        {/if}
        {if $paging.next}
        <li>
            {if $paging.next.href}
                <a href="{$paging.next.href}">{$paging.next.text}</a>
            {else}
                {$paging.next.text}
            {/if}
        </li>
        {/if}
    </ul>

{/if}

{if $paging.ipp}
    <form action="" method="post">
        <select name="ipp" onchange="this.form.submit();">
            {foreach from=$paging.ipp item=v key=k}
                <option value="{$k}" {if $paging.c_ipp eq $k}selected="selected"{/if}>{$v}</option>
            {/foreach}
        </select>
    </form>
{/if}

{if $paging.info}
    Page: {$paging.info.current_page} of {$paging.info.total_pages} Total: {$paging.info.total_records} records
{/if}