<?php if (!defined('APP')) exit('No direct script access allowed');
/**
 * Controller
 * @author amostovoy
 * @filesource
 */

Loader::loadController('FrontendCommonController', false);

/**
 * class ShowCommon
 *
 * @package		Project
 * @subpackage	Controllers
 * @author		amostovoy
 */
class ShowContentCommonController extends FrontendCommonController
{
    const DIRECTION_LIMIT = 2;
    
    protected function _init()
    {
        if(!$this->_request->isAjax())
        {
            parent::_init();
        }
        else
        {
            $this->ajax->set(Ajax::RESULT_EMPTY);
            
            if ($this->_user->isLogin())
            {
                $this->_view->is_login = true;
                $this->_view->user = $this->_user->getUserInfo();
            }
            elseif ('getvoted' != $this->_request->getActionName())
            {
                $this->ajax->send(Ajax::RESULT_REDIRECT, $this->domain_url.'/index/login');
            }
        }
        $this->loadModel('Content', false);
    }
    
    public function reviewAction()
    {
        $review = $this->mod_content->getReview($this->meta_id);
        
        $this->_view->review = $review;
        $this->_view->source = $review[modelContent::FIELD_SOURCE];
    }
    
     /**
     * Get list of voted users
     */ 
    public function getVotedAction()
    {
        $metaId = $this->_request->getParam('meta_id', 0, Request::FILTER_INT);
        
        $userId = isset($this->_user->id) ? $this->_user->id : 0;
        $likes = $this->mod_content->getLikes($metaId, $userId);
        App::model('frontend/mysdtn/UserRelation', false)
            ->appendFriends($likes, App::user()->getId());

        $this->_view->useSectionLayout(false);
        $this->_view->likes = $likes;
        $this->_lang->getSections('relation');
        $this->ajax->send(Ajax::RESULT_HTML, $this->fetchElementTemplate('voted_list'));
    }
    
    
    /**
     * Show tmp thumb after file was uploaded
     */
    public function tmpThumbAction()
    {
        $this->_view->files = array(
            array(
                'is_tmp'        => true,
                'meta_id'       => $this->data['file_id'],
                'source'        => $this->domain_url.'/file/'.$this->data['show_url'].'/id/'.$this->data['file_id'],
                'author_name'   => $this->_user->getCurrentName(),
                'resource'      => Media::VIDEO_RES_UPLOAD
            )
        );

        $this->_view->file_id = $this->data['file_id'];
        $this->_view->ftype = $this->data['ftype'];
        if(!empty($this->data['section']))
            $this->_view->section = $this->data['section'];
        $this->_view->tmp = 'tmp';
        $this->_view->main_select = $this->data['choose_main'] == 'false' ? false : true;
        $this->_view->hidden = true;
//        $this->_view->tpl_path = $this->_view->getTemplateElement()$this->data['tpl_path'];

//        if(!empty($this->data['tpls']))
//        {
//            $this->_view->tpl_path = true;
//            foreach($this->data['tpls'] as $tpl)
//            {
//                $this->_view->addTpl($tpl, $this->_view->getTemplateElement($tpl));
//            }
//        }
//        
//        if(!empty($this->data['additional_methods']))
//        {
//            foreach($this->data['additional_methods'] as $am)
//            {
//                if(count($am) == 2)
//                {
//                    $am[] = null;
//                }
//                list($c, $m, $p) = $am;
//                $c = App::controller($c, false);
//                call_user_func(array($c, $m), $p);
//            }
//        }

        $tpl = $this->fetchElementTemplate('file_upload_thumbs');

        if(!empty($tpl))
            $this->ajax->set(Ajax::RESULT_HTML, $tpl);
        
        $this->ajax->send();
    }
    
    public function videoAction($ext=null)
    {
        if($this->_request->getPost('is_tmp', false, Request::FILTER_BOOL))
        {
            $mid = $this->_request->getPost('mid', '', Request::FILTER_STRING);
            Loader::loadController('FileUploadController', false);
            $video = FileUploadController::getTmpVideoFile($mid);

            $movie = new ffmpeg_movie(FileUploadController::getTmpVideoDir().$video);
            
            $video = array(
                'is_tmp'        => true,
                'meta_id'       => $mid,
                'author_name'   => $this->_user->getCurrentName(),
                'resource'      => Media::VIDEO_RES_UPLOAD,
                'w'             => $movie->getFrameWidth(),
                'h'             => $movie->getFrameHeight()
            );
        }
        else
        {
            $section = $this->_request->getPost('section', '', Request::FILTER_STRING);
            if(!empty($section))
            {
                $this->_view->section = $section;
            }
            
            if(!empty($ext))
            {
                $video = $this->callMethod($ext);
            }
            else
            {
                $video = $this->mod_content->getVideo($this->meta_id);
            }
            
            if($video['resource'] != Media::VIDEO_RES_UPLOAD)
            {
                $video[Model::PERM][Permission::TARGET_DOWNLOAD][Permission::ACTION_VIEW] = 0;
            }
            elseif(!File::checkExist(
                    $this->{$video[modelContent::FIELD_SOURCE].'_video_dir'}
                    .$video[modelContent::FIELD_META_ID]
                    . DS 
                    . Media::MOVIE
                    .'.'.Media::MOVIE_EXT)
            ) {
                $video[Model::PERM][Permission::TARGET_DOWNLOAD][Permission::ACTION_VIEW] = 0;
            }
                
        }
        $this->fetchVideo($video);
    }
    
