<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Form Extension for DataMapper classes.
 *
 * Quickly turn DM Objects into GoodForm Objects
 *
 * TODO
 * - code a form_callback function to format values before sending them to 
 *   the goodform object.
 * - code a options_callback function to return an option array to send to
 *   the goodform object.
 *
 * CHANGES
 * + form() added the following methods: add_form_element(), add_model_field() and add_related_field()
 *	 changes enable a related model to be specified in the field array, which creates a dropdown to
 *	 select a realted record.
 *
 * + post_form() added the following methods: post_form_element(), post_related_field()
 *	 these methods handle posted related records, related objects are instaciated and
 *   returned from the post_form array so they can be used in the call to the save method
 *	 like so:
 *		$related = $obj->post_form($gf);
 *
 *		$obj->save($realted);
 *
 * + added code to auto detect 'model_id' joining fields in tables
 *		
 * @license 	MIT License
 * @category	DataMapper Extensions
 * @author  	Jim Wardlaw
 * @link    	http://www.stucktogetherwithtape.com/code/
 * @version 	1.3.3
 */

// --------------------------------------------------------------------------

/**
 * DMZ_Goodform Class
 */
class DMZ_Goodform {

   /**
	* Constructor
	*
	* @access	public
	* @param	string
	* @return	string
	*/
	public function __construct()
	{
		// get global CI instance
		$CI =& get_instance();
		
		$this->input = $CI->input;
	}

	####################################
	## METHODS TO GENERATE HTML FORMS ##
	####################################
	
   /**
	* Populates a goodform object reference
	* with fields from this model.
	*
	* Fields can be specified via array
	* as param. By default all fields are added.
	*
	* Returns a GoodForm Object instance containing the form.
	*
	* @access	public
	* @param	object 	-	instance of the DM Object calling this extension
	* @param	object	-	reference to instance of goodform object
	* @param	array	-	array of fields to add to array
	* @return	boolean
	*/
	public function form($object, &$goodform, $fields='')
	{
		// turn of CI validation as DM handels this for us
		$ci_validation = FALSE;
	
		// select all fields if not defined
		if(empty($fields))
		{
			$fields = $object->fields;
		}
		
		foreach($fields as $field => $spec)
		{
			// check for custom field spec array
			if(is_array($spec))
				$this->add_form_element($object, $goodform, $field, $spec);
			else
				$this->add_form_element($object, $goodform, $spec);
		}
		
		// update form
		return TRUE;
	}

   /**
	* Populates a goodform object reference
	* with fields from this model.
	*
	* Fields can be specified via array
	* as param. By default all fields are added.
	*
	* Returns a GoodForm Object instance containing the form.
	*
	* @access	public
	* @param	object	-	instance of the DM Object calling this extension
	* @param	object	-	reference to instance of goodform object
	* @param	array	-	array of fields to add to array
	* @return	boolean
	*/
	public function post_form($object, &$goodform, $fields='')
	{
		// select all fields to update if not defined
		if(empty($fields))
		{
			$fields = $object->fields;
		}
		
		// array containing posted related objects
		$related = array();
		
		foreach($goodform->fields as $field)
		{
			$r = $this->post_form_element($object, $goodform, $field);

			// check if a realted object array was returned
			if($r)
			{
				// marge related model to related array
				$related[$field] = $r;
			}
		}
		
		//log_message('error', 'related = '.print_r($related, TRUE));
		
		// return array of related objects
		return $related;
	}
	
   /**
	* Updates a DM Goodform with posted data and errors
	*
	* @access	public
	* @param	object 	-	instance of the DM Object calling this extension
	* @param	object	-	reference to instance of goodform object
	* @return	boolean
	*/
	public function update_form($object, &$goodform)
	{		
		// select all fields to update if not defined
		foreach($goodform->fields as $field)
		{
			if(isset($_POST[$field]))
			{
				// update form with posted value
				$goodform->elements[$field]['value'] = $_POST[$field];
			
				//log_message('error', $field.' = '.$_POST[$field]);
			}
						
			if (isset($object->error->{$field}))
			{
				// update form with error message
				$goodform->elements[$field]['error'] = $object->error->{$field};
			
				//log_message('error', $field.' = '.print_r($object->error, TRUE));
			}
		}
	}

