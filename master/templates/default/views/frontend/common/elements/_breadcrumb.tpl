{if $breadcrumb}
    <ul class="breadcrumbs">
    {foreach $breadcrumb as $item}
        <li>
            {if $item.href}
                <a href="{$item.href}" title="{$item.title}">
            {/if}
                {$item.title}
            {if $item.href}
                </a>
            {/if}
        </li>
        {if !$item@last}
        <li>
            {Defines::FRONTEND_BREADCRUMB_DELIMITER}
        </li>
        {/if}
    {/foreach}
    </ul>
{/if}