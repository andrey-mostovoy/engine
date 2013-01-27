<ul class="landmenu">
    <li><a href="{$url.domain}" title="Home" class="current">Home</a></li>
    <li><a href="{$url.base}/page/about-us" title="About Us">About Us</a></li>
    <li><a href="#" title="Contact Us">Contact Us</a></li>
    <li>
        <a href="#" title="Login" id="user_login_popup">Login</a>
        <div class="login-popup hidden">
            <div class="lp-content">
                <div class="lpc-form">
                    <div class="lpc-header">
                        <a href="#" title="Close" class="button btn-close">Close</a>
                        <span>Login</span> 
                    </div>                            
                    <form action="">
                        <label>Email:</label>
                        <input type="text" />
                        <label>Password:</label>
                        <input type="password" />
                        <div class="lpf-user-tools">
                            <label><input type="checkbox" />Remember me</label> | <a href="#" title="Forgot Password">Forgot Password</a>
                        </div>
                        <a href="#" title="Login" class="btn-submit btn-login">Login</a>
                    </form>         
                </div>
                <div class="lpc-form hidden">
                    <div class="lpc-header">
                        <a href="#" title="Close" class="button btn-close">Close</a>
                        <span>Forgot Password</span> 
                    </div>                            
                    <form action="">
                        <label>Email:</label>
                        <input type="text" />
                        <a href="#" title="Send" class="btn-submit btn-send">Send</a>
                    </form>         
                </div>                                     
            </div>
        </div>                    
    </li>
</ul>