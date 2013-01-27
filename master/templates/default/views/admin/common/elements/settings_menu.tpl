<!-- the tabs settings menu -->
<ul>
    {foreach from=$settings_menu item=menu}
        <li {if $menu.id == $settings_current_menu}class="selected"{/if}><a href="{$url.address}/settings/id/{$menu.id}">{$menu.name}</a></li>
    {/foreach}
</ul>