<div class="swf_upload_thumbnails">
</div>

{* old from sdtn
{foreach $files as $file}

<div class="tmp-thumb{if $hidden} hidden{/if}">
	<div class="row-settings-top">
		<a href="#" id="{$tmp}{$ftype}_{$file_id|default:$file.meta_id}{if !$tmp}-{$file_source|default:$file.source|default:''}{/if}" title="{$lang.button.delete}" class="delete-{$tmp}{$ftype} mdelete">{$lang.button.delete}</a>
		
		{if $main_select || $main}
			<input type="radio" name="data[main_{$ftype}]" value="{$file_id|default:$file.meta_id}" {if $main eq $file.meta_id}checked="checked"{/if}/> 			<label>{$lang.general["main_$ftype"]}</label>
		{/if}
	</div>
	<div class="thumb-content">
		<div class="thumb-block">
		{if $ftype eq 'img' || $ftype eq 'map'}
			{include_element file="image_embed" entry=$file preview=true}
		{elseif $ftype eq 'video'}
			{video item=$file size=block preview=true tmp=$file.is_tmp}
		{elseif $ftype eq 'doc'}
			
		{/if}    
		</div>
             {if $ftype == 'map'}
                 <input type="hidden" name="data[img][{$file_id|default:$entry.meta_id}][source]" value="map" />
             {else}
                {if $tpl_path}
                    {include file="`$tpl.file_upload_thumbs_fields`" entry=$file}
                {else}
                    {include_element file="file_upload_thumbs_fields" entry=$file}
                {/if}
             {/if}
	</div>
</div>

{/foreach}
*}