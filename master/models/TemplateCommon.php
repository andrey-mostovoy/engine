<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * Model
 * @author amostovoy
 * @filesource
 */
Loader::loadModel('Content', false);
/**
 * class modelTemplateCommon
 *
 * @package		Project
 * @subpackage	Models
 * @category	Database
 * @author		amostovoy
 */
abstract class modelTemplateCommon extends modelContent
{
    public $type = 'letter';
    
	protected function setName()
    {
        return self::$tbl_template;
    }
    
    public function setNameForEmail()
    {
        $this->_name = self::$tbl_email_template;
    }
    
    protected function getBy($params)
    {
        $d_params = array(
            'filter' => array(
                'type'=>true
            ),
        );
        
        $params = $this->builder->mergeArrays($d_params, $params);
        
        return $this->getContent($params);
    }
    
    public function filterType()
    {
        if($this->type != 'email')
        {
            return array(
                'where' => array(
                    'type' => $this->type,
                ),
            );
        }
    }
}

/* End of file TemplateCommon.php */
/* Location: ./models/TemplateCommon.php */
?>