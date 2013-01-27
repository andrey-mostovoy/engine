<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * @package		Base
 * @author		amostovoy
 * @copyright	Copyright (c) 2010, Qualium-Systems, Inc.
 * @version		1.2
 * @since		Version 1.1
 * @filesource
 */

/****************************************
 *			DO NOT EDIT					*
 ****************************************/

/**
 * class Paginator
 * containing methods and properties for
 * create array of paginations
 *
 * @package		Base
 * @author		amostovoy
 */
class Paginator
{
	/**
	 * num of items on one page
	 * @var array
	 */
	private $_items_per_page = array();
    /**
	 * choose variants of item per page to display
	 * @var array
	 */
	private $_choose_items_per_page = array();
	/**
	 * num of total result
	 * @var array
	 */
	private $_items_total = array();
	/**
	 * current page num
	 * @var array
	 */
	private $_current_page = array();
    /**
     * back page num
     * @var array
     */
    private $_back_page = null;
	/**
	 * num of pages to display
	 * @var int
	 */
	public $mid_range;
    /**
     * model for paginate
     * @var string 
     */
    private $paging_model = 'default';
	/**
	 * Current paging set name
	 * If we have more than one paging on page set it whatewer you want (but without '-')
	 * to customise current paging
	 * @var string
	 */
	public $paging_name = 'main';
	/**
	 * num of total pages
	 * @var array
	 */
	private $_num_pages = array();
	/**
	 * array with pagination information
	 * @var array
	 */
	private $_return;
	/**
	 * array of existings paging
	 * @var array
	 */
	private $_pagings;
	/**
	 * default item per page
	 * @var int
	 */
	private $_default_ipp = Config::NUMPAGE_MAIN;
	/**
	 * request query uri
	 * @var array
	 */
	private $_query_string = array();
	/**
	 * base link to page
	 * @var array
	 */
    private $_href = array();
    /**
     * language variables for paging
     * @var array
     */
    private $lang = null;
    /**
     * default language variables for paging
     * @var array
     */
    private static $default_lang = array(
        'prev'          => 'Prev',
        'prev_range'    => '...',
        'next'          => 'Next',
        'next_range'    => '...',
        'first'         => 'First',
        'last'          => 'Last',
        'all'           => 'All',
        'visible_row'   => 'Visible row'
    );

	/**
	 * name in SESSION where saves paging information
	 */
	const SESSION_NAME = 'paging';
    const SESSION_NAME_PAGES = 'pages';
    const SESSION_NAME_BACK = 'back';
    const SESSION_NAME_IPP = 'ipp';

    /**
     * initialize base parameters
     * 
     * @param array $paging_lang array with language variables for paging
     * @param int $total total num of result
     * @param string $href base link to page
     * @param int $items_per_page num of items per page by default
     * @param string $page current page num
     * @param array $ipp num of items per page set by user through select choose
     * @param string $paging_model model name for paging instance
     * @param string $paging_name paging name
     * @param int $mid_range num of page links to display in paging row
     */
	public function __construct( 
            $paging_lang=null,
            $total=0,
            $href='',
            $items_per_page=10,
            $page=null,
            $ipp=null,
            $paging_model='',
            $paging_name='',
            $mid_range=10 )
	{
        // set current paging name or use by default
        if(!empty($paging_name))
        {
            $this->setPagingName($paging_name);
        }
        if(!empty($paging_model))
        {
            $this->setPagingModel($paging_model);
        }
        
        // get and set current page number
		if (isset($_SESSION[self::SESSION_NAME][$this->getPagingModel()][self::SESSION_NAME_PAGES]) 
                && !empty($_SESSION[self::SESSION_NAME][$this->getPagingModel()][self::SESSION_NAME_PAGES])
            )
			$this->_current_page = $_SESSION[self::SESSION_NAME][$this->getPagingModel()][self::SESSION_NAME_PAGES];

		$this->setCurrentPage($page, true);

        // get and set current items per page
        if (isset($_SESSION[self::SESSION_NAME][$this->getPagingModel()][self::SESSION_NAME_IPP])
            && !empty($_SESSION[self::SESSION_NAME][$this->getPagingModel()][self::SESSION_NAME_IPP])
            && empty($this->_items_per_page)
            )
			$this->_items_per_page = $_SESSION[self::SESSION_NAME][$this->getPagingModel()][self::SESSION_NAME_IPP];

        if(!empty($ipp))
            $this->setItemPerPage( $ipp, true );
        if(empty($this->_items_per_page))
            $this->setItemPerPage( $items_per_page, true );
        
        if('all' == $this->getCurrentPage())
        {
            $this->setItemPerPage('all', true);
        }

        // collect other params
		$this->setTotalItems($total, true);
		$this->mid_range = $mid_range;
        $this->setCurrentHref($href, true);
//		$this->_href = $href;
        $this->lang = (!empty($paging_lang)?$paging_lang:self::$default_lang);
	}

