<?php

class Navigations extends SS_Admin_Controller {
	
   /*
	* Display a list of navigations in the site
	*
	* @access	public
	* @return	view
	*/
	function index()
	{
		// get navigations in site
		$navigations = $this->site->navigation->get();
		
		// check we have navigations
		if($navigations->exists())
		{
			// load badtable
			$this->load->library('badtable');
		
			// load badtable dm extension in navigation model
			$navigations->load_extension('badtable');
		
			// define table columns
			$columns = array(
				'name',
				'description',
				'updated',
				'Action' => anchor(
					'admin/navigations/update/{ID}',
					'Update'
				).' &middot; '.anchor(
					'admin/navigations/items/{ID}',
					'Items'
				)
			);
		
			// get columns from config if exists and build table
			$navigations->table($this->badtable, $columns);
		
			$data['table'] = $this->badtable->generate();
		}
		else
		{
			$data['table'] = '<p class="align-center">No Navigation Menus Exist</p>';
		}
	
		$this->load->view('admin/navigations/index', $data);
	}

   /*
	* Add a new navigation record
	*
	* @access	public
	* @return	view
	*/
	function create()
	{
		// create navigation model
		$navigation = new navigation();
	
		// load goodform
		$this->load->library('goodform');
		
		// load goodform dm extension in navigation model
		$navigation->load_extension('goodform');
		
		// defined form fields
		$fields = array(
			'name',
			'description',
		);		
		
		// build navigation form
		$navigation->form($this->goodform, $fields);
		
		// check for POST requst
		if($this->input->post('submit'))
		{
			// update navigation fields
			$related = $navigation->post_form($this->goodform);
		
			// add site to related models array
			$related[] = $this->site;
		
			// attempt to save new navigation to site
			if($navigation->save($related))
			{
				$this->oi->add_success('Navigation Menu Added');
				
				// redirect back to navigation index
				redirect('admin/navigations/');
			}
			else
			{
				$this->oi->add_error('Please correct the validation errors.');
			}
			
			// update goodform with posted values and error messages
			$navigation->update_form($this->goodform);			
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
	
		$this->load->view('admin/navigations/create', $data);
	}

   /*
	* Update a navigation record
	*
	* @access	public
	* @param	id
	* @return	view
	*/
	function update($navigation_id)
	{
		// create navigation model and load by id
		$navigation = new navigation($navigation_id);
			
		// load goodform
		$this->load->library('goodform');
		
		// load goodform dm extension in navigation model
		$navigation->load_extension('goodform');
		
		// defined form fields
		$fields = array(
			'name',
			'description',
		);	
				
		// build navigation form
		$navigation->form($this->goodform, $fields);
		
		// check for POST requst
		if($this->input->post('submit'))
		{
			// update navigation fields
			$navigation->post_form($this->goodform);
		
			// attempt to save new navigation to site
			if($navigation->save())
			{
				$this->oi->add_success('navigation Updated');
			}
			else
			{
				$this->oi->add_error('Please correct the validation errors.');
			}
			
			// update goodform with posted values and error messages
			$navigation->update_form($this->goodform);		
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
	
		$this->load->view('admin/navigations/update', $data);
	}

   /*
	* Update the navigation items
	*
	* @access	public
	* @param	id
	* @return	view
	*/
	function items($navigation_id)
	{
		// create navigation model and load by id
		$navigation = new navigation($navigation_id);
			
		// load goodform
		$this->load->library('goodform');
		
		
		# Check for post request ##
		if($this->input->post('add_item'))
		{
			// get page by posted id
			$page = new Page($this->input->post('page_id'));
		
			// check page exists
			if($page->exists())
			{
				// create navigation item
				$nav_item = new Navigation_Item();
				
				// add order
				$nav_item->order = $this->input->post('order');
			
				// save item to page and navigation
				if($nav_item->save(array($navigation, $page)))
				{			
					$this->oi->add_success('New Page added to Navigation');
				}
				else
				{
					$this->oi->add_error('Error adding Page: '.$nav_item->error->string);
				}
			}
			else
			{
				$this->oi->add_error('Cant add, Page not found');
			}			
		}
		
		
		## Add New Item Form ##
		
		$new_form = new GoodForm();
		
		// wrap form in fieldset
		$new_form->fieldset('New Nav Item');
			
		// get all pages in site
		$pages = $this->site->pages->get();
		
		// load goodform extension
		$pages->load_extension('goodform');
		
		// add dropdown to select page
		$spec = array(
			'name'		=> 'page_id',
			'label'		=> 'Page',
			'options' 	=> $pages->options(FALSE)
		);		
		$new_form->dropdown($spec);
		
		// add text define order
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
			'name'	=> 'add_item',
			'value'	=> 'submit',
			'text'	=> 'Add',
			'class'	=> 'button submit'
		);
		$new_form->button($spec);
		
		
		
		// check for post request
		if($this->input->post('update_items'))
		{
			// look for any items to delete
			$ids = $this->input->post('delete');
			
			if(!empty($ids))
			{
				$items = new Navigation_Item();
			
				$items->where_in('id', $ids)->get();
				
				if($items->exists())
				{
					if($items->delete_all())
					{
						$this->oi->add_success('Removed '.pluralise(count($ids), 'Navigation Item'));
					}
					else
					{
						$this->oi->add_error('Error deleting Navigation Item: '.$items->error->string);
					}
				}
				else
				{
					$this->oi->add_error('Could not find Navigation Item records to delete');
				}					
			}
			
			// update item order values
			$orders = $this->input->post('order');
			
			// get all items linked to nav
			$items = $navigation->navigation_item->get();
							
			foreach($items->all as $item)
			{
				// check if order is different
				if($item->order != $orders[$item->id])
				{
					// update order
					$item->order = $orders[$item->id];
					
					// save changes
					if($item->save())
					{
						$this->oi->add_success('Navigation Item order updated');
					}
					else
					{
						$this->oi->add_success('Error updating Navigation Item order: '.$item->error->string);							
					}
				}
			}
		}
		
		## Update Items Form ##
		
		$update_form = new GoodForm();
		
		// wrap form in fieldset
		$update_form->fieldset('Update Nav Items');
			
		// get current items in the navigation
		$items = $navigation->navigation_items->order_by('order', 'ASC')->get();
		
		foreach($items->all as $item)
		{
			// get page
			$page = $item->page->get();
		
			$update_form->label($page, $page.' '.$item->id);
					
			$update_form->html('<span class="label">Delete</span>');
			
			$spec = array(
				'name'	=> 'delete['.$item->id.']',
				'value'	=> 	$item->id,
			);						
			$update_form->checkbox($spec);
			
			$spec = array(
				'name'	=> 'order['.$item->id.']',							
				'size'		=> 2,
				'class'		=> 'small',
				'value'	=> 	$item->order,				
			);						
			$update_form->text($spec);
			
			$update_form->clear();
		}
		
		// close fieldset
		$update_form->close_fieldset();
		
		$spec = array(
			'name'	=> 'update_items',
			'value'	=> 'submit',
			'text'	=> 'Save',
			'class'	=> 'button submit'
		);
		$update_form->button($spec);
		
		$data['update_form'] 	= $update_form->generate(array('class' => 'update-nav-item-form'));
		
		$data['new_form'] 		= $new_form->generate();
	
		$this->load->view('admin/navigations/items', $data);
	}
}

/* End of file home.php */
/* Location: ./system/application/controllers/home.php */