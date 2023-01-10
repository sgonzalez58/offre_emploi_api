<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.koikispass.com
 * @since      1.0.0
 *
 * @package    Offre_emploi
 * @subpackage Offre_emploi/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Offre_emploi
 * @subpackage Offre_emploi/admin
 * @author     dev-iticonseil <dev@iti-conseil.com>
 */
class Offre_emploi_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		require_once plugin_dir_path( __FILE__ ) . '../model/model-offre_emploi.php';
		$this->model = new Offre_Emploi_Model();
		
		add_action('admin_menu', array($this, 'gestion_offre_emploi'));

		add_action('wp_ajax_get_nouvelles_offres', array($this,'get_nouvelles_offres'));
		add_action('wp_ajax_nopriv_get_nouvelles_offres', array($this,'get_nouvelles_offres'));

		add_action('wp_ajax_get_reponse_positive_offre', array($this,'get_reponse_positive_offre'));
		add_action('wp_ajax_nopriv_get_reponse_positive_offre', array($this,'get_reponse_positive_offre'));

		add_action('wp_ajax_get_reponse_negative_offre', array($this,'get_reponse_negative_offre'));
		add_action('wp_ajax_nopriv_get_reponse_negative_offre', array($this,'get_reponse_negative_offre'));
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Offre_emploi_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Offre_emploi_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name.'all', plugin_dir_url( __FILE__ ) . 'css/all.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name.'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name.'datatable', 'https://cdn.datatables.net/v/bs5/dt-1.12.1/date-1.1.2/r-2.3.0/sb-1.3.4/sp-2.0.2/sl-1.4.0/datatables.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name.'gestion_offre', plugin_dir_url( __FILE__ ) . 'css/gestion_offre_emploi.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Offre_emploi_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Offre_emploi_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name.'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name.'luxon', 'https://cdn.jsdelivr.net/npm/luxon@3.0.4/build/global/luxon.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name.'datatable', 'https://cdn.datatables.net/v/bs5/dt-1.12.1/date-1.1.2/r-2.3.0/sb-1.3.4/sp-2.0.2/sl-1.4.0/datatables.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name.'datatable_luxon', 'https://cdn.datatables.net/plug-ins/1.10.24/sorting/datetime-luxon.js', array( 'jquery' ), $this->version, false );

	}

	function get_nouvelles_offres(){
		check_ajax_referer('liste_nouvelles_offres');
        
        $offres = $this->model->findAllOffresUser();

		$jsonData = [];
		$idx = 0;
		foreach($offres as $offre){
			$jsonData[$idx++] = ['intitule' => $offre['intitule'], 'nomVille' => $offre['ville_libelle'], 'nomEntreprise' => $offre['nom_entreprise'], 'dateDemande' => $offre['date_actualisation'], 'etat' => $offre['validation'], 'id' => $offre['id']];
		}

        wp_send_json_success($jsonData);
	}

	function get_reponse_positive_offre(){
		check_ajax_referer('reponse_offre');

		$args = array(
			'id_offre' => $_POST['id_offre']
		);

		if(!$args['id_offre']){
			wp_send_json_error("L'id de l'offre n'a pas été envoyé.");
		}
		if(!$this->model->findOneOffre($args['id_offre'])){
			wp_send_json_error("L'offre n'existe pas.");
		}

		$response = $this->model->accepterOffre($args['id_offre']);
		if( $response != 'Sql succès'){
			wp_send_json_error($response);
		}
	}

	function get_reponse_negative_offre(){
		check_ajax_referer('reponse_offre');

		$args = array(
			'id_offre' => $_POST['id_offre']
		);

		if(!$args['id_offre']){
			wp_send_json_error("L'id de l'offre n'a pas été envoyé.");
		}
		if(!$this->model->findOneOffre($args['id_offre'])){
			wp_send_json_error("L'offre n'existe pas.");
		}

		$response = $this->model->refuserOffre($args['id_offre']);
		if( $response != 'Sql succès'){
			wp_send_json_error($response);
		}
	}


	function gestion_offre_emploi(){
		$notification_count = $this->model->findCountPendingOffresUser();
		add_menu_page('Offre Emploi', 'Offre Emploi <span class="awaiting-mod">' . $notification_count . '</span>', 'edit_posts', 'gestion_offre_emploi', array($this, 'gestion_offre'));
	}

	function gestion_offre(){
		if(isset($_GET['id_offre'])){
			if(file_exists(plugin_dir_path( __FILE__ ) .'partials/offre_emploi_admin_display.php')) {
				wp_enqueue_style( $this->plugin_name.'offre', plugin_dir_url( __FILE__ ) . 'css/offre.css', array(), $this->version, 'all' );
				wp_enqueue_script( $this->plugin_name.'masonry', "https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.js", array( 'jquery' ), $this->version, false);
				wp_enqueue_script( $this->plugin_name.'fiche_admin_offre_emploi', plugin_dir_url( __FILE__ ) . 'js/fiche_admin_offre_emploi.js', array(), $this->version, true );
				$reponse_offre = wp_create_nonce( 'reponse_offre' );
				wp_localize_script(
					$this->plugin_name.'fiche_admin_offre_emploi',
					'confirmation_ajax',
					array(
						'ajax_url' => admin_url( 'admin-ajax.php' ),
						'nonce'    => $reponse_offre,
					)
				);
				include(plugin_dir_path( __FILE__ ) .'partials/offre_emploi_admin_display.php');
				return;
			}
		}else{
			wp_enqueue_style( $this->plugin_name.'all', plugin_dir_url( __FILE__ ) . 'css/all.css', array(), $this->version, 'all' );
			wp_enqueue_script( $this->plugin_name.'gestion', plugin_dir_url( __FILE__ ) . 'js/gestion_offre_emploi.js', array( 'jquery' ), $this->version, true );
			$liste_offres = wp_create_nonce( 'liste_nouvelles_offres' );
			$refus_offre = wp_create_nonce( 'reponse_offre');
			wp_localize_script(
				$this->plugin_name.'gestion',
				'my_ajax_obj',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'nonce'    => $liste_offres,
				)
			);
			wp_localize_script(
				$this->plugin_name.'gestion',
				'confirmation_ajax',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'nonce'    => $refus_offre,
				)
			);
			include(plugin_dir_path( __FILE__ ) .'partials/gestion_offre_emploi.php');
		}
	}
}
