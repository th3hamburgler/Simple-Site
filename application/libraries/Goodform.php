<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * GoodForm
 *
 * Creat nice flexible forms in CodeIgniter
 *
 * @licence 	MIT Licence
 * @category	Librarys 
 * @author		Jim Wardlaw
 * @link		http://www.stucktogetherwithtape.com/code/goodform
 * @version 	1.3.3
 *
 * CHANGES
 *
 * - added new config 'force_field_id' to add id attribute to fields if they have none set
 * - added attributes placeholder to input group config prefixes
 * - added method prep_jquery_validation | constructs metadata class infomation to activate
 * 	 in the jquery validation plugin
 * - added global $ci_validation to prevent rules being added to the CI validation library
 *
 * - added element() - returns a built form element and removes it from the instance.
 * - changed label() - stopped php error when no for param is passed
 *
 */ 
class Goodform {

	// an array to hold all elements of the form
	public $elements = array();

	// added field array to store all posted values
	public $fields = array();

	// flag if fieldset is open
	public $open_fieldset = FALSE;
	
	// flag to add validation rules to CI validation library *HACK*
	public $ci_validation = TRUE;

	##########################
	## UTILITY METHODS	 	##
	##########################	
	
   /**
	* Object Constructor
	*
	* @access	private
	* @return	void
	*/
	public function __construct()
	{
		$this->load_config();
		
		$this->load_libraries();
	}	
	
   /**
	* Loads settings from the wrapup 
	* config file
	*
	* @access	private
	* @return	void
	*/
	private function load_config()
	{
		if ($CI =& get_instance())
		{
			$this->config = $CI->config;
		}
		
		// load goodform config vars
		$this->config->load('goodform', TRUE, TRUE);
	}
	
   /**
	* Loads settings from the wrapup 
	* config file
	*
	* @access	private
	* @return	void
	*/
	private function load_libraries()
	{
		$CI =& get_instance();
		
		if( ! isset($CI->form_validation))
		{
			$CI->load->library('form_validation');
		}
		
		$this->form_validation = $CI->form_validation;
		
		// get rid of error delimiters
		$this->form_validation->set_error_delimiters('', '');
	}
	
   /**
	* Returns true if this form has been submitted
	*
	* i.e. a posted value matching any of the defined 
	* elements is found
	*
	* @access	public
	* @param	mixed
	* @param	string
	* @return	object
	*/
	public function is_submitted()
	{
		foreach($this->elements as $name => $element)
		{
			if (isset($_POST[$name]))
				
				if($_POST[$name] !== FALSE)
				
					return TRUE;
		}
		
		return FALSE;
	}

   /**
	* Updates a given elements attribute
	*
	* i.e. update a fields error message with a custom string
	*
	* @access	public
	* @param	string
	* @param	string
	* @param	string
	* @return	object
	*/	
	public function update_element($element, $attribute, $value)
	{
		// check element exists
		if (isset($this->elements[$element]))
		{
			$this->elements[$element][$attribute] = $value;
		}
		else
			log_message('error', 'Can`t find element "'.$element.'" to update attribute.');
	}

	
	##########################
	## INPUT METHODS	 	##
	##########################	

   /**
	* Adds an input form element to the form
	* 
	* Can accept two parameters defining the elements 
	* name and value or one associative array param 
	* defining custom attributes.
	*
	* This method is called by all the other 'input'
	* field setters with the 'type' attribute defined
	* 
	* @access	public
	* @param	mixed
	* @param	string
	* @return	object
	*/
	public function input($name, $value=NULL)
	{
		$spec = array();
		
		// check if name isn't an array
		if (!is_array($name))
		{
			// make it so
			$spec['name'] = $name;
			$spec['value'] = $value;
			$spec['element'] = 'input';
		}
		else
		{
			$spec = $name;
			$spec['element'] = 'input';
		}
		
		// force type of text if not set
		if(!isset($spec['type']))
			$spec['type'] = 'text';
			
		// check we dont already have an element 
		// with this name in the form
		if (!$this->element_exists($spec['name']))
		{
			// look for validation element
			$rules = $this->get_element($spec, 'validation');
						
			if ($rules)
			{
				// convert rules to jquery validation classes
				$spec = $this->prep_jquery_validation($spec, $rules);
			
				// get label element, use name if NULL
				$label = $this->get_element($spec, 'label', $spec['name']);
											
				// if defined set validation rules for this element
				$this->set_rules($spec['name'], $label, $rules);
			}
			
			// is force field id set in config and not id set
			if ($this->config->item('force_field_id', 'goodform') AND !isset($spec['id']))	
			{
				// if id is not set use field name
				$spec['id'] = $spec['name'];
			}
			
			// add to objects element array
			$this->elements[$spec['name']] = $spec;
			
			// add name of element to field array
			$this->fields[] = $spec['name'];
		}
		else
		{
			log_message('error', 'GoodForm element '.$spec['name'].' already exists');
		}
		
		// chain it up
		return $this;
	}

   /**
	* Adds a text input form element to the form
	*
	* Can accept two parameters defining the elements 
	* name and value or one associative array param 
	* defining custom attributes.
	*
	* @access	public
	* @param	mixed
	* @param	string
	* @return	object
	*/
	public function text($name, $value=NULL)
	{
		$spec = array();
		
		// check if param isn't an array
		if (!is_array($name))
		{
			// make it so
			$spec['name'] = $name;
			$spec['value'] = $value;
			$spec['element'] = 'input';		// set element type to input
			$spec['type'] = 'text';			// set default input type attribute to text
		}
		else
		{
			$spec = $name;					// copy defined attributes to spec array
			$spec['element'] = 'input';		// set element type to input
			
			if (!isset($spec['type']))
				$spec['type'] = 'text';		// set default input type attribute to text
		}
		
		// call the input method chain it up
		return $this->input($spec);
	}

   /**
	* Adds a range input form element to the form
	*
	* Can accept two parameters defining the elements 
	* name and value or one associative array param 
	* defining custom attributes.
	*
	* @access	public
	* @param	mixed
	* @param	string
	* @return	object
	*/
	public function range($name, $value=NULL)
	{
		$spec = array();
		
		// check if param isn't an array
		if (!is_array($name))
		{
			// make it so
			$spec['name'] = $name;
			$spec['value'] = $value;
			$spec['element'] = 'input';		// set element type to input
			$spec['type'] = 'range';		// set default input type attribute to range
		}
		else
		{
			$spec = $name;					// copy defined attributes to spec array
			$spec['element'] = 'input';		// set element type to input
			
			if (!isset($spec['type']))
				$spec['type'] = 'range';	// set default input type attribute to range
		}
		
		// call the input method chain it up
		return $this->input($spec);
	}

