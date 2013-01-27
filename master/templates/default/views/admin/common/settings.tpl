<div class="settings {if $action eq 'settings'}special-settings{/if}">
    <div class="settigs-menu {if $action eq 'settings'}special-menu{/if}">
        {include_element file=settings_menu}
    </div>
<!--    <form id="manage-save" class="js_v" action="" method="post" enctype="multipart/form-data">-->
        <div class="settings-content">
            {include_element file=error show_form_error=true}
            {include_element file=message}
            <div class="error" id="error-messages"></div>
            {include_element file=$settings_content}
        </div>
<!--    </form>-->
</div>