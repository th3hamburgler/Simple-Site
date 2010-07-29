<?php

class Templates extends SS_Admin_Controller {
	
   /*
	* Display a list of templates in the site
	*
	* @access	public
	* @return	view
	*/
	function index()
	{
		// get templates in site
		$templates = $this->site->template->get();
		
		// check we have templates
		if($templates->exists())
		{
			// load badtable
			$this->load->library('badtable');
		
			// load badtable dm extension in template model
			$templates->load_extension('badtable');
		
			// define table columns
			$columns = array(
				'name',
				'description',
				'zones',
				'updated',
				'Action' => anchor(
					'admin/templates/update/{ID}',
					'Update'
				)
			);
		
			// get columns from config if exists and build table
			$templates->table($this->badtable, $columns);
		
			$data['table'] = $this->badtable->generate();
		}
		else
		{
			$data['table'] = '<p class="align-center">No templates Exist</p>';
		}
	
		$this->load->view('admin/templates/index', $data);
	}

   /*
	* Add a new template record
	*
	* @access	public
	* @return	view
	*/
	function create()
	{
		// create template model
		$template = new Template();
	
		// load goodform
		$this->load->library('goodform');
		
		// load goodform dm extension in template model
		$template->load_extension('goodform');
		
		// defined form fields
		$fields = array(
			'name',
			'description',
			'zones'
		);		
		
		// build template form
		$template->form($this->goodform, $fields);
		
		// check for POST requst
		if($this->input->post('submit'))
		{
			// update template fields
			$related = $template->post_form($this->goodform);
		
			// add site to related models array
			$related[] = $this->site;
		
			// attempt to save new template to site
			if($template->save($related))
			{
				$this->oi->add_success('template Added');
				
				// redirect back to template index
				redirect('admin/templates/');
			}
			else
			{
				$this->oi->add_error('Please correct the validation errors.');
			}
			
			// update goodform with posted values and error messages
			$template->update_form($this->goodform);			
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
	
		$this->load->view('admin/templates/create', $data);
	}

   /*
	* Update a template record
	*
	* @access	public
	* @param	id
	* @return	view
	*/
	function update($template_id)
	{
		// create template model and load by id
		$template = new template($template_id);
			
		// load goodform
		$this->load->library('goodform');
		
		// load goodform dm extension in template model
		$template->load_extension('goodform');
		
		// defined form fields
		$fields = array(
			'name',
			'description',
			'zones'
		);	
				
		// build template form
		$template->form($this->goodform, $fields);
		
		// check for POST requst
		if($this->input->post('submit'))
		{
			// update template fields
			$template->post_form($this->goodform);
		
			// attempt to save new template to site
			if($template->save())
			{
				$this->oi->add_success('template Updated');
			}
			else
			{
				$this->oi->add_error('Please correct the validation errors.');
			}
			
			// update goodform with posted values and error messages
			$template->update_form($this->goodform);		
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
	
		$this->load->view('admin/templates/update', $data);
	}
}

/* End of file home.php */
/* Location: ./system/application/controllers/home.php */