   /**
	* Adds a password form element to the form
	*
	* Can accept two parameters defining the elements 
	* name and value or one associative array param 
	* defining custom attributes.
	*
	* @access	public
	* @param	mixed
	* @param	string
	* @return	object
	*/
	public function password($name, $value=NULL)
	{		
		// check if name isn't an array
		if (!is_array($name))
		{
			// make it so
			$spec['name'] = $name;
			$spec['value'] = $value;
			$spec['element'] = 'input';		// set element type to input
			$spec['type'] = 'password';		// set default input type attribute to password
		}
		else
		{
			$spec = $name;					// copy defined attributes to spec array
			$spec['element'] = 'input';		// set element type to input
			
			if (!isset($spec['type']))
				$spec['type'] = 'password';	// set default input type attribute to text
		}
		
		// call the input method chain it up
		return $this->input($spec);
	}

   /**
	* Adds an upload form element to the form
	*
	* Can accept a string parameter defining the elements 
	* name or an associative array parameter defining 
	* custom attributes.
	*
	* @access	public
	* @param	mixed
	* @return	object
	*/
	public function upload($name)
	{	
		// check if name isn't an array
		if (!is_array($name))
		{
			// make it so
			$spec['name'] = $name;
			$spec['element'] = 'input';		// set element type to input
			$spec['type'] = 'file';			// set default input type attribute to file
		}
		else
		{
			$spec = $name;					// copy defined attributes to spec array
			$spec['element'] = 'input';		// set element type to input
			if (!isset($spec['type']))
				$spec['type'] = 'file';		// set default input type attribute to file
		}
		
		// call the input method chain it up
		return $this->input($spec);
	}

   /**
	* Adds a hidden form element to the form
	*
	* Can accept two parameters defining the elements 
	* name and value or one associative array param 
	* defining custom attributes.
	*
	* @access	public
	* @param	mixed
	* @param	string
	* @return	object
	*/
	public function hidden($name, $value=NULL)
	{
		
		// check if name isn't an array
		if (!is_array($name))
		{
			// make it so
			$spec['name'] = $name;
			$spec['value'] = $value;
			$spec['element'] = 'input';		// set element type to input
			$spec['type'] = 'hidden';		// set default input type attribute to hidden
		}
		else
		{
			$spec = $name;					// copy defined attributes to spec array
			$spec['element'] = 'input';		// set element type to input
			
			if (!isset($spec['type']))
				$spec['type'] = 'hidden';	// set default input type attribute to hidden
		}
		
		// remove label from hidden element
		// cos thats just silly
		if(isset($spec['label']))
			unset($spec['label']);
			
		// call the input method chain it up
		return $this->input($spec);
	}

   /**
	* Can accept three parameters defining the elements 
	* name, value and image source or one associative 
	* array param defining custom attributes.
	*
	* @access	public
	* @param	mixed
	* @param	string
	* @param	string
	* @return	object
	*/
	public function image($name, $value=NULL, $src=NULL)
	{
		// check if name isn't an array
		if (!is_array($name))
		{
			// make it so
			$spec['name'] = $name;
			$spec['value'] = $value;
			$spec['src'] = $src;
			$spec['element'] = 'input';
			$spec['type'] = 'image';
		}
		else
		{
			$spec = $name;
			$spec['element'] = 'input';
			if (!isset($spec['type']))
				$spec['type'] = 'image';
		}
		
		// chain it up
		return $this->input($spec);
	}

   /**
	* Adds a reset form element to the form
	*
	* Can accept two parameters defining the elements 
	* name and value or one associative array param 
	* defining custom attributes.
	*
	* @access	public
	* @param	mixed
	* @param	string
	* @return	object
	*/
	public function reset($name, $value=NULL)
	{
		// check if name isn't an array
		if (!is_array($name))
		{
			// make it so
			$spec['name'] = $name;
			$spec['value'] = $value;
			$spec['element'] = 'input';
			$spec['type'] = 'reset';
		}
		else
		{
			$spec = $name;
			$spec['element'] = 'input';
			if (!isset($spec['type']))
				$spec['type'] = 'reset';
		}
		
		// chain it up
		return $this->input($spec);

	}

   /**
	* Adds a submit form element to the form
	*
	* Can accept two parameters defining the elements 
	* name and value or one associative array param 
	* defining custom attributes.
	*
	* @access	public
	* @param	mixed
	* @param	string
	* @return	object
	*/
	public function submit($name, $value=NULL)
	{
		// check if name isn't an array
		if (!is_array($name))
		{
			// make it so
			$spec['name'] = $name;
			$spec['value'] = $value;
			$spec['element'] = 'input';
			$spec['type'] = 'submit';
		}
		else
		{
			$spec = $name;
			$spec['element'] = 'input';
			if (!isset($spec['type']))
				$spec['type'] = 'submit';
		}
		
		// chain it up
		return $this->input($spec);
	}

   /**
	* Adds a checkbox form element to the form
	*
	* Can accept three parameters defining the elements 
	* name, value and checked state or an associative array param 
	* defining custom attributes.
	*
	* This method will also convert any checked value that is not 
	* empty to 'checked'. Passing FALSE, NULL 0 or and empty string
	* will produced an unchecked input
	*
	* @access	public
	* @param	mixed
	* @param	string
	* @return	object
	*/
	public function checkbox($name, $value=NULL, $checked=FALSE)
	{
		// check if name isn't an array
		if (!is_array($name))
		{
			// make it so
			$spec['name'] = $name;
			$spec['value'] = $value;	
			$spec['element'] = 'input';			// set element type to input
			$spec['type'] = 'checkbox';			// set default input type attribute to checkbox
			
			// check to see if this element is checked
			if (!empty($checked))
				$spec['checked'] = 'checked';
				
		}
		else
		{
			$spec = $name;						// copy defined attributes to spec array
			$spec['element'] = 'input';			// set element type to input
			
			// check if a type has been set
			if (!isset($spec['type']))
				$spec['type'] = 'checkbox'; 	// set default input type attribute to checkbox
			
			// check if a checked value has been sent
			if (!empty($spec['checked']))
				$spec['checked'] = 'checked';	// set to proper html attribute value
		}
		
		// chain it up
		return $this->input($spec);		
	}

