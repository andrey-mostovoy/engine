{*address info*}
<input type="hidden" {include_element file="pif_name" _a="address" _f="id"} {include_element file="pif_value" _a="address" _f="id"}/>
<div class="row">
    <label>{$lang->address()->country}<span>*</span>:</label>
    {include_element file="pii_select" _a="address" _f="country_id" _for=$info.country}
</div> 
<div class="row">
    <label>{$lang->address()->city}<span>*</span></label>
    {include_element file="pii_text" _a="address" _f="city"}
</div>
<div class="row">
    <label>{$lang->address()->address}<span>*</span>:</label>
    {include_element file="pii_text" _a="address" _f="address"}
</div>
{if !$_nozip}
<div class="row">
    <label>{$lang->address()->zip}<span>*</span>:</label>
    {include_element file="pii_text" _a="address" _f="zip"}
</div>
{/if}