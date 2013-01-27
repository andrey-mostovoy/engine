{foreach $table_headers as $key_header=>$header}
    {if is_array($header) && $header.filter && $header.filter.type && !$header.apply}
        {$filter_present = 1}
    {/if}
{/foreach}
{if $table_headers && $filter_present}
{*filters*}
<tr class="filters">
    <td>&nbsp;</td>
    {foreach $table_headers as $key_header=>$header}
        <td{if is_array($header) && $header.filter.type == 'button'} class="actions"{/if}>
        {if is_array($header) && $header.filter && !$header.apply}
            {if is_array($header.filter.type)}
                {*dropdawn options*}
                <select title="{$key_header}" name="__filter[{$header.filter.key}]" id="filter-{$header.filter.key}">
                    <option value="" >{$key_header}</option>
                    {html_options options=$header.filter.type selected=$__filter[$header.filter.key]}
                </select>
                {*end dropdawn options*}
            {else}
                {if $header.filter.type == 'text'}
                    {*text field*}
                    <input type="text" title="{$key_header}" name="__filter[{$header.filter.key}]" id="filter_{$header.filter.key}-input" value="{$__filter[$header.filter.key]|default:$key_header}"/>
                    <input type="hidden" name="" value="{$key_header}" id="filter_{$header.filter.key}-hidden" />
                    {*end text field*}
                {elseif $header.filter.type == 'button'}
                    {*action buttons*}
                    {if $header.filter.apply}
                        <a href="#" title="{lang}button.apply{/lang}" class="apply ico" onclick="return filter();">{lang}button.apply{/lang}</a>
                    {/if}
                    {if $header.filter.clear}
                        <a href="#" title="{lang}button.clear{/lang}" class="clear ico" id="clear" onclick="return clear_filter();" {if !$__filter}style="display:none"{/if}>{lang}button.clear{/lang}</a>
                    {/if}
                    {*end action buttons*}
                {else}
                    &nbsp;
                {/if}
            {/if}
        {else}
            &nbsp;
        {/if}
        </td>
    {/foreach}
</tr>
{/if}