   /**
	* Adds a checkbox group form element to the form
	*
	* Can accept three parameters defining the elements 
	* name, options (value => name) and checked states of those options or 
	* an associative array param defining custom attributes.
	*
	* Both the $checked param or ['checked'] array attribute can
	* contain either an array or value. This should be equal to the
	* checkbox values/options that are to be checked.
	*
	* This method will also convert any checked value that is not 
	* empty to 'checked'. Passing FALSE, NULL 0 or and empty string
	* will produced an unchecked input
	*
	* @access	public
	* @param	mixed
	* @param	array
	* @param	mixed
	* @return	object
	*/
	public function checkbox_group($name, $options=NULL, $checked=NULL)
	{
		// check if name isn't an array
		if (!is_array($name))
		{
			// make it so
			$spec['name'] = $name;
			$spec['options'] = $options;
			$spec['element'] = 'group';			// set element type to group
			$spec['type'] = 'checkbox';			// set default group type to checkbox
			
			$spec['value'] = $checked;			// pass checked values to array
		}
		else
		{
			$spec = $name;						// copy defined attributes to spec array
			$spec['element'] = 'group';			// set element type to group
			
			// check if checked attribute has been set
			if (isset($spec['checked']))
			{
				$spec['value'] = $spec['checked']; // assign to value attr for uniformity
				unset($spec['checked']);			
			}			
			
			// check if a type has been set
			if (!isset($spec['type']))
				$spec['type'] = 'checkbox';		// set default group type to checkbox
		}
		
		// check we dont already have an element 
		// with this name in the form
		if (!$this->element_exists($spec['name']))
		{
			// look for validation element
			$rules = $this->get_element($spec, 'validation');
			
			if ($rules)
			{
				// get label element, use name if NULL
				$label = $this->get_element($spec, 'label', $spec['name']);
								
				// if defined set validation rules for this element
				$this->set_rules($spec['name'], $label, $rules);
			}
			
			// is force field id set in config and not id set
			if ($this->config->item('force_field_id', 'goodform') AND !isset($spec['id']))	
			{
				// if id is not set use field name
				$spec['id'] = $spec['name'];
			}

			// add to objects element array
			$this->elements[$spec['name']] = $spec;
			
			// add name of element to field array
			$this->fields[] = $spec['name'];
		}
		
		// chain it up
		return $this;
	}

   /**
	* Adds a radio form element to the form
	*
	* Can accept three parameters defining the elements 
	* name, value and checked state or an associative array param 
	* defining custom attributes.
	*
	* This method will also convert any checked value that is not 
	* empty to 'checked'. Passing FALSE, NULL 0 or and empty string
	* will produced an unchecked input
	*
	* @access	public
	* @param	mixed
	* @param	string
	* @return	object
	*/
	public function radio($name, $value=NULL, $checked=FALSE)
	{
		// check if name isn't an array
		if (!is_array($name))
		{
			// make it so
			$spec['name'] = $name;
			$spec['value'] = $value;	
			$spec['element'] = 'input';			// set element type to input
			$spec['type'] = 'radio';			// set default input type attribute to radio
			
			// check to see if this element is checked
			if (!empty($checked))
				$spec['checked'] = 'checked';
				
		}
		else
		{
			$spec = $name;						// copy defined attributes to spec array
			$spec['element'] = 'input';			// set element type to input
			
			// check if a type has been set
			if (!isset($spec['type']))
				$spec['type'] = 'radio'; 	// set default input type attribute to radio
			
			// check if a checked value has been sent
			if (!empty($spec['checked']))
				$spec['checked'] = 'checked';	// set to proper html attribute value
		}
		
		// chain it up
		return $this->input($spec);		
	}

   /**
	* Adds a radio group form element to the form
	*
	* Can accept three parameters defining the elements 
	* name, options (value => name) and elected option or 
	* an associative array param defining custom attributes.
	*
	* Both the $checked param or ['checked'] array attribute
	* should be equal to the checkbox value/options that 
	* is checked.
	*
	* @access	public
	* @param	mixed
	* @param	array
	* @param	string
	* @return	object
	*/
	public function radio_group($name, $options=NULL, $value=NULL)
	{
		// check if name isn't an array
		if (!is_array($name))
		{
			// make it so
			$spec['name'] = $name;
			$spec['options'] = $options;
			$spec['element'] = 'group';		// set element type to group
			$spec['type'] = 'radio';		// set default group type to radio
			
			$spec['value'] = $checked;			// pass checked values to array
		}
		else
		{
			$spec = $name;					// copy defined attributes to spec array
			$spec['element'] = 'group';		// set element type to group
			
			// check if checked attribute has been set
			if (isset($spec['checked']))
			{
				$spec['value'] = $spec['checked']; // assign to value attr for uniformity
				unset($spec['checked']);			
			}
			
			if (!isset($spec['type']))
				$spec['type'] = 'radio';	// set default group type to radio
		}
		
		// check we dont already have an element 
		// with this name in the form
		if (!$this->element_exists($spec['name']))
		{
			// look for validation element
			$rules = $this->get_element($spec, 'validation');
			
			if ($rules)
			{
				// get label element, use name if NULL
				$label = $this->get_element($spec, 'label', $spec['name']);
								
				// if defined set validation rules for this element
				$this->set_rules($spec['name'], $label, $rules);
			}
			
			// is force field id set in config and not id set
			if ($this->config->item('force_field_id', 'goodform') AND !isset($spec['id']))	
			{
				// if id is not set use field name
				$spec['id'] = $spec['name'];
			}
			
			// add to objects element array
			$this->elements[$spec['name']] = $spec;
			
			// add name of element to field array
			$this->fields[] = $spec['name'];
		}
		
		// chain it up
		return $this;
	}