	/**
	 * set current page
	 * if want set current page to special paging (if more than one on page)
	 * give string in format paging_name-page_num
	 * @param string	$page				page number
	 * @param boolean	$isSetPagingName	if true - save page num to current page
	 */
	public final function setCurrentPage($page, $isSetPagingName=false)
	{
		$this->_setVar($this->_current_page, $page, $isSetPagingName);
        
        $_SESSION[self::SESSION_NAME][$this->getPagingModel()][self::SESSION_NAME_PAGES] = $this->_current_page;
	}
    
    /**
	 * set current href page
	 * if want set href to special paging (if more than one on page)
	 * give string in format paging_name-page_num
	 * @param string	$href				page href
	 * @param boolean	$isSetPagingName	if true - save page num to current page
	 */
	public final function setCurrentHref($href, $isSetPagingName=false)
	{
        $this->_href[$this->paging_name] = $href;
	}

    /**
     * set back page num
     */
    public final function setBackPage()
    {
        $this->_setVar($this->_back_page, $this->getCurrentPage());
        $_SESSION[self::SESSION_NAME][$this->getPagingModel()][self::SESSION_NAME_BACK] = $this->_back_page;
    }

	/**
	 * Set item per page for current paging
	 * if want set item per page to special paging (if more than one on page)
	 * give string in format paging_name-items_per_page
	 * @param string	$ipp				items per page
	 * @param boll		$isSetPagingName	if true - save page num to current page
	 */
	public final function setItemPerPage($ipp, $isSetPagingName=false)
	{
		$this->_setVar($this->_items_per_page, $ipp, $isSetPagingName);

        $_SESSION[self::SESSION_NAME][$this->getPagingModel()][self::SESSION_NAME_IPP] = $this->_items_per_page;
	}

	/**
	 * set total result name for current paging
	 * if want set total number to special paging (if more than one on page)
	 * give string in format paging_name-total_number
	 * @param string	$total				total result number
	 * @param bool		$isSetPagingName	if true - save page num to current page
	 */
	public final function setTotalItems($total, $isSetPagingName=false)
	{
		$this->_setVar($this->_items_total, $total, $isSetPagingName);
	}

    /**
	 * set choose variant of item per page display
	 * @param array	$variants
     * @param bool $isSetPagingName	if true - save page num to current page
	 */
	public final function setChooseIpp($variants, $isSetPagingName=true)
	{
		$this->_setVar($this->_choose_items_per_page, $variants, $isSetPagingName);
	}
    
    /**
     * set current paging model
     */ 
    public final function setPagingModel($paging_model)
    {
        if(false !== strpos($paging_model, 'model'))
            $this->paging_model = $paging_model;
        else
            $this->paging_model = 'model'.ucfirst($paging_model);
    }
    /**
     * set current paging name
     */ 
    public final function setPagingName($paging_name)
    {
        $this->paging_name = $paging_name; 
        if (!isset($this->_current_page[$paging_name]))
        {
            $this->setCurrentPage(1, true);
        }
        if (!isset($this->_href[$paging_name]))
        {
            $this->setCurrentHref($this->getCurrentHref('main'),true);
        }
        if (!isset($this->_choose_items_per_page[$paging_name]))
        {
            $this->setChooseIpp($this->getChooseIpp('main'),true);
        }
        
        if (!isset($this->_items_per_page[$paging_name]))
        {
            $this->setItemPerPage($this->_default_ipp);
        } 
    }
    
