{if $breadcrumb}
<ul>
    {foreach $breadcrumb as $bc}
        <li>
        {if $bc.href}
            <a href="{$bc.href}" title="{$bc.title}">
        {/if}
                {$bc.title}
        {if $bc.href}
            </a>
        {/if}
        </li>
    {/foreach}
</ul>
{/if}