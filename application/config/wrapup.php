<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * WrapUp Configuration
 *
 * Global configuration settings that apply to all WrapUp objects.
 */


$config['css_dir'] = 'css/';							// *will be prefixed with base_url()
$config['js_dir'] = 'js/';								// *will be prefixed with base_url()
$config['put_js_at_bottom'] = TRUE;						// js scripts will be added to bottom of page
$config['path_to_minify'] = 'http://localhost/min/';	// if NULL resolves to base_url/min/
$config['minify_js_dir'] = 'simple-site/www/js';		// if NULL resolves to base_url/min/
$config['minify_css_dir'] = 'simple-site/www/css';		// if NULL resolves to base_url/min/
$config['minify_js'] = FALSE;
$config['minify_css'] = FALSE;


/* End of file config.php */
/* Location: ./system/application/config/wrapup.php */