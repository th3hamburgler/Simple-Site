<?php

/**
 * page_partial DataMapper Model
 *
 * @license		MIT License
 * @category	Models
 * @author		Jim Wardlaw
 * @link		http://www.artandsoul.co.uk
 */
class Page_Partial extends DataMapper_Ext {
	
	// Uncomment and edit these two if the class has a model name that
	//   doesn't convert properly using the inflector_helper.
	// var $model = 'page_partial';
	// var $table = 'page_partials';
	
	// You can override the database connections with this option
	// var $db_params = 'db_config_name';
	
	// --------------------------------------------------------------------
	// Relationships
	//   Configure your relationships below
	// --------------------------------------------------------------------
	
	// Insert related models that page_partial can have just one of.
	var $has_one = array(
		'page',
		'partial',
	);
	
	// Insert related models that page_partial can have more than one of.
	var $has_many = array();
	
	// --------------------------------------------------------------------
	// Validation
	//   Add validation requirements, such as 'required', for your fields.
	// --------------------------------------------------------------------
	
	var $validation = array(
		'id' => array(
			'rules' => array(),
			'label' => 'Id',
			'type'	=> FALSE
		),
		'zone' => array(
			'rules' => array('required', 'integer'),
			'label' => 'Zone',
			'type'	=> 'text',
			'size'	=> 2,
		),
		'order' => array(
			'rules' => array('integer'),
			'label' => 'Order',
			'type'	=> FALSE,
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
			'rules' => array(),
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
		$this->partial->get();
	
		return (string)$this->partial->name;
    }
}

/* End of file page_partial.php */
/* Location: ./application/models/page_partial.php */