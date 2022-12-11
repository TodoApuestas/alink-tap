<?php
/**
 *
 * @package   Alink_Tap
 * @author    Alain Sanchez <luka.ghost@gmail.com>
 * @license   GPL-2.0+
 * @link      http://www.linkedin.com/in/mrbrazzi/
 * @copyright 2014 Alain Sanchez
 *
 * @wordpress-plugin
 * Plugin Name:         Alink Tap
 * Plugin URI:          https://wordpress.org/plugins/alink-tap/
 * Description:         This plugin is a customization of <strong>KB Linker vTAP</strong> by Adam R. Brown to <strong>TodoApuestas.org</strong>. Looks for user-defined phrases in posts and automatically links them. Example: Link every occurrence of "TodoApuestas" to todoapuestas.org. It execute syncronizations task with TodoApuestas.org Server.
 * Version:             1.3.1
 * Author:              Alain Sanchez <luka.ghost@gmail.com>
 * Author URI:          http://www.linkedin.com/in/mrbrazzi/
 * Text Domain:         alink-tap
 * License:             GPL-2.0+
 * License URI:         http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:         /languages
 * GitHub Plugin URI:   https://github.com/mrbrazzi/alinkt-tap
 * WordPress-Plugin-Boilerplate: v2.6.1
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

//	OPTIONAL SETTINGS

/* 	would you like to add title="" tags to the links with the keyword in them? true or false. */

define( 'ALINK_TAP_USE_TITLES' , true );

/* 	if the preceding setting is TRUE, you can customize the text before or after the keyword below. For example, if you want titles to say "More about KEYWORD.",

    then make before = 'More about ' (note the space) and after = '.' 	*/

require_once( plugin_dir_path( __FILE__ ) . 'public/class-alink-tap.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
register_activation_hook( __FILE__, array( 'Alink_Tap', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Alink_Tap', 'deactivate' ) );

/*
 *
 */
add_action( 'plugins_loaded', array( 'Alink_Tap', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 *
 * If you want to include Ajax within the dashboard, change the following
 * conditional to:
 *
 * if ( is_admin() ) {
 *   ...
 * }
 *
 * The code below is intended to to give the lightest footprint possible.
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-alink-tap-admin.php' );
	add_action( 'plugins_loaded', array( 'Alink_Tap_Admin', 'get_instance' ) );

}

//Alink_Tap::get_instance()->sync_remote_server();