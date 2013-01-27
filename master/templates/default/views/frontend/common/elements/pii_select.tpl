{if $_p}
    {if $_a}
        {if isset($_n)}
            {$selected = $data[$_p][$_n][$_a][$_f]}
        {else}    
            {$selected = $data[$_p][$_a][$_f]}
        {/if}
    {else}
        {if isset($_n)}
            {$selected = $data[$_p][$_n][$_f]}
        {else}    
            {$selected = $data[$_p][$_f]}
        {/if}
    {/if}
{else}
    {if $_a}
        {if isset($_n)}
            {$selected = $data[$_n][$_a][$_f]}
        {else}    
            {$selected = $data[$_a][$_f]}
        {/if}
    {else}
        {if isset($_n)}
            {$selected = $data[$_n][$_f]}
        {else}    
            {$selected = $data[$_f]}
        {/if}
    {/if}
{/if}
<select {include_element file="pif_name"}>
    <option value="">{$lang->form()->default_select}</option>
    {foreach $_for as $k => $v}
    <option value="{$v.id}" {if $v.id == $selected}selected="selected"{/if}>{$v.name}</option>
    {/foreach}
</select>
    
    