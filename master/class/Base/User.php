<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * @package		Base
 * @author		amostovoy
 * @copyright	Copyright (c) 2010, Qualium-Systems, Inc.
 * @version		1.3
 * @since		Version 1.0
 * @filesource
 */

/****************************************
 *			DO NOT EDIT					*
 ****************************************/

/**
 * class User
 * containing base methods for work with user object
 * including methods for site administration
 * and set or get current user language
 *
 * @package		Base
 * @category	User
 * @author		amostovoy
 * @abstract
 */
abstract class BaseUser
{
    /**
     * User status - user not authorized
     */ 
    const GUEST = 0;
    /**
     * User status - user has profile but not authorized, i.e. pending registry
     */
    const USER = 1;
    /**
     * User status - user has member profile
     */
    const MEMBER = 2;
    /**
     * User status - user is content owner
     */
    const OWNER = 6;
    /**
     * User status - admin user full permissions
     */
    const ADMIN  = 9;
	/**
	 * array key name in $_SESSION array
	 */
	const SESSION_USER_NAME = 'user';
    /**
     * Current user status(role).
     * By default {@link self::GUEST}
     * @var int
     */
    private $_current = self::GUEST;
	/**
	 * Location for overloaded data.
	 * @var array
	 */
	private $overloaded_data = array();
	/**
	 * stores string identification of user language
	 * @var array
	 */
	private $_lang = null;
	/**
	 * instance of class
	 * @var BaseUser
	 * @static
	 */
	private static $_instance = null;

    /**
     * class constructor
     */
	private function __construct(){}

	/**
	 * get class object. Singleton
	 * @return BaseUser object
	 */
	public static function getInstance()
	{
		if (self::$_instance === null)
		{
			if ( isset($_SESSION[self::SESSION_USER_NAME]) && $_SESSION[self::SESSION_USER_NAME] instanceof self)
				self::$_instance = clone $_SESSION[self::SESSION_USER_NAME];
			elseif ( isset($_COOKIE[self::SESSION_USER_NAME]) )
			{
				self::$_instance = self::getFromCookie();
			}
			else
				self::$_instance = new User;
		}
		return self::$_instance;
	}

	/**
	 * function creates an element of the array and stores the value in it
	 * @param string $name
	 * @param mixed $value
	 */
	public final function __set($name,  $value)
    {
        $this->overloaded_data[$this->_current][$name] = $value;
    }

	/**
	 * function destroy an element of the overloaded_data array
	 * @param string $name
	 */
	public final function __unset($name)
    {
        unset($this->overloaded_data[$this->_current][$name]);
    }

	/**
	 * function return value of received element name of the array
	 * @param string $name
	 * @return mixed
	 */
	public final function &__get($name)
    {
		if (!isset($this->overloaded_data[$this->_current][$name]))
        {
			trigger_error(__CLASS__.' variable \''.$name.'\' not exist in '.$this->_current);
            $r=null;
            return $r;
        }
		return $this->overloaded_data[$this->_current][$name];
	}

	/**
	 * check for variable existing
	 * @param string $name
	 * @return boolean return true if var is exist else false
	 */
	public final function __isset( $name )
    {
        return isset($this->overloaded_data[$this->_current][$name]);
    }
    
    public final function __call($name, $arguments)
    {
        if(substr($name, 0, 2) == 'is' && defined($const='User::'.strtoupper(substr($name, 2))))
        {
            return $this->checkIsLogin(constant($const));
        }
    }

    /**
	 * encode and set user object to cookie
	 */
	public final function setToCookie()
	{
		setrawcookie(
                self::SESSION_USER_NAME,
                base64_encode(serialize($this)),
                time() + Defines::$cookie_alive_time,
                '/');
	}

	/**
	 * decode self object from cookie id excist
	 * @return self
	 */
	public static function getFromCookie()
	{
		if ( isset($_COOKIE[self::SESSION_USER_NAME]) )
		{
			$cookie = unserialize(base64_decode($_COOKIE[self::SESSION_USER_NAME]));
			if ( $cookie instanceof self)
				return $cookie;
		}
		return false;
	}

	/**
	 * unset user object from cookie
	 */
	private function unsetFromCookie()
	{
		setrawcookie(self::SESSION_USER_NAME, '', time() - 42000, '/');
	}

	/**
	 * save user object to session
	 */
	public final function setToSession()
	{
//        App::session(self::SESSION_USER_NAME)->add(clone $this);
		$_SESSION[self::SESSION_USER_NAME] = clone $this;
	}
    
