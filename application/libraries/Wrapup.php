<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * WrapUp
 *
 * Manages the HTML Head as well as inline css and javascript in CodeIgniter
 *
 * UnDocumented Changes
 *
 * - added add_custom/custom methods to inject custom strings into the head
 * - added add_analytics method to add google analytics to a page
 *
 * @licence 	MIT Licence
 * @category	Librarys 
 * @author		Jim Wardlaw
 * @link		http://www.stucktogetherwithtape.com/code/wrapup
 * @version 	1.0.1
 *
 * Fixed bug in add_css when added new file as an array
 */ 
class Wrapup {
	
	private $doctype;
	private $title;
	private $head_link=array();
	private $css_link=array();
	private $css_import=array();
	private $css_inline=array();
	private $js_file=array();
	private $jquery=array();
	private $js_inline=array();
	private $meta=array();
	private $custom=array();

   /**
	* constructor, initialises the default
	* css and js directories
	*
	* @access	public
	* @return	this
	*/
	public function __construct()
	{				
		$this->load_config();
	}

   /**
	* Loads settings from the wrapup 
	* config file
	*
	* @access	private
	* @return	void
	*/
	private function load_config()
	{
		if ($CI =& get_instance())
		{
			$this->config = $CI->config;
		}

		$this->config->load('wrapup', TRUE, TRUE);

		$this->css_root = base_url().$this->config->item('css_dir', 'wrapup');
		$this->js_root = base_url().$this->config->item('js_dir', 'wrapup');
	}

	/**
	 * returns the html head element as a string
	 *
	 * @access	public
	 * @param	mixed
	 * @return	string
	 */	
	public function head($attributes=NULL)
	{
		if (is_array($attributes))
			// get head attribute string
			$attributes = $this->array_to_attributes($attributes);
	
		// code default html sttribures
		if($this->doctype == 0)
			// html 5
			$html_attributes = 'lang="en"';
		else
			// html 4
			$html_attributes = 'xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"';
	
		$html = $this->doctype().'
<html '.$html_attributes.'>
	<head '.$attributes.'>
		'.$this->title().'
		'.$this->meta().'
		'.$this->links().'
		'.$this->styles().'
		'.($this->config->item('put_js_at_bottom', 'wrapup') == FALSE ? $this->scripts() : '').'
		'.$this->custom().'
	</head>
';
		// return head	
		return $html;
	}

   /**
	* returns all inline js and css as a string
	*
	* @access	public
	* @return	string
	*/	
	function inline()
	{
		return '
		'.($this->config->item('put_js_at_bottom', 'wrapup') == TRUE ? $this->scripts() : '').'
		'.$this->get_jquery().'
		'.$this->get_inline_js().'
		'.$this->get_inline_css();
	}

   /**
	* Returns the doctype string
	*
	* @access	private
	* @return	string
	*/	
	private function doctype()
	{
		switch($this->doctype)
		{
			case 0:
				// html 5
				return '<!DOCTYPE HTML>';
			case 11:
				// html frameset
				return '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">';
				
			case 12:
				// xhml 1.0 frameset
				return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">';
					
			case 21:
				// html strict	
				return '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">';
				
			case 22:
				// xhtml strict	
				return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
							
			case 31:
				// html transitional
				return '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
				
			case 32:
			default:
				// xhtml transitional
				return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
		}
	}
	
   /**
	* Defines doctype of this html page
	*
	* $type	
	* s - strict
	* t - transitional
	* f - frameset
	*
	* $doc
	* xhtml / html
	*
	* @access	public
	* @param	string
	* @param 	string
	* @return	void
	*/	
	public function set_doctype($type, $doc='xhtml')
	{
		if ($type = 5)
			$this->doctype = 0;
		else
			// convert doctype to int and store in object
			$this->doctype = $this->get_doctype_type(strtolower($type)) + $this->get_doctype_doc(strtolower($doc));		
	}

