<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter URL Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Jim Wardlaw
 */

 // ------------------------------------------------------------------------

/**
 * Mothership Site URL
 *
 * Create a local mothership URL based on your basepath and site config. 
 * Segments can be passed via the first parameter either as a string or an array.
 *
 * @access	public
 * @param	string
 * @return	string
 */
if ( ! function_exists('mo_site_url'))
{
	function mo_site_url($uri = '')
	{	
		$CI =& get_instance();
		
		// prefix uri with mothership dir
		$uri = $CI->config->item('mo_url').$uri;
	
		return $CI->config->site_url($uri);
	}
}

 // ------------------------------------------------------------------------

/**
 * Mothership CSS URL
 *
 * Create a local mothership CSS URL based on your basepath and view theme. 
 *
 * @access	public
 * @param	string
 * @return	string
 */
if ( ! function_exists('mo_css_url'))
{
	function mo_css_url($uri = '')
	{	
		$CI =& get_instance();
		
		// prefix uri with mothership and css dir
		$uri = $CI->config->item('mo_css_dir').$CI->config->item('mo_url').$CI->config->item('mo_theme_dir').$uri;
	
		return $CI->config->site_url($uri);
	}
}

 // ------------------------------------------------------------------------

/**
 * Mothership Anchor Link
 *
 * Creates an anchor based on the local mothership URL.
 *
 * @access	public
 * @param	string	the URL
 * @param	string	the link title
 * @param	mixed	any attributes
 * @return	string
 */
if ( ! function_exists('mo_anchor'))
{
	function mo_anchor($uri = '', $title = '', $attributes = '')
	{
		$title = (string) $title;

		if ( ! is_array($uri))
		{
			$site_url = ( ! preg_match('!^\w+://! i', $uri)) ? mo_site_url($uri) : $uri;
		}
		else
		{
			$site_url = mo_site_url($uri);
		}

		if ($title == '')
		{
			$title = $site_url;
		}

		if ($attributes != '')
		{
			$attributes = _parse_attributes($attributes);
		}

		return '<a href="'.$site_url.'"'.$attributes.'>'.$title.'</a>';
	}
}

// ------------------------------------------------------------------------

/**
 * Mothership Header Redirect
 *
 * Prefixes any redirect urls with the Mothership dir
 *
 * @access	public
 * @param	string	the URL
 * @param	string	the method: location or redirect
 * @return	string
 */
if ( ! function_exists('mo_redirect'))
{
	function mo_redirect($uri = '', $method = 'location', $http_response_code = 302)
	{
		$CI =& get_instance();
		
		// prefix uri with mothership and css dir
		$uri = $CI->config->item('mo_url').$uri;
		
		// call the CI recirect function
		redirect($uri, $method, $http_response_code);
	}	
}