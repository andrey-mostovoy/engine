{include_element file='messages_summary'}
<h1>Join us now please select your membership</h1>
<form action="{$url.domain}/paypal/paypal" method="post">
    <input type="hidden" name="validate_type" value="describe">
    {if $payment_plan}
        {foreach from=$payment_plan item=plan}
            <p><input type="radio" name="data[describe]" value="{$plan.id}">{$plan.initial_period} Months &pound;{$plan.instant_cost} Then &pound;{$plan.month_cost} Monthly Thereafter</p>
        {/foreach}
    {/if}
    <p>Payment automatically renews at &pound; after the initial period expires, you can cancel your membership at any time<br />
    A small price to pay ti find the right job for you</p>
    <p><input type="checkbox" name="data[terms]" value="1">I agree with <a href="#">Terms&Conditions</a></p>
    <p><a href="#" class="js_btn_submit">Go</a></p>
</form>
