<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * Auth Component
 * @filesource
 */

/**
 * class AuthComponent
 * Auth component. Common methods for authenticatoin proccess. Such as login,
 * forgot password
 * Child classes can redeclaire almost all functionality.
 * @package		Project
 * @subpackage	Component
 * @todo add method for send activation mail, accept activation
 */
class AuthComponent extends CommonController
{
    /**
     * Next url to go after authentication proccess
     * @var string
     */
    protected $next_url;
    /**
     * init component. store next url if exsist and check for logged in status 
     */
    protected function _init()
    {
        $this->next_url = $this->_request->getParam('next', false, Request::FILTER_STR);
        
        // if already logged in - go to success login actions
        if ($this->checkLogginStatus())
        {
            $this->successLogin();
        }
    }
    /**
     * redirect to login if not authenticated yet 
     */
    public function indexAction()
    {
        if( !$this->checkLogginStatus() )
        {
            $this->redirect($this->base_address.'/login'.($this->next_url?'/next/'.$this->next_url:''));
        }
        else
        {
            $this->successLogin();
        }
    }
    /**
     * check logged in status
     * @return bool
     */
    protected function checkLogginStatus()
    {
        return $this->_user->isLogin();
    }
    /**
     * login action proccess 
     */
    public function loginAction()
    {
        if($this->_request->isPost())
        {
            $this->doLogin();
        }
    }
    /**
     * main login functionality
     */
    protected function doLogin()
    {
        if($this->model->login($this->data()))
        {
            $this->successLogin();
        }
        else
        {
            $this->errorLogin();
        }
    }
    /**
     * functionality after successful login 
     */
    protected function successLogin()
    {
        $this->redirect($this->base_url.($this->next_url?'/'.$this->next_url:''));
    }
    /**
     * functionality on login error 
     */
    protected function errorLogin()
    {
        $this->addModelErrorsToSession();
        $this->redirect($this->base_address.'/login'.($this->next_url?'/next/'.$this->next_url:''));
    }
    /**
     * forgot password action proccess 
     */
    public function forgotAction()
    {
        if($this->_request->isPost())
        {
            $this->doForgot();
        }
    }
    /**
     * main forgot password logic 
     */
    protected function doForgot()
    {
        if($this->model->forgot($this->data()))
        {
            $this->successForgot();
        }
        else
        {
            $this->errorForgot();
        }
    }
    /**
     * functinality after successful forgot password
     */
    protected function successForgot()
    {
        $this->redirect($this->base_url);
    }
    /**
     * functionality on forgot password error 
     */
    protected function errorForgot()
    {
        $this->addModelErrorsToSession();
        $this->redirect($this->base_address.'/forgot');
    }
}
/* End of file Auth.php */
/* Location: ./components/Auth.php */
?>