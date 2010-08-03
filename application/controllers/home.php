<?php

class Home extends SS_Public_Controller {

	public function index($slug=NULL)
	{
		$data = array();
		
		$data['site'] = $this->site;
		
		$this->site_head();
		
		// load navigation
		$data = $this->load_navigation($data);
		
		// load page helper
		$this->load->helper('page');
			
		if(!$slug)
			// load the home page
			$page = $this->site->home_page->get();
		else
			// load the page by slug
			$page = $this->site->page->where('slug', $slug)->get();
		
		if($page->exists())
		{
			// load partials
			$data = $this->load_partials($page, $data);
	
			// get template view
			$view = $page->template->view();
		
			$data['page'] = $page;
			
			$this->load->view('public/'.$view, $data);
			
		}
		else
		{
			log_message('error', 'No page found "'.$slug.'"');
		}
	}
	
	public function _remap($method=NULL)
	{
		// prevent passing index as parameter to index!
		if($method == 'index')
			$method = NULL;
	
		$this->index($method);
	}

   /**
	* Adds site common data to head element
	*
	* @access	public
	* @param	void
	* @return	void
	*/
	public function site_head()
	{
		// Set the doctype to XHTML Transitional
        $this->wrapup->set_doctype('t');
        
        // meta charset
        $this->wrapup->add_meta(array('http-equiv' =>'Content-type', 'content' => 'text/html;charset=UTF-8'));
        
        //$this->wrapup->add_meta(array('http-equiv' =>'X-UA-Compatible', 'content' => 'IE-8'));
        
        // Add CSS links
        $this->wrapup->add_css('public/style.css', 'screen, print');
        $this->wrapup->add_css('fancybox/jquery.fancybox-1.3.1.css', 'screen');
	}

   /**
	* method
	*
	* @access	private
	* @param	array
	* @return	void
	*/
	private function load_navigation($data)
	{
		// get all navigations in the site
		$navigations = $this->site->navigation->get();
		
		foreach($navigations->all as $navigation)
		{
			// array of nav items
			$links = array();
			
			// load pages
			$items = $navigation->navigation_item->order_by('order', 'ASC')->get();
			
			// loop through them
			foreach($items->all as $item)
			{			
				$item->page->get();
			
				$links[] = anchor('home/'.$item->page->slug, $item->page->title); 
			}
			
			// return as list
			$data[$navigation->slug()] = ul($links);
		}
		
		
		return $data;
	}

   /**
	* method
	*
	* @access	private
	* @param	object
	* @param	array
	* @return	array
	*/
	private function load_partials(&$page, $data)
	{
		// get template
		$page->template->get();
		
		// loop through template zones
		for($i=0; $i<$page->template->zones; $i++)
		{
			// get page partials by zone
			$page_partials = $page->page_partial->order_by('order', 'ASC')->get_by_zone($i);
			
			$partial_array = array();
			
			// loop throught partials
			foreach($page_partials->all as $page_partial)
			{
				// get partials
				$partial_array[] = $page_partial->partial->get();
			}
			
			// pass zone partials to view
			$data['zone_'.number_to_alphabet($i)] = $partial_array;
		}
		
		return $data;
	}
}

/* End of file home.php */
/* Location: ./system/application/controllers/home.php */