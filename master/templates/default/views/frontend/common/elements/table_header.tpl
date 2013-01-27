{*headers*}
{if $table_headers}
<tr>
    <th class="group-column">
        <span>
            <input type="checkbox" name="all_items" value="1" />
        </span>
    </th>
    {foreach $table_headers as $key_header=>$header}
        {*if last column - it means that that column is actions*}
        <th{if $header@last} class="action-set{$content_actions_count|default:count($content_actions)}"{/if}
            {if is_array($header)}
                {if $header.html.style} style="{$header.html.style}"{/if}
            {/if}>
            {if is_array($header)}
                {if !$header.filter.order}
                    {$key_header}
                {/if}
                {if $header.filter.order}
                    <a href="{$url.address}/{$action}{$params}" onclick="return order(this);" id="order-{$header.filter.key}" 
                    class="sort
                        {if $header.filter.key == $__order.type}
                            {if $__order.dir == 'ASC'} down{else} up{/if}
                        {/if}" title="{$key_header}" >
                        {$key_header}
                    </a>
                {/if}
            {else}
                {$header}
            {/if}
        </th>
    {/foreach}
</tr>
{/if}
{*end headers*}