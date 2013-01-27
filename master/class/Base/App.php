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
 * class App
 * static class
 * retrieve acces to all application entity: controllers, models etc...
 *
 * @package		Base
 * @author		amostovoy
 */
class App
{
    /**
     * Identificator of some entity
     * @var int|string
     */
    protected $id = null;
    /**
     * Data array came from post in __data key
     * @var array
     */
    protected $data = null;
    /**
     * Data array came from post in __filter key
     * @var array
     */
    protected $filter = null;
    /**
     * Data array came from post in __order key
     * @var array
     */
    protected $order = null;
    /**
     * Data array with entities for list page
     * @var array
     */
    protected $list = null;
    /**
     * Data for one entity
     * @var array
     */
    protected $detail = null;
    
    public final function data($val=null)
    {
        if(is_null($val))
            return $this->data;
        else
            $this->data = $val;
    }
    
    public final function filter($val=null)
    {
        if(is_null($val))
            return $this->filter;
        else
            $this->filter = $val;
    }
    
    public final function order($val=null)
    {
        if(is_null($val))
            return $this->order;
        else
            $this->order = $val;
    }
    /**
     * Return manager object
     * @return Manager
     */
    public static function manager()
    {
        return Manager::getInstance();
    }
    /**
     * Return request object
     * @return Request
     */
    public static function request()
    {
        return Request::getInstance();
    }
    /**
     * Return user object
     * @return User
     */
    public static function user()
    {
        return User::getInstance();
    }

    /**
     * Return Paypal object
     * @return Paypal
     */
    public static function paypal()
    {
        return Paypal::getInstance();
    }

    /**
     * Return controller object
     * @param string $c - (d:null) controller name.
     * @param bool $sp - (d:true) flag to load controller from site part.
     * @param bool $init_flow - (d:true) flag to load controller as a usual way if true will load _run method
     * if false - not
     * @return Controller if empty controller name parametr return first loaded 
     * controller object
     */
    public static function controller($c=null,$sp=true,$init_flow=true)
    {
        return Controller::getInstance($c,$sp,$init_flow);
    }
    /**
     * Return model object
     * @param string $m - model name
     * @param bool $sp - (d:true) flag to use site part dir
     * @return Model
     */
    public static function model($m,$sp=true)
    {
        return BaseDb::getInstance($m,$sp);
    }
    /**
     * Return view object
     * @return View
     */
    public static function view()
    {
        return View::getInstance();
    }
    /**
     * Return lang object
     * @return Lang
     */
    public static function lang()
    {
        return Lang::getInstance();
    }
    /**
     * Return widget object
     * @return Widget
     */
    public static function widget()
    {
        return Widget::getInstance();
    }
    /**
     * Return permission object
     * @param string $e - (d:null) entity name.
     * @return Permission
     */
    public static function perm($e=null)
    {
        return Permission::getInstance($e);
    }
    
    /**
     * Returns permissions container
     * 
     * @return PermissionsContainer
     */
    public static function perm_container($section = null)
    {
        return PermissionsContainer::getInstance($section);
    }
    
    /**
     * Returns setting manager
     * @return Setting 
     */
    public static function setting($section = 'db')
    {
        return Setting::getInstance($section);
    }
    /**
     * Returns settings manager
     */
    public static function settings($section = null)
    {
        return Settings::getInstance($section);
    }
    
    /**
     * Return session object
     * @param string $section
     * @return Session
     */
    public static function session($section=null)
    {
        return Session::getInstance($section);
    }
    
    /**
     * Return validation object
     * @return Validation
     */
    public static function validation()
    {
        return Validation::getInstance();
    }
}

/**
 * Show debug information
 * @param mixed $content variable to debug
 * @param mixed $title title
 * @return void 
 */
function show($content, $title = '')
{
    $trace = debug_backtrace();
    
    if (App::request()->isAjax())
    {
        echo $title ? $title . ': ': '';
        empty($content) ? var_dump($content) : print_r($content); 
        echo "\n"; echo '--------   location:' . $trace[0]['file'] . ':' . $trace[0]['line'] . '   --------'; echo "\n"; return;
    }
    
    echo '<div style="background:#fff">';
    if ($title)
    {
        echo '<h3>';print_r($title); echo '</h3><br>';
    }
    
    echo '<pre>'; 
    empty($content) ? var_dump($content) : print_r($content);
    echo '</pre>';
    echo '<div style="font-size:8pt"><b>location:</b> <i>' . $trace[0]['file'] . ':' . $trace[0]['line'] . '</i></div>';
    echo '</div>';
}
/* End of file App.php */
/* Location: ./class/Base/App.php */
?>