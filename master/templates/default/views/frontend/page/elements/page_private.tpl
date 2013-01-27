<div class="columed-content multicolumn">
    {include_element file="left_menu"}
    <div class="content">
        {include_element file="breadcrumb"}
        <div class="static-pages">
            <h2>{$page.title}</h2>
            <div class="wysiwyg">
                {$page.content}
            </div>    
        </div>
    </div>
    {if $right_menu}
    <div class="sidebar">
        <div class="sidebar-nav">
            <ul>
                {foreach $right_menu as $rm}
                <li><a href="{$url.base}/page/{$rm.url}" {if $page.id == $rm.id}class="current"{/if} title="{$rm.title}">{$rm.title}</a></li>
                {/foreach}
            </ul>                      
        </div>
    </div>
    {/if}
</div>