	/**
	 * Set current page or items per page or total items various on given array and value
	 * @param array $r_var			array of existing current pages, items per page or total items
	 * @param string $value			value to add in given array
	 * @param bool $isSetPagingName	if true - save page num to current page
	 */
	private function _setVar(&$r_var, $value, $isSetPagingName=false)
	{
		if (is_string($value) && strpos($value, '-') !== false)
		{
			$value = explode('-', $value);
			if ($isSetPagingName)
				$this->paging_name = $value[0];
			$r_var[$this->paging_name] = $value[1];
		}
		else
			$r_var[$this->paging_name] = $value;
	}

	/**
	 * Retrive class variables for current paging name
	 * @param string $var	class variable name
	 * @param string $paging_name	paging name from that need retrive data
	 * @return string return value, containing in variable for current paging
	 */
	private function _getVar($var, $paging_name=null)
	{
		if (empty($paging_name))
			$paging_name = $this->paging_name;

		return isset($var[$paging_name]) ? $var[$paging_name] : null;
	}

    /**
     * Get current paging model
     * @return string
     */ 
    public final function getPagingModel()
    {
        return $this->paging_model;
    }
    /**
     * Get current paging name
     * @return string
     */
    public final function getPagingName()
    {
        return $this->paging_name;
    }
	/**
	 * Get current page for paging name
	 * @param string $paging_name
	 * @return string
	 */
	public final function getCurrentPage($paging_name=null)
	{
		return $this->_getVar($this->_current_page, $paging_name);
	}
    
    /**
	 * Get current href for paging name
	 * @param string $paging_name
	 * @return string
	 */
	public final function getCurrentHref($paging_name=null)
	{
		return $this->_getVar($this->_href, $paging_name);
	}

    /**
     * Get back page number
     * @param string $paging_name
     * @return string
     */
    public final function getBackPage($paging_name=null)
    {
        if(empty($this->_back_page))
        {
            $this->_back_page = $_SESSION[self::SESSION_NAME][$this->getPagingModel()][self::SESSION_NAME_BACK];
        }
        return $this->_getVar($this->_back_page, $paging_name);
    }

	/**
	 * Get items per page number for paging name
	 * @param string $paging_name
	 * @return string
	 */
	public final function getItemPerPage($paging_name=null)
	{
		return $this->_getVar($this->_items_per_page, $paging_name);
	}

	/**
	 * Get total items number for paging name
	 * @param string $paging_name
	 * @return string
	 */
	public final function getTotalItems($paging_name=null)
	{
		return $this->_getVar($this->_items_total, $paging_name);
	}
    /**
     * Get array of variants of num pages on page
     * @param string $paging_name
     * @return array 
     */
	public final function getChooseIpp($paging_name=null)
	{
		return $this->_getVar($this->_choose_items_per_page, $paging_name);
	}

	/**
	 * return total num of pages
	 * @return int
	 */
	public function getLastNoEmptyResultPage()
	{
        if(isset($this->_items_total[$this->paging_name]) && $this->_items_per_page[$this->paging_name])
            $last_page = ceil($this->_items_total[$this->paging_name] / $this->_items_per_page[$this->paging_name]);
        else
            $last_page = false;
		return $last_page ? $last_page : 1;
	}

	/**
	 * return array for formation sql LIMIT string
	 * @param integer $items_per_page
	 * @return array
	 */
	public function getLimitArray($items_per_page=null)
	{
		if (!empty($items_per_page))
			$this->setItemPerPage(intval($items_per_page)); 
        if( $this->getItemPerPage() == 'all' )
        {
            return 'all';
        } 

		return array(
            $this->getCurrentPage() * $this->getItemPerPage() - $this->getItemPerPage(),
            $this->getItemPerPage()
        );
	}

