{if !isset($num)}
    {$num="__num__"}
{/if}
<div {if $_tpl}id="tpl_education"{/if} class="item">
    {if !$_tpl}
        <input type="hidden" name="__data[education][{$num}][id]" value="{$data.education[$num].id}"/>
    {/if}
    <div class="columed-content">
        <div class="column">
            <div class="row">
                <label>{$lang->education()->degree}<span>*</span></label>
                <input type="text" name="__data[education][{$num}][degree]" value="{$data.education[$num].degree}"/>
            </div>
            <div class="row">
                <label>{$lang->education()->facility}<span>*</span></label>
                <input type="text" name="__data[education][{$num}][facility]" value="{$data.education[$num].facility}"/>
            </div>
        </div>
        <div class="column">
            {include_element file="address" _nozip=true _p="education" _n=$num}
        </div>
    </div>
    <div class="columed-content">
        <div class="column">
            <div class="row date-row">
                <label>{$lang->education()->from}<span>*</span>:</label>
                <input type="text" name="__data[education][{$num}][from_date]" value="{$data.education[$num].from_date}"/>
            </div>
        </div>
        <div class="column">
            <div class="row date-row">
                <label>{$lang->education()->to}<span>*</span>:</label>
                <input type="text" name="__data[education][{$num}][to_date]" value="{$data.education[$num].to_date}"/>
            </div>
        </div>
    </div>        
    <div class="row">
        <label>{$lang->education()->comments}</label>
        <textarea rows="" cols="" name="__data[education][{$num}][comment]">{$data.education[$num].comment}</textarea>
    </div>
    <div class="btns-toolbar del_more_one">
        <a href="#" class="h30btn" title="{$lang->button()->delete} {$guide_tip.education.title}"><span>{$lang->button()->delete} {$guide_tip.education.title}</span></a>  
    </div>
</div>