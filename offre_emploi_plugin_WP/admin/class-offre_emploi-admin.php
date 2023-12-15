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

 
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

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

	private $model;

	private $import;

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
		
		$this->import = new Offre_emploi_Import( $plugin_name, $version );

		add_action('admin_menu', array($this, 'gestion_offre_emploi'));

		add_action('wp_ajax_get_one_offre_admin', array($this,'get_one_offre'));
		add_action('wp_ajax_nopriv_get_one_offre_admin', array($this,'get_one_offre'));

		add_action('wp_ajax_get_nouvelles_offres', array($this,'get_nouvelles_offres'));
		add_action('wp_ajax_nopriv_get_nouvelles_offres', array($this,'get_nouvelles_offres'));

		add_action('wp_ajax_toggle_visibilite_offre_admin', array($this,'toggle_visibilite_offre'));
		add_action('wp_ajax_nopriv_toggle_visibilite_offre_admin', array($this,'toggle_visibilite_offre'));

		add_action('wp_ajax_get_reponse_negative_offre', array($this,'get_reponse_negative_offre'));
		add_action('wp_ajax_nopriv_get_reponse_negative_offre', array($this,'get_reponse_negative_offre'));

		add_action('wp_ajax_set_offre_archive', array($this,'set_offre_archive'));
		add_action('wp_ajax_nopriv_set_offre_archive', array($this,'set_offre_archive'));

		add_action('wp_ajax_importer_offres', array($this,'importer_offres'));
		add_action('wp_ajax_nopriv_importer_offres', array($this,'importer_offres'));
		
		add_action('admin_init', array($this,'offre_emploi_ajout_validation'));

		add_action('init', array($this,'offre_emploi_rewrite_rules'));
		add_filter('query_vars', array($this,'offre_emploi_register_query_var' ));
		add_filter('template_include', array($this,'offre_emploi_front_end'));
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
		wp_enqueue_style( $this->plugin_name.'.select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', array(), $this->version, 'all' );
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
		wp_enqueue_script( $this->plugin_name.'.select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array( 'jquery' ), $this->version, false );
	}

	/**
	 * Récupères les offres utilisateurs.
	 */
	function get_nouvelles_offres(){
		check_ajax_referer('liste_nouvelles_offres');
        
        $offres = $this->model->findAllOffresUser();

		$jsonData = [];
		$idx = 0;
		foreach($offres as $offre){
			$historiques = $this->model->getHistoriques($offre['id']);
			$vues = 0;
			$vues_liste = 0;
			$clics = 0;
			$demandes = 0;
			foreach($historiques as $historique){
				$vues += $historique['vues'];
				$vues_liste += $historique['vues_liste'];
				$clics += $historique['clics'];
				$demandes += $historique['postuler'];
			}
			$jsonData[$idx++] = ['intitule' => $offre['intitule'], 'nomVille' => $offre['ville_libelle'], 'nomEntreprise' => $offre['nom_entreprise'], 'dateDemande' => $offre['date_de_publication'], 'etat' => $offre['validation'], 'id' => $offre['id'], 'visibilite' => $offre['visibilite'], 'mail' => $offre['email_notification'], 'vue' => ['vues' => $vues, 'vues_liste' => $vues_liste], 'action' => ['clics' => $clics, 'demandes' => $demandes]];
		}

        wp_send_json_success($jsonData);
	}

	public function findOneOffre($id) {

		$offre = wp_cache_get('offre_emploi_offre_'.$id, 'offre_emploi');
		if ( false === $offre ){
			$offre = $this->model->findOneOffre($id);
			wp_cache_set( 'offre_emploi_offre_'.$id, $offre, 'offre_emploi' );
		}
		return $offre;
		
	}
	

	/**
	 * Récupère une offre d'emploi
	 */
	function get_one_offre(){
		check_ajax_referer('modification_admin');

		$args = array(
			'id_offre' => $_POST['id_offre']
		);
		
		if(!$args['id_offre']){
			wp_send_json_error("L'id de l'offre n'a pas été envoyé. Erreure lors de la demande ajax.");
		}

		$response = $this->findOneOffre($args['id_offre']);

		if(!$response){
            wp_send_json_error("L'offre n'existe pas.");
        }

		$jsonData = [
			'id' => $response['id'], 
			'intitule' => $response['intitule'], 
			'metier' => $response['libelle_metier'], 
			'secteur_activite' => $response['secteur_activite'],
			'nomEntreprise' => $response['nom_entreprise'],
			'type_contrat' => $response['type_contrat'], 
			'salaire' => $response['salaire'], 
			'description' => $response['description'], 
			'commune_id' => $response['commune_id'], 
			'ville' => $response['ville_libelle'], 
			'latitude' => $response['latitude'], 
			'longitude' => $response['longitude'],
			'email_notification' => $response['email_notification'],
			'date_debut' => $response['date_debut'],
			'date_fin' => $response['date_fin'],
			'image' => $response['image'],
			'logo' => $response['logo']
		];

		wp_send_json_success($jsonData);

	}

	/**
	 * Modifie la visibilité d'une offre d'emploi
	 */
	function toggle_visibilite_offre(){
		check_ajax_referer('liste_nouvelles_offres');

		$args = array(
			'id_offre' => $_POST['id_offre'],
			'visibilite' => $_POST['visibilite']
		);
		
		if(!$args['id_offre']){
			wp_send_json_error("L'id de l'offre n'a pas été envoyé. Erreure lors de la demande ajax.");
		}

		if(!$args['visibilite']){
			wp_send_json_error("La visibilité souhaitée n'a pas été envoyée. Erreure lors de la demande ajax.");
		}

		if(!$this->findOneOffre($args['id_offre'])){
            wp_send_json_error("L'offre n'existe pas.");
        }

		$reponse = $this->model->toggleVisibiliteOffre($args['id_offre'], $args['visibilite']);

		if($reponse != 'Suppression réussie'){
			wp_send_json_error('Erreure lors de la supression : ' . $reponse);
		}
	}

	/**
	 * Refuse une demande d'offre d'emploi et envoie un mail au demandeur
	 */
	function get_reponse_negative_offre(){
		check_ajax_referer('reponse_offre');

		$args = array(
			'id_offre' => $_POST['id_offre'],
			'raison' => $_POST['raison']
		);

		if(!$args['id_offre']){
			wp_send_json_error("L'id de l'offre n'a pas été envoyé.");
		}
		if(!$args['raison']){
			wp_send_json_error("Les raisons du refus n'ont pas été envoyées.");
		}
		if(!$this->model->findOneOffre($args['id_offre'])){
			wp_send_json_error("L'offre n'existe pas.");
		}

		$response = $this->model->refuserOffre($args['id_offre']);
		if( $response != 'Sql succès'){
			wp_send_json_error($response);
		}
		// $this->envoi_email_utilisateur(get_userdata($this->model->findOneOffre($args['id_offre'])['user_id'])->user_email, $args['raison'], 'refus');
		wp_send_json_success('Offre non mis en ligne');
	}

	/**
	 * Archive une offre d'emploi refusée
	 */
	function set_offre_archive(){
		check_ajax_referer('reponse_offre');

		$args = array(
			'id_offre' => $_POST['id_offre']
		);
		
		if(!$args['id_offre']){
			wp_send_json_error("L'id de l'offre n'a pas été envoyé. Erreure lors de la demande ajax.");
		}

		if(!$this->model->findOneOffre($args['id_offre'])){
            wp_send_json_error("L'offre n'existe pas.");
        }

		$reponse = $this->model->setOffreArchive($args['id_offre']);

		if($reponse != 'archivé'){
			wp_send_json_error('Erreure lors d\'archivage : '.$reponse);
		}else{
			wp_send_json_success($reponse);
		}
	}

	/**
	 * Ajoute un menu de gestion d'offre d'emploi sur la page admin.
	 */
	function gestion_offre_emploi(){
		$notification_count = $this->model->findCountPendingOffresUser();
		if($notification_count > 0){
			add_menu_page('Offre Emploi', 'Offre Emploi <span class="awaiting-mod">' . $notification_count . '</span>', 'edit_posts', 'gestion_offre_emploi', array($this, 'gestion_offre'));
		}else{
			add_menu_page('Offre Emploi', 'Offre Emploi', 'edit_posts', 'gestion_offre_emploi', array($this, 'gestion_offre'));
		}
		add_submenu_page('gestion_offre_emploi', 'Ajouter une Offre', 'Ajouter', 'edit_posts', 'ajouter_offres_emploi', array($this, 'ajouter_offres_emploi'));
		add_submenu_page('gestion_offre_emploi', 'Import offres', 'Import', 'edit_posts', 'import_offres_emploi', array($this, 'import_offres_emploi'));
		add_submenu_page('gestion_offre_emploi', 'Vider le cache offres emploi', 'Vider le cache', 'edit_posts', 'vider_cache_offres_emploi', array($this, 'vider_cache_offres_emploi'));
	}

	/**
	 * Rendu visuel du mode admin.
	 * Affiche la fiche d'une offre d'emploi ou affiche le tableau de gestion des offres d'emploi.
	 */
	function gestion_offre(){
		//Affiche ici la fiche d'offre d'emploi
		if(isset($_GET['id_offre'])){
			if(isset($_GET['edit'])){
				if(isset($_GET['validation'])){
					$this->modification_offre($_GET['id_offre']);
					wp_redirect("^/wp-admin/admin.php?page=gestion_offre_emploi&id_offre=".$_GET['id_offre']);
					exit;
					return;
				}
				wp_enqueue_style( $this->plugin_name.'.formulaire_offre_emploi_css', plugin_dir_url( __FILE__ ) . 'css/formulaire_offre_emploi.css', array(), $this->version, 'all' );
				wp_enqueue_style( $this->plugin_name.'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css', array(), $this->version, 'all' );
				//wp_enqueue_style( $this->plugin_name.'.leaflet', 'https://unpkg.com/leaflet@1.9.2/dist/leaflet.css', array(), $this->version, 'all' );
				wp_enqueue_script( $this->plugin_name.'.bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js', array( 'jquery' ), $this->version, false);
				//wp_enqueue_script( $this->plugin_name.'.leaflet', 'https://unpkg.com/leaflet@1.9.2/dist/leaflet.js', array( 'jquery' ), $this->version, false);
				wp_enqueue_script( $this->plugin_name.'.formulaire_offre_emploi_js', plugin_dir_url( __FILE__ ) . 'js/formulaire_offre_emploi.js', array( 'jquery' ), $this->version, true);

				wp_enqueue_script($this->plugin_name.'.jquery-ui-datepicker-js', 'https://code.jquery.com/ui/1.12.1/jquery-ui.js', array( 'jquery' ), $this->version, false);
				wp_enqueue_style($this->plugin_name.'.jquery-ui-style', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');

				wp_enqueue_script( $this->plugin_name.'.preremplissage_formulaire_admin', plugin_dir_url( __FILE__ ) . 'js/preremplissage_formulaire.js', array( 'jquery' ), $this->version, true);

				$modifier_offre = wp_create_nonce( 'modification_admin');
				wp_localize_script(
					$this->plugin_name.'.preremplissage_formulaire_admin',
					'my_ajax_obj',
					array(
						'ajax_url' => admin_url( 'admin-ajax.php' ),
						'nonce'    => $modifier_offre,
						'id_offre' => $_GET['id_offre']
					)
				);
				include(plugin_dir_path( __FILE__ ) .'partials/offre_emploi-admin-ajouter.php');
				return;
			}
			if(isset($_GET['valider'])){
				$this->model->accepterOffre($_GET['id_offre']);
				wp_redirect("^/wp-admin/admin.php?page=gestion_offre_emploi");
				exit;
				return;
			}
			if(isset($_GET['refuser'])){
				$this->model->refuserOffre($_GET['id_offre']);
				wp_redirect("^/wp-admin/admin.php?page=gestion_offre_emploi");
				exit;
				return;
			}
			if(isset($_GET['archiver'])){
				$this->model->archiverMonOffre($_GET['id_offre']);
				wp_redirect("^/wp-admin/admin.php?page=gestion_offre_emploi");
				exit;
				return;
			}
			if(file_exists(plugin_dir_path( __FILE__ ) .'partials/offre_emploi_admin_display.php')) {
				wp_enqueue_style( $this->plugin_name.'.font-awesome', plugin_dir_url( __FILE__ ) . 'css/all.css', array(), $this->version, 'all' );
				wp_enqueue_style( $this->plugin_name.'datatable', 'https://cdn.datatables.net/v/bs5/dt-1.12.1/date-1.1.2/r-2.3.0/sb-1.3.4/sp-2.0.2/sl-1.4.0/datatables.min.css', array(), $this->version, 'all' );
				wp_enqueue_style( $this->plugin_name.'gestion_offre', plugin_dir_url( __FILE__ ) . 'css/gestion_offre_emploi.css', array(), $this->version, 'all' );
				wp_enqueue_style( $this->plugin_name.'offre', plugin_dir_url( __FILE__ ) . 'css/offre.css', array(), $this->version, 'all' );
				wp_enqueue_style( $this->plugin_name.'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css', array(), $this->version, 'all' );
				wp_enqueue_script( $this->plugin_name.'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js', array( 'jquery' ), $this->version, false );
				wp_enqueue_script( $this->plugin_name.'luxon', 'https://cdn.jsdelivr.net/npm/luxon@3.0.4/build/global/luxon.min.js', array( 'jquery' ), $this->version, false );
				wp_enqueue_script( $this->plugin_name.'datatable', 'https://cdn.datatables.net/v/bs5/dt-1.12.1/date-1.1.2/r-2.3.0/sb-1.3.4/sp-2.0.2/sl-1.4.0/datatables.min.js', array( 'jquery' ), $this->version, false );
				wp_enqueue_script( $this->plugin_name.'datatable_luxon', 'https://cdn.datatables.net/plug-ins/1.10.24/sorting/datetime-luxon.js', array( 'jquery' ), $this->version, false );
				wp_enqueue_script( $this->plugin_name.'fiche_admin_offre_emploi', plugin_dir_url( __FILE__ ) . 'js/fiche_admin_offre_emploi.js', array( 'jquery' ), $this->version, true );
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
			//affiche ici la gestion des offres d'emploi
			wp_enqueue_style( $this->plugin_name.'datatable', 'https://cdn.datatables.net/v/bs5/dt-1.12.1/date-1.1.2/r-2.3.0/sb-1.3.4/sp-2.0.2/sl-1.4.0/datatables.min.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name.'gestion_offre', plugin_dir_url( __FILE__ ) . 'css/gestion_offre_emploi.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name.'all', plugin_dir_url( __FILE__ ) . 'css/all.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name.'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css', array(), $this->version, 'all' );
			wp_enqueue_script( $this->plugin_name.'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( $this->plugin_name.'luxon', 'https://cdn.jsdelivr.net/npm/luxon@3.0.4/build/global/luxon.min.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( $this->plugin_name.'datatable', 'https://cdn.datatables.net/v/bs5/dt-1.12.1/date-1.1.2/r-2.3.0/sb-1.3.4/sp-2.0.2/sl-1.4.0/datatables.min.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( $this->plugin_name.'datatable_luxon', 'https://cdn.datatables.net/plug-ins/1.10.24/sorting/datetime-luxon.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( $this->plugin_name.'.popperjs', 'https://unpkg.com/@popperjs/core@2.11.6/dist/umd/popper.min.js', array( 'jquery' ), $this->version, false);
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

	function import_offres_emploi(){
		wp_enqueue_style( $this->plugin_name.'admin_import_offre', plugin_dir_url( __FILE__ ) . 'css/offre_emploi-admin-import.css', array(), $this->version, 'all' );
		wp_enqueue_script( $this->plugin_name.'admin_import_offre', plugin_dir_url( __FILE__ ) . 'js/offre_emploi-admin-import.js', array( 'jquery' ), $this->version, true );
		$import = wp_create_nonce( 'import');
		wp_localize_script(
			$this->plugin_name.'admin_import_offre',
			'my_ajax_obj',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => $import,
			)
		);
		include(plugin_dir_path( __FILE__ ) .'partials/offre_emploi-admin-import.php');
		return;
	}

	function vider_cache_offres_emploi(){
		if( wp_cache_supports( 'flush_group' )){
			wp_cache_flush_group( 'offre_emploi' );
			echo 'Le cache des offres d\'emploi a bien été vidé.';
		}else{
			wp_cache_flush();
			echo 'Le cache du site a été vidé.';
		}
		return;
	}

	function importer_offres(){
		check_ajax_referer('import');
		$retour = $this->import->getAnnonce();
		if( wp_cache_supports( 'flush_group' )){
			wp_cache_flush_group( 'offre_emploi' );
		}else{
			wp_cache_flush();
		}
		wp_send_json_success($retour);
		return;
	}

	function importer_offres_cron(){
		$this->import->getAnnonce();
		if( wp_cache_supports( 'flush_group' )){
			wp_cache_flush_group( 'offre_emploi' );
		}else{
			wp_cache_flush();
		}
		return;
	}

	function ajouter_offres_emploi(){
		wp_enqueue_style( $this->plugin_name.'.formulaire_offre_emploi_css', plugin_dir_url( __FILE__ ) . 'css/formulaire_offre_emploi.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name.'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css', array(), $this->version, 'all' );
		//wp_enqueue_style( $this->plugin_name.'.leaflet', 'https://unpkg.com/leaflet@1.9.2/dist/leaflet.css', array(), $this->version, 'all' );
		wp_enqueue_script( $this->plugin_name.'.bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js', array( 'jquery' ), $this->version, false);
		//wp_enqueue_script( $this->plugin_name.'.leaflet', 'https://unpkg.com/leaflet@1.9.2/dist/leaflet.js', array( 'jquery' ), $this->version, false);
		wp_enqueue_script( $this->plugin_name.'.formulaire_offre_emploi_js', plugin_dir_url( __FILE__ ) . 'js/formulaire_offre_emploi.js', array( 'jquery' ), $this->version, true);

		wp_enqueue_script($this->plugin_name.'.jquery-ui-datepicker-js', 'https://code.jquery.com/ui/1.12.1/jquery-ui.js', array( 'jquery' ), $this->version, false);
    	wp_enqueue_style($this->plugin_name.'.jquery-ui-style', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');

		$ajouter_offre = wp_create_nonce( 'ajouter_offre');
		wp_localize_script(
			$this->plugin_name.'admin_ajouter_offre',
			'my_ajax_obj',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => $ajouter_offre,
			)
		);
		include(plugin_dir_path( __FILE__ ) .'partials/offre_emploi-admin-ajouter.php');
		return;
	}

	function offre_emploi_ajout_validation(){
		if (isset($_POST['formEmploiBackend'])) {
/* 			var_dump($_POST);
			return; */
			$intitule = sanitize_text_field($_POST['intitule']);
			$libelle_metier = sanitize_text_field($_POST['libelle_metier']);
			$nom_entreprise = sanitize_text_field($_POST['nom_entreprise']);
			$secteur_activite = sanitize_text_field($_POST['secteur_activite']);
			$type_contrat = sanitize_text_field($_POST['type_contrat']);
			if(!empty($_POST['montant_salaire'])){
				$salaire = $_POST['montant_salaire'].'€ par '.$_POST['periode_salaire'];
			}
			$description = sanitize_textarea_field($_POST['description']);
			if($_POST['commune'] != ''){
				$commune_id = $_POST['commune'];
				$commune = $this->get_commune_by_id($commune_id);
				$latitude = $commune['latitude'];
				$longitude = $commune['longitude'];
				$ville_libelle = ucwords($commune['slug']);
			}

			$email_notification = sanitize_text_field($_POST['email']);
			$date_debut = $_POST['date_debut'];
			$date_fin = $_POST['date_fin'];
			$image = $_POST['image'];
			$logo = $_POST['logo'];
			$this->model->createOneOffreInterne($intitule, $libelle_metier, $secteur_activite, $nom_entreprise, $type_contrat, $latitude, $longitude, $salaire, $commune_id, get_current_user_id(), $description, $ville_libelle, $email_notification, $date_debut, $date_fin, $image, $logo);
		}
	}

	/**
	 * Modifie une offre d'emploi
	 */
	function modification_offre(){
		$intitule = sanitize_text_field($_POST['intitule']);
		$libelle_metier = sanitize_text_field($_POST['libelle_metier']);
		$secteur_activite = sanitize_text_field($_POST['secteur_activite']);
		$nom_entreprise = sanitize_text_field($_POST['nom_entreprise']);
		$type_contrat = sanitize_text_field($_POST['type_contrat']);
		if(!empty($_POST['montant_salaire'])){
			$salaire = $_POST['montant_salaire'].'€ par '.$_POST['periode_salaire'];
		}
		$description = sanitize_textarea_field($_POST['description']);
		if($_POST['commune'] != ''){
			$commune_id = $_POST['commune'];
			$commune = $this->get_commune_by_id($commune_id);
			$latitude = $commune['latitude'];
			$longitude = $commune['longitude'];
			$ville_libelle = ucwords($commune['slug']);
		}

		$email_notification = sanitize_text_field($_POST['email']);
		$date_debut = $_POST['date_debut'];
		$date_fin = $_POST['date_fin'];
		$image = $_POST['image'];
		$logo = $_POST['logo'];

		$this->model->modifierOffreInterne($_POST['id_offre'], $intitule, $libelle_metier, $secteur_activite, $nom_entreprise, $type_contrat, $latitude, $longitude, $salaire, $commune_id, $description, $ville_libelle, $email_notification, $date_debut, $date_fin, $image, $logo);
		
	}

	public function get_commune_by_id($id) {
		
		$commune = wp_cache_get('offre_emploi_commune_'.$id, 'offre_emploi');
		if ( false === $commune ){
			$commune = $this->model->findOneCommune($id);
			wp_cache_set( 'offre_emploi_commune_'.$id, $commune, 'offre_emploi');
		}
		return $commune;
		
	}

	/**
	 * Envoi de mail
	 */
	public function envoi_email_utilisateur($user_email, $content, $response){
		
		$mail = new PHPMailer(true);
		try {
			$mail->isSMTP();                                            //Send using SMTP
			$mail->Host       = 'smtp-out.iti-conseil.com';                     //Set the SMTP server to send through
			$mail->SMTPAuth   = false;                                   //Enable SMTP authentication
			$mail->Username   = '';                     //SMTP username
			$mail->Password   = '';                               //SMTP password
			$mail->SMTPSecure = 'tls';         //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
			$mail->Port       = 587;                                    //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
			$mail->SMTPAutoTLS = false;
			$mail->SMTPOptions = array(
					'ssl' => array(
							'verify_peer' => false,
							'verify_peer_name' => false,
							'allow_self_signed' => true
					));
			//Recipients
			$mail->CharSet = 'utf-8';
			$mail->setFrom('no-reply@koikispass.com', 'Koikispass.com');
			$mail->addReplyTo('no-reply@koikispass.com', 'Koikispass');		
			$mail->addAddress($user_email);		

			//Content
			$mail->isHTML(true);                                  //Set email format to HTML
			if($response == 'valide'){
				$mail->Subject = utf8_decode("Validation de votre demande d'offre d'emploi");
				$message = "<table cellpadding=0 cellspacing=0>
						<tr>
							<td width='11px'></td>
								<td width='10px'>&nbsp;</td>
								<td width='729px'>
	
								<p>Bonjour,</p>
	
								<p>Votre offre a été validée et est visible dés maintenant sur notre site. </p>
							
								<p>".stripslashes($content)."</p>
								<p>Cordialement.</p>
							
								</td>
								<td width='10px'>&nbsp;</td>
							<td width='11px'></td>
						</tr>
						
					</table>";	
			}else{
				$mail->Subject = utf8_decode("Refus de votre demande d'offre d'emploi");
				$message = "<table cellpadding=0 cellspacing=0>
						<tr>
							<td width='11px'></td>
								<td width='10px'>&nbsp;</td>
								<td width='729px'>
	
								<p>Bonjour,</p>
	
								<p>Votre offre a été refusée pour les raisons suivante : </p>
							
								<p>".stripslashes($content)."</p>
								<p>Veuillez rectifier ces points et nous renvoyer la demande.<br>Cordialement.</p>
							
								</td>
								<td width='10px'>&nbsp;</td>
							<td width='11px'></td>
						</tr>
						
					</table>";	
			}
			
			$mail->Body    = $message;


			$mail->send();
			$filename = "/log_envoi.log";
			$fp = fopen($filename, "a+");
	 
			fputs($fp, '----'.$user_email.'---'."\n");
			fputs($fp, 'Message has been sent OK !'."\n");
			
			fclose($fp);
			return 1;
		} catch (Exception $e) {
			$filename = "/log_envoi.log";
			$fp = fopen($filename, "a+");
	 
			fputs($fp, '----'.$user_email.'---'."\n");
			fputs($fp, $mail->ErrorInfo."\n");
			
			fclose($fp);
			//echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
			return 0;
		}
	}

	function offre_emploi_rewrite_rules() {	

		add_rewrite_rule('^wp-admin/offreEmploi/postrs/?', 'index.php?postrs=1', 'top');  		
	}
	
	/**
	 * Initialisation des variables url
	 */
	function offre_emploi_register_query_var( $vars ) {
		
		$vars[] = 'postrs';

		return $vars;
	}
	
	/**
	 * affichage des pages du mode public
	*/
	function offre_emploi_front_end($template)
	{
		global $wp_query; //Load $wp_query object

		//affichage de la liste des offres
		if(array_key_exists('postrs',$wp_query->query_vars) && $wp_query->query_vars['postrs'] ==1){
			$nombre_total_offres = count($this->model->findByMotsClef());
			return;
		}
		return $template;
	}
}
