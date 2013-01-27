{if $__sidebar}
<div class="colubed-area">

    {include_element file="manage_sidebar"}

    <div class="column">
        <div class="container">
{/if}
            {include_element file="toolbar"}
            {if $__tabs}
            
            {include_element file="manage_tab"}

            <div class="tabbed-area">
            {/if}

                <form id="manage-save" class="js_v" action="" method="post" enctype="multipart/form-data">
                    {if $data.id}
                        <input type="hidden" name="content_id" value="{$data.id}" />
                        <input type="hidden" name="__data[id]" value="{$data.id}" />
                    {/if}
                    {if $data.meta_id}
                        <input type="hidden" name="meta_id" value="{$data.meta_id}" />
                    {/if}
                    <table cellpadding="0" cellspacing="0" class="manage">
                        {include_element file="manage_content"}
                    </table>
                </form>
                    
                {include_element file="manage_js_tpl"}
    
            {if $__tabs}
            </div>{*close div.tabbed-area*}
            {/if}
            
            {include_element file="toolbar" __class="bottom"}
            
{if $__sidebar}
        </div>{*close div.container*}
    </div>{*close div.column*}
</div>{*close div.colubed-area*}
{/if}