   /**
	* Adds a form element to the goodform object
	*
	* @access	protected
	* @param	object
	* @param	object ref
	* @param	string
	* @return	void
	*/
	protected function add_form_element($object, &$goodform, $field, $spec=NULL)
	{		
		// check field exists in dm model
		if(isset($object->has_one[$field]))
		{
			if(!isset($spec['label']))
			{
				$spec['label'] = humanize($field);
			}
		
			//log_message('error', 'add_related_field has_one '.$field );
			return $this->add_related_field($object, &$goodform, $field, $spec, TRUE);		
		}
		// attempt to remove _id from field and see if its a related model
		else if(isset($object->has_one[str_replace('_id', '', $field)]))
		{			
			$spec['type'] = 'dropdown';
			
			$spec['label'] = ucfirst($field);
		
			//log_message('error', 'add_related_field has_one_id '.$field );
			return $this->add_related_field($object, &$goodform, str_replace('_id', '', $field), $spec, TRUE);
		}
		else if(isset($object->has_many[$field]))
		{
			//log_message('error', 'add_related_field has_many '.$field );
			return $this->add_related_field($object, &$goodform, $field, $spec, FALSE);		
		}
		else if (isset($object->validation[$field]))
		{
			//log_message('error', 'add_related_field model '.$field );
			//log_message('error', 'add_model_field '.$field );
			return $this->add_model_field($object, &$goodform, $field, $spec);
		}
		else if (is_array($spec))
		{
			//log_message('error', 'add_custom_field '.$field );
			return $this->add_custom_field($object, &$goodform, $field, $spec);
		}
		else
		{
			log_message('error', 'DMZ Goodform: Field '.$field.' does not exist in DM validation array or one of its related models');
			return;
		}
	}

   /**
	* Adds a form element for one of the dm
	* records field
	*
	* @access	protected
	* @param	object
	* @param	object ref
	* @param	string
	* @return	void
	*/
	protected function add_model_field($object, &$goodform, $field, $spec)
	{
		## Prep form field spec ##
		
		// collect validation array for field from object
		$spec = $object->validation[$field];
		
		// add a name field - this is the form elements name 
		$spec['name'] = $spec['field'];
		
		// get the current field value from the object
		$spec['value'] = $object->$field;
		
		// look for any existing error messages for field
		if (!empty($object->error->{$field}))
			$spec['error'] = $object->error->{$field};
		
		
		## Get the fields input type ##
		
		if (isset($spec['type']))
			// get the form input type if defined
			$input_type = $spec['type'];
		else
			// use text input by default
			$input_type = 'text';

		// if input type is FALSE ignore field
		if ($input_type === FALSE)
			return;

		## Get the related records options ##
		
		// does this field contain an options attribute
		if(isset($spec['options']))
		{
			// is the options attribute a string?
			if(is_string($spec['options']))
			{
				// callback method to return custom
				// option array
				$spec['options'] = $object->{$spec['options']}();
			}
			// else? must be an array or defined options. let it be.
		}


		## Filter spec and set validation rules ##
		
		// filter spec array
		$spec = $this->filter_field_spec($goodform, $spec);
		
		// turn rules array into pipe seperated string
		$spec['validation'] = $this->convert_rules($spec);
		
		// add form to gf object
		return $goodform->{$input_type}($spec);
	}

   /**
	* Adds a form element for one of the dm
	* records related records
	*
	* @access	protected
	* @param	object
	* @param	object ref
	* @param	string
	* @param	boolean
	* @return	void
	*/
	protected function add_related_field($object, &$goodform, $field, $spec, $has_one=TRUE)
	{	
		## Prep form field spec ##
		if(empty($spec))
			$spec = array();
		
		if($has_one)
			// collect validation array for related object
			$spec = array_merge($spec, $object->has_one[$field]);
		else
			// collect validation array for related object
			$spec = array_merge($spec, $object->has_many[$field]);
		
		// related objects may also have infomation in the 
		// objects validation array, merge the two if set!
		if(isset($object->validation[$field]))
		{
			$spec = array_merge($spec, $object->validation[$field]);
		}
		
		
		log_message('error', 'Spec: '.print_r($spec, TRUE));
		
		// add a name field - this is the form elements name 
		$spec['name'] = $field;
		
		// get the current field value from the object
		
		// relationship already loaded?
		if($object->{$field}->exists())
		{
			// assign value
			$spec['value'] = $object->{$field}->id;
		}
		else
		{
			$object->{$field}->get();
			
			// load relationship and assign value
			$spec['value'] = $object->{$field}->id;
		}
		
		// look for any existing error messages for field
		//if (!empty($object->error->{$field}))
		//	$spec['error'] = $object->error->{$field};


		## Get the related records options ##
		
		// is this value defined as a relationship and options do not already exists
		if(!isset($spec['options']))
		{			
			// create new instance of related model
			$obj = new $spec['class']();
			
			// get all records
			$obj->get();
			
			// assign to options array
			$spec['options'] = $obj->options();
		}
		else
		{
			// is the options attribute a string?
			if(is_string($spec['options']))
			{
				// callback method to return custom
				// option array
				$spec['options'] = $object->{$spec['options']}();
			}
			// else? must be an array or defined options. let it be.
		}
		
		## Get the fields input type ##
		
		if (isset($spec['type']))
			// get the form input type if defined
			$input_type = $spec['type'];
		else
			// use text dropdown by default
			$input_type = 'dropdown';

		// if input type is FALSE ignore field
		if ($input_type === FALSE)
			return;


		## Filter spec and set validation rules ##
		
		// filter spec array
		$spec = $this->filter_field_spec($goodform, $spec);
		
		// turn rules array into pipe seperated string
		$spec['validation'] = $this->convert_rules($spec);
		
		// humanize label
		$spec['label'] = humanize($spec['label']);
		
		// add form to gf object
		return $goodform->{$input_type}($spec);
	}


