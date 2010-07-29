<?php

class Home extends SS_Admin_Controller {
	
	function index()
	{	
		$this->load->view('admin/home/index');
	}
	
	function update()
	{
		// load goodform
		$this->load->library('goodform');
		
		// load goodform dm extension in site model
		$this->site->load_extension('goodform');
				
		// build update site form
		$this->site->form($this->goodform);
		
		// check for POST requst
		if($this->input->post('submit'))
		{
			$related = $this->site->post_form($this->goodform);
			
			if($this->site->save($related))
			{
				$this->oi->add_success('Site Updated');
			}
			else
			{
				$this->oi->add_error('Please correct the validation errors.');
			}
			
			// update goodform with posted values and error messages
			$this->site->update_form($this->goodform);		
		}
		
		// add submit button
		$this->goodform
			->clear()
			->button(
				array(
					'name'	=> 'submit',
					'value'	=> 'submit',
					'text'	=> 'Save',
					'class'	=> 'button submit'
				)
			);
		
		// generate form HTML
		$data['form'] = $this->goodform->generate();
	
		$this->load->view('admin/home/update', $data);
	}
}

/* End of file home.php */
/* Location: ./system/application/controllers/home.php */