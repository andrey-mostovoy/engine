{if !isset($num)}
    {$num="__num__"}
{/if}
<div {if $_tpl}id="tpl_language"{/if} class="item">
    {if !$_tpl}
        <input type="hidden" name="__data[language][{$num}][id]" value="{$data.language[$num].id}"/>
    {/if}
    <div class="columed-content">
        <div class="column">
            <div class="row">
                <label>{$lang->language()->name}<span>*</span></label>
                <input type="text" name="__data[language][{$num}][name]" value="{$data.language[$num].name}"/>
            </div>
        </div>
        <div class="column">
            <div class="row">
                <label>{$lang->language()->knowledge}<span>*</span></label>
                {include_element file="pii_select" _for=$info.language_knowledge _f="knowledge" _p="language" _n=$num}
            </div>
        </div>
    </div>
    <div class="row">
        <label>{$lang->language()->comments}</label>
        <textarea rows="" cols="" name="__data[language][{$num}][comment]">{$data.language[$num].comment}</textarea>
    </div>
    <div class="btns-toolbar del_more_one">
        <a href="#" class="h30btn" title="{$lang->button()->delete} {$guide_tip.language.title}"><span>{$lang->button()->delete} {$guide_tip.language.title}</span></a>  
    </div>
</div>