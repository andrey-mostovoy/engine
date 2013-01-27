<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * Model
 * @author amostovoy
 * @filesource
 */

Loader::loadModel('Content', false);
        
/**
 * class modelAuthCommon.
 * Common model for authentication module.
 * Contains main common functinality to handle authenticate proccess.
 *
 * @package		Project
 * @subpackage	Models
 * @category	Database
 * @author		amostovoy
 */
abstract class modelAuthCommon extends modelContent
{
    /**
     * set database table to work with
     */
    protected function setName()
    {
        return self::$tbl_user;
    }
    
    /**
     * function performs login operation for registered users
     * @param array $data post data - login information
     * @return boolean 
     */
    public function login($data)
    {
        $hash = App::controller()->passwordHash(
            $data['password'],
            $this->getSalt($data)
        );
        $data['password'] = $hash['hash'];
        
        $user = $this->getRow( $this->getLoginWhere($data) );
        if(!empty($user))
        {
            return $this->setLogIn($user);
        }
        else
        {
            $this->addError('login', 'incorrect', null, 'model');
            return false;
        }
    }
    /**
     * Get array of where conditions for retrieve login user
     * @param mixed $data
     * @return array 
     */
    protected function getLoginWhere($data)
    {
        if (is_array($data))
        {   // if login action is performing by user
            return array('email' => $data['email'], 'password' => $data['password']);
        }
        else
        {   // if autologin is performing using cookies
            return array('id'=>$data);
        }
    }
    /**
     * Get user salt
     * @param string $data search string
     * @param string $field (optional d:'email') field name by search
     * @return string
     */
    public function getSalt($data, $field='email')
    {
        return $this->getOne('salt', array($field=>$data['email']));
    }
    /**
     * Set user as logedin
     * @param array $user current user info
     * @return boolean 
     */
    protected function setLogIn($user)
    {
        App::user()->setLogIn($user);
        return true;
    }
    /**
     * function perform forgot password operation for registered user
     * @param array $data post data from form forgot password information
     * @return boolean 
     */
    public function forgot($data)
    {
        $email = $this->getOne('email', array('email'=>$data['email']));
        
        if(empty($email))
        {
            $this->addError('forgot', 'incorrect', null, 'model');
            return false;
        }
        
        $hash = App::controller()->passwordHash();
        $data['password'] = $hash['hash'];
        $data['salt'] = $hash['salt'];
        
        if(App::controller()->sendMail($email, 'test subj forgot', 'test forgot. New pass: '.$hash['pass']))
        {
            if($this->update($data, array('email'=>$email)))
            {
                return true;
            }
            $this->addError('forgot', 'save_error', null, 'model');
            return false;
        }
        else
        {
            $this->addError('forgot', 'email_send_error', null, 'model');
        }
        
        return false;
    }
}

/* End of file AuthCommon.php */
/* Location: ./models/AuthCommon.php */
?>
