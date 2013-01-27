{if $lmenu}
<div class="toolbar">
    <div class="user-toolbar">
        <ul>
            {foreach $lmenu as $k => $lm}
            <li><a href="{$url.base}/{$lm.href}" class="lnk-{$lm@iteration}{if $selected_left_menu == $k} current{/if}" title="{$lm.title}">{$lm.title}</a></li>
            {/foreach}
        </ul>
    </div>
</div>
{/if}