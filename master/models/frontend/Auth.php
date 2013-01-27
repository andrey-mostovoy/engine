<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * Model Auth
 * @author amostovoy
 * @filesource
 */

Loader::loadModel('AuthCommon', false);

/**
 * class modelAuth.
 * Authentication proccess for frontend module.
 * 
 * @package		Project
 * @subpackage	Models
 * @category	Database
 * @author		amostovoy
 */
class modelAuth extends modelAuthCommon
{
    protected function setValidationRules()
    {
        return array(
            'common' => array(
            ),
            'login' => array(
                'required' => 'email, password',
                'email'    => 'email'
            ),
            'forgot' => array(
                'required' => 'email',
                'email'    => 'email'
            )
        );
    }
    
    protected function getLoginWhere($data)
    {
        if (is_array($data))
        {   // if login action is performing by user
            return array(
                'email' => $data['email'],
                'password' => $data['password'],
                'role !=' => User::ADMIN,
            );
        }
        else
        {   // if autologin is performing using cookies
            return array('id'=>$data);
        }
    }
    
    /**
     * Set user as logedin. If check remember me - set user cookie
     * @param array $user current user info
     * @return boolean 
     */
    protected function setLogIn($user)
    {
        //1 => active 2 => blocked 3 => cancel 4 => incomplete
        if($user['status'] == '2')
        {
            $this->addError('login', 'status_blocked', null, 'model');
        }
        elseif($user['status'] == '3')
        {
            $_SESSION['cancel_acc'] = $user;
            App::view()->addJsVarToSession(array(
                'login_status_cancel' => App::lang()->auth()->error()->login_status_cancel_confirm,
            ));
            App::view()->addErrorToSession(App::lang()->auth()->error()->login_status_cancel_message, 'lsonc');
        }
        elseif($user['status'] == '4')
        {
            $this->addError('login', 'status_incomplete', null, 'model');
        }
        else
        {
            App::user()->setLogIn($user);
            $data = App::controller()->data();
            if(isset($data['remember']) && $data['remember'] == '1')
            {
                App::user()->setToCookie();
            }
            return true;
        }
        return false;
    }
    /**
     * restore canceed user
     * @return bool
     */
    public function restore()
    {
        if(isset($_SESSION['cancel_acc']))
        {
            //1 => active 2 => blocked 3 => cancel 4 => incomplete
            if($this->update(array('status'=>1), array('id'=>$_SESSION['cancel_acc']['id'])))
            {
                $_SESSION['cancel_acc']['status'] = '1';
                return $this->setLogIn($_SESSION['cancel_acc']);
            }
        }
        return false;
    }
}

/* End of file Auth.php */
/* Location: ./models/frontend/Auth.php */
?>