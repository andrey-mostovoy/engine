{if $breadcrumb}
<div class="breadcrumbs">
    {foreach $breadcrumb as $item}
        {if $item.href}
            <a href="{$item.href}" title="{$item.title}">
        {/if}
            {$item.title}
        {if $item.href}
            </a>
        {/if}
        {if !$item@last}
        <span>{Defines::FRONTEND_BREADCRUMB_DELIMITER}</span>
        {/if}
    {/foreach}
</div>
{/if}