   /**
	* returns numerical representation of
	* the doctype document
	*
	* @access	public
	* @param	string
	* @return	int
	*/	
	private function get_doctype_doc($string)
	{
		switch($string)
		{
			case 'html':
				return 1;
				
			case 'xhtml':
			default:
				return 2;
		}
	}

   /**
	* returns numerical representation of
	* the doctype type
	*
	* @access	public
	* @param	string
	* @return	int
	*/	
	private function get_doctype_type($string)
	{
		switch($string)
		{
			case 'f':
				return 10;
			
			case 's':
				return 20;
				
			case 't':
			default:
				return 30;
		}
	}
	
   /**
	* Sets the title of the html document in the 
	* head title element
	*
	* @access	public
	* @param	string
	*/	
	public function set_title($title)
	{
		$this->title = $title;
	}

   /**
	* returns the page title wrapped in its html tag
	*
	* @access	private
	* @return	string
	*/	
	private function title()
	{
		return '<title>'.$this->title.'</title>';
	}

   /**
	* Adds a new meta tag that will be linked in the
	* html head
	*
	* First param can either be a string containing the
	* meta name or an associative array containing all 
	* the meta attributes.
	*
	* @access	public
	* @param	mixed
	* @param	string
	* @return	void
	*/	
	public function add_meta($name, $content=NULL)
	{
		// check if name is an array
		if (is_array($name))
		{			
			// add new file to css array
			$this->meta[] = $name;
		}
		// must be a string
		else
		{			
			// add new tag to meta array
			$this->meta[] = array(	'name' => $name,
							 		'content' => $content);
		}
		
		
	}
	
   /**
	* returns all meta tags wrapped in their html tag
	*
	* @access	private
	* @return	string
	*/
	private function meta()
	{
		$meta = array();
		
		foreach($this->meta as $m)
		{
			$meta[] = '<meta '.$this->array_to_attriburtes($m).'/>';
		}
		
		return implode("\n", $meta);
	}

   /**
	* Adds a new link element in the head
	*
	* First param is an associative array containing a the 
	* link attributes.
	*
	* Use this method to define a favicon. All css files should
	* be linked using 'add_css' as this method checks the href
	* attribute.
	*
	* @access	public
	* @param	array
	* @return	void
	*/	
	public function add_link($attributes)
	{
		if (isset($attributes['href']))
			// check href is defined absolutely
			$attributes['href'] = $this->append_base_url($attributes['href']);
	
		// add new link to link array
		$this->head_link[] = $link;	
	}
	
   /**
	* Adds a favicon link in the html head
	*
	* First param can be a path string or an 
	* array of link element attributes
	*
	* @access	public
	* @param	mixed
	* @return	void
	*/	
	public function add_favicon($mixed)
	{
		// define default rss attributes
		$default_favicon_att = array('rel' => 'icon', 'type'  => 'image/x-icon');
		
		// array to hold the rss links attributes
		$attributes = array();
		
		// is parameter an array of attributes?	
		if (is_array($mixed))
		{
			// merge the given array with default attributes
			// parameter array will overide duplicate keys			
			$attributes = array_merge($default_favicon_att, $mixed);
		}
		// 'prolly just an href string then
		else
		{
			// add default attributes to array
			$attributes = $default_favicon_att;
			
			// add path specified in parameter
			$attributes['href'] = $mixed;
		}
		
		// check href is defined absolutely
		$attributes['href'] = $this->append_base_url($attributes['href']);
		
		// add rss link to object array
		$this->css_link[] = $attributes;
	}	
	
