<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * @package		Base
 * @author		amostovoy
 * @copyright	Copyright (c) 2010, Qualium-Systems, Inc.
 * @version		1.26
 * @since		Version 1.0
 * @filesource
 */

/****************************************
 *			DO NOT EDIT					*
 ****************************************/

Loader::loadClass(array('Base_View', 'Base_Lang', 'Base_Paginator'));

/**
 * class Controller
 * 
 * main class for all controllers
 * containing basic methods and properties
 * 
 * @package     Base
 * @category    Controllers
 * @author      amostovoy
 * @abstract
 */
abstract class Controller extends App
{
    /**
     * key names in post of important data
     */
    const DATA = '__data';
    const FILTER = '__filter';
    const ORDER = '__order';
    const SAVE_FILTER = '__sf';
    const SAVE_ORDER = '__so';
    
    /**
     * Controller object
     * @var Controller
     */
    private static $instances = null;
    /**
	 * View class object
	 * @var View
	 */
	protected $_view = null;
	/**
	 * Request class object
	 * @var Request
	 */
	protected $_request = null;
	/**
	 * User class object
	 * @var User
	 */
	protected $_user = null;
    /**
     * current controller main model object
     * @var Model
     */
    protected $model = null;
	/**
	 * Lang class object
	 * @var Lang
	 */
	protected $_lang = null;
	/**
	 * Location for overloaded data.
	 * @var array
	 */
	protected $overloaded_data = null;
    /**
     * current item id
     * @var int
     */
    protected $id = null;
    
    public static function getInstance($c=null, $sp=true, $init_flow=true)
    {
        if(!is_null($c))
            $c .= Request::CONTROLLER_SUFFIX;
        if(!empty($c) && (!isset(self::$instances[$c]) || self::$instances[$c] === null))
        {
            Loader::loadController($c, $sp);
            $class = explode('.', $c);
            $class = array_pop( $class );
            self::$instances[$c] = new $class();
            if($init_flow)
            {
                self::$instances[$c]->_run();
            }
        }
        else
        {
            if(is_array(self::$instances))
            {
                $c = key( self::$instances );
            }
            else
            {
                throw new InternalException('no controllers objects');
            }
        }
        return self::$instances[$c];
    }

	/**
	 * save config, request, user objects and
	 * create lang, paging and view objects. Also save base_url, base_dir, controller and action names
	 * to smarty var
	 */
	private function __construct()
	{
		$this->_request = App::request();
		$this->_user = App::user();

		$this->base_dir = $this->_request->getBaseDir();
		$this->protected_dir = $this->base_dir.'protected'.DS;

        Log::$dir = $this->base_dir;
        
		$this->_lang = App::lang();
		$this->_view = App::view();
        
        $this->domain_url = $this->_request->getDomainUrl();
        $this->base_url = $this->_request->getBaseUrl();
        $this->base_address = $this->base_url.'/'.$this->_request->getControllerName();
        
        if($this->_request->isAjax())
        {   // set ajax class
            Loader::loadClass('Common_Ajax');
            $this->ajax = new Ajax();
        }
	}
    
    /**
     * Handle situation than call nonexistent method.
     * If call method like presaved constant SAVE_FILTER
     * or SAVE_ORDER take rule to call default action
     * @param string $name method name
     * @param mixed $arguments 
     */
    public function __call($name, $arguments)
    {
        if(self::SAVE_FILTER == $this->_request->getActionName()
            || self::SAVE_ORDER == $this->_request->getActionName()
        ) {
            $action = $this->_request->setActionName(Request::DEFAULT_ACTION);
            $this->_view->setTemplate($action);
            $this->{$action.Request::ACTION_SUFFIX}();
        }
    }
    
