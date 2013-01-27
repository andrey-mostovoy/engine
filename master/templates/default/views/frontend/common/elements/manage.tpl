{if $__sidebar}
<div class="columed-content twocolumn">

    {include_element file="manage_sidebar"}

    <div class="content">
{/if}{*end __sidebar*}

            {include_element file="breadcrumb"}
            <div class="pages">
                
                {if $__tabs}

                {include_element file="manage_tab"}

                <div class="tabbed-area">
                {/if}{*end __tabs*}
                    <div class="wide-form-block">
                        <div class="wf-description">
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi ultricies euismod augue quis auctor. Suspendisse aliquet consectetur magna, at sodales odio dapibus nec. Nunc nec ante lectus. Praesent vel imperdiet augue. Curabitur ullamcorper tempor fringilla. Cras eros tellus, vulputate vulputate pulvinar auctor, pretium vel est
                        </div>
                        <form id="manage-save" class="js_v" action="" method="post" enctype="multipart/form-data">
                            {if $data.id}
                                <input type="hidden" name="__data[id]" value="{$data.id}" />
                            {/if}
                            <div class="manage wf-form">
                                {include_element file="manage_content"}
                            </div>
                        </form>

                        {include_element file="manage_js_tpl"}
                    </div>

                {if $__tabs}
                </div>{*close div.tabbed-area   of  __tabs*}
                {/if}

                {include_element file="toolbar" __class="bottom"}
                
            </div>{*close  div.pages*}
            
{if $__sidebar}
        </div>{*close div.content*}
    </div>{*close div.columed-content twocolumn*}
{/if}