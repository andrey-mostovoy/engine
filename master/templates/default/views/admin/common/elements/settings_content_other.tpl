<!--  settings content tab -->
<form class="js_v" action="" method="post">
    <input type="hidden" name="validate" value="settings" />
    <table cellpadding="0" cellspacing="0" class="settings-other">
        <tr class="first">
            <td class="label">
                {if $controller eq 'specialoffers'}
                <label>{$lang.so.title_lenght}:</label>
                {/if}
                {if $controller eq 'packages'}
                <label>{$lang.packages.title_lenght}:</label>
                {/if}
            </td>
            <td>
                <input type="text" name="data[title_lenght]" value="{$data.title_lenght}" class="itxt num"/>
                <input type="hidden" name="data_h[title_lenght]" value="{$data_h.title_lenght}" />
            </td>
        </tr>
        <tr>
            <td  class="label">
                {if $controller eq 'specialoffers'}
                <label>{$lang.so.year_advance}:</label>
                {/if}
                {if $controller eq 'packages'}
                <label>{$lang.packages.year_advance}:</label>
                {/if}
            </td>
            <td>
                <input type="text" name="data[year_advance]" value="{$data.year_advance}"  class="itxt num"/>
                <input type="hidden" name="data_h[year_advance]" value="{$data_h.year_advance}" />
            </td>
        </tr>
	</table>
		<div class="save">
                <a href="#" class="btn-submit" title="{$lang.button.save}">{$lang.button.save}</a>
		</div>
</form>