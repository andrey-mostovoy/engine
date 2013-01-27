{*if $guide_tip && $guide_tip[$_tip]}
    <div class="gtip" title="{$guide_tip[$_tip].content}">
        {$guide_tip[$_tip].title}
    </div>
{/if*}


{if $guide_tip && $guide_tip[$_tip]}
    <h3>{$guide_tip[$_tip].title}</h3>
    <div class="help-message gtip" title="{$guide_tip[$_tip].content}">
        <a href="#" title="hint" class="hint">&nbsp;</a>
        <div class="hint-wrapper hidden">
            {$guide_tip[$_tip].content}
        </div>
    </div>
{/if}