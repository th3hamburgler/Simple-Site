<?php

/**
 * Page DataMapper Model
 *
 * @license		MIT License
 * @category	Models
 * @author		Jim Wardlaw
 * @link		http://www.artandsoul.co.uk
 */
class Page extends DataMapper_Ext {
	
	// Uncomment and edit these two if the class has a model name that
	//   doesn't convert properly using the inflector_helper.
	// var $model = 'page';
	// var $table = 'pages';
	
	// You can override the database connections with this option
	// var $db_params = 'db_config_name';
	
	// --------------------------------------------------------------------
	// Relationships
	//   Configure your relationships below
	// --------------------------------------------------------------------
	
	// Insert related models that page can have just one of.
	var $has_one = array(
		'parent_page' => array(
			'class' => 'page',
            'other_field' => 'page'
		),
		//'page_group',
		'site',
		'template',
	);
	
	// Insert related models that page can have more than one of.
	var $has_many = array(
		//'meta',
		'navigation_item',
		'page_partial',
		'page' => array(
			'class' => 'page',
            'other_field' => 'parent_page'
		),
		'home_site' => array(
			'class' => 'site',
            'other_field' => 'home_page'
		),
	);
	
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
		'title' => array(
			'rules' => array('required', 'max_length' => 128),
			'label' => 'Title',
			'type'	=> 'text',
		),
		'slug' => array(
			'rules' => array('required', 'max_length' => 128),
			'label' => 'Slug',
			'type'	=> 'text',
		),
		'content' => array(
			'rules' => array(),
			'class' => 'markitup',
			'cols'	=> 50,
			'rows'	=> 15,
			'label' => 'Content',
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
		return (string)$this->title;
    }
    
   /**
	* method
	*
	* @access	public
	* @param	void
	* @return	void
	*/
	public function content()
	{
		return auto_typography(ascii_to_entities($this->content));
	}
}

/* End of file page.php */
/* Location: ./application/models/page.php */