<?php

class SS_Admin_Controller extends MY_Controller {
	
   /**
	* Controller constructor
	*
	* Sets up HTML page head
	*
	* @access	public
	* @return	void
	*/
	public function __construct()
	{
		parent::__construct();
	
		$this->wrapup->set_title('Simple Site');
	
		$this->wrapup->add_css('admin/site.css');
		
		$this->wrapup->add_js('http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js');
		
		// create global site model
		$this->site = new Site();
		
		// load site record from db
		$this->site->get();
	}
	
   /**
	* Adds markitup support to page element
	*
	* @access	public
	* @param	void
	* @return	void
	*/
	public function add_markitup($selector='.markitup')
	{
		// Mark it up
		$this->wrapup->add_js('../markitup/jquery.markitup.js');
		$this->wrapup->add_js('../markitup/sets/html/set.js');
		$this->wrapup->add_css('../markitup/skins/macosx/style.css');		
		$this->wrapup->add_css('../markitup/sets/html/style.css');
		
		$this->wrapup->add_jquery('var base_url = "'.base_url().'"');
		$this->wrapup->add_jquery('$("'.$selector.'").markItUp(mySettings);');
	}
}

/* End of file SS_Admin_Controller.php */
/* Location: ./system/application/libraries/SS_Admin_Controller.php */