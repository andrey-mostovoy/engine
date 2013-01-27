<div class="swfupload-control">
    {*IMPORTANT: do NOT remove. Need to recognize session hash for tmp files*}
    <input type="hidden" name="{SwfUpload::FORM_HASH_NAME}" value="{SwfUpload::getHash()}"/>
    {*id attr indicate wich key from settings need take*}
    <span id="{$_key|default:image}" class="swf_file_upload_type" style="display:none;"></span>
    
    {*-----------  for log handler*}
    {*<ol class="log" style="height: 100px; overflow: auto;"></ol>*}
    {*----end----*}
    {*-------- for classic form handler*}
    <div>
        <input type="text" id="txtFileName" disabled="true" style="border: solid 1px; background-color: #FFFFFF;" />
        <span id="spanButtonPlaceholder"></span>
    </div>
    {*This is where the file ID is stored after SWFUpload uploads the file and gets the ID back from upload.php *}
    <input type="hidden" name="hidFileID" id="hidFileID" value="" />
    {*----end----*}
    
    <div class="flash" id="fsUploadProgress">
        {* This is where the file progress gets shown.  SWFUpload doesn't update the UI directly.
            The Handlers (in handlers.js) process the upload events and make the UI updates *}
    </div>
    
    {*-------- for classic form with thumbs handler *}
            {include_element file="swf_file_upload_thumbs"}
    {*----end----*}
</div>


{*
<div id="upl{if $ftype eq 'img'}Image{elseif $ftype eq 'video'}Video{elseif $ftype eq 'doc'}Doc{elseif $ftype eq 'map'}Map{/if}" class="upolad-files upload{if $ftype eq 'img'}-image{elseif $ftype eq 'video'}-video{elseif $ftype eq 'doc'}-doc{/if}">
      
{if $ftype == 'img'}
	
    <div class="row">
        <span id="spanImageButtonPlaceholder"></span>
    </div>

    <div id="divImageProgressContainer"></div>
    <div id="thumbnailsImage">
        
        {include_element file="file_upload_thumbs"}
        
    </div>
{elseif $ftype == 'map'}    
    <div class="row">
        <span id="spanMapButtonPlaceholder"></span>
    </div>

    <div id="divMapProgressContainer"></div>
    <div id="thumbnailsMap">
        
        {include_element file="file_upload_thumbs"}
        
    </div>
{elseif $ftype == 'video'}
    
	<div class="upload-video-tabs">
		<ul class="tabs">
			<li><a href="#">{lang}Resource link{/lang}</a></li>
			<li><a href="#">{lang}Upload your own{/lang}</a></li>
		</ul>
	</div>
        
<!--this never show. used as template-->
    <div id="tpl" class="ns hidden">
        <div class="video-items js-video_res">
            <div class="row-settings-top">
				<a href="#" title="{$lang.button.delete}" onclick="return deleteVideoUrl(this);" class="mdelete">{$lang.button.delete}</a>
			</div>
			<div class="row-settings">
				<label>{lang}Video URL{/lang}:</label>
				<input type="text" class="itxt" name="data[video_url][:|num|:][url]" value="" />
			</div>
            {include_element file="file_upload_thumbs_fields" ftype=video_url file_id=":|num|:"}
        </div>
    </div>
<!--end-->

    <div class="panes">
        <div class="video-list">
            
			<div class="h24smallbtn h24smallyellowbtn js-add_video_url_wrap">
                <a href="#" class="btn-add-more-video" title="Add more" onclick="return addVideoUrl(this);">
                    <span>Add more</span>
                </a>
            </div>
            
        </div>
        <div>
            <div>
                <span id="spanVideoButtonPlaceholder"></span>
            </div>
            <div id="divVideoProgressContainer"></div>    
        </div>
    </div>
    <div id="thumbnailsVideo">
        
        {include_element file="file_upload_thumbs"}
    
    </div>

{else if $ftype == 'doc'}
{/if}

</div>*}