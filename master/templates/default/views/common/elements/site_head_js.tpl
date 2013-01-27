<!--base common js-->
{js file="`$url.common_js`/jquery-1.7.1.min.js"}
{js file="`$url.common_js`/php_js.js"}
{js_code}
    {include_element file="globalvars"}
{/js_code}
{*js file="`$url.common_js`/site_tpl_onclick_functions.js"*}
<!--end base common js-->

<!--plugins common etc..-->
{js file="`$url.common_js`/ajaxer.js"}
<!--end plugins common etc..-->

<!--common site js-->
{js file="`$url.common_js`/site_common.js"}
{js file="`$url.common_js`/validator/jquery.tools.min.js"}
{js file="`$url.common_js`/validator/validator.js"}
{js file="`$url.js`/common.js"}
<!--end common site js-->

<!--other common js-->
<!--end other common js-->