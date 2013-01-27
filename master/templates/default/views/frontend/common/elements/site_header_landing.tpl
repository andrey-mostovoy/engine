<div id="header">
    <h1>Cum sociis natoque penatibus et magnis dis parturient montes</h1>
    <div class="logo">
        {include_element file="site_header_logo"}
        <span>Welcomes You!</span>             
    </div>
    <ul class="landmenu">
        <li><a href="{$url.domain}" title="Home" class="current">Home</a></li>
        <li><a href="{$url.base}/page/about-us" title="About Us">About Us</a></li>
        <li><a href="#" title="Contact Us">Contact Us</a></li>
        {if !App::user()->isAuth()}
        <li>
            <a href="#" title="Login" id="user_login_popup">Login</a>
            <div class="login-popup {if !isset($messages.error)}hidden{/if}">
                <div class="lp-content">
                    <div class="lpc-form login_f">
                        <div class="lpc-header">
                            <a href="#" title="Close" class="button btn-close">Close</a>
                            <span>Login</span>
                        </div>
                        {include_element file="messages_summary"}
                        <form action="/auth/login" class="js_v" method="post">
                            <input type="hidden" name="validate_type" value="login" />
                            <label>Email:</label>
                            <input type="text" name="__data[email]" value="" />
                            <label>Password:</label>
                            <input type="password" name="__data[password]" value="" />
                            <div class="lpf-user-tools">
                                <label>
                                    <input type="checkbox" name="__data[remember]" value="1" />Remember me
                                </label> | 
                                <a id="forgot_pass" href="#" title="Forgot Password">Forgot Password</a>
                            </div>
                            <a href="#" title="Login" class="js_btn_submit btn-submit btn-login">Login</a>
                        </form>         
                    </div>
                    <div class="lpc-form forgot_f hidden">
                        <div class="lpc-header">
                            <a href="#" title="Close" class="button btn-close">Close</a>
                            <span>Forgot Password</span> 
                        </div>
                        {include_element file="messages_summary"}
                        <form action="/auth/forgot" class="js_v" method="post">
                            <input type="hidden" name="validate_type" value="forgot" />
                            <label>Email:</label>
                            <input type="text" name="__data[email]" value="" />
                            <a href="#" title="Send" class="js_btn_submit btn-submit btn-send">Send</a>
                        </form>         
                    </div>
                </div>
            </div>
        </li>
        {/if}
    </ul>
    <div class="slideshow">
        <div class="scroller"> 
            <ul>                  
                <li>
                    <div class="slide-btns">
                        <a href="#" title="Take a Tour">Take a Tour</a>
                        <a href="#" title="Buy Now">Buy Now</a>
                    </div>                          
                    <img src="{$url.image}/temp/slide-1.png" alt="Focusing on you the jobseeker!" title="Focusing on you the jobseeker!" height="265" width="880" />                                                      
                </li>  
                <li>
                    <div class="slide-btns">
                        <a href="#" title="Take a Tour">Take a Tour</a>
                        <a href="#" title="Buy Now">Buy Now</a>
                    </div>
                    <img src="{$url.image}/temp/slide-2.png" alt="Focusing on you the jobseeker!" title="Focusing on you the jobseeker!" height="265" width="880" />                                          
                </li>                         
                <li>
                    <div class="slide-btns">
                        <a href="#" title="Take a Tour">Take a Tour</a>
                        <a href="#" title="Buy Now">Buy Now</a>
                    </div>
                    <img src="{$url.image}/temp/slide-3.png" alt="Focusing on you the jobseeker!" title="Focusing on you the jobseeker!" height="265" width="880" />                                         
                </li> 
                <li>
                    <div class="slide-btns">
                        <a href="#" title="Take a Tour">Take a Tour</a>
                        <a href="#" title="Buy Now">Buy Now</a>
                    </div>
                    <img src="{$url.image}/temp/slide-4.png" alt="Focusing on you the jobseeker!" title="Focusing on you the jobseeker!" height="265" width="880" />                                         
                </li>                         
                <li>
                    <div class="slide-btns">
                        <a href="#" title="Take a Tour">Take a Tour</a>
                        <a href="#" title="Buy Now">Buy Now</a>
                    </div>
                    <img src="{$url.image}/temp/slide-5.png" alt="Focusing on you the jobseeker!" title="Focusing on you the jobseeker!" height="265" width="880" />                                          
                </li>
                <li>
                    <div class="slide-btns">
                        <a href="#" title="Take a Tour">Take a Tour</a>
                        <a href="#" title="Buy Now">Buy Now</a>
                    </div>
                    <img src="{$url.image}/temp/slide-6.png" alt="Focusing on you the jobseeker!" title="Focusing on you the jobseeker!" height="265" width="880" />                                          
                </li>                                                                                                                                                                                            
            </ul>           
        </div> 
        <div class="navi"></div>                 
    </div>
</div>