   /**
	* Adds a button form element to the form
	*
	* Can accept two parameters defining the elements 
	* name and value or one associative array param 
	* defining custom attributes.
	*
	* @access	public
	* @param	mixed
	* @param	string
	* @return	object
	*/
	public function button($name, $value=NULL)
	{
		$spec = array();
		
		// check if name isn't an array
		if (!is_array($name))
		{
			// make it so
			$spec['name'] = $name;
			$spec['value'] = $value;
			$spec['text'] = $value;
			$spec['element'] = 'button';
		}
		else
		{
			$spec = $name;
			$spec['element'] = 'button';
		}
		
		//log_message('error', print_r($spec, TRUE));
		
		// check we dont already have an element 
		// with this name in the form
		if (!$this->element_exists($spec['name']))
		{
			// look for validation element
			$rules = $this->get_element($spec, 'validation');
			
			if ($rules)
			{
				// get label element, use name if NULL
				$label = $this->get_element($spec, 'label', $spec['name']);
								
				// if defined set validation rules for this element
				$this->set_rules($spec['name'], $label, $rules);
			}
			
			// is force field id set in config and not id set
			if ($this->config->item('force_field_id', 'goodform') AND !isset($spec['id']))	
			{
				// if id is not set use field name
				$spec['id'] = $spec['name'];
			}
			
			// add to objects element array
			$this->elements[$spec['name']] = $spec;
			
			// add name of element to field array
			$this->fields[] = $spec['name'];
		}
		
		// chain it up
		return $this;
	}

   /**
	* Adds a dropdown form element to the form
	*
	* Can accept three parameters defining the elements 
	* name, options array and selected option(s) value 
	* or one associative array param defining custom 
	* attributes.
	*
	* The $option array or $name['options'] attriubute should
	* be defined: $options['string'] = 'value'
	*
	* This is too allow two options with the same value in
	* your dropdown field.
	*
	* e.g.
	* 
	* $options = array(
	*	'UK' => 225,
	*	'Great Britain' => 225
	* );
	* 
	* @access	public
	* @param	mixed
	* @param	array
	* @param	string
	* @return	object
	*/
	public function dropdown($name, $options=NULL, $selected=NULL)
	{
		// check if name isn't an array
		if (!is_array($name))
		{
			// make it so
			$spec['name'] = $name;
			$spec['value'] = $selected;
			$spec['options'] = $options;
			$spec['element'] = 'select';
		}
		else
		{
			$spec = $name;
			$spec['element'] = 'select';
			
			// check if selected attribute has been set
			if (isset($spec['selected']))
			{
				$spec['value'] = $spec['selected']; // assign to value attr for uniformity
				unset($spec['selected']);			
			}
		}
		
		// check we dont already have an element 
		// with this name in the form
		if (!$this->element_exists($spec['name']))
		{
			// look for validation element
			$rules = $this->get_element($spec, 'validation');
			
			if ($rules)
			{
				// get label element, use name if NULL
				$label = $this->get_element($spec, 'label', $spec['name']);
								
				// if defined set validation rules for this element
				$this->set_rules($spec['name'], $label, $rules);
			}
			
			// is force field id set in config and not id set
			if ($this->config->item('force_field_id', 'goodform') AND !isset($spec['id']))	
			{
				// if id is not set use field name
				$spec['id'] = $spec['name'];
			}
			
			// add to objects element array
			$this->elements[$spec['name']] = $spec;
			
			// add name of element to field array
			$this->fields[] = $spec['name'];
		}
		
		// chain it up
		return $this;
	}

   /**
	* Adds a dropdown form element to the form
	* 
	* Alias method for those who like to keep
	* it propper!
	*
	* @access	public
	* @param	mixed
	* @param	array
	* @param	string
	* @return	object
	*/
	public function select($name, $options=NULL, $value=NULL)
	{
		return $this->dropdown($name, $options, $value);
	}

   /**
	* Adds a textarea form element to the form
	*
	* Can accept two parameters defining the elements 
	* name and value or one associative array param 
	* defining custom attributes.
	*
	* @access	public
	* @param	mixed
	* @param	string
	* @return	object
	*/
	public function textarea($name, $value=NULL)
	{
		$spec = array();
		
		// check if name isn't an array
		if (!is_array($name))
		{
			// make it so
			$spec['name'] = $name;
			$spec['value'] = $value;
			$spec['element'] = 'textarea';
		}
		else
		{
			$spec = $name;
			$spec['element'] = 'textarea';
		}
		
		// check we dont already have an element 
		// with this name in the form
		if (!$this->element_exists($spec['name']))
		{
			// look for validation element
			$rules = $this->get_element($spec, 'validation');
						
			if ($rules)
			{
				// convert rules to jquery validation classes
				$spec = $this->prep_jquery_validation($spec, $rules);
			
				// get label element, use name if NULL
				$label = $this->get_element($spec, 'label', $spec['name']);
											
				// if defined set validation rules for this element
				$this->set_rules($spec['name'], $label, $rules);
			}
			
			// is force field id set in config and not id set
			if ($this->config->item('force_field_id', 'goodform') AND !isset($spec['id']))	
			{
				// if id is not set use field name
				$spec['id'] = $spec['name'];
			}
			
			// add to objects element array
			$this->elements[$spec['name']] = $spec;
			
			// add name of element to field array
			$this->fields[] = $spec['name'];
		}
		
		// chain it up
		return $this;
	}


	## OTHER FORM ELEMENTS ##

   /**
	* Adds a Label to the form
	*
	* Can accept two parameters defining the labels 
	* string and for value or one associative array param 
	* defining custom attributes.
	*
	* @access	public
	* @param	string
	* @param	string
	* @return	object
	*/
	public function label($label, $for=NULL)
	{
		$spec = array();
		
		// check if label isn't an array
		if (!is_array($label))
		{
			// make it so
			if($for)
				$spec['for'] = $for;
			else
				$spec['for'] = $label;
				
			$spec['value'] = $label;
			$spec['element'] = 'label';
		}
		else
		{
			$spec = $label;
			$spec['element'] = 'label';
		}
		
		// construct name for storage purposes
		$name = $spec['for'].'_label';
		
		// check we dont already have an element 
		// with this name in the form
		if (!$this->element_exists($name))
		{
			// add to objects element array
			$this->elements[$name] = $spec;
		}
		
		// chain it up
		return $this;
	}

