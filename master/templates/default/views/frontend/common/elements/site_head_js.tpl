{include_up_element}
<!--base js-->
<!--end base js-->

<!--plugins etc..-->
<!--<script type="text/javascript" src="{$url.common_js}/popup/popup.js"></script>-->
{js file="`$url.common_js`/scrollTo/jquery.scrollTo.min.js"}
{js file="`$url.common_js`/slider/jquery.tools.min.js"}
<!--<script type="text/javascript" src="{$url.common_js}/dateinput/jquery.tools.min.js"></script>-->
{js file="`$url.common_js`/jquery.form.js"}
<!--<script type="text/javascript" src="{$url.common_js}/jquery.highlightFade.js"></script>-->
{if $guide_tip}
    {js file="`$url.common_js`/tooltip/jquery.tools.min.js"}
{/if}
<!--end plugins etc..-->

<!--common js-->
<!--end common js-->

<!--other js-->
{if !App::user()->isAuth()}
    {js file="`$url.js`/auth.js"}
{/if}

{if $action == 'index' && $controller != 'index'}
    {include_element file="table_js"}
{/if}

{if $action == 'manage'}
    {include_element file="manage_js"}
{/if}
<!--end other js-->
