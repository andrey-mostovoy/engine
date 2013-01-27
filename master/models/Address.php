<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * Model
 * @author amostovoy
 * @filesource
 */

Loader::loadModel('Content', false);

/**
 * class modelAddress
 *
 * @package		Project
 * @subpackage	Models
 * @category	Database
 * @author		amostovoy
 */
class modelAddress extends modelContent
{
    protected function setName()
    {
        return self::$tbl_address;
    }
    protected function setValidationRules()
    {
        return array(
            'common' => array(  ),
            'save' => array(
                'required' => 'country_id,city,address,zip',
            ),
        );
    }
    protected function bindTableAlias()
    {
        $this->builder->addAlias(self::$tbl_country, 'c');
    }
    
    public function getById($id)
    {
        $info = $this->getList(array(
            'session' => array('save' => false),
            'append' => array(
                'country' => true,
            ),
            'where'=>array('cc.id'=>$id))
        );
        return isset($info[0]) ? $info[0] : null;
    }
    
    public function appendCountry()
    {
        $c = $this->builder->getAlias(self::$tbl_country);
        $cc = $this->builder->getAlias('content');
        return array(
            'select' => $c.'.name as country',
            'join' => 'left join '.self::$tbl_country.' as '.$c.' on '.$c.'.id='.$cc.'.country_id'
        );
    }
    
    public function modelAppendAfter($id, $visitor_id, $params)
    {
        return $this->getById($id);
    }
}

/* End of file Address.php */
/* Location: ./models/Address.php */
?>
