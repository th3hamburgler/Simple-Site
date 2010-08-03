<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* CodeIgniter
*
* An open source application development framework for PHP 4.3.2 or newer
*
* @package        CodeIgniter
* @author        ExpressionEngine Dev Team
* @copyright    Copyright (c) 2006, EllisLab, Inc.
* @license        http://codeigniter.com/user_guide/license.html
* @link        http://codeigniter.com
* @since        Version 1.0
* @filesource
*/

// ------------------------------------------------------------------------


if ( ! function_exists('h1'))
{
   /**
	* 	h1
	*
	* Generates an HTML h1 element
	*
	* @author    Jim Wardlaw
	* @access    public
	* @param     string
	* @param     array
	* @return    string
	* @version   1.1
	*/ 
	function h1($contents, $attributes = NULL)
    {
    	return html_element('h1', $contents, $attributes);
    }
}
if ( ! function_exists('h2'))
{
   /**
	* h2
	*
	* Generates an HTML h2 element
	*
	* @author    Jim Wardlaw
	* @access    public
	* @param     string
	* @param     array
	* @return    string
	* @version   1.1
	*/ 
	function h2($contents, $attributes = NULL)
    {
    	return html_element('h2', $contents, $attributes);
    }
}
if ( ! function_exists('h3'))
{
   /**
	* h3
	*
	* Generates an HTML h2 element
	*
	* @author    Jim Wardlaw
	* @access    public
	* @param     string
	* @param     array
	* @return    string
	* @version   1.1
	*/ 
	function h3($contents, $attributes = NULL)
    {
    	return html_element('h3', $contents, $attributes);
    }
}
    
if ( ! function_exists('div'))
{
   /**
	* Div
	*
	* Generates an HTML div element
	*
	* @author    Jim Wardlaw
	* @access    public
	* @param     string
	* @param     array
	* @return    string
	* @version   1.1
	*/ 
	function div($contents, $attributes = NULL)
    {
    	return html_element('div', $contents, $attributes);
    }
}

if ( ! function_exists('html_element'))
{
   /**
	* HTML Element
	*
	* Generates a generic HTML element
	*
	* @author    Jim Wardlaw
	* @access    public
	* @param     string
	* @param     string
	* @param     array
	* @return    string
	* @version   1.1
	*/ 
	function html_element($tag, $contents, $attributes = NULL)
    {
    	// Were any attributes submitted?  If so generate a string
		if (is_array($attributes))
		{
			$atts = '';
			foreach ($attributes as $key => $val)
			{
				$atts .= ' ' . $key . '="' . $val . '"';
			}
			$attributes = $atts;
		}
		
    	return "<".$tag.$attributes.">".$contents."</".$tag.">";
    }
}

if ( ! function_exists('clear'))
{
   /**
	* Clear
	*
	* Generates an HTML clearing div element
	*
	* @author    Bradford Mar
	* @access    public
	* @return    string
	* @version   1.1
	*/ 
	function clear()
    {
    	return div('&nbsp;', array('class' => 'clear'));
    }
}

/**
* Definition List
*
* Generates an HTML definition list from an associative array.  Use "dt"
* and "dd" as keys and set the value as an array.
*
* @author    Bradford Mar
* @access    public
* @param     array
* @param     mixed
* @return    string
* @version   1.1
*/    
if ( ! function_exists('dlist'))
{
    function dlist($list, $attributes = '')
    {
        // If an array wasn't submitted there's nothing to do...
		if ( ! is_array($list))
		{
			return $list;
		}
		
		$out='';
		
		// Were any attributes submitted?  If so generate a string
		if (is_array($attributes))
		{
			$atts = '';
			foreach ($attributes as $key => $val)
			{
				$atts .= ' ' . $key . '="' . $val . '"';
			}
			$attributes = $atts;
		}
		
		// Write the opening list tag
		$out .= "\n<dl".$attributes.">\n";
		
		// Cycle through the list elements.  If an array is 
		foreach ($list as $title => $definition)
		{	
			// write the definition title tag
			$out .= "\t<dt>".$title."</dt>\n";
			
			// is definition an array?
			if(is_array($definition))
			{
				// loop through definitions
				foreach($definition as $d)
				{
					// add definition
					$out .= "\t<dd>".$d."</dd>\n";
				}
			}
			else if(!empty($definition))
			{
				// add definition
				$out .= "\t<dd>".$definition."</dd>\n";
			}
		}
	
		// Write the closing list tag
		$out .= "</dl>\n";

		return $out;
    }
}

/* End of file MY_html_helper.php */
/* Location: ./system/application/helpers/MY_html_helper.php */