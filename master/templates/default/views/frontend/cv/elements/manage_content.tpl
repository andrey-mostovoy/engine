<div class="hidden">
    <input type="hidden" name="validate_type" value="save"/>
    <input type="hidden" name="__data[user_id]" value="{$data.user_id|default:App::user()->id}"/>
</div>
{*cv name*}
<div class="row">
    <label>{$lang->cv()->name}<span>*</span></label>
    <input type="text" name="__data[name]" value="{$data.name}"/>
</div>

{*position information*}
<div class="title">
    {include_element file="guide_tip" _tip="pos-info"}
</div>
{if $data.position.id}
    <input type="hidden" name="__data[position][id]" value="{$data.position.id}"/>
{/if}
<div class="row">
    <label>{$lang->position()->title}<span>*</span></label>
    <input type="text" name="__data[position][title]" value="{$data.position.title}"/>
</div>
<div class="row">
    <label>{$lang->position()->facility}<span>*</span></label>
    <input type="text" name="__data[position][facility]" value="{$data.position.facility}"/>
</div>

{*personal info*}
<div class="title">
    {include_element file="guide_tip" _tip="pers-info"}
</div>
{include_element file="personal_info" __cv=true _p="personal"}{*_p attr for address tpl, _cv indicate that personal info for cv section*}

{*education*}
<div id="education" class="dublicate-fields mult_it"> {*for multiple inserts, class required: mult_it, add_more, items. Ids required: all*}
    <div class="title">
        {include_element file="guide_tip" _tip="education"}
    </div>
    <div class="items-content">
        {foreach $data.education as $num => $item}
            {include_element file="education"}
        {/foreach}
    </div>
    <div class="add_more btns-toolbar">
        <a href="#" class="h30btn" title="{$lang->button()->add} {$guide_tip.education.title}">
            <span>{$lang->button()->add} {$guide_tip.education.title}</span>
        </a>
    </div>
</div>

{*work experience*}
<div id="work" class="dublicate-fields mult_it"> {*for multiple inserts, class required: mult_it, add_more, items. Ids required: all*}
    <div class="title">
        {include_element file="guide_tip" _tip="work"}
    </div>
    <div class="row check">
        <input type="checkbox" name="__data[work][no_work]" value="{$data.work.no_work}"/>
        <label>{$lang->work()->no_work}</label>
    </div>
    <div class="items-content">
        {foreach $data.work as $num => $item}
            {include_element file="work"}
        {/foreach}
    </div>
    <div class="add_more btns-toolbar">
        <a href="#" class="h30btn" title="{$lang->button()->add} {$guide_tip.work.title}"><span>{$lang->button()->add} {$guide_tip.work.title}</span></a>
    </div>
</div>

{*professional skills*}
<div class="title">
    {include_element file="guide_tip" _tip="proff-skills"}
</div>
{if $data.cv_info.id}
    <input type="hidden" name="__data[cv_info][id]" value="{$data.cv_info.id}"/>
{/if}
<div class="row">
    <label>{$lang->cv()->proff_skills}<span>*</span></label>
    <textarea rows="" cols="" name="__data[cv_info][proff_skils]">{$data.cv_info.proff_skils}</textarea>
</div>

{*Courses & Сertificates*}
<div id="candc" class="dublicate-fields mult_it"> {*for multiple inserts, class required: mult_it, add_more, items. Ids required: all*}
    <div class="title">
        {include_element file="guide_tip" _tip="c-and-c"}
    </div>
    <div class="items-content">
        {foreach $data.candc as $num => $item}
            {include_element file="candc"}
        {/foreach}
    </div>
    <div class="add_more btns-toolbar">
        <a href="#" class="h30btn" title="{$lang->button()->add} {$guide_tip['c-and-c'].title}"><span>{$lang->button()->add} {$guide_tip['c-and-c'].title}</span></a>
    </div>
</div>

{*Languages*}
<div id="language" class="dublicate-fields mult_it"> {*for multiple inserts, class required: mult_it, add_more, items. Ids required: all*}
    <div class="title">
        {include_element file="guide_tip" _tip="language"}
    </div>
    <div class="items-content">
        {foreach $data.language as $num => $item}
            {include_element file="language"}
        {/foreach}
    </div>
    <div class="add_more btns-toolbar">
        <a href="#" class="h30btn" title="{$lang->button()->add} {$guide_tip.language.title}"><span>{$lang->button()->add} {$guide_tip.language.title}</span></a>
    </div>
</div>

{*additional information*}
<div class="title">
    {include_element file="guide_tip" _tip="add-info"}
</div>
<div class="row llabel">
    <label>{$lang->cv()->add_info}</label>
    <textarea rows="" cols="" name="__data[cv_info][add_info]">{$data.cv_info.add_info}</textarea>
</div>
    
{*cv template*}
<div class="title">
    {include_element file="guide_tip" _tip="cv-template"}
</div>
<div class="slider-main">
    <input type="hidden" name="__data[cv_info][template_id]" value="{$data.cv_info.template_id}"/>
    <a class="prev browse"></a>
    <div class="slider-content">
        <ul>
            {foreach $templates as $t}
                {if $t@first}
                <li>
                {/if}
                <div class="cv-item">
                    <a href="#" class="choice">
                        <span class="hidden">{$t.id}</span>
                        <img src="{$url.image}/temp/cv-template.jpg" alt="{$t.name}" title="{$t.name}">
                    </a>
                    <a href="#" title="{$lang->button()->preview}" class="preview">{$lang->button()->preview}</a> 
                </div>
                {if !$t@last && $t@iteration%5 == 0}
                    </li>
                    <li>
                {/if}
                {if $t@last}
                </li>
                {/if}
            {/foreach}
        </ul>
    </div>
    <a class="next browse"></a>
</div>