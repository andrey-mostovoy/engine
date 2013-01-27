<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * @package		Base
 * @author		amostovoy
 * @copyright	Copyright (c) 2011, Qualium-Systems, Inc.
 * @version		1.0
 * @since		Version 1.4
 * @filesource
 */

/****************************************
 *			DO NOT EDIT					*
 ****************************************/

/**
 * class Session
 * provide methods to work with session
 * @package		Base
 * @author		amostovoy
 */
class Session
{
    /**
	 * session name in url containing session id to restore
	 */
	const SESSION_NAME = 'sess';
    
    private static $instance = null;
    
    private function __construct()
    {
        $this->setParam();
        
        $this->start();
    }
    
    public function __destruct() {
//        $_SESSION = $this->mergeArrays($_SESSION, $this->getAll());
    }

    /**
     * Returns instance
     * @return Session
     */
    public static function getInstance($section = null)
    {
        self::$instance === null and self::$instance = new self();
        return self::$instance;
    }
    /**
     * Set session params - lifetime, path, etc... 
     */
    private function setParam()
    {
        if ( strpos(App::request()->getRequestUri(), self::SESSION_NAME.'=') !== false )
		{
			$pattern = "/(?:".self::SESSION_NAME."=)(.*)$/i";
			preg_match($pattern, App::request()->getRequestUri(), $sessid) and session_id($sessid[1]);
//			$this->_request_uri = preg_replace($pattern, '', $this->_request_uri);
		}
        elseif(isset($_POST[self::SESSION_NAME]) && !empty($_POST[self::SESSION_NAME]))
        {
            $this->setId($_POST[self::SESSION_NAME]);
        }
        
        // set the PHP session id (PHPSESSID) cookie to a custom value
//        session_set_cookie_params(Defines::$session_alive_time, Defines::$cookie_path);
        // here some problem then top line was active: session over no metter how 
        // many request you do. browser just stop send cookie and server start new session.
        // {@link http://ua.php.net/manual/ru/function.session-set-cookie-params.php#100672}
        session_set_cookie_params(0);
        // set the garbage collector - who will clean the session files -
        // to our custom timeout
        // timeout value for the garbage collector
        //   we add 300 seconds, just in case the user's computer clock
        //   was synchronized meanwhile; 600 secs (10 minutes) should be
        //   enough - just to ensure there is session data until the
        //   cookie expires
        ini_set('session.gc_maxlifetime', (Defines::$session_alive_time+600));
        // we need a distinct directory for the session files,
        //   otherwise another garbage collector with a lower gc_maxlifetime
        //   will clean our files aswell - but in an own directory, we only
        //   clean sessions with our "own" garbage collector (which has a
        //   custom timeout/maxlifetime set each time one of our scripts is
        //   executed)
        $sessdir = session_save_path().DS.str_replace(' ','',Config::SITE_NAME).'_sessions';
//        $sessdir = ini_get('session.save_path').DS.str_replace(' ','',Config::SITE_NAME).'_sessions';
        if (!is_dir($sessdir)) { mkdir($sessdir, 0777); }
        session_save_path($sessdir);
//        ini_set('session.save_path', $sessdir);
    }
    /**
     * set session id
     * @param int $id 
     */
    public final function setId($id)
    {
        session_id($id);
    }
    /**
     * start session 
     */
    public final function start()
    {
        session_start();
    }
}

/* End of file Session.php */
/* Location: ./class/Base/Session.php */
?>