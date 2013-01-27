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
                'required' => 'type,name,content',
                'wysiwyg' => 'content',
                'unique' => 'name'
            ),
            'save_add_email' => array(
                'required' => 'subject,from',
            ),
            'save_edit_email' => array(
                'required' => 'subject,from',
            ),
        );
    }
}

/* End of file Template.php */
/* Location: ./models/admin/Template.php */
?>