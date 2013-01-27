<div id="download_template" class="popup hidden">
    <form action="{$url.address}/downloadtemplate" method="post">
        <div class="row">
            <label>{$lang->cv()->choose_template}</label>
            <select name="__data[template_id">
                {foreach $templates as $t}
                    <option value="{$t.id}">{$t.name}</option>
                {/foreach}
            </select>
        </div>
        <div class="row">
            <a href="#" class="h30btn js_btn_submit" title="{$lang->button()->download}"><span>{$lang->button()->download}</span></a>
        </div>
    </form>
</div>
<div id="upload_cv" class="popup hidden">
    <form action="{$url.address}/uploadcv" method="post">
        <input type="hidden" name="__data[type]" value="upload"/>
        <div class="row">
            <label>{$lang->cv()->choose_template}</label>
            {include_element file="swf_file_upload" _key="cv"}
        </div>
        <div class="row">
            <a href="#" class="h30btn js_btn_submit" title="{$lang->cv()->button_upload}"><span>{$lang->cv()->button_upload}</span></a>
        </div>
    </form>
</div>