<?php

/**
 * Site DataMapper Model
 *
 * @license		MIT License
 * @category	Models
 * @author		Jim Wardlaw
 * @link		http://www.artandsoul.co.uk
 */
class Site extends DataMapper_Ext {
	
	// Uncomment and edit these two if the class has a model name that
	//   doesn't convert properly using the inflector_helper.
	// var $model = 'site';
	// var $table = 'sites';
	
	// You can override the database connections with this option
	// var $db_params = 'db_config_name';
	
	// --------------------------------------------------------------------
	// Relationships
	//   Configure your relationships below
	// --------------------------------------------------------------------
	
	// Insert related models that site can have just one of.
	var $has_one = array(
		'home_page' => array(
			'class' => 'page',
            'other_field' => 'home_site'
		),
	);
	
	// Insert related models that site can have more than one of.
	var $has_many = array(
		'meta',
		'navigation',
		'page',
		'page_group',
		'partial',
		'template',
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
			'label' => 'Site Name',
			'size'	=> 30,
			'type'	=> 'text',
		),
		'url' => array(
			'rules' => array('required', 'max_length' => 64),
			'label' => 'Site URL',
			'size'	=> 30,
			'type'	=> 'text',
		),
		'created' => array(
			'description' => 'When the record was created',	
			'label' => 'Created',
			'size' => 20,
			'table' => 'mysqldatetime_to_date[d/m/y]',	
			'type'	=> FALSE,
			'rules' => array(),
		),
		'updated' => array(
			'description' => 'When the record was last updated',	
			'label' => 'Updated',
			'table' => 'mysqldatetime_to_date[d/m/y]',	
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
}

/* End of file site.php */
/* Location: ./application/models/site.php */