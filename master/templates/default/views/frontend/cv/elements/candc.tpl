{if !isset($num)}
    {$num="__num__"}
{/if}
<div {if $_tpl}id="tpl_candc"{/if} class="item">
    {if !$_tpl}
        <input type="hidden" name="__data[candc][{$num}][id]" value="{$data.candc[$num].id}"/>
    {/if}
    <div class="columed-content">
        <div class="column">
            <div class="row">
                <label>{$lang->candc()->title}<span>*</span></label>
                <input type="text" name="__data[candc][{$num}][title]" value="{$data.candc[$num].title}"/>
            </div>
        </div>
        <div class="column">
            <div class="row">
                <label>{$lang->candc()->facility}<span>*</span></label>
                <input type="text" name="__data[candc][{$num}][facility]" value="{$data.candc[$num].facility}"/>
            </div>
        </div>
    </div>
    <div class="columed-content">
        <div class="column">
            {include_element file="address" _nozip=true _p="candc" _n=$num}
        </div>
        <div class="column">
            <div class="row date-row">
                <label>{$lang->candc()->from}<span>*</span>:</label>
                <input type="text" name="__data[candc][{$num}][from_date]" value="{$data.candc[$num].from_date}"/>
            </div>
            <div class="row date-row">
                <label>{$lang->candc()->to}<span>*</span>:</label>
                <input type="text" name="__data[candc][{$num}][to_date]" value="{$data.candc[$num].to_date}"/>
            </div>
        </div>
    </div>
    <div class="row">
        <label>{$lang->candc()->comments}</label>
        <textarea rows="" cols="" name="__data[candc][{$num}][comment]">{$data.candc[$num].comment}</textarea>
    </div>
    <div class="btns-toolbar del_more_one">
        <a href="#" class="h30btn" title="{$lang->button()->delete} {$guide_tip['c-and-c'].title}"><span>{$lang->button()->delete} {$guide_tip['c-and-c'].title}</span></a>  
    </div>
</div>