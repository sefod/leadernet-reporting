<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://ntmatter.com
 * @since      1.0.0
 *
 * @package    Leadernet_Users_Reporting
 * @subpackage Leadernet_Users_Reporting/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Leadernet_Users_Reporting
 * @subpackage Leadernet_Users_Reporting/includes
 * @author     NTMatter.com <dev@ntmatter.com>
 */
class Leadernet_Users_Reporting_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'leadernet-users-reporting',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
