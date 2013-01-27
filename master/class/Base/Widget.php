<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * @package		Base
 * @subpackage	Extension
 * @author		amostovoy
 * @copyright	Copyright (c) 2011, Qualium-Systems, Inc.
 * @filesource
 */

/**
 * class Widget
 * content methods for build widget
 * 
 * @package		Base
 * @subpackage	Extension
 * @author		amostovoy
 */
class Widget
{
    /**
     * widget object
     * @var object 
     */
    private static $instance = null;
    /**
     * array with all widgets. containing:
     * - widget name    - this is a key for array. this array containing:
     *      - class     - additional class name to past in widget div
     *      - name      - display widget name
     *      - content   - array with model name to load and numbers of entries to display.
     *                      After this array will be replaced by actualy data. containing next:
     *          - 0     - model name to load
     *          - 1     - limit of data to retriev
     *          - 2     - specify method name to call to get content
     *      - file      - specify file name to fetch. if not set use widget name.
     * @var array
     */
    private $list;
    
    /**
     * class construct
     */
    private function __construct()
    {
        $this->fillWidgetList();
    }
    
    /**
     * Retrieve instance of widget object
     * @return object
     */
    public static function getInstance()
    {
        self::$instance === null and self::$instance = new self();
        return self::$instance;
    }
    
    /**
     * Fill array of available widgets
     */
    private function fillWidgetList()
    {
        $this->list = array(
            'humor'         => array(
                'class'     => 'w-scuba-humor',
                'name'      => 'Scuba Humor',
                'content'   => '1'
            ),
            'community'     => array(
                'class'     => 'w-community',
                'name'      => 'Community',
                'content'   => '1'
            ),
            'dive-news'     => array(
                'class'     => 'w-dive-news',
                'name'      => 'Dive News',
                'content'   => array('frontend/news', Config::NUMPAGE_WIDGET_NEWS),
                'file'      => 'dive_news'
            ),
            'travel-forums' => array(
                'class'     => 'w-travel-forums',
                'name'      => 'Travel Forums',
                'content'   => '1',
                'file'      => 'travel_forums'
            ),
            'travel-blogs'  => array(
                'class'     => 'w-travel-blogs',
                'name'      => 'Travel Blogs',
                'content'   => '1',
                'file'      => 'travel_blogs'
            ),
            'advertisement' => array(
                'class'     => 'w-advertisement',
                'name'      => 'Advertisement',
                'content'   => '1'
            ),
            'video'         => array(
                'class'     => 'w-video',
                'name'      => 'Video',
                'content'   => '1'
            ),
            'newsletter'    => array(
                'class'     => 'w-sdtn-e-newsletter',
                'name'      => 'SDTN E-Newsletter',
                'content'   => '1'
            ),
            'facebook'      => array(
                'class'     => 'w-find-us-on-facebook',
                'name'      => '',
                'content'   => true
            ),
            'blog'          => array(
                'class'     => 'w-sdtn-blog',
                'name'      => 'SDTN Blog',
                'content'   => array('frontend/blog', Config::NUMPAGE_WIDGET_BLOG)
            ),
            'google'        => array(
                'class'     => 'w-google-ads',
                'name'      => 'Google Ads',
                'content'   => true
            )
        );
    }
    
    /**
     * Get content for widget
     * @param string $model             Model name
     * @param int $limit                (d:null) Count of data
     * @return array 
     */
    private function getWidgetContent($info)
    {
        list($model, $limit, $method) = $info;
        if(empty($method))
        {
            $method = 'widgetContent';
        }
        return App::model($model, false)->$method($limit);
    }
    
    /**
     * Get widget info
     * @param string $widget widget name
     * @return array
     */
    public final function getWidget($widget)
    {
        if(!isset($this->list[$widget]))
        {
            throw new InternalException('Widget not found');
        }
        
        $widget = $this->list[$widget];
        
        if(is_array($widget['content']))
        {
            $widget['content'] = $this->getWidgetContent($widget['content']);
        }
        return $widget;
    }
}

/* End of file Widget.php */
/* Location: ./class/Widget.php */
?>