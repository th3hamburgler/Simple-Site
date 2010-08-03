<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// ------------------------------------------------------------------------

/**
 * CodeIgniter Inflector Helpers
 *
 * Customised page template helpers.
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Jim Wardlaw
 * @link		http://artandsoul.co.uk
 */

// --------------------------------------------------------------------

/**
* Zones
*
* Takes an array of partial objects and returns the html for a zone
*
* @access	public
* @param	array
* @return	string
*/
if ( ! function_exists('zones'))
{
	function zones($partials)
	{
		// check we have partials
		if(!$partials)
			// return nbsp so we dont bust layout
			return '&nbsp;';
		
		$content = array();
		
		foreach($partials as $partial)
		{
			$spec = array(
				'class' => $partial->mkup_class(),
				'id'	=> $partial->mkup_id()
			);
		
			$content[] = div($partial->content(), $spec);
		}
		
		return implode("\n", $content);
	}
}

// --------------------------------------------------------------------

/**
* Navigation
*
* Takes a nav object and returns the html for a navigation object
*
* @access	public
* @param	array
* @return	string
*/
if ( ! function_exists('nav'))
{
	function nav($navigation)
	{
		$content = array();
		
				
		return implode("\n", $content);
	}
}