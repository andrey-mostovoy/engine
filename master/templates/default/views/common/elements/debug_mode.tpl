{if defined('Defines::SHOW_QUERY') && Defines::SHOW_QUERY}
    <div style="border: #0E3460 groove thin; margin: 10px; padding: 5px;">
        <h3>QUERIES</h3>
    {foreach BaseDb::getQueries() as $q}
        <p style="border: #3da122 dashed thin; margin: 5px; padding: 5px;">{$q}</p>
    {/foreach}
    </div>
{/if}