   /**
	* Adds a apple touch icon link in the html head
	*
	* First param can be a path string or an 
	* array of link element attributes
	*
	* @access	public
	* @param	mixed
	* @return	void
	*/	
	public function add_webclip($mixed=array())
	{		
		// define default rss attributes
		$default_webclip_att = array('rel' => 'apple-touch-icon', 'href'  => 'apple-touch-icon.png');
			
		// array to hold the rss links attributes
		$attributes = array();

		// is parameter an array of attributes?	
		if (is_array($mixed))
		{
			// merge the given array with default attributes
			// parameter array will overide duplicate keys			
			$attributes = array_merge($default_webclip_att, $mixed);
		}
		// 'prolly just an href string then
		else
		{
			// add default attributes to array
			$attributes = $default_webclip_att;
			
			// add path specified in parameter
			$attributes['href'] = $mixed;
		}
		
		// check href is defined absolutely
		$attributes['href'] = $this->append_base_url($attributes['href']);
		
		// add rss link to object array
		$this->css_link[] = $attributes;
	}	
	
	
   /**
	* Adds a rss link in the html head
	*
	* First param can be a path string or an 
	* array of link element attributes
	*
	* @access	public
	* @param	mixed
	* @return	void
	*/	
	public function add_rss($mixed)
	{	
		// define default rss attributes
		$default_rss_att = array('title' => 'RSS', 'rel' => 'alternate', 'type'  => 'application/rss+xml');
			
		// array to hold the rss links attributes
		$attributes = array();
		
		// is parameter an array of attributes?
		if (is_array($mixed))
		{			
			// merge the given array with default attributes
			// parameter array will overide duplicate keys			
			$attributes = array_merge($default_rss_att, $mixed);
		}
		// 'prolly just a href string then
		else
		{
			// add default attributes to array
			$attributes = $default_rss_att;
			
			// add path specified in parameter
			$attributes['href'] = $mixed;
		}
		
		// check href is defined absolutely
		$attributes['href'] = $this->append_base_url($attributes['href']);
		
		// add rss link to object array
		$this->css_link[] = $attributes;
	}
	
	
	
	
	
   /**
	* Returns all defined head links wrapped in 
	* their html tag
	*
	* @access	private
	* @return	string
	*/	
	private function links()
	{
		// return if no link files have been defined
		if (!$this->head_link)
			return;
			
		// init array to hold stylesheets
		$links = array();
		
		// loop through each file and define the html tag
		foreach ($this->head_link as $link)
		{
			// add link to styles array
			$links[] = link_tag($link);
		}
		
		return implode("\n\t\t", $links);
	}

	
   /**
	* Adds a custom string to the object to be added inside the head elemente
	*
	* @access	public
	* @param	string
	* @return	void
	*/	
	public function add_custom($string)
	{
		$this->custom[] = $string;
	}
	
   /**
   	* Returns all custom string defined in object
	*
	* @access	private
	* @return	void
	*/	
	private function custom()
	{
		if (count($this->custom) > 0 )
			return implode("\n", $this->custom);
	}
	
   /**
	* Adds a new css file that will be linked in the
	* html head
	*
	* First param can either be a string containing the
	* css path or an associative array containing a the 
	* link attributes.
	*
	* The CSS path will be prefixed with the default css 
	* bass directory unless it is a full path containing
	* http.... 
	*
	* @access	public
	* @param	mixed
	* @param	string
	* @return	void
	*/	
	public function add_css($path, $media='screen')
	{
		// check if path is an array
		if (is_array($path))
		{			
			// add new file to css array
			$this->css_link[] = $path;
		}
		// must be a string
		else
		{			
			// add new file to css array
			$this->css_link[] = array(	'href'  => $path,
								 		'media' => $media,
								 		'rel' 	=> 'stylesheet',
								 		'type'  => 'text/css');
		}	
	}
	