    /**
     * Run controller.
     * Create class options, save given data, filter and order,
     * save variables to js.
     * Init current active user, run common init method and inits method.
     */
    public function _run()
    {
        $this->id = $this->_request->getParam(
            'id',
            $this->_request->getPost(
                'id',
                $this->_request->getParam(
                    Defines::CONTENT_ID,
                    $this->_request->getPost(
                        Defines::CONTENT_ID,
                        null,
                        Request::FILTER_INT),
                    Request::FILTER_INT),
                Request::FILTER_INT),
            Request::FILTER_INT);
        $this->_view->id = $this->id;
        
        $this->data(
                $this->_request->getPost(
                        self::DATA,
                        $this->_request->getParam(
                                self::DATA,
                                null,
                                Request::FILTER_ARRAY),
                        Request::FILTER_ARRAY));
        $this->filter(
                $this->_request->getPost(
                        self::FILTER,
                        $this->_request->getParam(
                                self::FILTER,
                                null,
                                Request::FILTER_ARRAY),
                        Request::FILTER_ARRAY));
        $this->order(
                $this->_request->getPost(
                        self::ORDER, 
                        $this->_request->getParam(
                                self::ORDER, 
                                null, 
                                Request::FILTER_STRING),
                        Request::FILTER_STRING));
        
        $this->setCurrentActiveUser();
//        $this->checkAccessRules();
        
        $this->createControllerModel( $this->_request->getControllerName() );
        
        $this->formSiteTitle(Config::SITE_NAME);
        
        $this->_commonInit();
		$this->_init();
        
        $this->setDefaultBreadCrumb();
        $this->setDefaultSiteTitle();
    }
    
	/**
	 * setter
	 *
	 * @param string $param key of array overloaded_data
	 * @param mixed $value
	 */
	public final function __set($param, $value) { $this->overloaded_data[$param] = $value; }

	/**
	 * getter
	 *
	 * @param string $param key of array overloaded_data
	 * @return mixed value, if not set null
	 */
	public final function &__get($param)
	{
		if ( !$this->__isset($param) )
        {
			trigger_error('Controller variable \''.$param.'\' not exist in overloaded_data');
            $r=null;
            return $r;
        }
		return $this->overloaded_data[$param];
	}

	/**
	 * return true if key is set, false otherwise
	 *
	 * @param string $index key of array overloaded_data
	 * @return boolean
	 */
	public final function __isset($index)
    {
        return array_key_exists($index, $this->overloaded_data);
    }

    /**
	 * function destroy an element of the overloaded_data array
	 * @param string $name
	 */
	public final function __unset($name) { unset($this->overloaded_data[$name]); }
        
    /**
     * method to call in the end of controller work
     */
    public function _end()
    {
        if($this->_request->isAjax())
        {   // send ajax content and exit from script
//            if($this->_request->getParam('page', false, Request::FILTER_STRING))
//            {
                $tpl = $this->fetchElementTemplate();
//            }
//            else
//            {
//                $tpl = $this->fetchTemplate();
//            }
            $this->ajax->send(Ajax::RESULT_HTML, $tpl);
        }
        $this->showTemplate();
    }

	/**
	 * redirect to url. if no given url redirecting to baseUrl
	 * @param string $url [optional] url to redirect
	 * @param string $type [optional] type of redirection.
	 *			Allowed string 'js' - javascript redirection
	 *					string 'top' - javascript redirection on main parent document
	 *					null by default - standart php header
	 */
	public final function redirect($url='', $type=null)
	{
		if ( !empty($url) && strpos($url, 'http://') === false && strpos($url, 'https://') === false ){
			$url[0] == '/' or $url='/'.$url;
			$url = $this->base_url.$url;
		}
		elseif(empty($url))
			$url = $this->base_url;

		switch($type)
		{
			case 'top':
				echo "<script type='text/javascript'>top.location.href = '$url';</script>";
			break;
			case 'js':
				echo "<script type='text/javascript'>document.location.href = '$url';</script>";
			break;
		    default:
				header('Location: '.$url);
		}
		exit();
	}

	/**
	 * logout user from site and redirect to baseUrl
	 */
	protected function logout()
    {
		$this->_user->setLogOut();
		$this->redirect();
	}

