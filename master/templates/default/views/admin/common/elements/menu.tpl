<ul class="mmenu">
    {* tag span needed only for the first list item *}
    <li><a href="{$url.parts[0]}/user" title="{$lang->amenu()->user}" {if $controller == 'user'}class="selected"{/if}><span>{$lang->amenu()->user}</span></a></li>
    <li><a href="{$url.parts[0]}/static" title="{$lang->amenu()->static}" {if $controller == 'static'}class="selected"{/if}>{$lang->amenu()->static}</a>
        <ul class="submenu">
            <li><a href="{$url.parts[0]}/static/public" title="{$lang->static()->tab_public}">{$lang->static()->tab_public}</a></li>
            <li><a href="{$url.parts[0]}/static/private" title="{$lang->static()->tab_private}">{$lang->static()->tab_private}</a></li>
            <li class="last"><a href="{$url.parts[0]}/static/guide" title="{$lang->static()->tab_guide}">{$lang->static()->tab_guide}</a></li>
        </ul>
    </li>
    <li><a href="{$url.parts[0]}/template/letter" title="{$lang->amenu()->template_letter}" {if $controller == 'template' && $action == 'letter'}class="selected"{/if}>{$lang->amenu()->template_letter}</a></li>
    {if Defines::DEV}
    <li><a href="{$url.parts[0]}/template/cv" title="{$lang->amenu()->template_cv}" {if $controller == 'template' && $action == 'cv'}class="selected"{/if}>{$lang->amenu()->template_cv}</a></li>
    {/if}
    {*<li><a href="{$url.parts[0]}/template/email" title="{$lang->amenu()->template_email}" {if $controller == 'template' && $action == 'email'}class="selected"{/if}>{$lang->amenu()->template_email}</a></li>*}
    <li><a href="{$url.parts[0]}/setting" title="{$lang->amenu()->setting}" {if $controller == 'setting' || ($controller == 'template' && $action == 'email')}class="selected"{/if}>{$lang->amenu()->setting}</a>
        <ul class="submenu">
            <li><a href="{$url.parts[0]}/template/email" title="{$lang->amenu()->template_email}">{$lang->amenu()->template_email}</a></li>
            <li class="last"><a href="{$url.parts[0]}/setting" title="{$lang->amenu()->site_setting}">{$lang->amenu()->site_setting}</a></li>
        </ul>
    </li>
    <li><a href="{$url.parts[0]}/transactions" title="{$lang->amenu()->transactions}">{$lang->amenu()->transactions}</a></li>
</ul>
<ul class="right-menu">
    <li class="logout"><a href="{$url.address}/logout"><span>{$lang->button()->logout}</span></a></li>
    <li class="site"><a href="{$url.domain}" target="_blank">{$lang->amenu()->view_site}</a></li>
</ul>