   /**
	* Adds a tooltip to the form 
	*
	* Can accept a string parameter defining the tooltips 
	* message or one associative array param defining 
	* custom attributes.
	*
	* To link tooltip to an existing form item set the 'for'
	* attribute
	*
	* @access	public
	* @param	mixed
	* @return	object
	*/
	public function tooltip($description)
	{
		$spec = array();
		
		// check if description isn't an array
		if (!is_array($description))
		{
			$spec['element'] = 'p';
			$spec['value'] = $description;
		}
		else
		{
			$spec = $description;			
			$spec['element'] = 'p';
		}
		
		$tooltip_prefix = $this->config->item('tooltip_prefix', 'goodform');
		$tooltip_suffix = $this->config->item('tooltip_suffix', 'goodform');
		
		// add tooltip class
		$class_array[] = $tooltip_class;
		
		if ($spec['for'])
		{
			// add name-tooltip class
			$class_array[] = $tooltip_prefix.$spec['for'].$tooltip_suffix;
			unset($spec['for']);
		}
		
		// are classes defined in attributes
		if (isset($spec['class']))
			// add custom class to class array
			$class_array[] = $spec['class'];
		
		
		$spec['class'] = implode(' ', $class_array);
	
		$this->elements[] = $spec;
		
		return $this;
	}

   /**
	* Adds an error message to the form 
	*
	* Can accept two parameters defining the error 
	* string and the field it applys to or one 
	* associative array param defining custom attributes.
	*
	* To link tooltip to an existing form item set the 'for'
	* attribute
	*
	* @access	public
	* @param	mixed
	* @param	string
	* @return	object
	*/
	public function error($description, $for=NULL)
	{
		$spec = array();
		
		// check if description isn't an array
		if (!is_array($description))
		{
			$spec['class'] = 'error';
			
			if ($for)
				$spec['class'] .= ' error-'.$for;
			
			$spec['element'] = 'p';
			$spec['value'] = $description;
		}
		else
		{
			$spec = $description;
			$spec['element'] = 'p';
		}

		// load config vars
		$error_class = $this->config->item('error_class', 'goodform');
		$error_prefix = $this->config->item('error_prefix', 'goodform');
		$error_suffix = $this->config->item('error_suffix', 'goodform');
		
		// add error class
		$class_array[] = $error_class;
		
		if ($spec['for'])
		{
			// add name-error class
			$class_array[] = $error_prefix.$spec['for'].$error_suffix;
			unset($spec['for']);
		}
		
		// are classes defined in attributes
		if (isset($spec['class']))
			// add custom class to class array
			$class_array[] = $spec['class'];
		
		$spec['class'] = implode(' ', $class_array);
	
		$this->elements[] = $spec;
		
		return $this;
	}

   /**
	* Adds a clearing element to the form for
	* CSS clear floar hack
	*
	* @access	public
	* @return	object
	*/
	public function clear()
	{
		// add to objects element array
		$this->elements[] = array('element' => 'div', 'class' => 'clear');
		
		return $this;
	}

   /**
	* opens up a fieldset in the form
	* also closes an open fieldset
	*
	* @access	public
	* @param	mixed
	* @return	object
	*/
	public function fieldset($legend=NULL)
	{
		// close an existing fieldset
		$this->close_fieldset();
		
		// flag fieldset open
		$this->open_fieldset = TRUE;
		
		$attributes = array();
		
		// check if legend is an array
		if (is_array($legend))
		{
			$attributes = $legend;
			
			if (isset($attributes['legend']))
			{
				// convert to array
				$legend = $attributes['legend'];
				
				unset($attributes['legend']);
			}
		}
			
		// add fieldset html to element
		$this->html('<fieldset '.$this->array_to_attributes($attributes).'>');
						
		if ($legend)
			// add legend if label is set
			return $this->legend($legend);
		else
			return $this;
	}
	
   /**
	* closes an open fieldset in the form
	*
	* @access	public
	* @param	mixed
	* @param	array
	* @return	object
	*/
	public function close_fieldset()
	{
		// check if fieldset is already open
		if (!$this->open_fieldset)
			return $this;
		
		$this->open_fieldset = FALSE;
		
		// add fieldset closing tag html to element
		return $this->html('</fieldset>');	
	}
	
   /**
	* creates a legend element in the form
	*
	* @access	public
	* @param	string
	* @return	object
	*/
	public function legend($label, $attributes=NULL)
	{		
		$spec = array();
		
		// check if label isn't an array
		if (!is_array($label))
		{
			if (is_array($attributes))
				$spec = $attributes;
						
			// make it so
			$spec['value'] = $label;
			$spec['element'] = 'legend';
		}
		else
		{
			$spec = $label;
			$spec['element'] = 'legend';
		}
		
		// add to objects element array
		$this->elements[] = $spec;
		
		// chain it up
		return $this;
	}

   /**
	* Adds a custom html string to the form
	*
	* @access	public
	* @return	object
	*/
	public function html($string)
	{
		// add to objects element array
		$this->elements[] = array('element' => 'html', 'html' => $string);
		
		return $this;
	}


	##########################
	## VALIDATION METHODS	##
	##########################	

	/**
	 * Adds validation rules to a form element
	 *
	 * Can be rules for a specific field or an array
	 * of multiple fields and rules
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @param	string
	 * @return	void
	 */
	public function set_rules($field, $name, $rules = '')
	{
		// make sure CI validation is active
		if($this->ci_validation)
			// add rules
			$this->form_validation->set_rules($field, $name, $rules);
	}

	/**
	 * Set Error Message
	 *
	 * Lets users set their own error messages on the fly.  Note:  The key
	 * name has to match the  function name that it corresponds to.
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	function set_message($lang, $val = '')
	{		
		$this->form_validation->set_message($lang, $val);
	}

	##########################
	## GETTER/BUILD METHODS	##
	##########################	
	
   /**
	* Builds the form and returns the HTML
	*
	* Pass FALSE tp return the form contents without
	* a form element!
	*
	* @access	public
	* @param	mixed
	* @return	string
	*/
	public function generate($uri=NULL)
	{		
		$attributes = array();
	
		if (!is_array($uri))
			// add string to attributes array
			$attributes['action'] = $uri;
		else
			// copy param array to attributes
			$attributes = $uri;
		
		if(isset($attributes['action']))
			// convert to full url if not already
			$attributes['action'] = ( ! preg_match('!^\w+://! i', $attributes['action'])) ? site_url($attributes['action']) : $attributes['action'];

		if(!isset($attributes['method']))
			// add default method to the form
			$attributes['method'] = 'post';
		
		// return naked?
		if ($uri === FALSE)
			return $this->build_elements();
		
		return '<form '.$this->array_to_attributes($attributes).'>'.$this->build_elements().'</form>';
	}

