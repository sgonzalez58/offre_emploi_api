<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.koikispass.com
 * @since      1.0.0
 *
 * @package    Offre_emploi
 * @subpackage Offre_emploi/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Offre_emploi
 * @subpackage Offre_emploi/includes
 * @author     dev-iticonseil <dev@iti-conseil.com>
 */
class Offre_emploi_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		require_once plugin_dir_path( __FILE__ ) . '../library/recuperation_offre.php';

		getAnnonce();
		if(! wp_next_scheduled('Offres_emploi_Import')){
			wp_schedule_event(time(), 'hourly', 'Offres_emploi_Import');
		}
	}
}
