{if $toolbar}
<div class="btns-toolbar{if $__class} {$__class}{/if}">
    <form action="#" method="post">
        {foreach $toolbar as $action}
        <a {if $action.id}id="{$action.id}"{/if} href="{$action.href|default:"#"}" class="h30btn{if $action.class} {$action.class}{/if}" title="{$action.title}" {if $action.js}onclick="return {$action.js}(this);"{/if}><span>{$action.title}</span></a>
        {/foreach}
    </form>
</div>
{/if}