   /**
	* comment
	*
	* @access	public
	* @param	string
	* @return	string
	*/
	public function element($name)
	{
		if(isset($this->elements[$name]))
		{
			$element = $this->build_element($this->elements[$name]);
		
			unset($this->elements[$name]);
			
			return $element;
		}
	}

   /**
	* Builds a given element of the form.
	* Returns the HTML and removes element from the form
	*
	* @access	public
	* @param	mixed
	* @return	string
	*/
	public function field($name)
	{
		//log_message('error', print_r($this->elements[$name], TRUE));
	
		if (isset($this->elements[$name]))
		
			return $this->build_element($this->elements[$name]);

	}	

   /**
	* Runs the form validation
	*
	* @access	public
	* @return	boolean
	*/
	public function run($validate=TRUE)
	{	
		if ($validate)
			// run form validation
			$this->valid = $this->form_validation->run();
		
		// update form fields with posted values
		foreach ($this->fields as $field)
		{
			if(isset($_POST[$field]))
				// update form with posted value
				$this->elements[$field]['value'] = $_POST[$field];
			
			if (form_error($field))
				// update form with error message
				$this->elements[$field]['error'] = form_error($field);
		}
		
		if ($validate)
			// return validation result
			return $this->valid;
	}

   /**
	* Returns TRUE/FALSE if the form
	* has been submitted
	*
	* @access	public
	* @return	boolean
	*/
	public function submitted()
	{
		foreach($this->fields as $field)
		{
			if (isset($_POST[$field]))
				return TRUE;
		}
		
		return FALSE;
	}

   /**
	* Builds all form elements in the object
	*
	* @access	private
	* @param	string
	* @return	string
	*/
	private function build_elements()
	{
		// array to hold elements
		$form = array();
		
		// loop through elements in array
		foreach($this->elements as $name => $attributes)
		{
			$form[] = $this->build_element($attributes);
		}
		
		return implode("\n", $form);
	}

   /**
	* Builds specific elements in the object
	*
	* @access	private
	* @param	array
	* @return	string
	*/
	private function build_element($attributes)
	{	
		$element = '';
		
		// look for label
		if (isset($attributes['label']))
			$element .= $this->build_label($attributes, FALSE);
		
		$type = $attributes['element'];
			
		switch($type)
		{
			case 'html':
				$element .= $attributes['html'];
				break;
					
			case 'group':
				$element .= $this->build_input_group($attributes);
				break;
				
			case 'input':
				$element .= $this->build_empty_element($type, $attributes);
				break;
				
			case 'select':			
				$attributes['value'] = $this->build_select_options($attributes);
				$element .= $this->build_nested_element($type, $attributes);
				break;
				
			case 'button':
				$element .= $this->build_nested_element_with_value($type, $attributes);
				break;
				
			default:
				$element .= $this->build_nested_element($type, $attributes);
				break;
		}
		
		// look for label
		if (isset($attributes['description']))
			$element .= $this->build_tooltip($attributes, FALSE);
		
		// look for error
		if (isset($attributes['error']))
			$element .= $this->build_error($attributes, FALSE);
		
		return $element;
	}
	
   /**
	* Builds a nested HTML element
	*
	* @access	private
	* @param	string
	* @param	array
	* @return	string
	*/
	private function build_nested_element($element, $attributes)
	{	
		// extract value from attributes if it exists
		if (isset($attributes['value']))
		{
			$value = $attributes['value'];
			unset($attributes['value']);
		}
		else
		{
			$value = NULL;
		}
		
		// const attribute string
		$att_str = $this->array_to_attributes($attributes);
		
		return '<'.$element.' '.$att_str.'>'.$value.'</'.$element.'>';
	}

	
   /**
	* Builds a nested HTML element that also has a value attribute
	*
	* @access	private
	* @param	string
	* @param	array
	* @return	string
	*/
	private function build_nested_element_with_value($element, $attributes)
	{	
		// extract value from attributes if it exists
		if (isset($attributes['text']))
		{
			$text = $attributes['text'];
			unset($attributes['text']);
		}
		else
		{
			$text = $attributes['value'];
		}
		
		// const attribute string
		$att_str = $this->array_to_attributes($attributes);
		
		return '<'.$element.' '.$att_str.'>'.$text.'</'.$element.'>';
	}
	
   /**
	* Builds an empty HTML element
	*
	* @access	private
	* @param	string
	* @param	array
	* @return	string
	*/
	private function build_empty_element($element, $attributes)
	{		
		// const attribute string
		$att_str = $this->array_to_attributes($attributes);
		
		return '<'.$element.' '.$att_str.'/>';
	}
	
   /**
	* Constructs the option elements for a select dropdown
	* element
	*
	* @access	private
	* @param	array
	* @return	string
	*/
	private function build_select_options($attributes)
	{
		// if no options defined return NULL
		if(!isset($attributes['options']))
			return;
		else
			$options = $attributes['options'];
		
		$value = NULL;
		$selected=array();
		
		// get value
		if (isset($attributes['value']))
		{
			if(is_array($attributes['value']))
				$selected = $attributes['value'];
			else
				$value = $attributes['value'];
		}
		else if(isset($attributes['selected']))
		{	
			if(is_array($attributes['selected']))
				$selected = $attributes['selected'];
			else
				$value = $attributes['selected'];
		}
		else		
			$value = NULL;
		
		$opt_arr = array();
		
		foreach ($options as $name => $v)
		{
			// check for optgroup array
			if (is_array($v))
			{
				// open optgroup
				$opt_arr[] = '<optgroup label="'.$name.'">';
				
				// define optgroup attributes
				$optgroup['options'] = $v;
				$optgroup['value'] = $value;
				
				// get optgroup options, recuse!
				$opt_arr[] = $this->build_select_options($optgroup);
				
				// close opt group 
				$opt_arr[] = '</optgroup>';
			}
			else
			{
				if (in_array($v, $selected) OR $v == $value)
					$opt_arr[] = '<option value="'.$v.'" selected="selected">'.$name.'</option>';			
				else
					$opt_arr[] = '<option value="'.$v.'">'.$name.'</option>';
			}
		}
		
		return implode("\n\t", $opt_arr);
	}


