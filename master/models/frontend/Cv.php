<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * Model
 * @author amostovoy
 * @filesource
 */

Loader::loadModel('CvCommon', false);

/**
 * class modelCv
 *
 * @package		Project
 * @subpackage	Models
 * @category	Database
 * @author		amostovoy
 */
class modelCv extends modelCvCommon
{
    /**
     * Checking rules e.g. 'action' => rules
     */
//    protected $_rules = array(
//        'common' => array(  ),
//    );
    public function saveUpload($data)
    {
        Loader::loadExtension('Media');
        SwfUpload::init();

        $tmp_files = SwfUpload::getTmpArray('cv');
        foreach($tmp_files as $k => $file)
        {
            $data['user_id'] = App::user()->id;
            $data['name'] = substr($file,0,strrpos($file,'.'));
            if(($id = $this->save($data)))
            {
                // move uploaded single file
                SwfUpload::moveTmpFiles('cv', App::controller()->base_dir.Media::GALLERY_DIR.DS.'cv'.DS, $id, 'redirect', 'cv');
            }
        }
    }
}

/* End of file Cv.php */
/* Location: ./models/frontend/Cv.php */
?>
