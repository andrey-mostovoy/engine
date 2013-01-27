<td>
	{if $content.name}{$content.name}{else}&nbsp;{/if}
</td>
{if !$content.type}
    {include_element file="table_row_email"}
{/if}