   /**
	* Builds an input group form element 
	* i.e. a group of radio or checkboxes
	*
	* @access	private
	* @param	string
	* @param	array
	* @return	string
	*/
	private function build_input_group($attributes)
	{
		// is this a vertical/horizontal group?
		$style = $this->get_element($attributes, 'direction', 'v');
	
		// get config options
		$prefix = $this->config->item('input_group_'.$style.'_prefix', 'goodform');
		$suffix = $this->config->item('input_group_'.$style.'_suffix', 'goodform');
		
		// check if attributes placeholder is set in prefix string
		if(strpbrk($prefix, '%att%'))
		{
			// make a copy of the attributes array
			$attr = $attributes;
			
			// remove name, type and value attributes as this is not a form field
			unset($attr['name']);
			unset($attr['type']);
			unset($attr['value']);
			
			// get attribute string
			$attr_str = $this->array_to_attributes($attr);
			
			if($attr_str)
				// replace with id attribute if set
				$prefix = str_replace('%att%', $attr_str, $prefix);
			else
				// else remove		
				$prefix = str_replace('%att%', '', $prefix);
		}
		
		
		
		$option_prefix = $this->config->item('input_group_'.$style.'_option_prefix', 'goodform');
		$option_suffix = $this->config->item('input_group_'.$style.'_option_suffix', 'goodform');
		
		$selected_option_prefix = $this->config->item('input_group_'.$style.'_selected_option_prefix', 'goodform');
		$selected_option_suffix = $this->config->item('input_group_'.$style.'_selected_option_suffix', 'goodform');
		
		$label_prefix= $this->config->item('input_group_'.$style.'_label_prefix', 'goodform');
		$label_suffix = $this->config->item('input_group_'.$style.'_label_suffix', 'goodform');
			
		$elements = array();
		
		// itterate through the options
		foreach($attributes['options'] as $name => $value)
		{
			// check for optgroup array
			if (is_array($value))
			{
				// load config options
				$optgroup_prefix = $this->config->item('input_group_v_optgroup_prefix', 'goodform');
				$optgroup_suffix = $this->config->item('input_group_v_optgroup_suffix', 'goodform');
				$optgroup_label_prefix = $this->config->item('input_group_v_optgroup_label_prefix', 'goodform');
				$optgroup_label_suffix = $this->config->item('input_group_v_optgroup_label_suffix', 'goodform');
				
				// open optgroup
				$elements[] = $optgroup_prefix.$optgroup_label_prefix.$name.$optgroup_label_suffix;
				
				// define optgroup attributes
				$optgroup['options'] = $value;
				$optgroup['value'] = $this->get_element($attributes, 'value');
				$optgroup['type'] = $this->get_element($attributes, 'type');
				$optgroup['name'] = $this->get_element($attributes, 'name');
				
				// get optgroup options, recuse!
				$elements[] = $this->build_input_group($optgroup);
				
				// close opt group 
				$elements[] = $optgroup_suffix;
			}
			else
			{
				// construct each option elements attributes
				$element_att = array(
					'value' => $value,
					'name' => $attributes['name'].'[]',	// turn into array
					'type' => $attributes['type']
				);
				
				// construct the options label	
				$label = $label_prefix.$name.$label_suffix;
				
				// check if element is selected
				if ($this->is_selected($value, $this->get_element($attributes, 'value')))
				{
					// add checked attribute
					$element_att['checked'] = 'checked';
					
					$elements[] = $selected_option_prefix.$this->build_empty_element('input', $element_att).$label.$selected_option_suffix;
				}
				else 
				{
					$elements[] = $option_prefix.$this->build_empty_element('input', $element_att).$label.$option_suffix;
				}
			}
		}
		
		// return element
		return $prefix.implode("\n", $elements).$suffix;
		
		
		// replace element attribute value with input group		
		$attributes['value'] = $prefix.implode("\n", $elements).$suffix;
		
		// remove name, type and checked attributes
		unset($attributes['name']);
		unset($attributes['type']);
		
		// add default group container class
		if (isset($attributes['class']))
			// prepend to existing class string
			$attributes['class'] = 'input-group-'.$style.' '.$attributes['class'];
		else
			// add class to attributes
			$attributes['class'] = 'input-group-'.$style;
		
		// wrap input group with container that includes defined attributes
		return $this->build_nested_element('div', $attributes);
	}

   /**
	* Builds a label element for a form element
	*
	* if second parameter is false only the for 
	* attribute is used
	*
	* @access	private
	* @param	string
	* @param	boolean
	* @return	string
	*/
	private function build_label($attributes, $use_attributes=TRUE)
	{
		if ($use_attributes)
			$label = $attributes;
		else
			$label = array();
	
		// construct label attributes
		$label['for'] = $attributes['name'];
		
		unset($attributes['name']);
		
		// check if this field is required
		if (stristr($this->get_element($attributes, 'validation'), 'required'))
		{
			// add required sign
			$attributes['label'] .=  $this->config->item('required_suffix', 'goodform');
		}
		
		$label['value'] = $attributes['label'];
		
		return $this->build_nested_element('label', $label);
	}

   /**
	* Builds a tooltip element for a form element
	*
	* if second parameter is false only the for 
	* attribute is used
	*
	* @access	private
	* @param	string
	* @param	boolean
	* @return	string
	*/
	private function build_tooltip($attributes, $use_attributes=TRUE)
	{
		if ($use_attributes)
			$tooltip = $attributes;
		else
			$tooltip = array();
	
		// load config vars
		$tooltip_class = $this->config->item('tooltip_class', 'goodform');
		$tooltip_prefix = $this->config->item('tooltip_prefix', 'goodform');
		$tooltip_suffix = $this->config->item('tooltip_suffix', 'goodform');
		
		// add tooltip class
		$class_array[] = $tooltip_class;
		
		// add name-tooltip class
		$class_array[] = $tooltip_prefix.$attributes['name'].$tooltip_suffix;
		
		// are classes defined in attributes
		if (isset($tooltip['class']))
			// add custome class to class array
			$class_array[] = $tooltip['class'];
		
		// implode classes to attribute string
		$tooltip['class'] = implode(' ', $class_array);
		
		$tooltip['value'] = $attributes['description'];
		
		return $this->build_nested_element('p', $tooltip);
	}

