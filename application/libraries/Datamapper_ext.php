<?php
/**
 * Data Mapper Extension Class
 *
 * @license		MIT License
 * @category	Models
 * @author		Jim Wardlaw
 * @link		http://www.artandsoul.co.uk
 */
class DataMapper_ext extends DataMapper {
	
   /**
	* Override the datamapper contruct class to
	* prevent this class autoloading
	*
	* @access	public
	* @param	string
	* @return	string
	*/
	public function __construct($id = NULL)
	{
		// do not auto load library
		if (get_class($this) == 'DataMapper_ext')
			return;
		
		parent::__construct($id);
		
		// if controller not set use the models plural
		if(!$this->controller)
			$this->controller = strtolower($this->table);
		
		// if action not set use 'update'
		if(!$this->action)
			$this->action = 'update';
	}

   /**
	* Returns capitalised singular of this model
	*
	* @access	public
	* @return	string
	*/
	public function singular()
	{
		return ucfirst($this->model);
	}

   /**
	* Returns capitalised singular of this model
	*
	* @access	public
	* @return	string
	*/
	public function plural()
	{
		return ucfirst(str_replace($this->prefix, '', $this->table));
	}
	
   /**
	* Returns the name of this models default controller
	* norminally this is the same as the table name
	*
	* @access	public
	* @return	string
	*/
	public function model_controller()
	{
		if($this->default_controller)
			return $this->default_controller;
			
		return $this->table;
	}
	
   /**
	* Returns the default group controller method
	* default is table
	*
	* @access	public
	* @return	string
	*/
	public function group_method()
	{
		if($this->default_group_method)
			return $this->default_group_method;
			
		return 'table';
	}

   /**
	* Returns the default action controller method
	* default is update
	*
	* @access	public
	* @return	string
	*/
	public function action_method()
	{
		if($this->default_action_method)
			return $this->default_action_method;
			
		return 'update';
	}	

   /**
	* Returns an array of field values for all records
	* in the object
	*
	* @access	public
	* @param	string
	* @return	string
	*/
	public function get_field_array($field='id')
	{
		$fields = array();
		
		if($this->exists())
		{
			foreach($this->all as $o)
			{
				$fields[] = $o->{$field};
			}
		}
		
		return $fields;
	}

   /**
	* Returns a string depending on the type of relationship
	* this model holds with the given model
	*
	* Possible return string values are:
	*	mm 		- Many to Many
	*	mo 		- Many to One
	*	om 		- One to Many
	*	oo 		- One to One
	*	FALSE	- No relationship
	*
	* @access	public
	* @param	string
	* @return	string
	*/
	public function get_relationship_type($related_model=NULL)
	{
		//log_message('error', 'get_relationship_type('.$related_model.')');
	
		// check related model exits
		if(!$related_model)
			return FALSE;
		
		// lowercase model name
		$related_model = strtolower($related_model);
		
		// is the related model in the has_many array?
		if(element($related_model, $this->has_many))
		{
			// create instance of the related model
			$related_obj = new $related_model();
			
			// is this model in the related models has_many array?
			if(element($this->model, $related_obj->has_many))
			{
				// many to many
				return 'mm';
			}
			else if(element($this->model, $related_obj->has_one))
			{
				// many to many
				return 'mo';
			}
			else
			{
				// oops, illegal relationship
				log_message('error', 'get_relationship_type: '.$this->model.' is not defined as a related model in '.$related_model);
				return FALSE;
			}			
		}
		else if (element($related_model, $this->has_one))
		{
			// create instance of the related model
			$related_obj = new $related_model();
			
			// is this model in the related models has_many array?
			if(element($this->model, $related_obj->has_many))
			{
				// many to many
				return 'om';
			}
			else if(element($this->model, $related_obj->has_one))
			{
				// many to many
				return 'oo';
			}
			else
			{
				// oops, illegal relationship
				log_message('error', 'get_relationship_type: '.$this->model.' is not defined as a related model in '.$related_model);
				return FALSE;
			}
		}
		else
		{
			// model is not related
			//log_message('error', 'model '.$related_model.' is not related to '.$this->model);
			return FALSE;
		}
	}

