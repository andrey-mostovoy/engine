<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * Model
 * @author amostovoy
 * @filesource
 */

Loader::loadModel('Content', false);

/**
 * class modelLetterCommon
 *
 * @package		Project
 * @subpackage	Models
 * @category	Database
 * @author		amostovoy
 */
class modelLetterCommon extends modelContent
{
    protected $d_orders = array(
        'create_date'         => 'DESC',
    );
    
    protected function setName()
    {
        return self::$tbl_letter;
    }

    public function getDefaultOrder()
    {
        return 'create_date';
    }
    
    protected function getBy($params)
    {
        $d_params = array(
            'append' => array(
            ),
        );
        
        $params = $this->builder->mergeArrays($d_params, $params);
        
        return $this->getContent($params);
    }
    
    /**
     * Gets filter map
     * 
     * @return array
     */
//    protected function getContentFilter() {
//        return array(
//            'digital' => array(
//                'content' => array('id'),
//            ),
//            'string' => array(
//                'content' => array('name', 'type'),
//            ),
//        );
//    }
}

/* End of file LetterCommon.php */
/* Location: ./models/LetterCommon.php */
?>
