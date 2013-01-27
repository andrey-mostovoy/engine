<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * Model
 * @author amostovoy
 * @filesource
 */

Loader::loadModel('Content', false);

/**
 * class modelCountry
 *
 * @package		Project
 * @subpackage	Models
 * @category	Database
 * @author		amostovoy
 */
class modelCountry extends modelContent
{
    protected function setName()
    {
        return self::$tbl_country;
    }
    protected function setValidationRules()
    {
        return array();
    }
}

/* End of file Country.php */
/* Location: ./models/Country.php */
?>
