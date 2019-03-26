<?php
/**
 * Plugin Name:       RabbitBuilder Global Central JS CSS
 * Plugin URI:        https://www.rabbitbuilder.com/plugins/rabbitbuilder-js-css
 * Description:       Better CSS editing in a central location with scss preprocessing, Supports Elementor Global Styles, Centralised area for CSS editing in Elementor, to keep things tidy and easy to implement, as well as maintain. The current way of doing things, ends up being very messy very quickly, with css code attached to elements here there and everywhere, with no indication for where custom styles are. This Plugin allows you to add your own custom css styles and javascript code with a powerful editor.
 * Version:           1.0.0
 * Author:            RabbitBuilder
 * Author URI:        https://www.rabbitbuilder.com/
 * License:           GPLv3
 * Text Domain:       rabbitbuilder_js_css
 * Domain Path:       /languages
 */


// prevent direct access
defined( 'ABSPATH' ) or die( 'Hey, you can\t access this file, you silly human!' );

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('RBJSCSS_PLUGIN_NAME', 'rabbitbuilder_js_css');
define('RBJSCSS_PLUGIN_VERSION', '1.0.0');
define('RBJSCSS_DB_VERSION', '1.0.0');


//The code that runs during plugin activation
register_activation_hook( __FILE__, 'rabbitbuilder_js_css_activate' );

function rabbitbuilder_js_css_activate() {

	require_once( plugin_dir_path( __FILE__ ) . 'inc/activator.php' );
	$activator = new RBJSCSS_Activator();
	$activator->activate();

}

//The code that runs after plugins loaded
add_action('plugins_loaded', 'rabbitbuilder_js_css_check_db');
function rabbitbuilder_js_css_check_db() {

	require_once( plugin_dir_path( __FILE__ ) . 'inc/activator.php' );
	$activator = new RBJSCSS_Activator();
	$activator->check_db();

}


//The code that runs during plugin deactivation
register_deactivation_hook( __FILE__, 'rabbitbuilder_js_css_deactivate' );
function rabbitbuilder_js_css_deactivate() {

	require_once( plugin_dir_path( __FILE__ ) . 'inc/deactivator.php' );
	$deactivator = new RBJSCSS_Deactivator();
	$deactivator->deactivate();

}




//Begins execution of the plugin.
require_once( plugin_dir_path( __FILE__ ) . 'inc/plugin.php' );

add_action('init', 'rabbitbuilder_js_css_run');
function rabbitbuilder_js_css_run() {

	$pluginBasename = plugin_basename(__FILE__);
	$plugin = new RabbitBuilderJsCss( $pluginBasename );
	$plugin->run();

}



//Refersh files when elementor global options updates. Hook will run when data is updated.
add_action( 'update_option_elementor_scheme_color', 				'RabbitBuilderJsCss_Remove_Files' );
add_action( 'update_option_elementor_scheme_typography', 		'RabbitBuilderJsCss_Remove_Files' );
add_action( 'update_option_elementor_scheme_color-picker', 	'RabbitBuilderJsCss_Remove_Files' );
add_action( 'update_option_elementor_container_width', 			'RabbitBuilderJsCss_Remove_Files' );
add_action( 'update_option_elementor_viewport_lg', 					'RabbitBuilderJsCss_Remove_Files' );
add_action( 'update_option_elementor_viewport_md', 					'RabbitBuilderJsCss_Remove_Files' );
function RabbitBuilderJsCss_Remove_Files() {
	require_once( plugin_dir_path( __FILE__ ) . 'inc/deactivator.php' );
	$deactivator = new RBJSCSS_Deactivator();
	$deactivator->delete_files( RBJSCSS_PLUGIN_UPLOAD_DIR . '/' );
}




//load refresh button when elementor editor loads.
add_action( 'elementor/editor/after_enqueue_scripts', 'load_ele_rb_refresh' );
function load_ele_rb_refresh(){

	$plugin_url = plugin_dir_url( __FILE__ );
	wp_enqueue_style( RBJSCSS_PLUGIN_NAME . '_rb_ele_button_css', $plugin_url . 'assets/css/_rb_ele_button.css', array(), RBJSCSS_PLUGIN_VERSION, 'all' );
	wp_enqueue_script( RBJSCSS_PLUGIN_NAME . '_rb_ele_button_js', $plugin_url . 'assets/js/_rb_ele_button.js', array( 'jquery' ), RBJSCSS_PLUGIN_VERSION, true );

}


//live preview functions ends here
