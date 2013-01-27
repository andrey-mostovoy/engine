<div id="content-{if $is_admin}page{else}login{/if}">
    <div class="title-container">
        <div class="title-content-inner">
            <div class="title-content">
                {include_element file="breadcrumb"}
            </div>
        </div>
    </div>
    <div class="area">
        <div class="{if $is_admin}area-content{else}lform{/if}">
            
            {include_element file="messages_summary"}
            
            {include file=$tpl.content}
        </div>
    </div>
</div>