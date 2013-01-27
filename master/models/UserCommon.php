<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * Model
 * @author amostovoy
 * @filesource
 */

Loader::loadModel('Personal', false);

/**
 * class modelUserCommon
 *
 * @package		Project
 * @subpackage	Models
 * @category	Database
 * @author		amostovoy
 */
class modelUserCommon extends modelPersonal
{
    protected $relative = 'user';
    
    protected $d_orders = array(
        'reg_date'         => 'DESC',
    );
    
    protected function setName()
    {
        return self::$tbl_user;
    }

    public function getDefaultOrder()
    {
        return 'reg_date';
    }
    
    protected function getBy($params)
    {
        $d_params = array(
            'append' => array(
                'personal' => true,
            ),
            'where' => array(
                'role <>' => 0,
            ),
        );
        
        $params = $this->builder->mergeArrays($d_params, $params);
        
        return $this->getContent($params);
    }
    
//    public function getById($id)
//    {
//        $info = $this->getList(
//            array(
//                'append_after' => array(
//                    'point' => array(App::model('Point', false), 'field'=>'id'),
//                    'num_picks' => array(App::model('PickCommon',false), 'getNumPicks'),
//                    'correct_picks' => array(App::model('PickCommon',false), 'getCorrectPicks'),
//                ),
//                'where'=>array('cc.id'=>$id),
//            )
//        );
//        return isset($info[0]) ? $info[0] : null;
//    }

    /**
     * Gets filter map
     * 
     * @return array
     */
    protected function getContentFilter() {
        return array(
            'digital' => array(
                'content' => array('id', 'status', 'role'),
            ),
            'string' => array(
                'content' => array('email', 'name'),
            ),
        );
    }
}

/* End of file UserCommon.php */
/* Location: ./models/UserCommon.php */
?>
