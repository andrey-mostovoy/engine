<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" dir="ltr">
<head>
    {strip}
    {include_element file="site_head"}
    <title>{$site_title}</title>
    {/strip}
</head>
<body>
    {strip}
    {include_element file="ajaxer_block"}

    {include_element file="site_main"}
    
    {/strip}
    {include_element file="debug_mode"}
</body>
</html>