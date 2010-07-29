<?php

/**
 * Template DataMapper Model
 *
 * @license		MIT License
 * @category	Models
 * @author		Jim Wardlaw
 * @link		http://www.artandsoul.co.uk
 */
class Template extends DataMapper_Ext {
	
	// Uncomment and edit these two if the class has a model name that
	//   doesn't convert properly using the inflector_helper.
	// var $model = 'template';
	// var $table = 'templates';
	
	// You can override the database connections with this option
	// var $db_params = 'db_config_name';
	
	// --------------------------------------------------------------------
	// Relationships
	//   Configure your relationships below
	// --------------------------------------------------------------------
	
	// Insert related models that template can have just one of.
	var $has_one = array();
	
	// Insert related models that template can have more than one of.
	var $has_many = array(
		'page',
		'site'
	);
	
	// --------------------------------------------------------------------
	// Validation
	//   Add validation requirements, such as 'required', for your fields.
	// --------------------------------------------------------------------
	
	var $validation = array(
		'id' => array(
			'rules' => array(),
			'label' => 'Id',
			'type'	=> FALSE,
		),
		'name' => array(
			'rules' => array('required', 'max_length' => 64),
			'label' => 'Name',
			'type'	=> 'text',			
		),
		'description' => array(
			'rules' => array('required', 'max_length' => 256),
			'cols'	=> 50,
			'rows'	=> 5,
			'label' => 'Description',
			'table'	=> 'word_limiter[10]',
			'type'	=> 'textarea',
		),
		'zones' => array(
			'rules' => array('required', 'integer'),
			'label' => 'No# Zones',
			'size'	=> 2,
			'type'	=> 'text',
		),	
		'created' => array(
			'description' => 'When the record was created',	
			'label' => 'Created',
			'size' => 20,
			'table' => 'mysqldatetime_to_vector',	
			'type'	=> FALSE,
			'rules' => array(),
		),
		'updated' => array(
			'description' => 'When the record was last updated',	
			'label' => 'Updated',
			'table' => 'mysqldatetime_to_vector',
			'type'	=> FALSE,
		)
			
	);
	
	// --------------------------------------------------------------------
	// Default Ordering
	//   Uncomment this to always sort by 'name', then by
	//   id descending (unless overridden)
	// --------------------------------------------------------------------
	
	// var $default_order_by = array('name', 'id' => 'desc');
	
	// --------------------------------------------------------------------

	/**
	 * Constructor: calls parent constructor
	 */
    function __construct($id = NULL)
	{
		parent::__construct($id);
    }
    
    /**
	 * toString: returns string of object
	 */
    function __toString($id = NULL)
	{
		return (string)$this->name;
    }
    
    
    
  	// --------------------------------------------------------------------
	// Simple Site Methods
	// --------------------------------------------------------------------
    
   /**
	* return the view path relative to application/view
	*
	* @access	public
	* @return	string
	*/
	public function view()
	{
		return 'templates/'.url_title(strtolower($this->name), 'underscore');
	}
   
   /**
	* return true/false if this template has zones for partials
	*
	* @access	public
	* @return	boolean
	*/
	public function supports_partials()
	{
		if($this->zones > 0)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

   /**
	* returns an array of zone options for this template
	*
	* @access	public
	* @return	array
	*/
	public function zone_options()
	{
		$options=array();
		
		for($i=0; $i<$this->zones; $i++)
		{
			$options['Zone '.number_to_alphabet($i)] = $i;
		}
		
		return $options;
	}
}

/* End of file template.php */
/* Location: ./application/models/template.php */