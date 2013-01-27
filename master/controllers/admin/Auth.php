<?php if (!defined('APP')) exit('No direct script access allowed');
/**
 * Auth Controller
 * @author amostovoy
 * @filesource
 */

Loader::loadController('AuthCommonController', false);

/**
 * class AuthController.
 * Authentication proccess for admin side
 *
 * @package		Project
 * @subpackage	Controllers
 * @author		amostovoy
 */
class AuthController extends AuthCommonController
{
    // module dependences
    protected function setCurrentActiveUser()
    {
        $this->_user->isAdmin() && $this->_user->setCurrent(User::ADMIN);
    }
    protected function setDefaultBreadCrumb()
    {
        $this->addBreadCrumb(array(
            'title' => $this->_lang->auth()->header
        ));
    }
    protected function setDefaultSiteTitle()
    {
        $this->formSiteTitle($this->_lang->auth()->site_title_admin);
    }
    //----------------------------------------------------
    
    //      here auth below
    
    protected function checkLogginStatus()
    {
        return $this->_user->isAdmin();
    }
    
    protected function successLogin()
    {
        $this->redirect($this->base_url.($this->next_url?'/'.$this->next_url:'/dashboard'));
    }
    
    protected function errorLogin()
    {
        $this->_view->addErrorToSession($this->_lang->admin()->error()->error_login);
        $this->redirect($this->base_address.'/login'.($this->next_url?'/next/'.$this->next_url:''));
    }
    
    public function forgotAction()
    {
        $this->redirect('auth');
    }
}

/* End of file Auth.php */
/* Location: ./controllers/admin/Auth.php */
?>