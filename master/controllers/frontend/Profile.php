<?php if (!defined('APP')) exit('No direct script access allowed');
/**
 * Controller
 * @author amostovoy
 * @filesource
 */

Loader::loadController('FrontendCommonController', false);

/**
 * class ProfileController
 *
 * @package		Project
 * @subpackage	Controllers
 * @author		amostovoy
 */
class ProfileController extends FrontendCommonController
{
    protected final function _init()
    {
        if(!$this->_user->isAuth())
        {
            $this->_view->addErrorToSession($this->_lang->get('profile','must_be_auth'), 'must_be_auth');
            $this->redirect();
        }
        if(!isset($this->_user->id))
        {
            $this->error404();
        }
        parent::_init();
        $this->loadModel('User');
        $this->model = $this->mod_user;
    }

    protected function createControllerModel($model, $site_part=true)
    {
    }
    
    public function indexAction()
    {
        $id = $this->_request->getParam('id', $this->_user->id, Request::FILTER_INT);
        $user = $this->model->getById($id);

        $this->_view->profile = $user;
    }
    
    public function friendsAction()
    {
        if(!$this->_request->isAjax())
        {
            $this->error404();
        }
        $user = array();
        $user['user_id'] = $this->_request->getPost('user_id', $this->_user->user_id, Request::FILTER_STRING);

        if(!isset($_SESSION['t_fr'][$user['user_id']]))
        {
            $_SESSION['t_fr'][$user['user_id']] = array();
            
            $friends = $this->twitter->friendsIds($user['user_id']);
            if(!empty($friends['ids']))
            {
//                $fr_ids = array_chunk($friends['ids'], 100);
//
//                $friends = array();
//                foreach($fr_ids as &$ids)
//                {
//                    $friends += $this->twitter->usersLookup($ids);
//                }

                $followers = $this->twitter->followersIds(null, Config::TWITTER_NAME);
                
                $intr = array_intersect($followers['ids'], $friends['ids']);

                $friend_ids = array();
                foreach($intr as $k => &$fr)
                {
                    $s=$this->twitter->friendshipsShow($fr,null,null,Config::TWITTER_NAME);
                    if(!empty($s['relationship']['target']['followed_by']))
                    {
                        $friend_ids[] = $fr;
                    }
                }
                
                if(!empty($friend_ids))
                {
                    $user['friends'] = $this->model->getByTwitterIds($friend_ids);
                    $_SESSION['t_fr'][$user['user_id']] = $user['friends'];
                }
            }
        }
        else
        {
            $user['friends'] = $_SESSION['t_fr'][$user['user_id']];
        }
        
        $this->_view->profile = $user;
        
        $this->ajax->send(Ajax::RESULT_HTML, $this->fetchElementTemplate('friends_list'));
    }
}

/* End of file Index.php */
/* Location: ./controllers/frontend/Index.php */
?>