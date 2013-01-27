<?php if (!defined('APP')) exit('No direct script access allowed');
/**
 * Auth Controller
 * @author amostovoy
 * @filesource
 */

Loader::loadController('AuthCommonController', false);

/**
 * class AuthController.
 * Authentication proccess for frontend
 *
 * @package		Project
 * @subpackage	Controllers
 * @author		amostovoy
 */
class AuthController extends AuthCommonController
{
    protected function checkLogginStatus()
    {
        return $this->_user->isAuth();
    }
    
    protected function successLogin()
    {
        $this->redirect($this->base_url.($this->next_url?'/'.$this->next_url:'/page/show/id/3'));
    }
    
    protected function errorLogin()
    {
        $this->addModelErrorsToSession();
        $this->redirect($_SERVER['HTTP_REFFERER']);
    }
    
    protected function errorForgot()
    {
        $this->addModelErrorsToSession();
        $this->redirect($_SERVER['HTTP_REFFERER']);
    }
    
    public function restoreAction()
    {
        if($this->model->restore())
        {
            $this->redirect($this->base_url);
        }
        $this->_view->addErrorToSession($this->_lang->auth()->error()->restore_canceled_fail);
        $this->redirect($this->base_url);
    }
}

/* End of file Auth.php */
/* Location: ./controllers/frontend/Auth.php */
?>