   /**
	* Adds a new conditional css file that will be linked
	* in the html head wrapped with a conditional css tag
	*
	* First param can either be a string containing the
	* css path or an associative array containing a the 
	* link attributes.
	*
	* The CSS path will be prefixed with the default css 
	* bass directory unless it is a full path containing
	* http.... 
	*
	* @access	public
	* @param	mixed
	* @param	string
	* @param	string
	* @return	void
	*/	
	public function add_conditional_css($path, $condition=NULL, $media='screen')
	{
		// check if path is an array
		if (is_array($path))
		{
			// has a condition been defined in array?
			if (!$path['condition'] AND $condition)
				// add condtion to array
				$path['condition'] = $condition;
						
			// add new file to css array
			$this->css_link[] = $path;
		}
		// must be a string
		else
		{			
			// add new file to css array
			$this->css_link[] = array(	'href'  => $path,
										'media' => $media,
										'condition' => $condition,
								 		'rel' 	=> 'stylesheet',
										'type'  => 'text/css');
		}	
	}

   /**
	* Adds a new css import file that will appear
	* in the html head section
	*
	* although technically a media type can be defined
	* in imports, IE chokes so user the add_css method
	* instead
	*
	* @access	public
	* @param	string
	* @return	void
	*/	
	public function import_css($path)
	{
		// add new file to import array
		$this->css_import[] = $path;
	}
	
   /**
	* Adds some inline css that will be inserted
	* in the html head
	*
	* @access	public
	* @param	string
	* @return	void
	*/	
	public function add_css_inline($css)
	{
		// add new css string to inline array
		$this->css_inline[] = $css;
	}

   /**
	* returns all defined css files wrapped in an html tag
	*
	* @access	private
	* @return	string
	*/	
	private function styles()
	{	
		// string to hold css
		$css = '';
		
		// get linked css stylesheets
		$css .= $this->get_css_links();
		
		// get imported stylesheets
		$css .= $this->get_imported_css();
		
		// return css string
		return $css;
	}

   /**
	* Returns all defined css link files wrapped in 
	* their html tag
	*
	* @access	private
	* @return	string
	*/	
	private function get_css_links()
	{
		// return if no css files have been defined
		if (!$this->css_link)
			return;
			
		// init array to hold stylesheets
		$styles = array();
		
		// ini array to hold minify css (local)
		$min_css = array();
		
		// loop through each file and define the html tag
		foreach ($this->css_link as $css)
		{
			// get full href path
			$css['href'] = $this->get_full_css_path($css['href']);
		
			// check for IE Condition
			if (isset($css['condition']))
			{
				$condition = $css['condition'];
				
				// remove condition
				unset($css['condition']);
			
				// add link to styles array wrapped in conditional comment
				$styles[] = '<!--[if '.$condition.']>'.link_tag($css).'<![endif]-->';
			}
			else
			{
				// check if minify js is on and this is a local file
				if($this->config->item('minify_css', 'wrapup') AND strstr($css['href'], $this->css_root))
				{
					// get local path
					$path = str_replace($this->css_root, '', $css['href']);
								
					//log_message('error', 'minify local path = '.$path);
					
					$min_css[] = $path;
									
					//$scripts[] = '<script type="'.$js['type'].'" src="'.$js['src'].'"></script>';
				}
				else
				{
					// add link to styles array
					$styles[] = link_tag($css);
				}
			}
		}
		
		if($min_css)
		{
			$min_path = $this->config->item('path_to_minify', 'wrapup');
			
			if(!$min_path)
				$min_path = base_url().'/min/';
				
			$min_path = $min_path.'b='.$this->config->item('minify_css_dir', 'wrapup').'&f='.implode(',', $min_css).'&1';
			
			//log_message('error', print_r($min_css, TRUE));
			
			//log_message('error', 'min path = '.$min_path);
			
			$styles[] = link_tag($min_path);
		}
		
		return implode("\n\t\t", $styles);
	}

   /**
	* Returns all defined css import files wrapped in 
	* their html tag
	*
	* @access	private
	* @return	string
	*/	
	private function get_imported_css()
	{
		// return if no css files have been defined
		if (!$this->css_import)
			return;
			
		// init array to hold stylesheets
		$styles = array();
		
		// loop through each file and define the html tag
		foreach ($this->css_import as $path)
		{
			// get full href path
			$path = $this->get_full_css_path($path);
		
			// add import file to styles array
			$styles[] = '@import url("'.$path.'");';
		}
		
		return '
		<style type="text/css">
		'.implode("\n\t\t", $styles).'
		</style>';
	}

