<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * Model
 * @author amostovoy
 * @filesource
 */

Loader::loadModel('Content', false);

/**
 * class modelApplicationCommon
 *
 * @package		Project
 * @subpackage	Models
 * @category	Database
 * @author		amostovoy
 */
class modelApplicationCommon extends modelContent
{
    
    protected $d_orders = array(
        'create_date'         => 'DESC',
    );
    
    protected function setName()
    {
        return self::$tbl_cv;
    }

    public function getDefaultOrder()
    {
        return 'create_date';
    }
    
    protected function getBy($params)
    {
        $d_params = array(
            'append' => array(
                'personal' => true,
            ),
        );
        
        $params = $this->builder->mergeArrays($d_params, $params);
        
        return $this->getContent($params);
    }
    
    public function formOrderName($dir)
    {
        $content = $this->builder->getAlias('content');
        return $content.'.name '.$dir;
    }
    /**
     * Gets filter map
     * 
     * @return array
     */
    protected function getContentFilter() {
        return array(
            'digital' => array(
                'content' => array('id'),
            ),
            'string' => array(
                'content' => array('name', 'type'),
            ),
        );
    }
}

/* End of file ApplicationCommon.php */
/* Location: ./models/ApplicationCommon.php */
?>
