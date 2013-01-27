{if $data.parent_id}
    <input type="hidden" name="data[parent_id]" value="{$data.parent_id}" />
{/if}

<label>{$lang.frontendmenu.title}</label>
<input type="text" class="required" name="data[title]" value="{$data.title}" />
<br>
<label>{$lang.frontendmenu.url}</label>
<input type="text" class="" name="data[url]" value="{$data.url}" />
<br>

{foreach name=position from=$positions item=position}
<input type="checkbox" class="required" name="position[]" value="{$position.id}" {if in_array($position.id, $data_position)}checked="checked"{/if} /> {$lang.position[$position.position]}
<br>
{/foreach}