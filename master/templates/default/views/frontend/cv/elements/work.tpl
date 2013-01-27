{if !isset($num)}
    {$num="__num__"}
{/if}
<div {if $_tpl}id="tpl_work"{/if} class="item">
    {if !$_tpl}
        <input type="hidden" name="__data[work][{$num}][id]" value="{$data.work[$num].id}"/>
    {/if}
    <div class="columed-content">
        <div class="column">
            <div class="row">
                <label>{$lang->work()->title}<span>*</span></label>
                <input type="text" name="__data[work][{$num}][title]" value="{$data.work[$num].title}"/>
            </div>
        </div>
        <div class="column">
            <div class="row">
                <label>{$lang->work()->employer}<span>*</span></label>
                <input type="text" name="__data[work][{$num}][employer]" value="{$data.work[$num].employer}"/>
            </div>
        </div>
    </div>
    <div class="row">
        <label>{$lang->work()->brif}<span>*</span></label>
        <textarea rows="" cols="" name="__data[work][{$num}][brif]">{$data.work[$num].brif}</textarea>
    </div>
    <div class="columed-content">
        <div class="column">
            {include_element file="address" _nozip=true _p="work" _n=$num}
        </div>
        <div class="column">
            <div class="row date-row">
                <label>{$lang->work()->from}<span>*</span>:</label>
                <input type="text" name="__data[work][{$num}][from_date]" value="{$data.work[$num].from_date}"/>
            </div>
            <div class="row date-row">
                <label>{$lang->work()->to}<span>*</span>:</label>
                <input type="text" name="__data[work][{$num}][to_date]" value="{$data.work[$num].to_date}"/>
            </div>
            <div class="row">
                <label>{$lang->work()->phone}<span>*</span></label>
                <input type="text" name="__data[work][{$num}][phone]" value="{$data.work[$num].phone}"/>
            </div>
        </div>
    </div>
    <div class="row">
        <label>{$lang->work()->achievment}<span>*</span></label>
        <textarea rows="" cols="" name="__data[work][{$num}][achievment]">{$data.work[$num].achievment}</textarea>
    </div>
    <div class="btns-toolbar del_more_one">
        <a href="#" class="h30btn" title="{$lang->button()->delete} {$guide_tip.work.title}"><span>{$lang->button()->delete} {$guide_tip.work.title}</span></a>  
    </div>
</div>