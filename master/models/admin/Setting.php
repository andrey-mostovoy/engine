<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * Model
 * @author amostovoy
 * @filesource
 */

Loader::loadModel('SettingCommon', false);

/**
 * class modelSetting
 *
 * @package		Project
 * @subpackage	Models
 * @category	Database
 * @author		amostovoy
 */
class modelSetting extends modelSettingCommon
{
    protected function setValidationRules()
    {
        return array(
            'common' => array(
                'required' => 'value',
                'unique' => 'name',
            ),
            'save_add' => array(
                'required' => 'name',
            )
        );
    }
    
    /**
     * Gets filter map
     * 
     * @return array
     */
    protected function getContentFilter() {
        return array(
            'string' => array(
                'content' => array('name', 'value'),
            ),
        );
    }
}

/* End of file Setting.php */
/* Location: ./models/admin/Setting.php */
?>