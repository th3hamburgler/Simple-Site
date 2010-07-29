<?php
/**
 * Badtable
 *
 * Build HTML Tables in CI
 *
 * ## BUG FIXES ##
 * 	- get_cell_value() was looking for validation field 'table callback
 *	  Renamed 'table_callback' to 'table'
 *
 * CHANGES
 * - added support for cell links! (in progress)
 * - added support for link suffix
 *
 * @licence 	MIT Licence
 * @category	Librarys 
 * @author		Jim Wardlaw
 * @link		http://www.stucktogetherwithtape.com/code/goodform
 * @version 	1.0.2
 */ 
class Badtable {

	public $head_rows		= array();		// Array of table head rows
	public $head_attributes	= array();		// Array of table head row attributes
	
	public $foot_rows		= array();		// Array of table foot rows	
	public $foot_attributes	= array();		// Array of table foot row attributes	
		
	public $body_rows		= array();		// Array of table body rows
	public $body_attributes	= array();		// Array of table body row attributes
	
	public $caption='';						// Caption string for table
	public $columns=NULL;
	public $column_count=NULL;
	
	public $base_url=NULL;					// Base URL prefixed to all links in the table
	public $column_urls=NULL;				// Base URL prefixed to all links in a specific column
	public $row_urls=NULL;					// Base URL prefixed to all links in a specific row
	public $body_urls=NULL;					// 2D array of URLs for each table cell
	public $url_suffix=NULL;
	
	public $odd_class = 'odd';
	public $even_class = 'even';

   /**
	* Constructor
	*
	* @access	public
	* @param	string
	* @return	string
	*/
	public function __construct()
	{
	
	}
	
   /**
	* Clears data in the object
	*
	* @access	public
	* @param	void
	* @return	null
	*/
	public function clear()
	{
		$this->head_rows = array();			
		$this->head_attributes = array();
		
		$this->foot_rows = array();			
		$this->foot_attributes = array();
		
		$this->body_rows = array();			
		$this->body_attributes = array();	
		
		$this->caption = NULL;
		$this->caption_attributes = array();
		
		$this->columns = array();
		$this->column_count = NULL;
	}

   /**
	* Builds the table and returns the HTML
	*
	* @access	public
	* @param	array	-	table data/attributes
	* @param	array	-	attributes
	* @return	string
	*/
	public function generate($table_data=NULL, $attributes=NULL)
	{
		//log_message('error', print_r($this->head_attributes, TRUE));
	
		// The table data can optionally be passed to this function
		// either as a database result object or an array
		//if (!is_null($table_data) AND empty($this->body_rows))
		//{
			if (is_object($table_data))
			{
				$this->set_from_db_object($table_data);
			}
		//	else if (is_array($table_data))
		//	{
		//		$this->set_from_array($table_data);
		//	}
		//}
		else
		{
			// data already present in obj, first param will be attributes
			$attributes = $table_data;
		}
				
		$caption = $this->build_caption();	
		
		$head = $this->build_head();	
		
		$body = $this->build_body();
		
		$foot = $this->build_foot();
			
		return 
		$this->build_element('table', 
							 $caption.$head.$body.$foot, 
							 $attributes);
	
	}

