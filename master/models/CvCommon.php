<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * Model
 * @author amostovoy
 * @filesource
 */

Loader::loadModel('Content', false);

/**
 * class modelCvCommon
 *
 * @package		Project
 * @subpackage	Models
 * @category	Database
 * @author		amostovoy
 */
class modelCvCommon extends modelContent
{
//    protected $relative = 'cv';
    
    protected $d_orders = array(
        'create_date'         => 'DESC',
    );
    
    protected function setName()
    {
        return self::$tbl_cv;
    }

    protected function setValidationRules()
    {
        return array(
            'common' => array(  ),
            'save' => array(
                'required' => 'name,user_id',
            ),
        );
    }
    
    public function getDefaultOrder()
    {
        return 'create_date';
    }
    
    protected function getBy($params)
    {
        $d_params = array(
            'append' => array(
//                'personal' => true,
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
    
    public function save($data, $id = null, $field = 'id', $foreign_key_before=null, $foreign_key_after=null)
    {
        return parent::save($data, $id, $field, null, $foreign_key_after);
    }
    
    public function getById($id)
    {
        $info = $this->getList(
            array(
                'session' => array('save' => false),
                'append_after' => array(
                    'position' => array(
                        $this->model('Position',false),
                        'modelAppendToCv',
                        'field' => 'id',
                    ),
                    'personal' => array(
                        $this->model('Personal',false),
                        'modelAppendToCv',
                        'field' => 'id',
                    ),
                    'education' => array(
                        $this->model('Education',false),
                        'modelAppendToCv',
                        'field' => 'id',
                    ),
                    'work' => array(
                        $this->model('Work',false),
                        'modelAppendToCv',
                        'field' => 'id',
                    ),
                    'candc' => array(
                        $this->model('Candc',false),
                        'modelAppendToCv',
                        'field' => 'id',
                    ),
                    'language' => array(
                        $this->model('Language',false),
                        'modelAppendToCv',
                        'field' => 'id',
                    ),
                    'cv_info' => array(
                        $this->model('CvInfo',false),
                        'modelAppendToCv',
                        'field' => 'id',
                    ),
                ),
                'where'=>array('cc.id'=>$id)
            )
        );
        return isset($info[0]) ? $info[0] : null;
    }
    
    public function appendPersonal()
    {
        
    }
}

/* End of file CvCommon.php */
/* Location: ./models/CvCommon.php */
?>
