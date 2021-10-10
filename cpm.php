<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://ameer.ir
 * @since             1.0.0
 * @package           Cpm
 *
 * @wordpress-plugin
 * Plugin Name:       Custom Payment
 * Plugin URI:        https://ameer.ir/cpm
 * Description:       Enable users to pay a custom amount using BPM gateway
 * Version:           1.0.0
 * Author:            Ameer Mousavi
 * Author URI:        https://ameer.ir
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       cpm
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'CPM_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-cpm-activator.php
 */
function activate_cpm() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cpm-activator.php';
	Cpm_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-cpm-deactivator.php
 */
function deactivate_cpm() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cpm-deactivator.php';
	Cpm_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_cpm' );
register_deactivation_hook( __FILE__, 'deactivate_cpm' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-cpm.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_cpm() {

	$plugin = new Cpm();
	$plugin->run();

}
run_cpm();