	/**
	 * Set table data from a database result object
	 *
	 * @access	private
	 * @param	object
	 * @return	void
	 */
	private function set_from_db_object($query)
	{		
		// First generate the headings from the table column names
		// check if headings have been defined
		if (empty($this->head_rows))
		{
			if (!method_exists($query, 'list_fields'))
			{
				return FALSE;
			}
			
			$this->head_rows[] = $query->list_fields();
		}
				
		// Next blast through the result array and build out the rows		
		if ($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
				$this->body_rows[] = $row;
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
	public function get_cell_value($object, $field)
	{
		//log_message('error', 'Table column = '.$field);
		
		$field_callback = FALSE;
		
		// first check if this item is a callback function, i.e. contains '[]'
		// return string to var. $callback will be false if not a callback
		$callback = $this->strstr($field, ']', TRUE);
		
		// if its not a callback see if there is a table_callback
		// declared in the validation array for this field
		if ($callback == FALSE AND isset($object->validation[$field]['table']) == TRUE)
		{
			$field_callback = TRUE;
			// assign callback string to var
			$callback = $object->validation[$field]['table'];
		}
		
			
		// do we need to call a callback function
		if ($callback != FALSE)
		{					
			// split method from parameters
			$function = substr($this->strstr($callback, '[', TRUE), 0, -1);
			
			// check if no [] present
			if (!$function)
			{
				$function = $callback;
				
				$params = array();
			}
			else
			{
				// split the parameters string into an array
				$params = explode(",", substr($callback, (strlen($function)+1), -1));
			}
			
			if($field_callback)
			{
				// if this is a field callback function, add the 
				// field value as first parameter
				$params = array_merge(array($object->{$field}), $params);				
			}
						
			// check if method exists
			if(method_exists($object, $function))
			{				
				//log_message('error', 'call object '.$function.' with '.print_r($params, TRUE));
				
				return call_user_method_array($function, $object, $params);
			}	
			// check function exists
			else if (function_exists($function))
			{
				//log_message('error', 'call function '.$function.' with '.print_r($params, TRUE));
		
				// call function with arguments
				return call_user_func_array($function, $params);
			}
			else
			{
				log_message('error', 'function "'.$function.'" does not exist "'.$callback.'" in object '.get_class($object));
			}			
		}
		// is there an option array defined?
		if (isset($object->validation[$field]['options']) AND is_array($object->validation[$field]['options']))
		{
			//log_message('error', 'return option key');
			
			// return option key for matching value
			return array_search($object->{$field}, $object->validation[$field]['options']);
		}
		else
		{
			// return the raw value
			return $object->{$field};
		}		
	}
	
	
   /**
	* method to replicate php 5.3 functionality
	*
	* @access	private
	* @param	string
	* @return	string
	*/
	private function strstr($haystack, $needle, $before_needle=FALSE)
	{
		//Find position of $needle or abort
		if(($pos=strpos($haystack,$needle))===FALSE) return FALSE;
		
		if($before_needle) return substr($haystack,0,$pos+strlen($needle));
		else return substr($haystack,$pos);
	}

	/**
	 * Set table data from an array
	 *
	 * @access	private
	 * @param	array
	 * @return	void
	 */
	private function set_from_array($data)
	{
		// check if headings have been defined
		if (empty($this->head_rows))
		{
			// get first array and remove it
			$headings = array_shift($data);
			
			// set table headers
			$this->set_heading($headings);
		}
		
		$this->add_rows($data);
	}
		
   /**
	* Define the columns to display in this table
	*
	* Order is also defined by the order of columns
	* in the array.
	*
	* @access	public
	* @param	mixed
	* @param	array
	* @return	obj
	*/
	public function set_columns($num_columns, $column_attr=NULL)
	{
		// check parameter is an array
		if (!is_array($num_columns) AND !is_int($num_columns))
			log_message('error', 'Badtable: set_columns() - first argument must be an array or interger');
	
		// is this an integer
		if (is_int($num_columns))
		{
			$this->column_count = $num_columns;
			
			if(!empty($column_attr))
			{
				$this->columns = $column_attr;
			}
			else
			{
				$this->columns = array();
			}
		}
		else if (is_array($num_columns))
		{
			$this->column_count = count($num_columns);
			$this->columns = $num_columns;
		}
	
		return $this;
	}
	
   /**
	* sets the heading row from an array of values
	*
	* @access	public
	* @param	array	-	array of row values
	* @param	array	-	array of row attributes
	* @return	object
	*
	* NOTE: Can also accept discrete params as columns.
	* 		If last parameter is an array it will be used
	* 		as row attributes.
	*/
	public function set_heading($row, $attributes=NULL)
	{
		// collect method params as array
		$args = func_get_args();
		
		// check if first argument is not an array
		if (!is_array($row))
		{
			// see if last argument is an attribute array?
			if(is_array(end($args)))
			{
				// set attributes
				$attributes = array_pop($args);
			}
			else
			{
				// no attributes set
				$attributes = NULL;
			}
			// set row array
			$row = $args;			
		}		
	
		// add heading row to array
		$this->head_rows[] = $row;
		
		// add heading attributes to array	
		$this->head_attributes[] = $attributes;
		
		// return instance for chaniablility!
		return $this;
	}

   /**
	* sets the fotter row from an array of values
	*
	* @access	public
	* @param	array	-	array of row values
	* @param	array	-	array of row attributes
	* @return	object
	*
	* NOTE: Can also accept discrete params as columns.
	* 		If last parameter is an array it will be used
	* 		as row attributes.
	*/
	public function set_footer($row, $attributes=NULL)
	{
		// collect method params as array
		$args = func_get_args();
		
		// check if first argument is not an array
		if (!is_array($row))
		{
			// see if last argument is an attribute array?
			if(is_array(end($args)))
			{
				// set attributes
				$attributes = array_pop($args);
			}
			else
			{
				// no attributes set
				$attributes = NULL;
			}
			// set row array
			$row = $args;			
		}
	
		// add footer row to array
		$this->foot_rows[] = $row;
		
		// add footer attributes to array	
		$this->foot_attributes[] = $attributes;
		
		// return instance for chaniablility!
		return $this;
	}
	
   /**
	* Defines the caption for this table
	*
	* @access	public
	* @param	string
	* @param	array
	* @return	object
	*/
	public function set_caption($caption, $attributes=NULL)
	{
		$this->caption = $caption;
		
		$this->caption_attributes = $attributes;
	}

   /**
	* Define the base url for all cells in the table
	*
	* @access	public
	* @param	string
	* @return	obj
	*/
	public function set_base_url($url)
	{
		$this->base_url = $url;
		
		return $this;
	}

   /**
	* Define a suffix for all urls in the table
	*
	* @access	public
	* @param	string
	* @return	obj
	*/
	public function set_url_suffix($url)
	{
		$this->url_suffix = $url;
		
		return $this;
	}
	
   /**
	* Define a url prefix for specific columns
	*
	* @access	public
	* @param	mixed
	* @param	array
	* @return	obj
	*/
	public function set_column_url($num_columns, $column_url=NULL)
	{		
		// check parameter is an array
		if (!is_array($num_columns) AND !is_int($num_columns))
			log_message('error', 'Badtable: set_column_url() - first argument must be an array or integer');
	
		// is this an integer
		if (is_int($num_columns))
		{			
			$this->column_urls[$num_columns] = $column_url;

		}
		else if (is_array($num_columns))
		{
			$this->column_urls = $num_columns;
		}
	
		return $this;
	}

   /**
	* Define a url prefix for specific row(s)
	*
	* @access	public
	* @param	mixed
	* @param	array
	* @return	obj
	*/
	public function set_row_url($row_key, $row_url=NULL)
	{	
		// is this an integer
		if (is_int($row_key))
		{			
			$this->row_urls[$row_key] = $row_url;

		}
		else if (is_array($row_key))
		{
			$this->row_urls = $row_key;
		}
	
		return $this;
	}

   /**
	* Define a 2D array of cell urls
	*
	* @access	public
	* @param	array
	* @return	obj
	*/
	public function set_body_urls($arr)
	{
		$this->body_urls = $arr;
		
		return $this;
	}

   /**
	* Adds a new body data rows from an 2-d array of values
	*
	* @access	public
	* @param	array	-	array of row values
	* @param	array	-	array of row attributes
	* @return	object
	*/
	public function add_rows($rows, $attributes=NULL)
	{
		foreach($rows as $key => $row)
		{
			// check if attributes exist
			if (empty($attributes[$key]))
				$this->add_row($row);
			else	
				$this->add_row($row, $attributes[$key]);
		}
		
		return $this;
	}	

   /**
	* Adds a new body data row from an array of values
	*
	* @access	public
	* @param	array	-	array of row values
	* @param	array	-	array of row attributes
	* @return	object
	*
	* NOTE: Can also accept discrete params as columns.
	* 		If last parameter is an array it will be used
	* 		as row attributes.
	*/
	public function add_row($row, $attributes=NULL)
	{		
		// collect method params as array
		$args = func_get_args();
		
		// check if first argument is not an array
		if (!is_array($row))
		{
			// see if last argument is an attribute array?
			if(is_array(end($args)))
			{
				// set attributes
				$attributes = array_pop($args);
			}
			else
			{
				// no attributes set
				$attributes = NULL;
			}
			// set row array
			$row = $args;			
		}		
		
		// add body row to array
		$this->body_rows[] = $row;
		
		// add body attributes to array	
		$this->body_attributes[] = $attributes;
		
		// return instance for chaniablility!
		return $this;
	}
	
   /**
	* Builds the heading row 
	*
	* @access	private
	* @return	string
	*/
	private function build_head()
	{
		// return if heading empty
		if (empty($this->head_rows))
			return;
		
		// variable to hold thead contents
		$table_head = '';
				
		foreach($this->head_rows as $key => $row)
		{
			// define attribute var
			$attr = NULL;
			
			// see if this row has defined attributes		
			if (isset($this->head_attributes[$key]))
				// collect attributes
				$attr = $this->head_attributes[$key];
			
			// build the row HTML
			$table_head .= $this->build_head_row($row, $attr);	
		}
		
		// build thead HTML section and return
		return $this->build_element('thead', $table_head);
	}
	
   /**
	* Builds a table heading row
	*
	* @access	private
	* @param	array
	* @param	array
	* @return	string
	*/
	private function build_head_row($cells, $attributes)
	{
		$row = '';
		
		foreach($cells as $key => $data)
		{
			$attr = $this->get_column_attributes($key);
					
			$row .= $this->build_element('th', $data, $attr);
		}
		
		return $this->build_element('tr', $row, $attributes);
	}

   /**
	* Builds the body section of the table
	* without 'roids! 
	*
	* @access	private
	* @param	string
	* @return	string
	*/
	private function build_body()
	{
		// return if body empty
		if (empty($this->body_rows))
			return;
			
		// variable to hold tbody contents
		$table_body = '';
		
		// odd/even counter
		$count = 0;
				
		foreach($this->body_rows as $key => $row)
		{
			// define attribute var
			$attr = NULL;
			
			// see if this row has defined attributes		
			if (isset($this->body_attributes[$key]))
				// collect attributes
				$attr = $this->body_attributes[$key];
			
			if(!isset($attr['class']))
				$attr['class'] = '';
			
			// add odd/even class name to attributes				
			$attr['class'] = $this->add_odd_even_class($count, $attr['class']);
		
			// build row html
			$table_body .= $this->build_body_row($row, $attr, $key);
			
			// inc row counter
			$count++;
		}
		
		// build tbody HTML section and return
		return $this->build_element('tbody', $table_body);
	}

   /**
	* Builds the tables body rows
	*
	* @access	private
	* @param	array
	* @param	array
	* @param	int
	* @return	string
	*/
	private function build_body_row($cells, $r_attr, $row_key)
	{
		$row = '';
		
		foreach($cells as $cell_key => $data)
		{
			// force nbsp if null
			if(empty($data) OR $data == ' ')
				$data = '&nbsp;';
				
			$c_attr	= $this->get_column_attributes($row_key);
			
			$column_url = element($cell_key, $this->column_urls, TRUE);
						
			// check for FALSE column url
			if(!isset($this->column_urls[$cell_key]) OR $this->column_urls[$cell_key] !== FALSE)
			{
				$class = element('class', $c_attr, '');
				
				$c_attr['class'] = 'link-cell '.$class;
				
				$data 	= $this->build_body_anchor($data, $r_attr, $c_attr);
			}
			//else
			//	log_message('error', 'skip column');
			
			
			$row .= $this->build_element('td', $data, $c_attr);
		}
		
		return $this->build_element('tr', $row, $r_attr);
	}

   /**
	* Builds the body section of the table
	* without 'roids! 
	*
	* @access	private
	* @param	string
	* @return	string
	*/
	private function build_foot()
	{
		// return if footer empty
		if (empty($this->foot_rows))
			return;
		
		// variable to hold tfoot contents
		$table_head = '';
				
		foreach($this->foot_rows as $key => $row)
		{
			// define attribute var
			$attr = NULL;
			
			// see if this row has defined attributes		
			if (isset($this->foot_attributes[$key]))
				// collect attributes
				$attr = $this->foot_attributes[$key];
			
			// build the row HTML
			$table_head .= $this->build_foot_row($row, $attr);	
		}
		
		// build tfoot HTML section and return
		return $this->build_element('tfoot', $table_head);

	}

   /**
	* Builds the table footer rows
	*
	* @access	private
	* @param	array
	* @param	array
	* @return	string
	*/
	private function build_foot_row($cells, $attributes)
	{
		$row = '';
		
		foreach($cells as $key => $data)
		{
			$attr = $this->get_column_attributes($key);
			$row .= $this->build_element('td', $data, $attr);
		}
		
		return $this->build_element('tr', $row, $attributes);
	}

   /**
	* Builds the table caption element
	*
	* @access	private
	* @return	string
	*/
	private function build_caption()
	{
		// return null if headers not set
		if (empty($this->caption))
			return;
		
		return $this->build_element('caption', $this->caption, $this->caption_attributes);
	}


   /**
	* Constructs a cell link and wraps content in anchor
	*
	* @access	private
	* @param	string
	* @param	string
	* @param	string
	* @return	string
	*/
	private function build_body_anchor($cell_data, $row_attributes, $column_attributes)
	{
		$url = $this->base_url;
		
		if(isset($column_attributes['url']))
			$url .= $column_attributes['url'];
	
		if (isset($row_attributes['url']))
			$url .= $row_attributes['url'];
			
		if (empty($cell_data) AND $cell_data !== 0)
			$cell_data = '&nbsp;';
		
		return $this->build_anchor($cell_data, $url);
	}
	
   /**
	* wraps cell data in a clickable anchor
	*
	* @access	private
	* @param	string
	* @param	string
	* @return	string
	*/
	private function build_anchor($cell_data, $url)
	{
		if ($this->base_url)
		{
			if($url)
				$url .= $this->url_suffix;
		
			return anchor($url, $cell_data);
		}
		
		return $cell_data;
	}

   /**
	* returns an attribute array for given column
	*
	* @access	private
	* @param	stirng
	* @return	array
	*/
	private function get_column_attributes($key)
	{		
		if (isset($this->columns[$key]))
		{
			$attributes = $this->columns[$key];
			
			if (is_array($attributes))
			
				return $attributes;
		}
	}

   /**
	* adds an odd/even class to row attributes array
	*
	* @access	private
	* @param	string
	* @param	array
	* @return	array
	*/
	private function add_odd_even_class($number, $class=NULL)
	{
		// get class
		$oe_class = $this->get_row_class($number);
		
		if(empty($class))
			$class = $oe_class;
		else
			$class .= ' '.$oe_class;
			
		return $class;
	}

   /**
	* returns a odd even class for a row
	*
	* @access	private
	* @param	int
	* @return	string
	*/
	private function get_row_class($number)
	{
		if( $odd = $number%2 )
		{
		    return $this->even_class;
		}
		else
		{
		    return $this->odd_class;
		}
	}
	
   /**
	* Builds an HTML attribute from a tag string and
	* associative array of field values
	*
	* @access	private
	* @param	string	-	HTML tag
	* @param	string	-	element value
	* @param	array	-	attribute array
	* @return	string
	*/
	private function build_element($tag, $value, $attributes=NULL)
	{
		if (empty($value) AND $value !== 0)
			$value = '&nbsp;';
	
		return '<'.$tag.$this->array_to_attributes($attributes).'>'.$value.'</'.$tag.'>';
	}

   /**
	* converts an associative array to a string of html
	* attributes
	*
	* @access	protected
	* @param	array
	* @return	string
	*/
	protected function array_to_attributes($attributes=NULL)
	{
		if (!is_array($attributes))
			return;
		
		// remove url attr
		if(isset($attributes['url']))
			unset($attributes['url']);
			
		$att_array = array();	
		
		foreach ($attributes as $name => $value)
		{
			$att_array[] = $name.'="'.$value.'"';
		}
		
		return ' '.implode(' ', $att_array);
	}
	
   /**
	* Checks if an array is associative or not
	*
	* @access	private
	* @param	array
	* @return	boolean
	*/
	private function is_assoc($array) 
	{
		foreach (array_keys($array) as $k => $v) {
			
			if ($k !== $v)
				return true;
			}
		
		return false;
	}
	
}

/* End of file badtable.php */
/* Location: ./application/library/badtable.php */