{strip}value="
{if $_p}
    {if $_a}
        {if isset($_n)}
            {$data[$_p][$_n][$_a][$_f]}
        {else}    
            {$data[$_p][$_a][$_f]}
        {/if}
    {else}
        {if isset($_n)}
            {$data[$_p][$_n][$_f]}
        {else}    
            {$data[$_p][$_f]}
        {/if}
    {/if}
{else}
    {if $_a}
        {if isset($_n)}
            {$data[$_n][$_a][$_f]}
        {else}    
            {$data[$_a][$_f]}
        {/if}
    {else}
        {if isset($_n)}
            {$data[$_n][$_f]}
        {else}    
            {$data[$_f]}
        {/if}
    {/if}
{/if}
"{/strip}