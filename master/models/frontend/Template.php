<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * Model
 * @author amostovoy
 * @filesource
 */

Loader::loadModel('TemplateCommon', false);

/**
 * class modelTemplate
 *
 * @package		Project
 * @subpackage	Models
 * @category	Database
 * @author		amostovoy
 */
class modelTemplate extends modelTemplateCommon
{
    protected function setValidationRules()
    {
        return array(
            'common' => array(
            ),
        );
    }
}

/* End of file Template.php */
/* Location: ./models/frontend/Template.php */
?>