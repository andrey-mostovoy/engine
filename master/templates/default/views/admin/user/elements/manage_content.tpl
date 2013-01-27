{if !$data || $data.role == User::ADMIN} {*create*}
<tr class="hidden">
    <td colspan="2">
        <input type="hidden"  name="validate_type" value="admin_save_{if $data.id}edit{else}add{/if}" />
        <input type="hidden"  name="__data[role]" value="{User::ADMIN}" />
    </td>
</tr>
<tr>
    <td><label>{$lang->user()->email}: <span>*</span></label></td>
    <td>
        <input type="text" class="required" name="__data[email]" value="{$data.email}" />
    </td>
</tr>
<tr>
    <td><label>{$lang->user()->password}: <span>*</span></label></td>
    <td>
        <input type="password" class="required" name="__data[password]" value="" />
    </td>
</tr>
<tr>
    <td><label>{$lang->user()->confirm_password}: <span>*</span></label></td>
    <td>
        <input type="password" class="required" name="__data[confirm_password]" value="" />
    </td>
</tr>
<tr>
    <td><label>{$lang->user()->first_name}:</label></td>
    <td>
        <input type="text" name="__data[first_name]" value="{$data.first_name}" />
    </td>
</tr>
<tr>
    <td><label>{$lang->user()->last_name}:</label></td>
    <td>
        <input type="text" name="__data[last_name]" value="{$data.last_name}" />
    </td>
</tr>
{else if $data.role == User::MEMBER}
<tr class="hidden">
    <td colspan="2">
        <input type="hidden"  name="validate_type" value="user_save_{if $data.id}edit{else}add{/if}" />
        <input type="hidden"  name="__data[role]" value="{User::MEMBER}" />
    </td>
</tr>
<tr>
    <td><label>{$lang->user()->screen_name}:</label></td>
    <td>
        {if $data.screen_name}{$data.screen_name}{else}&nbsp;{/if}
    </td>
</tr>
<tr>
    <td><label>{$lang->user()->name}:</label></td>
    <td>
        {if $data.first_name}{$data.first_name}{else}&nbsp;{/if}
        {if $data.last_name}{$data.last_name}{else}&nbsp;{/if}
    </td>
</tr>
<tr>
    <td><label>{$lang->user()->reg_date}:</label></td>
    <td>
        {if $data.date}{$data.date|date_format:Defines::SMARTY_DF_ADMIN_TABLE_VIEW}{else}&nbsp;{/if}
    </td>
</tr>
<tr>
    <td><label>{$lang->user()->label}:</label></td>
    <td>
        {if $data.label}{$data.label}{else}&nbsp;{/if}
    </td>
</tr>
<tr>
    <td><label>{$lang->user()->num_picks}:</label></td>
    <td>
        {if $data.num_picks}{$data.num_picks}{else}0{/if}
    </td>
</tr>
<tr>
    <td><label>{$lang->user()->correct_picks}:</label></td>
    <td>
        {if $data.correct_picks}{$data.correct_picks}{else}0{/if}
    </td>
</tr>
{foreach $sports as $sport}
<tr>
    <td><label>{$sport.name} {$lang->user()->points}:</label></td>
    <td>
        <input type="text" name="__data[point][{$sport.id}]" value="{$data.point[$sport.id].all|default:0}" />
    </td>
</tr>
{/foreach}
<tr>
    <td><label>{$lang->user()->total}:</label></td>
    <td>
        {if isset($data.total_point)}{$data.total_point}{else}0{/if}
    </td>
</tr>
{/if}