    /**
	 * Set user language identity
	 * @param string $lang [optional] language identity. Default takes from DEFAULT_LANG constant
	 */
	public final function setLang( $lang=Lang::DEFAULT_LANG )
    {
        $this->_lang[$this->_current] = $lang;
    }
    
	/**
	 * Return user language identity
	 * @return string 
	 */
	public final function getLang()
    {
        return $this->_lang[$this->_current];
    }

    /**
     * Set current loged in user status
     * @param int $current 
     */
    public final function setCurrent($current)
    {
        if(!is_array($current))
        {
            $this->_current = $current;
        }
        elseif(isset($current['role']))
        {
            $this->_current = $current['role'];
        }
    }
    
    /**
	 * function return true, if someone loged in
	 * @return bool
	 */
	public final function isLogin()
    {
        return (bool)$this->_current;
    }
    
	/**
	 * function set user as loged in and set his data
	 * @param array $data user data
	 */
	public final function setLogIn($data)
	{
        $this->setCurrent($data);
        $this->setUserInfo($data);
	}

	/**
	 * function set user as loged out and destroy current session
	 */
	public final function setLogOut()
	{
        unset($this->overloaded_data[$this->_current]);
        unset($this->_lang[$this->_current]);
		
		unset($_SESSION[self::SESSION_USER_NAME]);
//		if (isset($_COOKIE[session_name()])) {
//			setcookie(session_name(), '', time()-42000, '/');
//		}
		$this->unsetFromCookie();
//		session_destroy();
        
        $this->setCurrent(self::GUEST);
	}

	/**
	 * save user info into overloaded_data array.
     * Exclude keys by defaul: password, salt
	 * @param array $info user data array
     * @param array $exclude exclude keys. by defaul: password, salt
	 */
	public final function setUserInfo($info, $exclude=null)
    {
        if(!empty($info) && is_array($info))
        {
            $exclude = array('password','salt') + (array)$exclude;
            foreach($info as $k => $v)
            {
                if (!in_array($k, $exclude))
                {
                    $this->$k = $v;
                }
            }
        }
	}

	/**
	 * function return all stored user variable
	 * @return array
	 */
	public final function getUserInfo()
    {
        if(isset($this->overloaded_data[$this->_current]))
            return $this->overloaded_data[$this->_current];
        else
            return null;
    }

    /**
     * Check for loged in user by his role
     * @param int $role
     * @return bool
     */
    protected final function checkIsLogin($role)
    {
        if($role === self::GUEST && $this->_current == self::GUEST && empty($this->overloaded_data))
        {
            return true;
        }
        elseif(isset($this->overloaded_data[$role]))
        {
            return true;
        }
        return false;
    }
    
    /**
     * Check is user has needed permission.
     * If current user role has bigger level acceess than return true.
     * @param int $needed level of permission. Use predefined const
     */ 
    public function checkPermission($needed)
    {
        return ($this->_current >= $needed || $this->isAdmin());
    }
    
	/**
	 * function return true if admin loged in
	 * @return bool
	 */
	public final function isAdmin()
    {
        return $this->checkIsLogin(self::ADMIN);
    }
    
    /**
     * Return current loged in user status (role)
     * @return int
     */
    public function getStatus()
    {
        return $this->_current;
    }
    
    /**
     * Check is current user owner
     * @param int $id of user
     * @return bool
     */ 
    public function isOwner($id)
    {
        return (isset($this->id) && $id == $this->id);
    }
    
    /**
     * Update only already existed fields
     * @param array $data
     */ 
    public function updateInfo($data)
    {
        foreach ($data as $key => $value)
        {
            if (isset($this->$key) && !empty($value))
            {
                $this->$key = $value;
            }
        }
    }
    
    /**
     * Check if user logged in, but not as ADMIN or GUEST
     * @param bool $set_auth_as_current set to true to set finded 
     * authenticated user as current
     * @return bool
     */
    public final function isAuth($set_auth_as_current=false)
    {
        $constants = new ReflectionClass($this);
        $constants = $constants->getConstants();
        unset($constants['ADMIN'],$constants['GUEST'],$constants['SESSION_USER_NAME']);
        foreach($constants as $c)
        {
            if($this->checkIsLogin($c))
            {
                if($set_auth_as_current)
                    $this->setCurrent($c);
                return true;
            }
        }
        return false;
    }
    
    /**
     * set first finded authenticated user as current
     * @return bool
     */
    public final function setCurrentAuth()
    {
        return $this->isAuth(true);
    }
}

/* End of file User.php */
/* Location: ./class/Base/User.php */
?>