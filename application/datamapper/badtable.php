<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Table Extension for DataMapper classes.
 *
 * Quickly turn DM Objects into HTML Tables
 *
 * @license 	MIT License
 * @category	DataMapper Extensions
 * @author  	Jim Wardlaw
 * @link    	http://www.stucktogetherwithtape.com/code/
 * @version 	1.02
 *
 * Changes
 * -------
 * 1.02			- added add_parsed_string() method
 * 1.01			- added callback function support to add_model_field method
 * 1.0			- first release
 */

// --------------------------------------------------------------------------

/**
 * DMZ_Badtable Class
 */
class DMZ_Badtable {

	#####################################
	## METHODS TO GENERATE HTML TABLES ##
	#####################################

   /**
	* Method will return records currently in
	* '$object->all' array as an html table
	*
	* @access	public
	* @param	obj		-	instance of the DM Object calling this extension
	* @param	object	-	reference to instance of badtable object
	* @param	array	-	array of fields to add to table as columns
	* @return	obj
	*/
	public function table($object, &$badtable, $columns='')
	{
		if(empty($columns))
			// extract all fields from validation array
			// into the columns array if it is empty
			$columns = $object->fields;
			
		// add table heading
		$this->table_heading($object, $badtable, $columns);
		
		$this->table_body($object, $badtable, $columns);
		//$badtable->from_dmz($object, $columns);
		
		return TRUE;
	}

   /**
	* Constructs the table heading
	*
	* @access	private
	* @param	obj		-	instance of the DM Object calling this extension
	* @param	object	-	reference to instance of badtable object
	* @param	string
	* @return	string
	*/
	private function table_heading($object, &$badtable, $columns='')
	{
		// array of head cell strings
		$header = array();
	
		// loop through column array
		foreach($columns as $key => $field)
		{
			// look to see if this column has a heading
			// defined in the key
			if (!is_numeric($key))
			{
				$header[] = $key;
			}
			// get label from object
			else
			{
				// get label
				$label = $object->get_label($field);
				
				// add blank heading
				$header[] = $label;
			}
		}
		
		// add headers
		$badtable->set_heading($header);
	}

   /**
	* Constructs the table body
	*
	* @access	private
	* @param	obj		-	instance of the DM Object calling this extension
	* @param	object	-	reference to instance of badtable object
	* @param	string
	* @return	string
	*/
	private function table_body($object, &$badtable, $columns='')
	{
		// blast through dm all array
		foreach($object->all as $obj)
		{
			// ini row array
			$row = array();
			
			foreach($columns as $key => $field)
			{
				$row[] = $this->add_cell_value($obj, $field);
			}
			
			$badtable->add_row($row, array('id' => $obj->id, 'url' => $obj->id));
		}
	}

   /**
	* Method returns cell value from an object
	*
	* enacts callback function/method if it is set
	*
	* @access	public
	* @param	object
	* @param	field
	* @return	string
	*/
	public function add_cell_value($object, $field)
	{
		// check field exists in dm model
		if(isset($object->has_one[$field]))
		{
			return $this->add_related_field($object, $field);		
		}
		else if(isset($object->has_many[$field]))
		{
			return $this->add_related_count($object, $field);		
		}
		else if (isset($object->validation[$field]))
		{
			return $this->add_model_field($object, $field);
		}
		else
		{
			// check if this field is a callback
			$callback = $field;
			
			// replace '[' with ','
			$callback = str_replace('[', ',', $callback);
						
			// replace ']' with ''
			$callback = str_replace(']', '', $callback);
			
			// explode on ','
			$params = explode(',', $callback);
			
			// first element = function name
			$function = $params[0];
			
			// replace function name with field value
			$params[0] = $object->{$field};
			
			// does method exist in object
			if(method_exists($object, $function))
			{
				return call_user_func_array(array($object, $function), $params);
			}
			// does function exist
			else if(function_exists($function))
			{
				//log_message('error', 'call function '.$function);
				return call_user_func_array($function, $params);
			}
			else {	
				// must be a string
				return $this->add_parsed_string($object, $field);
				//log_message('error', 'DMZ Badtable: Field '.$field.' does not exist in DM validation array or one of its related models');
			}
		}
	}
	
   /**
	* Method returns cell value from an object
	*
	* enacts callback function/method if it is set
	*
	* @access	public
	* @param	object
	* @param	field
	* @return	string
	*/
	public function add_related_field($object, $model)
	{
		//log_message('error', 'add_related_field '.$model);
		
		$object->related($model);
		
		if($object->$model->exists())	
			return $object->$model;
		
		return NULL;
		
	}
	
   /**
	* Method returns cell value from an object
	*
	* enacts callback function/method if it is set
	*
	* @access	public
	* @param	object
	* @param	field
	* @return	string
	*/
	public function add_related_count($object, $model)
	{
		return $object->related_count($model);
	}
	
   /**
	* Method returns cell value from an object
	*
	* enacts callback function/method if it is set
	*
	* @access	public
	* @param	object
	* @param	field
	* @return	string
	*/
	public function add_model_field($object, $field)
	{
		//log_message('error', 'add_model_field');
		
		// attempt to load callback function
		$callback = element('table', $object->validation[$field]);
		
		// check for callback func exists
		if($callback)
		{
			// replace '[' with ','
			$callback = str_replace('[', ',', $callback);
						
			// replace ']' with ''
			$callback = str_replace(']', '', $callback);
			
			// explode on ','
			$params = explode(',', $callback);
			
			// first element = function name
			$function = $params[0];
			
			// replace function name with field value
			$params[0] = $object->{$field};
			
			// does method exist in object
			if(method_exists($object, $function))
			{
				return call_user_func_array(array($object, $function), $params);
			}
			// does function exist
			else if(function_exists($function))
			{
				return call_user_func_array($function, $params);
			}
			else
			{
				log_message('error', 'Badtable Datamapper Extension: table callback '.$callback.' does not exist');
			}
		}
		
		// has this field got an option array?
		if($object->has_options($field))
			// return option string
			return $object->get_option_string($field);
		
		return $object->{$field};
	}
	
   /**
	* Method returns cell value from an object
	*
	* enacts callback function/method if it is set
	*
	* @access	public
	* @param	object
	* @param	field
	* @return	string
	*/
	public function add_custom_field($object, $field)
	{
		log_message('error', 'add_custom_field');
	}
	
   /**
	* Method returns a parsed string.
	*
	* Placeholder sections of the string are replaces
	* with this objects field values
	*
	* @access	public
	* @param	object
	* @param	string
	* @return	string
	*/
	public function add_parsed_string($object, $field)
	{
		return str_replace('{ID}', $object->id, $field);
	}
}