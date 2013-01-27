<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * Model
 * @author amostovoy
 * @filesource
 */

Loader::loadModel('Content', false);

/**
 * class modelMarital
 *
 * @package		Project
 * @subpackage	Models
 * @category	Database
 * @author		amostovoy
 */
class modelMarital extends modelContent
{
    protected function setName()
    {
        return self::$tbl_marital;
    }
    
    protected function setValidationRules()
    {
        return array();
    }
}

/* End of file Marital.php */
/* Location: ./models/Marital.php */
?>
