<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * Model
 * @author amostovoy
 * @filesource
 */

Loader::loadModel('Content', false);

/**
 * class modelPersonal
 *
 * @package		Project
 * @subpackage	Models
 * @category	Database
 * @author		amostovoy
 */
class modelPersonal extends modelContent
{
    /**
     * relative field in db. need to be set in child
     * @var string
     */
    protected $relative;
    
    protected function setName()
    {
        return self::$tbl_personal;
    }
    
    protected function setValidationRules()
    {
        return array(
            'common' => array(  ),
            'save' => array(
                'required' => 'first_name,last_name,birth_date,sex',
            ),
        );
    }

    /**
     * makes sql alias for table lang
     */
    protected function bindTableAlias()
    {
        $this->builder->addAlias(self::$tbl_personal, 'p');
        $this->builder->addAlias(self::$tbl_address, 'a');
        $this->builder->addAlias(self::$tbl_country, 'c');
        $this->builder->addAlias(self::$tbl_marital, 'm');
    }
    
    public function modelAppendToCv($cv_id)
    {
        $info = $this->getBy(array(
            'append' => array(
                'marital' => 'cc',
            ),
            'append_after' => array(
                'address' => array(
                    $this->model('Address',false),
                    'field' => 'address_id',
                ),
            ),
            'where' => array(
                'cv_id' => $cv_id,
            )
        ));
        
        return isset($info[0]) ? $info[0] : $info;
    }
    
    public function appendPersonal()
    {
        $p = $this->builder->getAlias(self::$tbl_personal);
        $cc = $this->builder->getAlias('content');
        return array(
            'append' => array(
                'address' => true,
                'marital' => true,
            ),
            'select' => $p.'.first_name,'
                        .$p.'.last_name,'
                        .$p.'.birth_date,'
                        .$p.'.children,'
                        .$p.'.sex,'
                        .$p.'.phone,'
                        .$p.'.mobile',
            'join' => 'left join '.self::$tbl_personal.' as '.$p.'
                on '.$p.'.'.$this->relative.'_id = '.$cc.'.id',
        );
    }
    
    public function appendAddress($alias)
    {
        $p = $alias === true ? $this->builder->getAlias(self::$tbl_personal) : $alias;
        $a = $this->builder->getAlias(self::$tbl_address);
        return array(
            'append' => array(
                'country' => true,
            ),
            'select' => $a.'.city,'
                        .$a.'.address,'
                        .$a.'.zip',
            'join' => 'left join '.self::$tbl_address.' as '.$a.'
                on '.$p.'.address_id = '.$a.'.id',
        );
    }
    public function appendCountry()
    {
        $a = $this->builder->getAlias(self::$tbl_address);
        $c = $this->builder->getAlias(self::$tbl_country);
        return array(
            'select' => $c.'.name as country',
            'join' => 'left join '.self::$tbl_country.' as '.$c.'
                on '.$a.'.country_id = '.$c.'.id',
        );
    }
    public function appendMarital($alias)
    {
        $p = $alias === true ? $this->builder->getAlias(self::$tbl_personal) : $alias;
        $m = $this->builder->getAlias(self::$tbl_marital);
        return array(
            'select' => $m.'.name as marital',
            'join' => 'left join '.self::$tbl_marital.' as '.$m.'
                on '.$p.'.marital_id = '.$m.'.id',
        );
    }
    
    public function filterName($val)
    {
        $name_parts = explode(' ', $val);
        foreach($name_parts as &$np)
        {
            $np = '%'.$np.'%';
        }
        return array(
            'where' => array(
                'or' => array(
                    'first_name like' => implode(' or ', $name_parts),
                    'last_name like' => implode(' or ', $name_parts),
                ),
            ),
        );
    }
    
    public function formOrderName($dir)
    {
        $content = $this->builder->getAlias('content');
        return $content.'.first_name '.$dir.', '.$content.'.last_name '.$dir;
    }
    /**
     * save information portion to personal table
     * @param type $data
     * @param type $id
     * @param type $field
     * @return type 
     */
    public function save($data, $id=null, $field='id', $foreign_key_before=null, $foreign_key_after=null)
    {
        return parent::save($data, $id, 'id', array('address'=>'address_id'), $foreign_key_after);
    }
//    public function save($data, $id=null, $field='id')
//    {
//        //save info to main table
//        $relation_id = parent::save($data, $id);
//        if($relation_id)
//        {
//            // set new data depend on previous save
//            $data[$this->relative.'_id'] = $relation_id;
//            unset($data['id']);
//            
//            // on add new row to user table trigger create
//            // new row in personal table
//            
//            // save to personal table
//            $old = $this->_name;
//            $this->_name = self::$tbl_personal;
//            
//            $res = parent::save($data, $relation_id, $this->relative.'_id');
//            
//            $this->_name = $old;
//            
//            return $res;
//        }
//        return $relation_id;
//    }
}

/* End of file Personal.php */
/* Location: ./models/Personal.php */
?>
