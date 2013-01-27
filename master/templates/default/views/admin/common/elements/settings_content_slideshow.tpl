<!--  settings content tab -->
<table cellpadding="0" cellspacing="0" class="settings-slide">
    {if $slideshow_images}
        <tr>
            <th>
                Image
            </th>
        </tr>
        {foreach from=$slideshow_images item=image}
        <tr style="background: {cycle values='#eaf6fb, #f4fcff'};">
            <td>
                <img src="{image section=slider id=$image.id}" alt="{$image.name}" width="755" height="230"/>
                <a href="{$url.address}/settings/delete/{$image.id}" title="{$lang.button.delete}" onclick="return confirm_delete();" class="btn-toolbar">{$lang.button.delete}</a>
            </td>
        </tr>
        {/foreach}
    {else}
    <tr>
        <td>
            {$lang.general.no_data}
        </td>
    </tr>
    {/if}
    <tr>
        <td class="upload">
			
            <form action="" method="post" enctype="multipart/form-data">
				<div class="actions-upoad">
					<label>Upload image:</label>
					<input type="file" name="image" value="" />
				</div>
				<div class="actions-upoad">
					<a href="#" class="btn-submit" title="{$lang.button.update}">{$lang.button.update}</a>
				</div>
            </form>
        </td>
    </tr>
</table>