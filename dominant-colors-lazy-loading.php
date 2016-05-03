<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 *
 * @link              https://manu.ninja/dominant-colors-for-lazy-loading-images
 * @since             0.1.0
 * @package           Dominant_Colors_Lazy_Loading
 *
 * @wordpress-plugin
 * Plugin Name:       Dominant Colors Lazy Loading
 * Plugin URI:        https://manu.ninja/dominant-colors-for-lazy-loading-images
 * Description:       This plugin allows you to lazy load your images while showing the dominant color of each image as a placeholder – like Pinterest or Google Images.
 * Version:           0.5.0
 * Author:            Manuel Wieser
 * Author URI:        https://manu.ninja/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       dominant-colors-lazy-loading
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-dominant-colors-lazy-loading-activator.php
 */
function activate_dominant_colors_lazy_loading() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-dominant-colors-lazy-loading-activator.php';
	Dominant_Colors_Lazy_Loading_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-dominant-colors-lazy-loading-deactivator.php
 */
function deactivate_dominant_colors_lazy_loading() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-dominant-colors-lazy-loading-deactivator.php';
	Dominant_Colors_Lazy_Loading_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_dominant_colors_lazy_loading' );
register_deactivation_hook( __FILE__, 'deactivate_dominant_colors_lazy_loading' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-dominant-colors-lazy-loading.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.1.0
 */
function run_dominant_colors_lazy_loading() {

	$plugin = new Dominant_Colors_Lazy_Loading();
	$plugin->run();

}
run_dominant_colors_lazy_loading();
