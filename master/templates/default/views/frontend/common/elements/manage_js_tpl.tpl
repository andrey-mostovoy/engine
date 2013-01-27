{* this tpl for templates using in js to past into page dynamicly.
This tpl outside from form tag and will not submit to the server*}

{*Example
<table class="hidden">
    <tr id="tpl_league">
        <td>:text:</td>
        <td class="actions">
            <input type="hidden" name="__data[league_id][]" value=":id:" />
            <a href="#" title="{lang}button.delete{/lang}" class="delete ico" onclick="return delete_league(this, '{$data.id}');">{lang}button.delete{/lang}</a>
        </td>
    </tr>
</table>
*}