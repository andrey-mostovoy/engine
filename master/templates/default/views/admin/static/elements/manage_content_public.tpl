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
<tr>
    <td><label>{$lang->static()->meta_keywords}:</label></td>
    <td>
        <input type="text" name="__data[meta_keywords]" value="{$data.meta_keywords}" />
    </td>
</tr>
<tr>
    <td><label>{$lang->static()->meta_description}:</label></td>
    <td>
        <input type="text" name="__data[meta_description]" value="{$data.meta_description}" />
    </td>
</tr>
<tr>
    <td class="label"><label>{$lang->static()->content}: <span>*</span></label></td>
    <td>
        <textarea class="ckeditor" name="__data[content]" cols="" rows="">{$data.content}</textarea>
    </td>
</tr>