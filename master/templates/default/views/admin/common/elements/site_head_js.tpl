{include_up_element}
<!--base js-->
<!--end base js-->

<!--plugins etc..-->
{js file="`$url.common_js`/jquery.form.js"}
{if $action == 'manage'}
    {include_element file="manage_js"}
{/if}

<!--common js-->
{*js file="`$url.js`/admin.js"*}
{if $action != 'manage'}
    {include_element file="table_js"}
{/if}