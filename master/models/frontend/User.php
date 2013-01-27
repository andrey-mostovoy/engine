<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * Model
 * @author amostovoy
 * @filesource
 */

Loader::loadModel('UserCommon', false);

/**
 * class modelUser
 *
 * @package		Project
 * @subpackage	Models
 * @category	Database
 * @author		amostovoy
 */
class modelUser extends modelUserCommon
{
    protected function setValidationRules()
    {
        return array(
            'common' => array(),
        );
    }
    /**
     * function return curent user name from database
     * @return array return first and last names
     */
    public function getUserInfo()
    {
        return $this->getById(App::user()->id);
    }
}

/* End of file User.php */
/* Location: ./models/frontend/User.php */
?>