   /**
	* Returns all defined inline css script wrapped in 
	* a css tag
	*
	* @access	private
	* @return	string
	*/	
	private function get_inline_css()
	{
		// return if no css files have been defined
		if (!$this->css_inline)
			return;
			
		// init array to hold stylesheets
		$styles = array();
		
		// loop through each file and define the html tag
		foreach ($this->css_inline as $css)
		{		
			// add inline css to styles array
			$styles[] = $css;
		}
		
		return '		
	<!-- Inline CSS -->
	<style type="text/css">	
		'.implode("\n\t\t", $styles).'
	</style>
';
	}

   /**
	* Returns the full path of the css file prefixed
	* with the root dir unless it is already a full
	* path
	*
	* @access	private
	* @param	string
	* @return	string
	*/		
	private function get_full_css_path($path)
	{
		// check if this link starts with http
		if(stripos($path, 'http') === 0)
			// full path, leave string alone
			return $path;
		
		else
			// append default css dir to path
			return $this->css_root.$path;
	}
	
   /**
	* Adds a new javascript file that will be linked in the
	* html head
	*
	* First param can either be a string containing the
	* js path or an associative array containing all the 
	* script elements attributes.
	*
	* The js path will be prefixed with the default js 
	* bass directory unless it is a full path containing
	* http.... 
	*
	* @access	public
	* @param	mixed
	* @return	void
	*/	
	public function add_js($path)
	{
		// check if path is an array
		if (is_array($path))
		{			
			// add new file to css array
			$this->js_file = $path;
		}
		// must be a string
		else
		{			
			// add new file to js array
			$this->js_file[] = array(	'src'  => $path,
								 		'type' => 'text/javascript');
		}	
	}

   /**
   	* Adds some inline javascript that will be inserted
	* in the html head
	*
	* @access	public
	* @param	string
	* @return	void
	*/	
	public function add_js_inline($script)
	{
		// add new js string to array
		$this->js_inline[] = $script;
	}

   /**
   	* Adds some inline jquery that will be inserted
	* in the html head between the jquery onLoad()
	* script
	*
	* @access	public
	* @param	string
	* @return	void
	*/
	public function add_jquery($script)
	{
		// add new jquery script string to array
		$this->jquery[] = $script;
	}
	
   /**
   	* Adds some inline jquery that will be inserted
	* in the html head between the jquery onLoad()
	* script
	* 
	* TODO: Needs to appear in seperate <script> tags to 
	* work properly!
	*
	* @access	public
	* @param	string
	* @return	void
	*/
	public function add_analytics($web_property_id)
	{
		// google analytics code
		$script = '
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src=\"" + gaJsHost + "google-analytics.com/ga.js\" type=\"text/javascript\"%3E%3C/script%3E"));';

		$this->js_inline[] = $script;
		
		$script = '
