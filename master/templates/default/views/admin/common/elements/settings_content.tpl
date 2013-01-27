<!--  settings content tab -->
<div class="settings-content">
{foreach $block_sections as $blocks}    
    <table cellpadding="0" cellspacing="0">
        {foreach from=$blocks name=main item=main}
            {if $smarty.foreach.main.index % $blocks_limit == 0}
                {$start = $smarty.foreach.main.index}
                {$end = $smarty.foreach.main.index + $blocks_limit}
                {$is_next = 1}
            {/if}
            {if $is_next}
                <tr>
                    {foreach from=$blocks name=block_title item=section}
                        {if $smarty.foreach.block_title.index >= $start && $smarty.foreach.block_title.index < $end}
                            <th>
                                {$lang.settings[$section.type]}
                            </th>
                        {/if}
                    {/foreach}
                </tr>
                <tr>
                {foreach from=$blocks name=block item=section}
                    {if $smarty.foreach.block.index >= $start && $smarty.foreach.block.index < $end}
                        {$cur_section=$settings[$section.type]}
                        <td id="group-{$section.type}-{$section.parent}" class="group" >
                            <div id="box_{$cur_section.0.parent}" class="box" >
                                {if $cur_section}
                                    {foreach from=$cur_section item=value}
                                        <div id="entry_{$value.id}_{$value.parent}">{$value.name}</div>
                                    {/foreach}
                                {/if}
                            </div>
                        </td>
                    {/if}
                {/foreach}
                </tr>
                <tr>
                {foreach from=$blocks name=actions item=section}
                    {if $smarty.foreach.actions.index >= $start && $smarty.foreach.actions.index < $end}
                        <td id="input-{$section.type}" class="action_block">
                            <div class="settings-actions">
                                <label>{$lang.settings.input_field}</label>
                                <input type="text" class="itxt small"/>
                                <div class="row-actions">
                                    <a href="{$url.parts[0]}/settings/manage" class="action_edit btn">{$lang.button.add} / {$lang.button.edit}</a><span> | </span>
                                    <a href="{$url.parts[0]}/settings/delete" class="action_delete btn">{$lang.button.delete}</a><span> | </span>
                                    <a href="#" class="action_clear btn">{$lang.button.clear}</a>
                                </div>
                            </div>
                        </td>
                    {/if}
                {/foreach}
                </tr>
            {/if}
            {$is_next = 0}
        {/foreach}
    </table>
{/foreach}
</div>