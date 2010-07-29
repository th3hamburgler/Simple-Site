<?php

/**
 * Meta DataMapper Model
 *
 * @license		MIT License
 * @category	Models
 * @author		Jim Wardlaw
 * @link		http://www.artandsoul.co.uk
 */
class Meta extends DataMapper {
	
	// Uncomment and edit these two if the class has a model name that
	//   doesn't convert properly using the inflector_helper.
	// var $model = 'meta';
	// var $table = 'metas';
	
	// You can override the database connections with this option
	// var $db_params = 'db_config_name';
	
	// --------------------------------------------------------------------
	// Relationships
	//   Configure your relationships below
	// --------------------------------------------------------------------
	
	// Insert related models that meta can have just one of.
	var $has_one = array();
	
	// Insert related models that meta can have more than one of.
	var $has_many = array();
	
	// --------------------------------------------------------------------
	// Validation
	//   Add validation requirements, such as 'required', for your fields.
	// --------------------------------------------------------------------
	
	var $validation = array(
		'id' => array(
			'rules' => array('required'),
			'label' => 'Id'
		),
		'name' => array(
			'rules' => array('required', 'max_length' => 64),
			'label' => 'Name'
		),
		'content' => array(
			'rules' => array('required'),
			'label' => 'Content'
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
}

/* End of file meta.php */
/* Location: ./application/models/meta.php */