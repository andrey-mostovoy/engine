<tr>
    <td><label>{$lang->template()->name}: <span>*</span></label></td>
    <td>
        <input type="text" class="required" name="__data[name]" value="{$data.name}" />
    </td>
</tr>
<tr>
    <td class="label"><label>{$lang->template()->content}: <span>*</span></label></td>
    <td>
        <textarea class="ckeditor" name="__data[content]" cols="" rows="">{$data.content}</textarea>
    </td>
</tr>