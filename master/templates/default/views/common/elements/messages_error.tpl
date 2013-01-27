{* Template for display errors *}
{if isset($messages.error) && $messages.error}
    {* if pass form_error var with key name of element _form array display it value*}
    {if $form_error} {*single error line*}
        {if $messages.error.form.$form_error}
            <span class="message form-error">{$messages.error.form.$form_error}</span>
        {/if}
    {else} {*error wall*}
        <div class="message error hidden">
            {*<p>Please fix the following input errors:</p>*}
			<span class="ico error">&nbsp;</span>
            <div class="messages-text">
                {foreach $messages.error.general as $error}
                <p>{$error}</p>
                {/foreach}
                {* if pass show_form_error var display form errors *}
                {if $show_form_error|default:false eq true and $messages.error.form}
                    {foreach $messages.error.form as $form_error}
                    <p>{$form_error}</p>
                    {/foreach}
                {/if}
            </div>
            <a href="#" title="{$lang->button()->close}" class="close">{$lang->button()->close}</a>
        </div>
    {/if}
{/if}