   /**
	* Returns a comma separated list of all fields that can be 'fulltext' searched
	*
	* @access	public
	* @return	string
	*/
	public function get_fulltext_fields()
	{
		$fulltext = array();
		
		foreach($this->validation as $field)
		{
			$type = element('type', $field);
			
			if((in_array($type, array('text','textarea')) AND element('search', $field, TRUE)) OR element('search', $field, FALSE) == TRUE)

				$fulltext[] = element('field', $field);
		}
		
		return implode(',', $fulltext);
	}	

   /**
	* Returns a comma separated label list of all fields that can be 'fulltext' searched
	*
	* @access	public
	* @return	string
	*/
	public function get_fulltext_labels()
	{
		$fulltext = array();
		
		foreach($this->validation as $field)
		{
			$type = element('type', $field);
			
			if((in_array($type, array('text','textarea')) AND element('search', $field, TRUE)) OR element('search', $field, FALSE) == TRUE)

				$fulltext[] = element('label', $field);
		}
		
		return implode(', ', $fulltext);
	}

   /**
	* Returns field list of default table columns
	*
	* @access	public
	* @return	array
	*/
	public function get_default_columns()
	{
		return $this->fields;
	}

   /**
	* Returns field list of all table column options
	*
	* @access	public
	* @return	array
	*/
	public function get_column_options()
	{
		// get has one model names
		$has_one = array_keys($this->has_one);
		
		// get field name array
		$fields = $this->fields;
		
		//log_message('error', print_r($fields, TRUE));
		
		// loop through has one models
		foreach($has_one as $name)
		{			
			// check if this model has an id field in this object
			if(in_array($name.'_id', $fields))
			{
				$key = array_search($name.'_id', $fields);
				
				// remove it!
				unset($fields[$key]);
				
				//log_message('error', 'field '.$name.'_id unset at key '.$key);
			}
		}

		//log_message('error', print_r($fields, TRUE));
		
		// merge fields and related models 
		$columns = array_merge($fields, $has_one, array_keys($this->has_many));
	
		//log_message('error', print_r($columns, TRUE));
	
		return $columns;
	}

   /**
	* Returns label for given field
	*
	* @access	public
	* @return	string
	*/
	public function get_label($field)
	{
		// is this a model field
		if(in_array($field, $this->fields))
			// return label if set in validation array
			return element('label', $this->get_spec($field), ucwords(str_replace('_', ' ', $field)));

		// is this a has_one related model
		if(isset($this->has_one[$field]))
		{
			// create instance of model
			$r = new $field();
			
			// return singular
			return $r->singular();
		}
			
			
		// is this a has_many related model
		if(isset($this->has_many[$field]))
		{
			// create instance of model
			$r = new $field();
			
			// return plural
			return $r->plural();
		}
	}
	
   /**
	* Returns field spec array for given field
	*
	* @access	public
	* @return	array
	*/
	public function get_spec($field)
	{
		return element($field, $this->validation, element($field, $this->has_one, element($field, $this->has_many, array())));
	}
	
   /**
	* Returns boolean value if field has options array
	*
	* @access	public
	* @param	string
	* @return	boolean
	*/
	public function has_options($field)
	{
		return isset($this->validation[$field]['options']);
	}

   /**
	* Returns option array for field
	*
	* @access	public
	* @param	string
	* @return	array
	*/
	public function get_option_array($field)
	{
		return element('options', $this->get_spec($field), array());
	}
	
   /**
	* Returns string for field option
	*
	* @access	public
	* @param	string
	* @return	string
	*/
	public function get_option_string($field)
	{
		// return string key matching current field value
		return array_search($this->{$field}, $this->get_option_array($field));
	}

   /**
	* Returns html grid item for this record
	*
	* Buy default this is just the objects toString with associated actions
	*
	* @access	public
	* @param	array	- array of action urls for this record
	* @return	string
	*/
    public function grid_item($urls)
    {
    	// get first action url
    	$url = array_shift($urls);
    	
    	$checked = "";
    	
    	if($this->is_linked)
    		$checked = "checked";
    
    	return anchor($url, '<input type="checkbox" name="id[]" value="'.$this->id.'" '.$checked.' />'.$this, array('class' => 'default-grid-item'));
	}

