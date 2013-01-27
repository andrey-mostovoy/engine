<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * Model
 * @author amostovoy
 * @filesource
 */

Loader::loadModel('UserCommon', false);

/**
 * class modelUser
 *
 * @package		Project
 * @subpackage	Models
 * @category	Database
 * @author		amostovoy
 */
class modelUser extends modelUserCommon
{
    protected $_dynamic_rules = array(
        'admin_save_edit' => array(
            'required' => '+:password,confirm_password',
            'length'   => '+:password:5|16',
            'match'    => '+:password|confirm_password',
        ),
    );
    protected function setValidationRules()
    {
        return array(
            'common' => array(
                'unique' => 'email',
            ),
            'admin_save_add' => array(
                'required' => 'email,password,confirm_password',
                'length'   => 'email:4|35,password:5|16',
                'email'    => 'email',
                'match'    => 'password|confirm_password',
            ),
            'admin_save_edit' => array(
                'required' => 'email',
                'length'   => 'email:4|35',
                'email'    => 'email',
            ),
        );
    }
    
    protected function getValidationParams($scenario, $data)
    {
        if($scenario == 'admin_save_edit')
        {
            if(!empty($data['id']) && !empty($data['password']))
            {
                return isset($this->_dynamic_rules[$scenario]) ? $this->_dynamic_rules[$scenario]: null;
            }
        }
        return null;
    }
    
    public function save($data, $id=null, $field='id', $foreign_key_before=null, $foreign_key_after=null)
    {
        if(isset($data['role']) && $data['role'] == User::MEMBER) // in that case we save user
        {
//            App::model('Point',false)->saveUserPoint($data, $id);
//            unset($data['point']);
        }
        if(isset($data['role']) && $data['role'] == User::ADMIN) // in that case we save admin
        {
            if(!empty($data['password']))
            {
                $hash = App::controller()->passwordHash($data['password']);
                $data['password'] = $hash['hash'];
                $data['salt'] = $hash['salt'];
            }
            else
            {
                unset($data['password'],$data['confirm_password']);
            }
        }
        return parent::save($data, $id);
    }

//    public function validate($data, $action=null, $params=null)
//    {
//        if ( !($this->checkData($data, $action)) )
//        {
//            return false;
//        }
//        
//        $where = array(
//               'email' => $data['email'] );
//        
//        if(isset($data['id']) && $data['id']!='')   
//        {
//            $where['id !='] = $data['id'];
//        }
//            
//        if ( $this->getRow($where) !== false )
//           {
//               $this->addError('[email]', 'uniqua', null,
//                       'validation');
//               return false;
//           }
//    
//       return true; 
//    }
    
    /**
     * Gets filter map
     * 
     * @return array
     */
//    protected function getContentFilter() {
//        return array(
//            'digital' => array(
//                'content' => array('id','user_id','role'),
//                array('total_point'),
//            ),
//            'string' => array(
//                'content' => array('screen_name'),
//                array('label', 'name'),
//            ),
//        );
//    }
    
    public function getTitle($id)
    {
        $info = $this->getById($id);
        if($info['role'] == User::ADMIN)
        {
            return $info['email'];
        }
        else
        {
            return $info['first_name'].' '.$info['last_name'];
        }
    }
}

/* End of file User.php */
/* Location: ./models/admin/User.php */
?>
