<?php

class SS_Public_Controller extends MY_Controller {
	
	function __construct()
	{
		parent::__construct();
	
		$this->wrapup->set_title('Simple Site');
	
		$this->wrapup->add_css('public/site.css');
		
		// create global site model
		$this->site = new Site();
		
		// load site record from db
		$this->site->get();
	}
}

/* End of file SS_Public_Controller.php */
/* Location: ./system/application/libraries/SS_Public_Controller.php */