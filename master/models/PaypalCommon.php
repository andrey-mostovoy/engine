<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * Model
 * @author ynedostup
 * @filesource
 */
Loader::loadModel('Content', false);
/**
 * class modelPaypalCommon
 *
 * @package		Project
 * @subpackage	Models
 * @category	Database
 * @author		ynedostup
 */
class modelPaypalCommon extends modelContent
{
    protected function setName()
    {
        return self::$tbl_payment;
    }
    protected function setValidationRules()
    {
        return array(
            'describe' => array(
                'required' => 'terms,describe',
            ),
        );
    }

    public function addTransaction(){
        echo 'aaa';
    }

    public function getPaypalPlan($id=''){
        $this->_name=self::$tbl_payment_plan;
        if (empty($id)){
            $plan=$this->_selectData();
        } else {
            $plan=$this->getById($id);
        }
        $this->_name = $this->setName();
        return $plan;
    }

}