try {
var pageTracker = _gat._getTracker("'.$web_property_id.'");
pageTracker._trackPageview();
} catch(err) {}';
	
		// add new jquery script string to array
		$this->js_inline[] = $script;
	}

	/**
	 * returns all links to external javascript files
	 *
	 * @access	private
	 * @return	string
	 */	
	private function scripts()
	{
		// return if no css file have been defined
		if (!$this->js_file)
			return;
		
		// ini string
		$scripts = array();
		
		// ini array to hold minify scripts (local)
		$min_js = array();
		
		// loop through each file and define the html tag
		foreach ($this->js_file as $js)
		{
			// get full src path
			$js['src'] = $this->get_full_js_path($js['src']);
				
			// check for IE Condition
			if (isset($js['condition']))
			{
				// add link to styles array wrapped in conditional comment
				$scripts[] = '
		<!--[if '.$js['condition'].']>
		<script type="'.$js['type'].'" src="'.$js['src'].'"></script>
		<![endif]-->';
			}
			else
			{
				// check if minify js is on and this is a local file
				if($this->config->item('minify_js', 'wrapup') AND strstr($js['src'], $this->js_root))
				{
					// get local path
					$path = str_replace($this->js_root, '', $js['src']);
								
					//log_message('error', 'minify local path = '.$path);
					
					$min_js[] = $path;
									
					//$scripts[] = '<script type="'.$js['type'].'" src="'.$js['src'].'"></script>';
				}
				else
				{
					// add file to scripts array
					$scripts[] = '<script type="'.$js['type'].'" src="'.$js['src'].'"></script>';
				}
			}
		}
		
		if($min_js)
		{
			$min_path = $this->config->item('path_to_minify', 'wrapup');
			
			if(!$min_path)
				$min_path = base_url().'/min/';
				
			$min_path = $min_path.'b='.$this->config->item('minify_js_dir', 'wrapup').'&f='.implode(',', $min_js).'&1';
			
			//log_message('error', print_r($min_js, TRUE));
			
			//log_message('error', 'min path = '.$min_js);
			
			$scripts[] = '<script type="text/javascript" src="'.$min_path.'"></script>';
		}
		
		// return script string
		return implode("\n\t\t", $scripts);
	}

   /**
	* Returns any jquery defined in object
	* wrapped in the jQuery onLoad function
	*
	* @access	private
	* @return	string
	*/	
	public function get_jquery()
	{
		// return if no onload js has been defined
		if (!$this->jquery)
			return;
		
		// ini string
		$jquery = null;
		
		// loop through each file and define the html tag
		foreach ($this->jquery as $script){
		
			$jquery[] = $script;
		}
		
		return '		
	<!-- jQuery to be executed on page load -->
	<script type="text/javascript">
	$(document).ready(function() {
		'.implode("\n\t\t", $jquery).'
	});
	</script>';
	}

	/**
	 * Returns each piece of inline javascript
	 * as a string
	 *
	 * @access	private
	 * @return	string
	 */	
	private function get_inline_js()
	{
		// return if no inline js has been defined
		if (!$this->js_inline)
			return;
		
		// ini string
		$inline_js = null;
		
		// loop through each file and define the html tag
		foreach ($this->js_inline as $script){
		
			$inline_js[] = $script;
		}
		
		return '		
	<!-- Inline Javascript -->
	<script type="text/javascript">
		'.implode("\n\t\t", $inline_js).'
	</script>';
	}
		
   /**
	* Returns the full path of the js file 
	* prefixed path with the root dir unless 
	* it is already a full path
	*
	* @access	private
	* @param	string
	* @return	string
	*/		
	private function get_full_js_path($path)
	{
		// check if this link starts with http
		if(stripos($path, 'http') === 0)
			// full path, leave string alone
			return $path;
		
		else
			// append default js dir to path
			return $this->js_root.$path;
	}

   /**
	* Converts an associative array to a string
	* of element attributes and values
	*
	* @access	private
	* @param	array
	* @return	string
	*/		
	private function array_to_attriburtes($array)
	{
		$string_array = array();
		
		foreach($array as $name => $value)
		{
			$string_array[] = $name.'="'.$value.'"';
		}
		
		return implode(' ', $string_array);
	}
	
   /**
	* checks path to see if it is a full path.
	*
	* if not then appends the sites base url.
	* Extra directory(s) can be appended to the 
	* base_url by setting the second param
	*
	* @access	private
	* @param	string
	* @return	string
	*/
	private function append_base_url($path, $extra='')
	{
		// see if this is a full path
		if (substr($path, 0, 4) == 'http')
			// nothing to do here, move along
			return $path;
		
		// return full url with extra dir appended if set
		return base_url().$extra.$path;
	}

}
/* End of file tooltip.php */
/* Location: ./application/librarys/wrapup.php */