   /**
	* Returns html grid item for this record
	*
	* Buy default this is just the objects toString with associated actions
	*
	* @access	public
	* @param	array	- array of action urls for this record
	* @return	string
	*/
    public function catalog_item($urls)
    {    
    	// get first action url
    	$url = array_shift($urls);
    	
    	$checked = "";
    	
    	if($this->is_linked)
    		$checked = "checked";
    	
    	return anchor($url, '<input type="checkbox" name="id[]" value="'.$this->id.'" '.$checked.' />'.$this, array('class' => 'default-catalog-item'));
	}
	

	## GLOBAL METHODS ##
	
	// These are usefull methods used by all child DM Models

	## Public Methods ##

   /**
	* Debug this model to output a variaty
	* of messages/properties
	*
	* @access	public
	* @param	string
	* @return	void
	*/
	public function debug($what='')
	{
		if ($what == 'db' OR empty($what))
			log_message('error', $this->model." Last Query:\n".$this->db->last_query());
		
		if ($what == 'exists' OR empty($what))
			log_message('error', $this->model." ".($this->exists() ? "exists" : "does not exist")." ");
	
	}

   /**
	* Checks if given model exists
	* loads it if not and retuns true/false
	* if exists after load
	*
	* @access	public
	* @param	string
	* @return	boolean
	*/
	public function load_relationship($model)
	{
		// check if it exists
		if(!$this->{$model}->exists())
			// load it
			$this->{$model}->get();
			
		// return result
		return $this->{$model}->exists();
	}

   /**
	* Method returns an array of field values
	* for each record in the object
	*
	* @access	public
	* @param	string
	* @return	array
	*/
	public function field_array($field)
	{
		$temp = array();
		
		foreach($this->all as $o)
		{
			$temp[] = $o->{$field};
		}
		
		return $temp;
	}

   /**
	* Method returns an array of method results
	* for each record in the object
	*
	* @access	public
	* @param	string
	* @return	array
	*/
	public function method_array($method)
	{
		$temp = array();
		
		foreach($this->all as $o)
		{
			$temp[] = $o->{$method}();
		}
		
		return $temp;
	}


	#################################
	## BADTABLE CALLBACK FUNCTIONS ##
	#################################

   /**
	* returns related record as a string
	*
	* @access	public
	* @param	string
	* @return	string
	*/
	public function related($model)
	{		
		if(!$this->{$model}->exists())
			$this->{$model}->get();
		
		return $this->{$model};
	}

   /**
	* returns true or false if related record
	* exists
	*
	* @access	public
	* @param	string
	* @return	string
	*/
	public function has_related($model)
	{		
		$this->related($model);
		
		return $this->{$model}->exists();
	}

   /**
	* returns related record count
	*
	* @access	public
	* @param	string
	* @param	string
	* @return	string
	*/
	public function related_count($model)
	{		
		return $this->{$model}->count();
	}
	
   /**
	* Returns a html checkbox.
	*
	* @access	public
	* @param	string	- record field name to use as checkbox value
	* @param	string	- name of checkbox suffixed with array brackets '[]'
	* @return	string
	*/
	public function checkbox_input($field, $name)
	{	
		return '<input type="checkbox" name="'.$name.'[]" value="'.$this->id.'" />';
	}

   /**
	* Returns a html checkbox.
	*
	* Query's a related model and sets as checked if exists
	*
	* @access	public
	* @param	string	- record field name to use as checkbox value
	* @param	string	- name of checkbox suffixed with array brackets '[]'
	* @param	string	- related model name
	* @param	int		- related record id
	* @return	string
	*/
	public function related_checkbox_input($field, $name, $model, $id)
	{
		$checked = '';
		
		$this->{$model}->get_by_id($id);
		
		if($this->{$model}->exists())
			
			$checked = 'checked';
		
		//log_message('error', 'id = '.$this->{$field}.', '.$field);
		
		return '<input type="checkbox" name="'.$name.'[]" value="'.$this->id.'" '.$checked.' />';
	}
	
