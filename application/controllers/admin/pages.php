<?php

class Pages extends SS_Admin_Controller {
	
   /*
	* Display a list of pages in the site
	*
	* @access	public
	* @return	view
	*/
	function index()
	{
		// get pages in site
		$pages = $this->site->page->get();
		
		// check we have pages
		if($pages->exists())
		{
			// load badtable
			$this->load->library('badtable');
		
			// load badtable dm extension in page model
			$pages->load_extension('badtable');
		
			// define table columns
			$columns = array(
				'title',
				'slug',
				'updated',
				'Action' => anchor(
					'admin/pages/update/{ID}',
					'Content'
				).' &middot; '.anchor(
					'admin/pages/partials/{ID}',
					'Partials'
				)
			);
		
			// get columns from config if exists and build table
			$pages->table($this->badtable, $columns);
		
			$data['table'] = $this->badtable->generate();
		}
		else
		{
			$data['table'] = '<p class="align-center">No Pages Exist</p>';
		}
	
		$this->load->view('admin/pages/index', $data);
	}

   /*
	* Add a new page record
	*
	* @access	public
	* @return	view
	*/
	function create()
	{
		// add markitup support
		$this->add_markitup();
		
		// create page model
		$page = new Page();
	
		// load goodform
		$this->load->library('goodform');
		
		// load goodform dm extension in page model
		$page->load_extension('goodform');
		
		// defined form fields
		$fields = array(
			'title',
			'slug',
			'content',
			'parent_page',
			'template',
		);		
		
		// build page form
		$page->form($this->goodform, $fields);
		
		// check for POST requst
		if($this->input->post('submit'))
		{
			// update page fields
			$related = $page->post_form($this->goodform);
		
			// add site to related models array
			$related[] = $this->site;
		
			// attempt to save new page to site
			if($page->save($related))
			{
				$this->oi->add_success('Page Added');
				
				// redirect back to page index
				redirect('admin/pages/');
			}
			else
			{
				$this->oi->add_error('Please correct the validation errors.');
			}
			
			// update goodform with posted values and error messages
			$page->update_form($this->goodform);			
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
	
		$this->load->view('admin/pages/create', $data);
	}

   /*
	* Update a page record
	*
	* @access	public
	* @param	id
	* @return	view
	*/
	function update($page_id)
	{
		// add markitup support
		$this->add_markitup();
	
		// create page model and load by id
		$page = new Page($page_id);
			
		// load goodform
		$this->load->library('goodform');
		
		// load goodform dm extension in page model
		$page->load_extension('goodform');
		
		// defined form fields
		$fields = array(
			'title',
			'slug',
			'content',
			'parent_page',
			'template',
		);	
				
		// build page form
		$page->form($this->goodform, $fields);
		
		// check for POST requst
		if($this->input->post('submit'))
		{
			// update page fields
			$related = $page->post_form($this->goodform);
		
			// attempt to save new page to site
			if($page->save($related))
			{
				$this->oi->add_success('Page Updated');
			}
			else
			{
				$this->oi->add_error('Please correct the validation errors.');
			}
			
			// update goodform with posted values and error messages
			$page->update_form($this->goodform);			
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
	
		$this->load->view('admin/pages/update', $data);
	}

   /*
	* Update a page record
	*
	* @access	public
	* @param	id
	* @return	view
	*/
	function partials($page_id)
	{
		// create page model and load by id
		$page = new Page($page_id);
			
		// get the page template
		$template = $page->template->get();
		
		// check template supports partials
		if($template->supports_partials())
		{
			
			// load goodform
			$this->load->library('goodform');
			
			// load goodform dmz extension
			$template->load_extension('goodform');
			
			
			// check for post request
			if($this->input->post('add_partial'))
			{
				// create new page_partial object
				$page_partial = new Page_Partial();
				
				// update page_partial fields
				$page_partial->zone = $this->input->post('zone');
				$page_partial->order = $this->input->post('order');
			
				// load related partial record
				$new_partial = new Partial($this->input->post('partial_id'));
				
				// save new page_partial to page and partial
				if($page_partial->save(array($new_partial, $page)))
				{
					$this->oi->add_success('New partial added to Zone '.number_to_alphabet($page_partial->zone));
				}
				else
				{
					$this->oi->add_success('Error adding Partial to Page. '.$page_partial->error->string);
				}
			}
			
			
			## New Partial Form ##
			
			$new_form = new GoodForm();
			
			// get all site partials
			$partials = $this->site->partial->get();
			
			// load goodform dmz extension
			$partials->load_extension('goodform');
			
			// wrap form in fieldset
			$new_form->fieldset('New Partial');
			
			// add dropdown to select partial
			$spec = array(
				'name'		=> 'partial_id',
				'label'		=> 'Partial',
				'options'	=> $partials->options(FALSE),
			);			
			$new_form->dropdown($spec);
						
			
			// add dropdown to select zone
			$spec = array(
				'name'		=> 'zone',
				'label'		=> 'Zone',
				'options'	=>$template->zone_options(),
			);			
			$new_form->dropdown($spec);
			
			
			// add text to define order
			$spec = array(
				'name'		=> 'order',
				'label'		=> 'Order',
				'size'		=> 2,
				'class'		=> 'small'
			);			
			$new_form->text($spec);
			
			// close fieldset
			$new_form->close_fieldset();
			
			// add submit button
			$spec = array(
				'name'	=> 'add_partial',
				'value'	=> 'submit',
				'text'	=> 'Add',
				'class'	=> 'button submit'
			);
			$new_form->button($spec);
			
			
			// check for post request
			if($this->input->post('update_partials'))
			{
				// look for any partials to delete
				$ids = $this->input->post('delete');
				
				if(!empty($ids))
				{
					$page_partials = new Page_Partial();
				
					$page_partials->where_in('id', $ids)->get();
					
					if($page_partials->exists())
					{
						if($page_partials->delete_all())
						{
							$this->oi->add_success('Removed '.pluralise(count($ids), 'Partial'));
						}
						else
						{
							$this->oi->add_error('Error deleting Page Partials: '.$page_partials->error->string);
						}
					}
					else
					{
						$this->oi->add_error('Could not find Partial records to delete');
					}					
				}
				
				// update partial order values
				$orders = $this->input->post('order');
				
				// get all partials linked to page
				$page_partials = $page->page_partials->get();
								
				foreach($page_partials->all as $page_partial)
				{
					if(isset($orders[$page_partial->id]))
					{
						// check if order is different
						if($page_partial->order != $orders[$page_partial->id])
						{
							// update order
							$page_partial->order = $orders[$page_partial->id];
							
							// save changes
							if($page_partial->save())
							{
								$this->oi->add_success('Partial order updated');
							}
							else
							{
								$this->oi->add_success('Error updating Partial order: '.$page_partial->error->string);							
							}
						}
					}
				}
			}
			
			
			## Update Partials Form ##
			
			$update_form = new GoodForm();
			
			// loop through zones creating a fieldset for each
			for($i = 0; $i < $template->zones; $i++)
			{
				// define fieldset legend
				$legend = 'Zone '.number_to_alphabet($i);
			
				$update_form->fieldset($legend);
				
				// get pages in this zone already
				$page_partials = $page->page_partials->order_by('order', 'ASC')->get_by_zone($i);
				
				// check partials exist
				if($page_partials->exists())
				{				
					foreach($page_partials->all as $page_partial)
					{					
						// load partial record
						$partial = $page_partial->partial->get();
										
						$update_form->label($partial, $partial.' '.$page_partial->id);
					
						$update_form->html('<span class="label">Delete</span>');
						
						$spec = array(
							'name'	=> 'delete['.$page_partial->id.']',
							'value'	=> 	$page_partial->id,
						);						
						$update_form->checkbox($spec);
						
						$spec = array(
							'name'	=> 'order['.$page_partial->id.']',							
							'size'		=> 2,
							'class'		=> 'small',
							'value'	=> 	$page_partial->order,				
						);						
						$update_form->text($spec);
						
						$update_form->clear();
					}
				}
				else
				{
					$update_form->html('<p><em>No Partials exist in '.$legend.' yet.</em></p>');
				}
			}
			
			// close last fieldset
			$update_form->close_fieldset();
			
			// add submit button
			$spec = array(
				'name'	=> 'update_partials',
				'value'	=> 'submit',
				'text'	=> 'Save',
				'class'	=> 'button submit'
			);
			$update_form->button($spec);
			
			$data['update_form'] 	= $update_form->generate(array('class' => 'update-partial-form'));
			
			$data['new_form'] 		= $new_form->generate();
		}
		else
		{
			$data['new_form'] = '';
			$data['update_form'] = '<p class="align-center">This Page Template does not have any Zones for Partials</p>';		
		}
				
	
		$this->load->view('admin/pages/partials', $data);
	}
}

/* End of file home.php */
/* Location: ./system/application/controllers/home.php */