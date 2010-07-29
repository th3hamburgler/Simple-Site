<?php

class Partials extends SS_Admin_Controller {
	
   /*
	* Display a list of partials in the site
	*
	* @access	public
	* @return	view
	*/
	function index()
	{
		// get partials in site
		$partials = $this->site->partial->get();
		
		// check we have partials
		if($partials->exists())
		{
			// load badtable
			$this->load->library('badtable');
		
			// load badtable dm extension in partial model
			$partials->load_extension('badtable');
		
			// define table columns
			$columns = array(
				'name',
				'slug',
				'updated',
				'Action' => anchor(
					'admin/partials/update/{ID}',
					'Update'
				)
			);
		
			// get columns from config if exists and build table
			$partials->table($this->badtable, $columns);
		
			$data['table'] = $this->badtable->generate();
		}
		else
		{
			$data['table'] = '<p class="align-center">No partials Exist</p>';
		}
	
		$this->load->view('admin/partials/index', $data);
	}

   /*
	* Add a new partial record
	*
	* @access	public
	* @return	view
	*/
	function create()
	{
		// add markitup support
		$this->add_markitup();
		
		// create partial model
		$partial = new partial();
	
		// load goodform
		$this->load->library('goodform');
		
		// load goodform dm extension in partial model
		$partial->load_extension('goodform');
		
		// defined form fields
		$fields = array(
			'name',
			'content'
		);		
		
		// build partial form
		$partial->form($this->goodform, $fields);
		
		// check for POST requst
		if($this->input->post('submit'))
		{
			// update partial fields
			$related = $partial->post_form($this->goodform);
		
			// add site to related models array
			$related[] = $this->site;
		
			// attempt to save new partial to site
			if($partial->save($related))
			{
				$this->oi->add_success('partial Added');
				
				// redirect back to partial index
				redirect('admin/partials/');
			}
			else
			{
				$this->oi->add_error('Please correct the validation errors.');
			}
			
			// update goodform with posted values and error messages
			$partial->update_form($this->goodform);			
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
	
		$this->load->view('admin/partials/create', $data);
	}

   /*
	* Update a partial record
	*
	* @access	public
	* @param	id
	* @return	view
	*/
	function update($partial_id)
	{		
		// add markitup support
		$this->add_markitup();
		
		// create partial model and load by id
		$partial = new partial($partial_id);
			
		// load goodform
		$this->load->library('goodform');
		
		// load goodform dm extension in partial model
		$partial->load_extension('goodform');
		
		// defined form fields
		$fields = array(
			'name',
			'content',
		);	
				
		// build partial form
		$partial->form($this->goodform, $fields);
		
		// check for POST requst
		if($this->input->post('submit'))
		{
			// update partial fields
			$related = $partial->post_form($this->goodform);
		
			// attempt to save new partial to site
			if($partial->save($related))
			{
				$this->oi->add_success('partial Updated');
			}
			else
			{
				$this->oi->add_error('Please correct the validation errors.');
			}
			
			// update goodform with posted values and error messages
			$partial->update_form($this->goodform);			
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
	
		$this->load->view('admin/partials/update', $data);
	}
}

/* End of file home.php */
/* Location: ./system/application/controllers/home.php */