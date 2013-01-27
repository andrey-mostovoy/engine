<div id="header">
    <div class="header-content">
        <h1>Cum sociis natoque penatibus et magnis dis parturient montes</h1>
        <div class="logo">
            {include_element file="site_header_logo"}
        </div>
        <div class="user-account-tool">
            {$lang->general()->welcome}, <a href="#" class="username" title="{App::user()->first_name} {App::user()->last_name}">{App::user()->first_name} {App::user()->last_name}</a> | <a href="{$url.address}/logout" title="{$lang->button()->logout}">{$lang->button()->logout}</a> 
        </div>
    </div>
    {if $mmenu}
    <div class="mmenu-marker">
        <div class="marker hidden"></div>
    </div>
    <ul class="mmenu">
        {foreach $mmenu as $menu}
        <li{if $selected_main_menu == $menu.id} class="current marked"{/if}>
            <a href="{$url.base}/page/show/id/{$menu.id}" title="{$menu.title}" {*class="current"*}>{$menu.title}</a>
        </li>
        {/foreach}
    </ul>
    {/if}
</div>