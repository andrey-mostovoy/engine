<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * Model Auth
 * @author amostovoy
 * @filesource
 */

Loader::loadModel('AuthCommon', false);

/**
 * class modelAuth.
 * Authentication proccess for admin module
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
                'required' => 'login, password',
            ),
        );
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
            return array(
                'email' => $data['login'],
                'password' => $data['password'],
                'role' => User::ADMIN
            );
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
        return $this->getOne('salt', array($field=>$data['login']));
    }
}

/* End of file Auth.php */
/* Location: ./models/frontend/Auth.php */
?>