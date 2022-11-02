<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.koikispass.com
 * @since             1.0.0
 * @package           Offre_emploi
 *
 * @wordpress-plugin
 * Plugin Name:       Offre_emploi
 * Plugin URI:        https://www.koikispass.com
 * Description:       Plugin de récupération d'offre d'emploi et création d'offres locales.
 * Version:           1.0.0
 * Author:            dev-iticonseil
 * Author URI:        https://www.koikispass.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       offre_emploi
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
define( 'OFFRE_EMPLOI_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-offre_emploi-activator.php
 */
function activate_offre_emploi() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-offre_emploi-activator.php';
	Offre_emploi_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-offre_emploi-deactivator.php
 */
function deactivate_offre_emploi() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-offre_emploi-deactivator.php';
	Offre_emploi_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_offre_emploi' );
register_deactivation_hook( __FILE__, 'deactivate_offre_emploi' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-offre_emploi.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_offre_emploi() {

	$plugin = new Offre_emploi();
	$plugin->run();

}
run_offre_emploi();
