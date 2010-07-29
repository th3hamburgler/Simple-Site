<?php

/**
 * Navigation DataMapper Model
 *
 * @license		MIT License
 * @category	Models
 * @author		Jim Wardlaw
 * @link		http://www.artandsoul.co.uk
 */
class Navigation extends DataMapper_Ext {
	
	// Uncomment and edit these two if the class has a model name that
	//   doesn't convert properly using the inflector_helper.
	// var $model = 'navigation';
	// var $table = 'navigations';
	
	// You can override the database connections with this option
	// var $db_params = 'db_config_name';
	
	// --------------------------------------------------------------------
	// Relationships
	//   Configure your relationships below
	// --------------------------------------------------------------------
	
	// Insert related models that navigation can have just one of.
	var $has_one = array();
	
	// Insert related models that navigation can have more than one of.
	var $has_many = array(
		'navigation_item',
		'site',
	);
	
	// --------------------------------------------------------------------
	// Validation
	//   Add validation requirements, such as 'required', for your fields.
	// --------------------------------------------------------------------
	
	var $validation = array(
		'id' => array(
			'rules' => array(),
			'label' => 'Id'
		),
		'name' => array(
			'rules' => array('required', 'max_length' => 64),
			'label' => 'Name'
		),
		'description' => array(
			'rules' => array('required', 'max_length' => 256),
			'cols'	=> 50,
			'rows'	=> 5,
			'label' => 'Description',
			'table'	=> 'word_limiter[12]',
			'type'	=> 'textarea',
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
	* Return the slug for this navigation
	*
	* @access	public
	* @return	string
	*/
	public function slug()
	{
		return underscore($this->name);
	}
}

/* End of file navigation.php */
/* Location: ./application/models/navigation.php */