<div id="header">
    <h1>{include_element file="site_header_logo"}</h1>
    {if App::user()->isAuth()}
    <div>
        {include_element file="menu"}
    </div>
    {/if}
</div>