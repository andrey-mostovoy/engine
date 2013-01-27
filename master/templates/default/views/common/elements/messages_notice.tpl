{* Template for display notice messages *}
{if isset($messages.notice) && $messages.notice}
<div class="message notice">
	<span class="ico notice">&nbsp;</span>
    <div class="messages-text">
        {foreach $messages.notice as $notice}
        <p>{$notice}</p>
        {/foreach}
    </div>
    <a href="#" title="{$lang->button()->close}" class="close">{$lang->button()->close}</a>
</div>
{/if}