    public function formNumPages()
    {
        if($this->getItemPerPage() == 'all')
		{
			$this->_num_pages[$this->paging_name] = 1;
		}
		else
		{
			if(!is_numeric($this->getItemPerPage()) 
                || $this->getItemPerPage() <= 0)
            {
                $this->setItemPerPage($this->_default_ipp);
            }
			$this->_num_pages[$this->paging_name] = $this->getLastNoEmptyResultPage();
		}
    }
	/**
	 * create paging information
	 */
	public function paginate()
	{
		$this->_return = array();

//		if(isset($_GET['ipp']) && $_GET['ipp'] == 'All')
        
        $this->formNumPages();
        
//		$this->_current_page = (int) $_GET['page']; // must be numeric > 0
		if($this->getCurrentPage() < 1 || !is_numeric($this->getCurrentPage())) $this->setCurrentPage(1);
		if($this->getCurrentPage() > $this->_num_pages[$this->paging_name]) $this->setCurrentPage( $this->_num_pages[$this->paging_name] );
		$prev_page = $this->getCurrentPage()-1;
		$next_page = $this->getCurrentPage()+1;

		if($_GET)
		{
			$args = explode("&",$_SERVER['QUERY_STRING']);
			// stop generate notice warning
            $this->_query_string[$this->paging_name] = '';
			foreach($args as $arg)
			{
				$keyval = explode("=",$arg);
				if($keyval[0] != "page" && $keyval[0] != "ipp") $this->_query_string[$this->paging_name] .= "&" . $arg;
			}
		}

		if($_POST)
		{
			// stop generate notice warning
			$this->_query_string[$this->paging_name] = '';
			foreach($_POST as $key=>$val)
			{
				if($key != "page" && $key != "ipp") $this->_query_string[$this->paging_name] .= "&$key=$val";
			}
		}        

		if($this->_num_pages[$this->paging_name] > 1)
		{
            // all
			if ($this->getCurrentPage() != 'all')
			{
				$this->_return['all']['href'] = $this->getCurrentHref().'/page/'.(!empty($this->paging_name)?$this->paging_name.'-':'').'all';
			}
			$this->_return['all']['text'] = $this->lang['all'];
            
            // prev
			if ($this->getCurrentPage() != 1)
			{
				$this->_return['prev']['href'] = $this->getCurrentHref().'/page/'.(!empty($this->paging_name)?$this->paging_name.'-':'').$prev_page;
			}
			$this->_return['prev']['text'] = $this->lang['prev'];

            // calc range
			$this->start_range = $this->getCurrentPage() - floor($this->mid_range/2);
			$this->end_range = $this->getCurrentPage() + floor($this->mid_range/2);

			if($this->start_range <= 0)
			{
				$this->end_range += abs($this->start_range)+1;
				$this->start_range = 1;
			}
			if($this->end_range > $this->_num_pages[$this->paging_name])
			{
				$this->start_range -= $this->end_range-$this->_num_pages[$this->paging_name];
				$this->end_range = $this->_num_pages[$this->paging_name];
			}
			$this->range = range($this->start_range,$this->end_range);

            // first
            if ($this->getCurrentPage() != 1)
			{
				$this->_return['first']['href'] = $this->getCurrentHref().'/page/'.(!empty($this->paging_name)?$this->paging_name.'-':'').'1';
			}
            $this->_return['first']['text'] = $this->lang['first'];

            // pages
			for($i=1;$i<=$this->_num_pages[$this->paging_name];$i++)
			{
				if($this->range[0] > 2 && $i == $this->range[0])
				{					
					$this->_return['pages'][$i-1]['href'] = $this->getCurrentHref().'/page/'.(!empty($this->paging_name)?$this->paging_name.'-':'').($i-1);
					$this->_return['pages'][$i-1]['text'] = $this->lang['prev_range'];
				}
				// loop through all pages. if first, last, or in range, display
				if($i==1 || $i==$this->_num_pages[$this->paging_name] || in_array($i,$this->range))
				{
					if ($i != $this->getCurrentPage())
					{
						$this->_return['pages'][$i]['href'] = $this->getCurrentHref().'/page/'.(!empty($this->paging_name)?$this->paging_name.'-':'').$i;
					}
					$this->_return['pages'][$i]['text'] = $i;
				}
				if($this->range[$this->mid_range-1] < $this->_num_pages[$this->paging_name]-1 && $i == $this->range[$this->mid_range-1])
				{
					$this->_return['pages'][$i+1]['href'] = $this->getCurrentHref().'/page/'.(!empty($this->paging_name)?$this->paging_name.'-':'').($i+1);
					$this->_return['pages'][$i+1]['text'] = $this->lang['next_range'];
                    // to jump to last
                    $i = $this->_num_pages[$this->paging_name]-1;
				}
			}
            // next
			if ($this->getCurrentPage() != $this->_num_pages[$this->paging_name])
			{
				$this->_return['next']['href'] = $this->getCurrentHref().'/page/'.(!empty($this->paging_name)?$this->paging_name.'-':'').$next_page;
			}
			$this->_return['next']['text'] = $this->lang['next'];

            // last
            if ($this->getCurrentPage() != $this->_num_pages[$this->paging_name])
			{
				$this->_return['last']['href'] = $this->getCurrentHref().'/page/'.(!empty($this->paging_name)?$this->paging_name.'-':'').$this->_num_pages[$this->paging_name];
			}
            $this->_return['last']['text'] = $this->lang['last'];
		}
        
        $this->getPagingInfo();
        
        if(!empty($this->_choose_items_per_page[$this->paging_name]))
        {
            $this->_return['ipp'] = $this->formatChooseIpp();
            $this->_return['c_ipp'] = $this->getItemPerPage($this->paging_name);
        }
        $this->_pagings[$this->getPagingName()] = $this->_return;
	}

