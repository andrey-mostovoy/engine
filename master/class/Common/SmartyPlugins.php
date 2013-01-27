<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * @package		Base
 * @author		amostovoy
 * @copyright	Copyright (c) 2010, Qualium-Systems, Inc.
 * @version		1.0
 * @since		Version 1.0
 * @filesource
 */

Loader::loadClass(array('Base_SmartyPlugins'));

/**
 * class Smarty plugins
 * Set up smarty user plugins
 *
 * @package		Base
 * @author		amostovoy
 */
class SmartyPlugins extends BaseSmartyPlugins
{
    /**
     * function return additional plugins to register
     * to project. Format:
     * array(
     *      array(
     *          'type' => 'function',
     *          'name' => 'name_in_smarty_tpl',
     *          'callback' => 'functionInSmartyPluginsClass'
     *      ),
     *  ); 
     * type key is optional. {@see http://www.smarty.net/docs/en/api.register.plugin.tpl}
     * name key is a plugin name in smarty temlates
     * callback is a method name in SmartyPlugins class
     * @return array
     */
    public final function init()
    {
        return array(
            array(
                'type' => 'function',
                'name' => 'image',
                'callback' => 'image'
            ),
            array(
                'name' => 'date_diff',
                'callback' => 'dateDiff'
            ),
            array(
                'name' => 'tweet_pick_tip',
                'callback' => 'tweetPickTip'
            ),
        );
    }
    //---------------------------------------------------------------
    
    public final function image($params, $template)
    {
        if(isset($params['tmp']) && $params['tmp'])
        {
            return $params['section'];
        }
        
        if(!isset($params['section']))
        {
            throw new Exception('No section name \''.$params['section'].'\' in smarty function image');
            return false;
        }

        $def_params = array(
            'need_default' => true, //if false, default image won't be used
        );

        $params = array_merge($def_params, $params);

        $size = '';
        if(!isset($params['size']))
        {
            $size = Media::ORIGIN;
        }
        else
        {
            $constant = strtoupper($params['size']);
            eval('$size = (defined(\'Media::'.$constant.'\') ? Media::'.$constant.' : \'\');');
            if(empty($size))
            {
                throw new Exception('No constant name \''.$constant.'\' in Media class in smarty function image');
            }
        }

        $dir = Media::DEFAULT_DIR;
        if(isset($params['id']))
        {
            $dir = $params['id'];
        }
        
        $file_url = $this->getImageUrl($params, $dir, $size);
       
        return $file_url . (!empty($params['rand']) ? '?' . rand() : '');
    }
    /**
     * Get image url. Method check for file exsist
     * @param array $params
     * @param string $dir
     * @param string $size
     * @return string 
     */
    private function getImageUrl($params, $dir, $size)
    {
       
        $img_dir = App::view()->dir;
        
        // file dir
        $d = '';
        // file url
        $u = '';

        if(!empty($params['video']))
        {
            $d =  App::controller()->video_dir;
            $u = App::controller()->{$params['section'].'_video_url'};
        }
        else
        {
            $d = App::controller()->img_dir;
            $u = App::controller()->{$params['section'].'_url'};
        }
        $file =  $d . $img_dir[$params['section']] . DS . $dir . DS . $size . '.' . Media::EXT;
        $file_url =  $u . $dir . '/' . $size . '.' . Media::EXT;
  
        if(!File::checkExist($file))
        {
            if ($params['need_default'])
            {
                $file =  $d . $img_dir[$params['section']] . DS . Media::DEFAULT_DIR . DS . $size . '.' . Media::EXT;
                if(!File::checkExist($file))
                {
                    if(!empty($params['video']))
                    {
                        $u = App::controller()->video_url;
                    }
                    else
                    {
                        $u = App::controller()->img_url;
                    }
                    $file_url = $u . Media::DEFAULT_DIR . '/' . $size . '.' . Media::EXT;
                }
                else
                {
                    $file_url = $u . Media::DEFAULT_DIR . '/' . $size . '.' . Media::EXT;
                }
                
                $params['rand'] = false; //not use rand for default images
            }
            else
            {
                return false;
            }
        }
        if(isset($params['path']) && $params['path'])
        {
            return $file;
        }
        return $file_url;
    }
    