   /**
	* Adds a form element for one of the dm
	* records field
	*
	* @access	protected
	* @param	object
	* @param	object ref
	* @param	string
	* @return	void
	*/
	protected function add_custom_field($object, &$goodform, $field, $spec)
	{
		## Prep form field spec ##
		
		// add a name field - this is the form elements name 
		$spec['name'] = $field;
		
		// get the current field value from the object
		$spec['value'] = $object->$field;
		
		// look for any existing error messages for field
		if (!empty($object->error->{$field}))
			$spec['error'] = $object->error->{$field};
		
		
		## Get the fields input type ##
		
		if (isset($spec['type']))
			// get the form input type if defined
			$input_type = $spec['type'];
		else
			// use text input by default
			$input_type = 'text';

		// if input type is FALSE ignore field
		if ($input_type === FALSE)
			return;

		## Get the related records options ##


		## Filter spec and set validation rules ##
		
		// filter spec array
		$spec = $this->filter_field_spec($goodform, $spec);
		
		// turn rules array into pipe seperated string
		$spec['validation'] = $this->convert_rules($spec);
		
		// add form to gf object
		return $goodform->{$input_type}($spec);
	}


   /**
	* Posts a form element to the dm object
	*
	* @access	protected
	* @param	object
	* @param	object ref
	* @param	string
	* @return	void
	*/
	private function post_form_element($object, &$goodform, $field)
	{
		// check field value has been posted
		if ($this->input->post($field) !== FALSE)
		{
			// check field exists in dm model
			if(isset($object->has_one[$field]))
			{
				return $this->post_related_field($object, &$goodform, $field, TRUE);		
			}
			else if(isset($object->has_many[$field]))
			{
				return $this->post_related_field($object, &$goodform, $field, FALSE);		
			}
			else if (isset($object->validation[$field]))
			{
				// assign posted value to object field
				$object->{$field} = $this->input->post($field);
				
				return;
			}					
		}
	}

   /**
	* Adds a form element for one of the dm
	* records related records
	*
	* @access	protected
	* @param	object
	* @param	object ref
	* @param	string
	* @param	boolean
	* @return	void
	*/
	protected function post_related_field($object, &$goodform, $field, $has_one=TRUE)
	{
		// get posted value
		$value = $this->input->post($field);
		
		// return null if post is empty
		if(empty($value))
			return;


		## Get form field spec ##
		
		if($has_one)
			// collect validation array for related object
			$spec = $object->has_one[$field];
		else
			// collect validation array for related object
			$spec = $object->has_many[$field];
		
		
		## Create new model instance ##
		
		$obj = new $spec['class']();
		
		// was an array posted?
		if(is_array($value))
		{
			// make sure array is not empty
			if(count($value) > 0)
			{
				// get multiple records
				$obj->where_in('id', $value)->get();
			
				// if record found return objects all array
				if($obj->exists())
				{
					return $obj->all;
				}
			}						
		}
		else
		{
			// just the one
			$obj->get_by_id($value);
			
			// if record found return object
			if($obj->exists())
			{
				return $obj;
			}
		}
		
		return NULL;
	}

   /**
	* Filters a spec array getting rid of attributes not
	* needed by the goodform library
	*
	* @access	protected
	* @param	object ref
	* @param	array
	* @return	array
	*/
	protected function filter_field_spec(&$goodform, $spec)
	{
		// get array of allowed spec attributes
		$allowed = $goodform->config->item('allowed_dm_attributes', 'goodform');
		
		foreach ($spec as $k => $v)
		{
			if(!in_array($k, $allowed))
				// remove unwanted element from validation array
				unset($spec[$k]);
		}
		
		// return filterd array
		return $spec;
	}

   /**
	* returns an options array for all records
	* in the object. Uses the objects id field and
	* its __toString method
	*
	* @access	public
	* @param	object
	* @param	boolean
	* @return	array
	*/
	public function options($object, $include_null=TRUE)
	{
		$options = array();
		
		if($include_null)
			$options['---'] = NULL;
		
		foreach($object->all as $o)
			
			$options[(string)$o] = $o->id;
		
		return $options;
	}

   /**
	* comment
	*
	* @access	public
	* @param	string
	* @return	string
	*/
	public function convert_rules($spec)
	{
		if(!isset($spec['rules']))
			return '';
		
		$rule_array = array();
		
		foreach($spec['rules'] as $key => $value)
		{
			if(is_numeric($key))
				$rule_array[] = $value;
			else
				$rule_array[] = $key.'['.$value.']';
		}
		
		return implode('|', $rule_array);
	}
}