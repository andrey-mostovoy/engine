{if $data.parent_id == 0}
<tr>
    <td><label>{$lang->static()->title}: <span>*</span></label></td>
    <td>
        <input type="text" class="required" name="__data[title]" value="{$data.title}" />
    </td>
</tr>
<tr>
    <td><label>{$lang->static()->url}: <span>*</span></label></td>
    <td>
        <input type="text" class="required" name="__data[url]" value="{$data.url}" />
    </td>
</tr>
{else}
<tr>
    <td><label>{$lang->static()->title}: <span>*</span></label></td>
    <td>
        <input type="text" class="required" name="__data[title]" value="{$data.title}" />
    </td>
</tr>
{if Defines::DEV}
<tr>
    <td><label>{$lang->static()->url}: <span>*</span></label></td>
    <td>
        <input type="text" class="required" name="__data[url]" value="{$data.url}" />
    </td>
</tr>
{else}
<tr class="hidden">
    <td colspan="2">
        <input type="hidden" class="required" name="__data[url]" value="{$data.url}" />
    </td>
</tr>
{/if}
<tr>
    <td class="label"><label>{$lang->static()->content}: <span>*</span></label></td>
    <td>
        <textarea name="__data[content]" cols="" rows="">{$data.content}</textarea>
    </td>
</tr>
{/if}