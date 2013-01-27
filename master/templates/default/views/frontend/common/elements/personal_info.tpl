{*personal info*}
{if $data.personal.id}
    <input type="hidden" {include_element file="pif_name" _f="id"} {include_element file="pif_value" _f="id"}/>
{/if}
<div class="columed-content">
    <div class="column">
        {if !$__cv}
        <h3>{$lang->user()->pers_info}</h3>
        {/if}
        <div class="row">
            <label>{$lang->user()->first_name}<span>*</span>:</label>
            {include_element file="pii_text" _f="first_name"}
        </div> 
        <div class="row">
            <label>{$lang->user()->last_name}<span>*</span>:</label>
            {include_element file="pii_text" _f="last_name"}
        </div>
        <div class="row date-row">
            <label>{$lang->user()->birth_date}<span>*</span>:</label>
            {include_element file="pii_text" _f="birth_date"}
        </div>
        <div class="row">
            <label>{$lang->user()->sex}<span>*</span>:</label>
            {include_element file="pii_select" _f="sex" _for=$info.sex}
        </div> 
        <div class="row">
            <label>{$lang->user()->marital}:</label>
            {include_element file="pii_select" _f="marital_id" _for=$info.marital}
        </div>
        <div class="row">
            <label>{$lang->user()->children}:</label>
            {include_element file="pii_text" _f="children"}
        </div>
        {if $__cv}
            {include_element file="swf_file_upload"}
        {/if}
        {if !$__cv}
        <div class="row btns-row">
            <a href="#" class="h30btn" title="Update personal information"><span>Update personal information</span></a>  
        </div>
        {/if}
    </div>
    <div class="column">
        {if !$__cv}
        <h3>{$lang->user()->cont_info}</h3>
        {/if}
        {include_element file="address"}
        <div class="row">
            <label>{$lang->user()->phone}{if !$__cv}<span>*</span>{/if}:</label>
            {include_element file="pii_text" _f="phone"}
        </div>
        <div class="row">
            <label>{$lang->user()->mobile}:</label>
            {include_element file="pii_text" _f="mobile"}
        </div>
    </div>
</div>