   /**
	* Builds an error element for a form element
	*
	* if second parameter is false only the for 
	* attribute is used
	*
	* @access	private
	* @param	string
	* @param	boolean
	* @return	string
	*/
	private function build_error($attributes, $use_attributes=TRUE)
	{
		if (!$attributes['error'])
			return;
	
		if ($use_attributes)
			$error = $attributes;
		else
			$error = array();
	
		// load config vars
		$error_class = $this->config->item('error_class', 'goodform');
		$error_prefix = $this->config->item('error_prefix', 'goodform');
		$error_suffix = $this->config->item('error_suffix', 'goodform');
		
		// add error class
		$class_array[] = $error_class;
		
		// add name-error class
		$class_array[] = $error_prefix.$attributes['name'].$error_suffix;
		
		// are classes defined in attributes
		if (isset($error['class']))
			// add custome class to class array
			$class_array[] = $error['class'];
		
		// implode classes to attribute string
		$error['class']	= implode(' ', $class_array);
		
		$error['value'] = $attributes['error'];
		
		return $this->build_nested_element('p', $error);
	}


	##########################
	## INTERNAL METHODS	 	##
	##########################	

   /**
	* This method will check if a form element is selected
	* used to decide selected elements in multi form elements
	* i.e. select boxes and check boxes
	*
	* - First param is the value of the current form element
	* - Second param is the elements selected values. Can be
	* 	an array or singular value.
	*
	* @access	private
	* @param	mixed
	* @param	mixed
	* @return	boolean
	*/
	private function is_selected($value, $selected='')
	{
		if (empty($selected))
			return FALSE;
	
		// is the selected value an array
		if (is_array($selected))
		{
			// is the value present in the array
			if (in_array($value, $selected))
				return TRUE;
			else
				return FALSE;
		}
		else
		{
			// value selected?
			if ($value == $selected)
				return TRUE;
			else
				return FALSE;
		}
		
	}

   /**
	* Safely returns an array element.
	* returns NULL if not defined
	*
	* @access	private
	* @param	array
	* @param	string
	* @param	mixed
	* @return	mixed
	*/
	private function get_element($array, $key, $default=NULL)
	{
		// check element exists
		if(isset($array[$key]))
			// return it
			return $array[$key];
		
		return $default;
	}

   /**
	* converts an associative array to a string of html
	* attributes
	*
	* @access	protected
	* @param	array
	* @return	string
	*/
	protected function array_to_attributes($attributes)
	{
		// return if no attributes defined
		if (!is_array($attributes))
			return '';
	
		// remove known invalid attribute keys
		$attributes = $this->prep_attributes($attributes);
	
		$att_array = array();	
		
		foreach ($attributes as $name => $value)
		{
			$att_array[] = $name.'="'.$value.'"';
		}
		
		return implode(' ', $att_array);
	}

   /**
	* removes all invalid attribute elements from
	* array
	*
	* @access	protected
	* @param	array
	* @return	array
	*/
	protected function prep_attributes($attributes)
	{
		// return if no attributes defined
		if (!is_array($attributes))
			return array();
			
		// define invalid attributes
		$invalid_attributes = $this->config->item('invalid_attributes', 'goodform');
	
		// loop though array and remove any invalid attributes
		foreach($attributes as $name => $value)
		{
			if(in_array($name, $invalid_attributes))
				unset($attributes[$name]);
		}
		 	
		// return array
		return $attributes;
	}

   /**
	* adds css classes to input field for jquery validation plugin
	*
	* @access	private
	* @param	string
	* @return	string
	*/
	private function prep_jquery_validation($attr, $rules)
	{
		// make sure jquery validation is active in config
		if($this->config->item('jquery_validation', 'goodform'))
		{
			// get existing classes and explode into an array
			$class_array = explode(' ', $this->get_element($attr, 'class', ''));
			
			// get class key from config
			$class_key = $this->config->item('jquery_validation_classes', 'goodform');
			
			// explode rules into array
			$rules = explode('|', $rules);
			
			// create array for metadata validation rules
			$metadata = array();
			
			// loop through field validation rules
			foreach($rules as $ci_rule)
			{
				// explode parameter from rule
				$array = explode('[', $ci_rule);
				
				$rule = $array[0];
				
				if(isset($array[1]))
					$param = str_replace(']', '', $array[1]);
				else
					$param = TRUE;
	
				//log_message('error', 'rule = '.$rule.' param = '.$param);
			
				// check if ci_rule exists in class key and it is not empty
				if(isset($class_key[$ci_rule]) AND !empty($class_key[$ci_rule]))
				{
					// add jquery class to attribute array
					$metadata[$class_key[$rule]] = $param;
				}
				/*else
				{
					log_message('error', 'cant find class for validation rule '.$ci_rule);
				}*/
			}
			
			if($metadata)
			{
				$meta_string = '{validate:{';
				
				foreach($metadata as $key => $value)
				{
					if($value === TRUE)
						$meta_string .= $key." : true,";
					else if($value === FALSE)
						$meta_string .= $key." : false,";
					else
						$meta_string .= $key." : '".$value."',";	
				}
				
				$meta_string .= '}}';
				
				// add metadata json obj to class
				$class_array[] = $meta_string;
				
				//log_message('error', 'metadata class = '.$meta_string);
			}
					
			if($class_array)
				// implode class array back to string
				$attr['class'] = implode(' ', $class_array);
		}
		
		// return attribute array
		return $attr;
	}

   /**
	* Checks the object element array to make sure this
	* is not a duplicate element name.
	*
	* @access	private
	* @param	string
	* @return	boolean
	*/
	private function element_exists($name)
	{
		if (array_key_exists($name, $this->elements))
		{
			log_message('error', 'Goodform Error: An element with the name '.$name.' already exists. This element has not been added to the form');
			
			return TRUE;
		}
		
		return FALSE;
	}

}
/* End of file goodform.php */
/* Location: ./application/librarys/goodform.php */