	/**
	 * return array with encoded password, password and salt string
	 *
	 * @param string $pass password string
	 * @param string $salt secrete or random string
	 * @return mixed return array than generate new encoded password
	 *				 or encoded string on check password action
	 */
	public final function passwordHash($pass = null, $salt = null)
	{
        if(is_null($pass))
        {
            $pass = md5(uniqid(rand(), true));
        }
		if(is_null($salt))
        {
			$salt = substr(uniqid(rand()), -10);
		}
        return array('hash'=>md5($pass.$salt), 'pass'=>$pass, 'salt'=>$salt );
	}

    /**
     * formate site title
     * @param string $title new part of title
     * @param bool $is_revers revers title formation. %site_title% will be in the end of string
     */
    protected function formSiteTitle($title, $is_revers=false)
    {
        if(!isset($this->_view->site_title))
        {
            $this->_view->site_title = '';
        }
        if(!empty($title))
        {
            if($is_revers)
            {
                $this->_view->site_title = $title . (!empty($this->_view->site_title)?Defines::TITLE_DELIMITER:'') . $this->_view->site_title;
            }
            else
            {
                $this->_view->site_title .= (!empty($this->_view->site_title)?Defines::TITLE_DELIMITER:'') . $title;
            }
        }
    }

    /**
	 * common logout action for all controllers.
	 * Could be overriden
	 */
	public function logoutAction()
	{
		$this->logout();
	}

	/**
	 * send alert to user by sending email
	 * @param int $to_user user id
	 * @param string $subj subject of email
	 * @param string $text email message body
	 * @param bool $forced_send if set it to true function will not check permission from user to send email
	 * @return bool return true on success sending email, false on errors
	 */
	protected function sendAlert($to_user, $subj, $text, $forced_send=false)
	{
		$permission = false;
		if ($forced_send)
			$permission = true;
		else
		{
			$this->loadModel('alert');
			if ( $this->mod_alert->getAlertPerm($to_user, 'email') === '1')
				$permission = true;
		}
		if ($permission)
		{
			$this->loadModel('user');
			return $this->sendMail($this->mod_user->getEmail($to_user), $subj, $text);
		}
		return false;
    }

	/**
	 * send mail
	 * @param string $email email address
	 * @param string $subj email subject
	 * @param string $mess email body
	 * @param array	 $info array with header information such as from name, from email, content-type etc.
	 * @return bool return true on success, false on some errors
	 */
	public function sendMail($email, $subj, $mess, $info=null)
	{
		if (empty($info['from_name'])) $info['from_name'] = 'Dev Site ['.Config::SITE_NAME.']';
		if (empty($info['from_email'])) $info['from_email'] = 'no-reply@'.$_SERVER['HTTP_HOST'];
		if (empty($info['content_type'])) $info['content_type'] = 'text/html';
		if (empty($info['charset'])) $info['charset'] = 'iso-8859-1';

		$headers = 'From: '.$info['from_name'].' <'.$info['from_email'].'>' . "\r\n" .
				   'Content-type: '.$info['content_type'].'; charset='.$info['charset'] . "\r\n" .
				   'X-Mailer: PHP/' . phpversion();

		return mail($email, $subj, $mess, $headers);
	}
    
	/**
	 * initialize some functions in specific child controller
	 * @abstract
	 */
	abstract protected function _init();

	/**
	 * initialize some functions in common controller
	 * @abstract
	 */
	abstract protected function _commonInit();

	public function __destruct(){}
    
    /**
     * create default breadcrumb for current controller.
     * If not reinitialize will create nothing
     */
    protected function setDefaultBreadCrumb()
    {
    }
    /**
     * Create default site title for current controller.
     * If not reinitialize will create title from
     * lang file of current controller section under
     * 'site_title' variable
     */
    protected function setDefaultSiteTitle()
    {
        $this->formSiteTitle($this->_lang->{$this->_request->getControllerName()}()->site_title);
    }

    /**
     * Add new step into breadcrumb chain
     * @param array $breadcrumb can be following parameters
     *              - title
     *              - href
     */
    protected final function addBreadCrumb(array $breadcrumb)
    {
        if(!isset($this->_view->breadcrumb))
        {
            $this->_view->breadcrumb = array();
        }
        $this->_view->breadcrumb[] = $breadcrumb;
    }
}
/* End of file Controller.php */
/* Location: ./class/Base/Controller/Controller.php */
?>