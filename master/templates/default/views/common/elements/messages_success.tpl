{* Template for display success messages *}
{if isset($messages.success) && $messages.success}
<div class="message success">
	<span class="ico success">&nbsp;</span>
    <div class="messages-text">
        {foreach $messages.success as $success}
        <p>{$success}</p>
        {/foreach}
    </div>
    <a href="#" title="{$lang->button()->close}" class="close">{$lang->button()->close}</a>
</div>
{/if}