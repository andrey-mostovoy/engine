<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * Model Dashboard
 * @author amostovoy 
 * @filesource
 */
Loader::loadModel('Content', false);
/**
 * class modelDashboard
 *
 * @package		Project
 * @subpackage	Models
 * @category	Database
 * @author		amostovoy 
 */
class modelDashboard extends modelContent
{
    protected function setValidationRules()
    {
        return array();
    }
}

/* End of file Dashboard.php */
/* Location: ./models/admin/Dashboard.php */
?>