<?php

/**
 * navigation_item DataMapper Model
 *
 * @license		MIT License
 * @category	Models
 * @author		Jim Wardlaw
 * @link		http://www.artandsoul.co.uk
 */
class Navigation_Item extends DataMapper_Ext {
	
	// Uncomment and edit these two if the class has a model name that
	//   doesn't convert properly using the inflector_helper.
	// var $model = 'navigation_item';
	// var $table = 'navigation_items';
	
	// You can override the database connections with this option
	// var $db_params = 'db_config_name';
	
	// --------------------------------------------------------------------
	// Relationships
	//   Configure your relationships below
	// --------------------------------------------------------------------
	
	// Insert related models that navigation_item can have just one of.
	var $has_one = array(
		'page',
		'navigation',
	);
	
	// Insert related models that navigation_item can have more than one of.
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

/* End of file navigation_item.php */
/* Location: ./application/models/navigation_item.php */