   /**
	* Returns a html radio input.
	*
	* @access	public
	* @param	string	- record field name to use as checkbox value
	* @param	string	- name of radio suffixed with array brackets '[]'
	* @return	string
	*/
	public function radio_input($field, $name)
	{	
		return '<input type="radio" name="'.$name.'[]" value="'.$this->id.'" />';
	}

   /**
	* Returns a html radio box.
	*
	* Query's a related model and sets as checked if exists
	*
	* @access	public
	* @param	string	- record field name to use as checkbox value
	* @param	string	- name of checkbox suffixed with array brackets '[]'
	* @param	string	- related model name
	* @param	int		- related record id
	* @return	string
	*/
	public function related_radio_input($field, $name, $model, $id)
	{
		$checked = '';
		
		$this->{$model}->get_by_id($id);
		
		if($this->{$model}->exists())
			
			$checked = 'checked';
		
		return '<input type="radio" name="'.$name.'[]" value="'.$this->id.'" '.$checked.' />';
	}	
   /**
	* Returns an html anchor link.
	*
	* @access	public
	* @param	string	- record field name to use as the last uri value
	* @param	string	- uri string of link
	* @param	string	- anchor string
	* @return	string
	*/
	public function href($href)
	{
		return anchor($href);
	}
	
   /**
	* Returns an anchor from a href link.
	*
	* @access	public
	* @param	string	- record field name to use as the last uri value
	* @param	string	- uri string of link
	* @param	string	- anchor string
	* @return	string
	*/
	public function anchor($field='id', $uri, $string)
	{
		return anchor($uri.'/'.$this->{$field}, $string);
	}
	
   /**
	* Returns an anchor list from a href array.
	*
	* @access	public
	* @param	string	- record field name to use as the last uri value
	* @param	array	- uri string of links	uri => label
	* @return	string
	*/
	public function anchors($field='id', $anchors)
	{
		$links = array();
		
		foreach($anchors as $uri => $string)
		{
			$links[] = anchor($uri.'/'.$this->{$field}, $string);
		}
	
		return $links;
	}
	
   /**
	* Returns an html anchor link.
	*
	* @access	public
	* @param	string	- record field name to use as the last uri value
	* @param	string	- uri string of link
	* @param	string	- anchor string
	* @return	string
	*/
	public function button($name, $value)
	{
		return '<button name="'.$name.'" class="">'.$value.'</button>';
	}

   /**
	* returns a html checkbox representing if this record is linked
	* to the related model defined by parameters
	*
	* @access	public
	* @param	string
	* @param	string
	* @return	string
	*/
	public function related_checkbox($model, $id)
	{
		$checked = '';
		
		// attempt to load related model
		$this->{$model}->get_by_id($id);
		
		if ($this->{$model}->exists())
			// set checked attribute
			$checked = 'checked="checked"';
		
		return '<input type="checkbox" name="linked[]" value="'.$this->id.'" '.$checked.' />';
		
	}

   /**
	* returns a html input field
	*
	* @access	public
	* @param	string
	* @param	string
	* @return	string
	*/
	public function table_hours_input($field, $name)
	{
		return '<input type="text" name="'.$name.'['.$this->{$field}.']" class="small align-center" size="3" value="0" />';
	}



	#########################################
	## FORM VALIDATAION/PREPPING FUNCTIONS ##
	#########################################

   /**
	* if an posted value is empty/false
	* this method will force it to be NULL
	*
	* @access	protected
	* @param	string	- the name of the models field
	* @return	mixed
	*/
	protected function _force_null($field)
	{
		//log_message('error', 'force_null field = '.$this->{$field});
		
		if (empty($this->{$field}))
		{
			// force it to NULL
			$this->{$field} = NULL;
			
			
			log_message('error', 'it is now = '.$this->{$field});
		}
	}
	
	##################################
	## BADTABLE CALLBACK METHODS 	##
	##################################

   /**
	* Returns a html checkbox for a given object field
	*
	* @access	public
	* @param	string	- $field 	the model field to use as the checkbox value
	* @param	string	- $name 	the name of the checkbox (will be suffixed with array brackets '[]')
	* @return	string
	*/
	public function bt_checkbox($field, $name)
	{	
		return '<input type="checkbox" name="'.$name.'[]" value="'.$this->{$field}.'" />';
	}	

