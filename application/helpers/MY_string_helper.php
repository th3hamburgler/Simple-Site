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
 * Pluralize
 *
 * Takes a singular word and a count and will return a pluralized word if
 * the count is anything but 1. This usually involves suffixing an
 * 's' to the singular string. If the plural is different from this a plural 
 * string can be passed in the third parameter.
 *
 * the is_verbose flag will return the word prefixed with the count as this
 * is the common use and prevents having to reference the count var twice in
 * your code.
 * 	e.g '1 world'
 *
 * @access	public
 * @param	int
 * @param	string
 * @param	string
 * @param	bolean
 * @return	string
 */

// ------------------------------------------------------------------------

if ( ! function_exists('pluralise'))
{
	function pluralise($count, $singular, $plural='', $is_verbose=TRUE)
	{
		// the word we'll use
		$word = $singular;
	
		// create plural if empty
		if (empty($plural))
			// suffix an 's' to singular
			$plural = $singular.'s';
	
		if($count == 1)
			// return singular
			$word = $singular;
		else
			$word = $plural;
			
		// duncs variable name!
		if($is_verbose)
			// retun nice string e.g. 6 Swans
			return $count.' '.$word;
		
		// just return the word boss
		return $word;
	}
}

 
// ------------------------------------------------------------------------

/**
 * Pluralize
 *
 * Returns the alphabet characher corresponding to the int parameter
 * e.g.
 *	0 = A
 *	1 = B
 *	2 = C...
 *
 * @access	public
 * @param	int
 * @return	char
 */

// ------------------------------------------------------------------------

if ( ! function_exists('number_to_alphabet'))
{
	function number_to_alphabet($i)
	{
		// add ASCII offset to get to uppercase chars
		$i += 65;

		// return char
		return chr($i);
	}
}