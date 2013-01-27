{if $toolbar}
<div class="actions{if $__class} {$__class}{/if}">
    <form action="#" method="post">
        {foreach $toolbar as $action}
        <div class="btn-base{if $actoin@first || $action@last} auto-width{/if}{if $action.class} {$action.class}{/if}">
            <a href="{$action.href|default:"#"}" title="{$action.title}" {if $action.id}id="{$action.id}"{/if}  {if $action.js}onclick="return {$action.js}(this);"{/if}><span>{$action.title}</span></a>
        </div>
        {/foreach}
    </form>
</div>
{/if}