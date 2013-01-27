{if isset($_model) && $_model != ''}
	{$paging=App::model($_model)->paging()->displayPages()}
{else}
    {$paging=App::controller()->getCModel()->paging()->displayPages()}
{/if}

{if isset($_name) && $_name != ''}
	{$paging=$paging.$_name}
{else if isset($paging_name)}
    {$paging=$paging.$paging_name}
{else}
    {$paging=$paging.main}
{/if}

{if $paging.ipp || $paging.pages || $paging.info}
<div class="pagination">
{/if}
    {if $paging.ipp}
    <div class="select-items">
        <form action="{$paging.info.href}/page/{$paging.info.page_name}-{$paging.info.current_page}" method="post">
            <input type="hidden" name="{Controller::SAVE_FILTER}" value="1" />
            <label>{$paging.info.lang.visible_row}:</label>
            <select name="ipp" onchange="this.form.submit();">
            {foreach from=$paging.ipp item=v key=k}
                <option value="{$k}" {if $paging.c_ipp eq $k}selected="selected"{/if}>{$v}</option>
            {/foreach}
            </select>
        </form>
    </div>
    {/if}
    {if $paging.pages}
    <ul>
        {if $paging.prev}
        <li>
            {if $paging.prev.href}
            <a href="{$paging.prev.href}">{$paging.prev.text}</a>
            {else}
            <span class="disabled">{$paging.prev.text}</span>
            {/if}
        </li>
        {/if}
        {if $paging.pages}
        {foreach from=$paging.pages item=page name=pages}
            <li>
            {if $page.href}
                <a href="{$page.href}">{$page.text}</a>
            {else}
                <span>{$page.text}</span>
            {/if}
            </li>
        {/foreach}
        {/if}
        {if $paging.next}
        <li>
            {if $paging.next.href}
            <a href="{$paging.next.href}">{$paging.next.text}</a>
            {else}
            <span class="disabled">{$paging.next.text}</span>
            {/if}
        </li>
        {/if}
    </ul>
    {/if}
    {if $paging.info}
    <p>Page: {$paging.info.current_page} of {$paging.info.total_pages} Total: {$paging.info.total_records} records</p>
    {/if}
{if $paging.ipp || $paging.pages || $paging.info}
</div>
{/if}