    public final function dateDiff($params, $template)
    {
        if(!isset($params['date']))
        {
            throw new InternalException('empty \'date\' param in date_diff plugin');
        }
        
        $now = date("Y-m-d H:i:s");
        if($params['date'] < $now) // past - ago
        {
            if(!empty($params['dir']))
                return 'ago';
            
            $date = $this->dateDiffCalc($now, $params['date']);
            $arr = explode(' ', $date);
            if(!empty($params['stay']))
                return rtrim(strtolower(array_pop($arr)), 's');
            
            return $date.' '.App::lang()->get('date_diff', 'ago');
        }
        else    // future - in
        {
            if(!empty($params['dir']))
                return 'in';
            $date = $this->dateDiffCalc($params['date'], $now);
            $arr = explode(' ', $date);
            if(!empty($params['stay']))
                return rtrim(strtolower(array_pop($arr)), 's');
            
            return App::lang()->get('date_diff', 'in').' '.$date;
        }
    }
    
    private function dateDiffCalc($d1, $d2)
    {
        $diff = abs(strtotime($d1) - strtotime($d2));

        $years = floor($diff / (365*60*60*24));
        if($years != 0) return $this->pluralize($years, "year");
        $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
        if($months != 0) return $this->pluralize($months, "month");
        $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
        if($days != 0) return $this->pluralize($days, "day");
        $hours = floor(($diff - $years*365*60*60*24 - $months*30*60*60*24 - $days*60*60*24)/(60*60));
        if($hours != 0) return $this->pluralize($hours, "hour");
        $minutes = floor(($diff - $years*365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/60);
        if($minutes != 0) return $this->pluralize($minutes, "minute");
        $seconds = floor($diff - $years*365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minutes*60);
        if($seconds != 0) return $this->pluralize($seconds, "second");
        return App::lang()->get('date_diff', 'just_now');
    }
    
    private function pluralize($count, $text) 
    {
        $lang_var = (($count == 1) ? $text : ("${text}s"));
        return $count .' '. App::lang()->get('date_diff', $lang_var);
    }
    
    public final function tweetPickTip($params, $template)
    {
        if(!isset($params['data']))
        {
            throw new InternalException('empty \'data\' param in tweet_pick_tip plugin');
        }
        
        $data = $params['data'];
//        @140picks NBA pick #celtics in #celtics at #lakers
//        @140picks NHL pick #canucks in #redwings at #canucks
//        @140picks MLB pick #celtics in #redsox at #yankees
//        @140picks NFL pick #cowboys in #cowboys at #buccanneers
//        @140picks soccer pick #mcfc in #mufc v #mcfc
        if($data['sport_id'] == 1)
        {
            $sport = strtolower($data['sport']);
            $vs = 'v';
        }
        else
        {
            $sport = $data['sport'];
            $vs = 'at';
        }
        
//        $strs[] = '@'.Config::TWITTER_NAME.' '.$sport.' pick <span>#'.$data['team1_alias'].'</span> in <span>#'.$data['team1_alias'].'</span> '.$vs.' <span>#'.$data['team2_alias'].'</span>';
        $strs[] = $this->makeTip($sport, $data['team1_alias'], $data['team1_alias'], $vs, $data['team2_alias']);
//        $strs[] = '@'.Config::TWITTER_NAME.' '.$sport.' pick <span>#'.$data['team2_alias'].'</span> in <span>#'.$data['team1_alias'].'</span> '.$vs.' <span>#'.$data['team2_alias'].'</span>';
        $strs[] = $this->makeTip($sport, $data['team2_alias'], $data['team1_alias'], $vs, $data['team2_alias']);
        if($data['sport_id'] == 1)
        {
//            $strs[] = '@'.Config::TWITTER_NAME.' '.$sport.' pick <span>#draw</span> in <span>#'.$data['team1_alias'].'</span> '.$vs.' <span>#'.$data['team2_alias'].'</span>';
            $strs[] = $this->makeTip($sport, 'draw', $data['team1_alias'], $vs, $data['team2_alias']);
        }
        
        return $this->includeElement(array('file'=>'tweet_pick_tip', 'tips'=>$strs), $template);
    }
    
    //https://twitter.com/intent/tweet?source=webclient&text=%40140picks+soccer+pick+%23getafe+in+%23getafe+v+%23levante
    private function makeTip($sport,$pick,$team1,$vs,$team2)
    {
        $title = '@'.Config::TWITTER_NAME.' '.$sport.' pick <span>#'.$pick.'</span> in <span>#'.$team1.'</span> '.$vs.' <span>#'.$team2.'</span>';
        $url = 'https://twitter.com/intent/tweet?source=webclient&amp;text='.urlencode(str_replace(array('<span>', '</span>'),'',$title));
        return '<a href="'.$url.'" target="_blank" title="'.str_replace(array('<span>', '</span>'),'',$title).'">'.$title.'</a>';
    }
}

/* End of file SmartyPlugins.php */
/* Location: ./class/common/SmartyPlugins.php */
?>