<tr class="hidden">
    <td colspan="2">
        <input type="hidden"  name="validate_type" value="save_{if $data.id && !Defines::DEV}edit{else}add{/if}" />
    </td>
</tr>
<tr>
    <td><label>{$lang->setting()->name}: {if Defines::DEV}<span>*</span>{/if}</label></td>
    <td>
        {if Defines::DEV}
            <input type="text" name="__data[name]" value="{$data.name}" />
        {else}
            {$data.name}
        {/if}
    </td>
</tr>
<tr>
    <td><label>{$lang->setting()->value}: <span>*</span></label></td>
    <td>
        <input type="text" name="__data[value]" value="{$data.value}" />
    </td>
</tr>