    private function fetchVideo($video)
    {
        $p_arr = array('item'=>$video, 'no_fetch'=>true);
        if(isset($this->_view->section))
        {
            $p_arr['section'] = $this->_view->section;
        }
        $entry = $this->_view->plugin()->video($p_arr, $this->_view);

        $this->_view->entry = array_merge($entry, $video);
        $tpl = $this->fetchElementTemplate('video_embed');
                
        if(!empty($tpl))
            $this->ajax->set(Ajax::RESULT_HTML, array('tpl'=>$tpl, 'size'=>$entry['size']));
        
        $this->ajax->send();
    }
    
    public function playAction()
    {
        $is_tmp = $this->_request->getParam('tmp', false, Request::FILTER_BOOL);
        
        if($is_tmp)
        {
            $video = $this->_request->getParam('video', null, Request::FILTER_STRING);
            Loader::loadController('FileUploadController', false);
            $video = FileUploadController::getTmpVideoFile($video);
            $video = FileUploadController::getTmpVideoDir().$video;
        }
        else
        {
            $video = $this->_request->getParam('video', null, Request::FILTER_INT);
            $video = $this->mod_content->getVideo($video);
            
            if(($section = $this->_request->getParam('section', '', Request::FILTER_STR)))
            {
                $dir = $section.'_video_dir';
            }
            else
                $dir = $video['source'].'_video_dir';
            
            $video = $this->$dir.$video['meta_id'].DS.Media::MOVIE.'.'.Media::MOVIE_EXT;
        }
        
        File::outputFile( $video );
    }
    
    /**
     * load image on show it on popup
     */
    public function imageAction($ext=null)
    {
        $is_tmp = $this->_request->getPost('is_tmp', false, Request::FILTER_INT);
    
        if($is_tmp)
        {// tmp image show
            $file_id = $this->_request->getPost('mid', 0, Request::FILTER_STRING);
            $image = array(
                'is_tmp'        => true,
                'meta_id'       => $file_id,
                'source'        => $this->domain_url.'/file/showFullImage/id/'.$file_id,
                'author_name'   => $this->_user->getCurrentName()
            );
        }
        else
        {
            $section = $this->_request->getPost('section', '', Request::FILTER_STRING);
            if(!empty($section))
            {
                $this->_view->section = $section;
            }

            if(!empty($ext))
            {
                $image = $this->callMethod($ext);
            }
            else
            {
                $image = $this->mod_content->getImage($this->meta_id);
            }
            
            if(!$this->_view->plugin()
                        ->image(
                                array(
                                    'section'       => $section,
                                    'id'            => $image[modelContent::FIELD_META_ID],
                                    'need_default'  => false
                                ),
                                $this->_view)
            ) {
                $image[Model::PERM][Permission::TARGET_DOWNLOAD][Permission::ACTION_VIEW] = 0;
            }
        }
        
        $this->fetchImage($image);
        $this->ajax->send(); 
    }
    
    private function fetchImage($image)
    {
        $this->_view->entry = $image;
        
        $this->_view->size = Media::POPUP;
        $tpl = $this->fetchElementTemplate('image_embed');
        
        if(!empty($tpl))
            $this->ajax->set(Ajax::RESULT_HTML, array('tpl'=>  $tpl));
    }
    
    public function nextAction($ext=null)
    {
        $compare = '>';
        $order = 'ASC';
        $this->direction($compare, $order, $ext);
    }
    
    public function prevAction($ext=null)
    {
        $compare = '<';
        $order = 'DESC';
        $this->direction($compare, $order, $ext);
    }
    
    private function direction($d,$o, $ext)
    {
        $type = $this->_request->getPost('type', '', Request::FILTER_STRING);
        
//        $params['order'] = modelContent::ALIAS_CM.'.'.modelContent::FIELD_META_ID.' '.$o;
//        $params['where'] = modelContent::ALIAS_CM.'.'.modelContent::FIELD_META_ID.' '.$d.' '.$this->meta_id;
//        $params['limit'] = self::DIRECTION_LIMIT;
        
        if($type == 'image')
        {
//            $method = 'getImages';
            $method = 'getImage';
            $method_fetch = 'fetchImage';
        }
        elseif($type == 'video')
        {
//            $method = 'getVideos';
            $method = 'getVideo';
            $method_fetch = 'fetchVideo';
        }
        
        if(empty($ext))
        {
//            $meta_info = $this->mod_content->getMeta($this->meta_id);
//            $parent_id = $meta_info[modelContent::FIELD_PARENT_ID];
//            $r = $this->mod_content->$method($parent_id, $this->_user->getId(), $params);
            $r = $this->mod_content->$method($this->meta_id);
        }
        else
            $r = $this->callMethod($ext);
        
        if(isset($r[0]))
        {
            $r = $r[0];
        }
        
        if(empty($r))
        {
            $this->ajax->set(Ajax::RESULT_EMPTY);
        }
        else
        {
            $this->$method_fetch($r);
        }
        
        $this->ajax->send();
    }
    
    private function callMethod($ext, $params=null)
    {
        $args = '';
        if(!empty($ext['params']))
            foreach($ext['params'] as $k=>$p)
                $args .= $p.',';
        
        if(isset($ext['eval']) && $ext['eval'])
            eval('$res = App::model("'.$ext['model'].'", false)->'.$ext['method'].'('.rtrim($args,',').');');
        elseif(isset($ext['array']) && $ext['array'])
            $res = App::model($ext['model'], false)->{$ext['method']}($ext['params']);
        elseif(is_null($params))
            $res = App::model($ext['model'], false)->{$ext['method']}();
        else
            $res = App::model($ext['model'], false)->{$ext['method']}($params);
            
        return $res;
    }
}

/* End of file ShowContentCommon.php */
/* Location: ./components/ShowContentCommon.php */
?>