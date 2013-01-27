<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * @package		Base
 * @subpackage	Common
 * @author		amostovoy
 * @copyright	Copyright (c) 2010, Qualium-Systems, Inc.
 * @version		1.0
 * @since		Version 1.0
 * @filesource
 */

Loader::loadClass('Common_DB_ModelBuilder');

/**	
 * class Model.
 * Containing common methods and class properties for work with db
 * for current project only
 * 
 * @package		Base
 * @subpackage	Models
 * @category	Database
 * @author		amostovoy
 * @abstract
 */
abstract class Model extends ModelBuilder
{
	/*
	 * common table list presented in project
	 */
    protected static $tbl_user              = 'user';
    protected static $tbl_static            = 'static';
    protected static $tbl_setting           = 'setting';
    protected static $tbl_email_template    = 'email_template';
    // ----------  other
    protected static $tbl_personal          = 'personal';
    protected static $tbl_address           = 'address';
    protected static $tbl_country           = 'country';
    protected static $tbl_marital           = 'marital';
    protected static $tbl_template          = 'template';
    protected static $tbl_cv                = 'cv';
    protected static $tbl_cv_info           = 'cv_info';
    protected static $tbl_position          = 'position';
    protected static $tbl_payment           = 'payment';
    protected static $tbl_payment_plan      = 'payment_plan';
    protected static $tbl_letter            = 'letter';
    protected static $tbl_application       = 'application';
    protected static $tbl_job               = 'job';
    protected static $tbl_company           = 'company';
    protected static $tbl_candc             = 'candc';
    protected static $tbl_language          = 'language';
    protected static $tbl_work              = 'work';
    protected static $tbl_education         = 'education';
//
//    /*
//     * PLACE YOUR CODE BELOW
//     */
    
    protected function getBy($params)
    {
        return $this->getContent($params);
    }
    
    public function getList($params=null)
    {
        return $this->getBy($params);
    }
    
    public function getById($id)
    {
        $info = $this->getList(array(
            'session' => array('save' => false),
            'where'=>array('cc.id'=>$id))
        );
        return isset($info[0]) ? $info[0] : null;
    }
    
    /**
     * <p>Save model data. Save data with relations. Choose model
     * for relation table depends on name of key in data array
     * what contains data array for other table</p>
     * @param array $data
     * @param string|int $id id value of item on update action
     * @param string $field field name on update action perform as primary key
     * @param array $foreign_key_before array with foreign key to save related 
     * table before main table. Need in situations, when id of related content
     * inserts into main table. Format is: key_in_data_array => key_to set_relation_id
     * @param array $foreign_key_after array with foreign key to save related 
     * table after main table. After current table saved this params going to 
     * related model with relation id as value and posible foreign field name (based
     * on current table name, for example table - user, so field name will be - 
     * user_id).
     * @return int|bool 
     */
	public function save($data, $id=null, $field='id', $foreign_key_before=null, $foreign_key_after=null)
    {
        $res = true;
        $r_id = false;
        
        //add to current data relations ids which was paste before this model 
        //called
        if(!is_null($foreign_key_after) && is_array($foreign_key_after))
        {
            foreach($foreign_key_after as $k=>$v)
            {
                $data[$k] = $v;
            }
        }
        
        // save relations before save current model table
        if(!is_null($foreign_key_before))
        {
            $res = $res && $this->saveRelationsBefore($data, $foreign_key_before);
        }
        // save current model table
        if(empty($id))
        {
            if( $this->add($data) )
            {
                $r_id = $this->_insert_id;
            }
        }
        else
        {
            if( $this->update($data, array($field=>$id)) )
            {
                $r_id = $id;
            }
        }
        // save relations after save current model table
        $res = $res && $this->saveRelationsAfter($data, $foreign_key_before, $r_id);
        
        return $res ? $r_id : $res;
    }
    /**
     * Save relation data table before main table does.
     * {@see Model::save}
     * @param array $data
     * @param array $foreign_key
     * @return boolean 
     */
    private function saveRelationsBefore(&$data, $foreign_key)
    {
        $res = true;
        foreach($data as $k => &$d) // loop data
        {
            if(is_array($d))
            {
                foreach($foreign_key as $f => $fk) // loop on posible foreign keys
                {
                    if($f == $k) // if found current key in foreign key - do actions
                    {
                        //save data using related table.
                        $m = implode(array_map('ucfirst', explode('_', $k)));
                        $data[$fk] = $this->model($m, false)->save(
                                $d,
                                isset($data[$f]['id'])?$data[$f]['id']:null
                        );
                        if(!isset($data[$fk]) || !$data[$fk])
                            $res = false;
                        break;
                    }
                }
            }
        }
        unset($d);
        
        return $res;
    }
    /**
     * Save data relation model after main table does.
     * {@see Model::save}
     * @param array $data
     * @param array $foreign_key
     * @param int $relation_id
     * @return boolean 
     */
    private function saveRelationsAfter(&$data, $foreign_key, $relation_id)
    {
        $res = true;
        if($relation_id)
        {
            foreach($data as $k => &$d) // loop data
            {
                if(is_array($d) 
                    && (!is_array($foreign_key) 
                        || (is_array($foreign_key) && !array_key_exists($k, $foreign_key))
                    )
                ) { // if sub array is not data for before save - do actions
                    $m = implode(array_map('ucfirst', explode('_', $k)));
                    // if data is multiple datas for some model
                    if(is_numeric(key($d)) && is_array(current($d)))
                    {
                        foreach($d as &$dd) 
                        { // save each multiple data using related model
                            $res = $res && $this->model($m, false)->save(
                                    $dd,
                                    isset($dd['id'])?$dd['id']:null,
                                    'id',
                                    null,
                                    array($this->_name.'_id'=>$relation_id)
                            );
                        }
                        unset($dd);
                    }
                    else
                    { // save data using related model
                        $res = $res && $this->model($m, false)->save(
                                $d,
                                isset($d['id'])?$d['id']:null,
                                'id',
                                null,
                                array($this->_name.'_id'=>$relation_id)
                        );
                    }
                }
            }
            unset($d);
        }
        else
            $res = false;
        
        return $res;
    }
}

/* End of file Model.php */
/* Location: ./class/Common/DB/Model.php */
?>
