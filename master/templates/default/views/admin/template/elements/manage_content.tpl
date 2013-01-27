<tr class="hidden">
    <td colspan="2">
        <input type="hidden"  name="validate_type" value="save_{if $data.id}edit{else}add{/if}_{$data.type}" />
        <input type="hidden"  name="__data[type]" value="{$data.type}" />
    </td>
</tr>
{include_element file="manage_content_`$data.type`"}