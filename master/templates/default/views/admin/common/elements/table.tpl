<div id="main_table_div" {if $tbl_class}class="{$tbl_class}"{/if}>
    {if !$_no_table}
    <form action="{$url.address}/{$action}{$params}" method="post" id="filter-apply">
        <table id="main_table" cellpadding="0" cellspacing="0" class="listing">
            {include_element file="table_header"}
            {include_element file="table_filter"}
            {*content*}
            {if $table_content}
                {foreach $table_content as $content_key => $content}
                <tr id="line_{$content[$primary_key]}" {if $content@iteration%2==0}class="light"{/if}>
                    <td>
                        <span class="niceCheck">
                            <input type="checkbox" name="item_{$content[$primary_key]}" value="{$content[$primary_key]}" />
                        </span>
                    </td>
                    {include_element file="table_row"}
                    {include_element file="table_content_action"}
                </tr>
                {/foreach}
            {else}
                <tr>
                    <td {if $table_headers}colspan="{count($table_headers) + 1}"{/if}>{$lang->general()->no_data}</td>
                </tr>
            {/if}
            {*end content*}
        </table>
    </form>
    {include_element file="paging"}
    {/if}
</div>