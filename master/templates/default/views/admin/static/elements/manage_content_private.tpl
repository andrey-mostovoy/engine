{if $data.parent_id == 0}
<tr>
    <td><label>{$lang->static()->title}: <span>*</span></label></td>
    <td>
        <input type="text" class="required" name="__data[title]" value="{$data.title}" />
    </td>
</tr>
{else}
    {include_element file="manage_content_public"}
{/if}