   /**
	* Returns a unique anchor for this record
	*
	* @access	public
	* @param	string	- $field 	the model field to use as the last URI segment value
	* @param	string	- $uri		the links uri path
	* @param	string	- $text		the anchor string
	* @return	string
	*/
	public function bt_link($field, $uri, $text)
	{
		return anchor($uri.'/'.$this->{$field}, $text);
	}
	
   /**
	* Suffix's a string to the end of a field value
	*
	* @access	public
	* @param	mixed
	* @param	string
	* @return	string
	*/
	public function bt_suffix($value, $string)
	{
		if($value)
			return $value.$string; 
	}

   /**
	* returns related record as a string
	*
	* @access	public
	* @param	string
	* @param	string
	* @return	string
	*/
	public function bt_related($model)
	{
		$this->load_relationship($model);
		
		if($this->{$model}->exists())
		{
			if(count($this->{$model}->all) == 1)
				return (string)$this->{$model};
			else
			{
				// related
				$related = array();
				
				foreach($this->{$model}->all as $r)
				{
					$related[] = $r;
				}
				
				return implode(', ', $related);
			}
		}
	
	}

   /**
	* returns related record count
	*
	* @access	public
	* @param	string
	* @param	string
	* @return	string
	*/
	public function bt_related_count($model)
	{
		return $this->{$model}->count();
	}






	## Private/Protected Methods ##

   /**
	* Returns view(s) from for each record in this objects all array.
	* - $view paramerter specifies the view to use.
	* - $implode with either return an imploded string of views or array
	* - $all will iterate over the all array and call a view, if false just this is used
	* 
	* @access	protected
	* @param	string
	* @param	boolean
	* @param	boolean
	* @return	string
	*/
	protected function view($view, $implode=TRUE, $all=TRUE)
	{	
		// check this model exists
		if (!$this->exists())
		{
			log_message('error', 'Can not load view as model record does not exist.');
			
			if ($implode)
				return NULL;
			else
				return array();
		}
		
		// if all is false just return view for this model
		if (!$all)
			return $this->load->view($view, array('o' => $this), TRUE);
		
		// array to hold each view
		$views = array();

		// loop through all records in this object
		foreach($this->all as $o)
		{
			$data['o'] = $o;
		
			$views[$o->id] = $this->load->view($view, $data, TRUE);
		}
		
		if ($implode)
			return implode("\n", $views);
		else
			return $views;
	}

   /**
	* This method is the same as the view method but the object
	* passed to the view is defined as the second parameter
	* 
	* @access	protected
	* @param	string
	* @param	object
	* @param	boolean
	* @param	boolean
	* @return	string
	*/
	protected function related_view($view, $object, $implode=TRUE, $all=TRUE)
	{	
		// check this model exists
		if (!$object->exists())
		{
			log_message('error', 'Can not load view as model does not exist.');
			return NULL;
		}
		
		// if all is false just return view for this model
		if (!$all)
			return $this->load->view($view, array('o' => $object), TRUE);
		
		// array to hold each view
		$views = array();

		// loop through all records in this object
		foreach($object->all as $o)
		{
			$data['o'] = $o;
		
			$views[$o->id] = $this->load->view($view, $data, TRUE);
		}
		
		if ($implode)
			return implode("\n", $views);
		else
			return $views;
	}
	
   /**
	* Calls a method in the model for all records in the 'all' array
	*
	* @access	public
	* @param	string
	* @param	string
	* @param	array
	* @return	string
	*/
	public function call_model_method($method, $glue='<br />', $params='')
	{
		if (count($this->all) == 0)
			return '';
		
		// array to hold return values of method calls
		$temp = array();
		
		foreach($this->all as $o)
		{
			$temp[] = $o->{$method}();
		}
	
		if ($glue)
			return implode($glue, $temp);
		else
			return $temp;
	}
}

/* End of file datamapper_ext */
/* Location: ./application/libraries/datamapper_ext.php */