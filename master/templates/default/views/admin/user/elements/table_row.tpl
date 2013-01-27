<td class="w25">
    {if $content.email}{$content.email}{else}&nbsp;{/if}
</td>
<td class="w75">
    {if $content.first_name}{$content.first_name}{else}&nbsp;{/if}
    {if $content.last_name}{$content.last_name}{else}&nbsp;{/if}
</td>
<td>
    {if $content.role == User::MEMBER}
        {$lang->role()->member}
    {else if $content.role == User::USER}
        {$lang->role()->user}
    {else if $content.role == User::ADMIN}
        {$lang->role()->admin}
    {else}
        &nbsp;
    {/if}
</td>
<td>
    {if $content.role == User::ADMIN}
        &nbsp;
    {else}
        {if $content.status}{$lang->status()->{User::$status[$content.status]}}{else}&nbsp;{/if}
    {/if}
</td>
<td>
    {if $content.reg_date}{$content.reg_date|date_format:Defines::SMARTY_DF_ADMIN_TABLE_VIEW}{else}&nbsp;{/if}
</td>