{if !Config::DEBUG}
<script type="text/javascript">
{literal}
    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', '{/literal}{App::setting()->ga_code}{literal}']);
    _gaq.push(['_trackPageview']);

    (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();
{/literal}
</script>
{/if}