<?php

/**
 * Partial DataMapper Model
 *
 * @license		MIT License
 * @category	Models
 * @author		Jim Wardlaw
 * @link		http://www.artandsoul.co.uk
 */
class Partial extends DataMapper_Ext {
	
	// Uncomment and edit these two if the class has a model name that
	//   doesn't convert properly using the inflector_helper.
	// var $model = 'partial';
	// var $table = 'partials';
	
	// You can override the database connections with this option
	// var $db_params = 'db_config_name';
	
	// --------------------------------------------------------------------
	// Relationships
	//   Configure your relationships below
	// --------------------------------------------------------------------
	
	// Insert related models that partial can have just one of.
	var $has_one = array(
		'site',
	);
	
	// Insert related models that partial can have more than one of.
	var $has_many = array(
		'page_partial'
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
		'name' => array(
			'rules' => array('required', 'max_length' => 128),
			'label' => 'Name',
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
		return (string)$this->name;
    }

   /**
	* returns the unique markup id for this partial
	*
	* @access	public
	* @return	string
	*/
	public function mkup_id()
	{
		return underscore($this->name);
	}

   /**
	* returns the unique markup classes for this partial
	*
	* @access	public
	* @return	string
	*/
	public function mkup_class()
	{
		$classes = array();
		
		// default partial class
		$classes[] = 'partial';
		
		// add id as class
		$classes[] = $this->mkup_id();
	
		return implode(' ', $classes);
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

/* End of file partial.php */
/* Location: ./application/models/partial.php */