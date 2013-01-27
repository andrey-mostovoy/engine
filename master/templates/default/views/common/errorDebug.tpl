<div style="margin: 10px; font-size: 12px; font-family: Arial; line-height: 20px;">
	<h1>Error! Debug Info:</h1>
	<p>
        <span style="color: #ff0000">{$error.message}</span><br/>
		in file <strong style="text-decoration: underline;">{$error.file}</strong><br/>
		on line <strong>{$error.line}</strong>
	</p>
    {if $excTrace}
	<div>
		<p>
		{foreach $excTrace as $key=>$trace}
			#{$key}&nbsp;
            {$trace.file}({$trace.line}):&nbsp;
			<strong>{$trace.class}{$trace.type}{$trace.function}</strong>({foreach $trace.args as $args=>$arg}'{$arg}'{if !$smarty.foreach.args.last}, {/if}{/foreach})
			<br/>
		{/foreach}
		</p>
	</div>
    {/if}
</div>