    /**
     * Return array of choose items per page display elements
     * @param string $paging_name
     * @return array
     */
    private function formatChooseIpp($paging_name=null)
    {
        if (empty($paging_name))
			$paging_name = $this->paging_name;

        return array_combine($this->_choose_items_per_page[$paging_name], $this->_choose_items_per_page[$paging_name])
               + array('all' => $this->lang['all']);
    }

	/**
	 * Get additional information for paging. For example, current page etc.
	 * @param string $paging_name paging name from
	 */
	public function getPagingInfo($paging_name=null)
	{
		if (empty($paging_name))
			$paging_name = $this->paging_name;

        if('all' == $this->getItemPerPage($paging_name))
        {
            $this->formNumPages();
            $from = '1';
            $to = $this->_num_pages[$this->paging_name];
        }
        else
        {
            $from = $this->getCurrentPage($paging_name)*$this->getItemPerPage($paging_name) - $this->getItemPerPage($paging_name)+1;
            $to = $this->getCurrentPage($paging_name)*$this->getItemPerPage($paging_name);
            if($to > $this->getTotalItems($paging_name))
            {
                $to = $this->getTotalItems($paging_name);
            }
        }
		$this->_return['info'] = array(	
                        'total_records'	=> $this->getTotalItems($paging_name),
						'current_page'	=> $this->getCurrentPage($paging_name),
                        'page_name'     => $this->getPagingName(),
                        'href'          => $this->getCurrentHref($paging_name),
						'total_pages'	=> $this->_num_pages[$paging_name],
                        'lang'          => $this->lang,
                        'shown'         => array(
                            'from'  => $from,
                            'to'    => $to,
                        ),
				);
	}

	/**
	 * return pagination information
	 * @return array
	 */
	public final function displayPages()
	{
		return $this->_pagings;
//		return count(self::$_pagings)>1 ? self::$_pagings : $this->_return;
	}
}

/* End of file Paginator.php */
/* Location: ./class/Base/Paginator.php */
?>