{if $content_actions}
    <td class="actions a{$content_actions_count|default:count($content_actions)}">
    {foreach $content_actions as $action}
        {if !$action.condition || (
            ((!isset($action.condition.type) || $action.condition.type == '=') && $action.condition.value == $content[$action.condition.key])
            || ($action.condition.type == '!=' && $action.condition.value != $content[$action.condition.key])
            || ($action.condition.type == '>' && $action.condition.value > $content[$action.condition.key])
            || ($action.condition.type == '<' && $action.condition.value < $content[$action.condition.key])
            )}
            <a href="{strip}{$action.href}
                        {if $action.params}                
                            {foreach $action.params as $key => $aparam}
                                /{if is_string($key)}{$key}{else}{$aparam}{/if}/{$content[$aparam]}
                            {/foreach}
                        {else}
                            /id/{$content.id}
                        {/if}
                        {if $action.condition.set}
                            /{$action.condition.key}/{$action.condition.set|default:$action.condition.value}
                        {/if}{/strip}"
                title="{$action.title}" class="ico{if $action.class} {$action.class}{/if}"
                {if $action.js}onclick="return {$action.js}(this);"{/if}>
                {$action.title}</a>
        {/if}
    {/foreach}
    </td>
{/if}