<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * Model
 * @author amostovoy
 * @filesource
 */
Loader::loadModel('Content', false);
/**
 * class modelStatic
 *
 * @package		Project
 * @subpackage	Models
 * @category	Database
 * @author		amostovoy
 */
abstract class modelStaticCommon extends modelContent
{
    public $type = 'private';
    public $parent_id = 0;
    
	protected function setName()
    {
        return self::$tbl_static;
    }
    
    protected function getBy($params)
    {
        $d_params = array(
            'where' => array(
                'type' => $this->type,
                'parent_id' => $this->parent_id,
            )
        );
        
        $params = $this->builder->mergeArrays($d_params, $params);
        
        return $this->getContent($params);
    }
}

/* End of file StaticCommon.php */
/* Location: ./models/StaticCommon.php */
?>