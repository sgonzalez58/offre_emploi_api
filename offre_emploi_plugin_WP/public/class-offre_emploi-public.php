<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.koikispass.com
 * @since      1.0.0
 *
 * @package    Offre_emploi
 * @subpackage Offre_emploi/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Offre_emploi
 * @subpackage Offre_emploi/public
 * @author     dev-iticonseil <dev@iti-conseil.com>
 */
 
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class Offre_emploi_Public {

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

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		require_once plugin_dir_path( __FILE__ ) . '../model/model-offre_emploi.php';
		$this->model = new Offre_Emploi_Model();

		add_action('wp_ajax_info_nb_com_cont', array($this,'info_nb_com_cont'));
		add_action('wp_ajax_nopriv_info_nb_com_cont', array($this,'info_nb_com_cont'));
		
		add_action('wp_ajax_info_nb_com_cont_filtres', array($this,'info_nb_com_cont_filtres'));
		add_action('wp_ajax_nopriv_info_nb_com_cont_filtres', array($this,'info_nb_com_cont_filtres'));

		add_action('wp_ajax_change_limit_offre', array($this, 'change_limit_offre'));
		add_action('wp_ajax_nopriv_change_limit_offre', array($this, 'change_limit_offre'));

		add_action('wp_ajax_get_one_offre', array($this,'get_one_offre'));
		add_action('wp_ajax_nopriv_get_one_offre', array($this,'get_one_offre'));

		add_action('wp_ajax_get_mes_offres', array($this,'get_mes_offres'));
		add_action('wp_ajax_nopriv_get_mes_offres', array($this,'get_mes_offres'));

		add_action('wp_ajax_supprimer_mon_offre', array($this,'supprimer_mon_offre'));
		add_action('wp_ajax_nopriv_supprimer_mon_offre', array($this,'supprimer_mon_offre'));

		add_action('wp_ajax_toggle_visibilite_offre', array($this,'toggle_visibilite_offre'));
		add_action('wp_ajax_nopriv_toggle_visibilite_offre', array($this,'toggle_visibilite_offre'));

		add_action('init', array($this,'offre_emploi_rewrite_rules'));
		add_action('init', array($this,'monprefixe_session_start'), 1);
		add_filter('query_vars', array($this,'offre_emploi_register_query_var' ));
		add_filter('template_include', array($this,'offre_emploi_front_end'));

		add_shortcode('offre_emploi', array($this, 'liste_villes_offre'));

		add_shortcode('offre_emploi_mail_contact', array($this, 'sc_offre_emploi_mail_contact'));

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name.'.font-awesome', plugin_dir_url( __FILE__ ) . 'css/all.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name.'.select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/offre_emploi-public.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name.'.select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array( 'jquery' ), $this->version, false );
	}

	/**
	 * Shortcode liste des villes avec au moins un offre existante avec le lien vers les offres
	 */
	function liste_villes_offre(){
		$retour = '<ul>';
		$communes = $this->model->findAllCommunes();
		foreach($communes as $commune){
			if($this->model->offreCommuneExist($commune['id'])){
				$retour .= "<li><a href='/offres-emploi/ville/".$commune['slug']."/'>".$commune['nom_commune']."</a></li>";
			}
		}
		$retour .= '</ul>';
		return $retour;
	}

	/**
	 * Shortcode récupération du mail de contact d'une offre d'emploi
	 */
	public function sc_offre_emploi_mail_contact($atts) {

		global $wp_query; //Load $wp_query object
		
		ob_start();

			extract( shortcode_atts(array(
						'id' => $wp_query->query_vars['idOffreEmploi'],
				), $atts) );

 			// print_r($atts);			
			if( $id ){
				$offre = $this->findOneOffre($id);

				if(file_exists(plugin_dir_path( __FILE__ ) .'partials/mail_contact.php')) {
					include(plugin_dir_path( __FILE__ ) .'partials/mail_contact.php');
				}
				
			}

		return ob_get_clean();
		
	}

	function monprefixe_session_start() {

		if ( ! session_id() ) {
	 
		   @session_start();
	 
		}
	 
	}
	
	public function getOffresValides($mots_clef = '', $type_de_contrat = null, array $communes = [], $page = 1, $limit = '') {
		
		return $offres = $this->model->findByMotsClef($mots_clef, $type_de_contrat, $communes, $page, $limit);	
	}

	public function getMetier() {
		
		$metier = wp_cache_get('offre_emploi_metier', 'offre_emploi');
		if ( false === $metier ){
			$metier = $this->model->getMetier();
			wp_cache_set('offre_emploi_metier', $metier, 'offre_emploi');
		}
		return $metier;
	}	

	public function getAllCommunes() {

		$villes = wp_cache_get('offre_emploi_all_communes', 'offre_emploi');
		if ( false === $villes ){
			$villes = $this->model->findAllCommunes();
			wp_cache_set( 'offre_emploi_all_communes', $villes, 'offre_emploi' );
		}
		return $villes;
	}	

	public function getAllTypeContrat() {
		
		$categories = wp_cache_get('offre_emploi_all_contrat', 'offre_emploi');
		if ( false === $categories ){
			$categories = $this->model->getAllTypeContrat();
			wp_cache_set( 'offre_emploi_all_contrat', $categories, 'offre_emploi' );
		}
		return $categories;
	}	

	public function get_commune_by_slug($slug) {
		
		$commune = wp_cache_get('offre_emploi_commune_'.$slug, 'offre_emploi');
		if ( false === $commune ){
			$commune = $this->model->findOneCommuneBySlug($slug);
			wp_cache_set( 'offre_emploi_commune_'.$slug, $commune, 'offre_emploi' );
		}
		return $commune;
		
	}

	public function get_commune_by_id($id) {
		
		$commune = wp_cache_get('offre_emploi_commune_'.$id, 'offre_emploi');
		if ( false === $commune ){
			$commune = $this->model->findOneCommune($id);
			wp_cache_set( 'offre_emploi_commune_'.$id, $commune, 'offre_emploi');
		}
		return $commune;
		
	}

	public function get_nb_communes($recherche_input = '') {
		
		$nb_communes = wp_cache_get('offre_emploi_nb_communes_'.$recherche_input, 'offre_emploi');
		if ( false === $nb_communes ){
			$nb_communes = $this->model->getNbCommunes($recherche_input);
			wp_cache_set( 'offre_emploi_nb_communes_'.$recherche_input, $nb_communes, 'offre_emploi');
		}
		return $nb_communes;
		
	}

	public function get_nb_types_contrat($recherche_input = '') {
		
		$nb_types_contrat = wp_cache_get('offre_emploi_nb_types_contrat_'.$recherche_input, 'offre_emploi');
		if ( false === $nb_types_contrat ){
			$nb_types_contrat = $this->model->getNbTypesContrat($recherche_input);
			wp_cache_set( 'offre_emploi_nb_types_contrat_'.$recherche_input, $nb_types_contrat, 'offre_emploi' );
		}
		return $nb_types_contrat;
		
	}

	public function get_nb_communes_1($them, $recherche_input = '') {
		
		$nb_communes1 = wp_cache_get('offre_emploi_nb_communes_'.$recherche_input.'_'.$them, 'offre_emploi');
		if ( false === $nb_communes1 ){
			$nb_communes1 = $this->model->getNbCommunes1($them, $recherche_input);
			wp_cache_set( 'offre_emploi_nb_communes_'.$recherche_input.'_'.$them, $nb_communes1, 'offre_emploi' );
		}
		return $nb_communes1;
		
	}

	public function get_nb_types_contrat_1($comm, $recherche_input = '') {

		$nb_types_contrat1 = wp_cache_get( 'offre_emploi_nb_types_contrat_'.$recherche_input.'_'.$comm, 'offre_emploi');
		if ( false === $nb_types_contrat1 ){
			$nb_types_contrat1 = $this->model->getNbTypesContrat1($comm, $recherche_input);
			wp_cache_set( 'offre_emploi_nb_types_contrat_'.$recherche_input.'_'.$comm, $nb_types_contrat1, 'offre_emploi' );
		}
		return $nb_types_contrat1;
		
	}

	public function findOneOffre($id) {

		$offre = wp_cache_get('offre_emploi_offre_'.$id, 'offre_emploi');
		if ( false === $offre ){
			$offre = $this->model->findOneOffre($id);
			wp_cache_set( 'offre_emploi_offre_'.$id, $offre, 'offre_emploi' );
		}
		return $offre;
		
	}

	public function getMoreOffre($secteur_activite = '', $id = '') {

		$other_offres = wp_cache_get('offre_emploi_more_offres_'.$secteur_activite.'_'.$id, 'offre_emploi');
		if ( false === $other_offres ){
			$other_offres = $this->model->getMoreOffre($secteur_activite, $id);
			wp_cache_set( 'offre_emploi_more_offres_'.$secteur_activite.'_'.$id, $other_offres, 'offre_emploi' );
		}
		
		return $other_offres;
		
	}

	function change_limit_offre(){
		$args = array(
			'limit' => $_GET['limit']
		);

		$_SESSION['limit_offres_liste'] = $args['limit'];

		wp_send_json_success('Success');
	}

	function info_nb_com_cont(){
		check_ajax_referer('liste_offres');
		$args = array(
			'mots_clef' => $_GET['mots_clef'],
		);

		$array_nb_communes = $this->get_nb_communes($args['mots_clef']);

		$array_nb_types_contrat = $this->get_nb_types_contrat($args['mots_clef']);

		wp_send_json_success(json_encode(['com' => $array_nb_communes, 'cont' => $array_nb_types_contrat]));
	}

	function info_nb_com_cont_filtres(){
		check_ajax_referer('liste_offres');
		$args = array(
			'mots_clef' => $_GET['mots_clef'],
			'idCommune' => $_GET['ville'],
			'distance' => $_GET['distance'],
			'type_de_contrat' => urldecode($_GET['type_de_contrat']),
		);

		if(!$args['idCommune']){
			$array_nb_communes = $this->get_nb_communes_1($args['type_de_contrat'], $args['mots_clef']);
			$array_nb_types_contrat = $this->get_nb_types_contrat($args['mots_clef']);
		}else{
			if($args['type_de_contrat']){
				$array_nb_communes = $this->get_nb_communes_1($args['type_de_contrat'], $args['mots_clef']);
			}
			else{
				$array_nb_communes = $this->get_nb_communes($args['mots_clef']);
			}
			$ville_cible = [];
			array_push($ville_cible, $args['idCommune']);
				
			$ville_a_trier = $this->getAllCommunes();

			foreach($ville_a_trier as $commune){
				$villeFrom = $this->get_commune_by_id($args['idCommune']);
				$villeTo = $commune;
				$latFrom = deg2rad($villeFrom['latitude']);
				$lonFrom = deg2rad($villeFrom['longitude']);
				$latTo = deg2rad($villeTo['latitude']);
				$lonTo = deg2rad($villeTo['longitude']);
				$lonDelta = $lonTo - $lonFrom;
				$a = pow(cos($latTo) * sin($lonDelta), 2) +
					pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
				$b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);
				$angle = atan2(sqrt($a), $b);
				$distanceVilles = $angle * 6371;
				if($distanceVilles < $args['distance']){
					array_push($ville_cible, $commune['id']);
				}
			}
			$array_nb_types_contrat = $this->get_nb_types_contrat_1($ville_cible, $args['mots_clef']);
		}
		wp_send_json_success(json_encode(['com' => $array_nb_communes, 'cont' => $array_nb_types_contrat]));
	}

	/**
	 * Sécurise partiellement les champs du formulaire
	 */
	function secureInput($input){
		return htmlspecialchars(trim($input));
	}

	/**
	 * Crée une offre d'emploi
	 */
	function creation_offre_emploi(){
		$intitule = $this->secureInput($_POST['intitule']);
		$libelle_metier = $this->secureInput($_POST['libelle_metier']);
		$nom_entreprise = $this->secureInput($_POST['nom_entreprise']);
		$secteur_activite = $this->secureInput($_POST['secteur_activite']);
		$type_contrat = $this->secureInput($_POST['type_contrat']);
		if(!empty($_POST['montant_salaire'])){
			$salaire = $_POST['montant_salaire'].'€ par '.$_POST['periode_salaire'];
		}
		$description = $this->secureInput($_POST['description']);
		if($_POST['commune'] != ''){
			$commune_id = $_POST['commune'];
			$commune = $this->get_commune_by_id($commune_id);
			$latitude = $commune['latitude'];
			$longitude = $commune['longitude'];
			$ville_libelle = ucwords($commune['slug']);
		}else{
			$ville_libelle = $this->secureInput($_POST['ville_libelle']);
		}
		if($_POST['latitude'] != ''){
			$latitude = $_POST['latitude'];
			$longitude = $_POST['longitude'];
		}
		$this->model->createOneOffre($intitule, $libelle_metier, $secteur_activite, $nom_entreprise, $type_contrat, $latitude, $longitude, $salaire, $commune_id, get_current_user_id(), $description, $ville_libelle);
	}

	/**
	 * Modifie une offre d'emploi
	 */
	function modification_offre(){
		$intitule = $this->secureInput($_POST['intitule']);
		$libelle_metier = $this->secureInput($_POST['libelle_metier']);
		$secteur_activite = $this->secureInput($_POST['secteur_activite']);
		$nom_entreprise = $this->secureInput($_POST['nom_entreprise']);
		$type_contrat = $this->secureInput($_POST['type_contrat']);
		if(!empty($_POST['montant_salaire'])){
			$salaire = $_POST['montant_salaire'].'€ par '.$_POST['periode_salaire'];
		}
		$description = $this->secureInput($_POST['description']);
		if($_POST['commune'] != ''){
			$commune_id = $_POST['commune'];
			$commune = $this->get_commune_by_id($commune_id);
			$latitude = $commune['latitude'];
			$longitude = $commune['longitude'];
			$ville_libelle = ucwords($commune['slug']);
		}else{
			$ville_libelle = $this->secureInput($_POST['ville_libelle']);
		}
		if($_POST['latitude'] != ''){
			$latitude = $_POST['latitude'];
			$longitude = $_POST['longitude'];
		}

		$this->model->modifierOffre($_POST['id_offre'], $intitule, $libelle_metier, $secteur_activite, $nom_entreprise, $type_contrat, $latitude, $longitude, $salaire, $commune_id, get_current_user_id(), $description, $ville_libelle);
	}

	/**
	 * Récupère une offre d'emploi
	 */
	function get_one_offre(){
		check_ajax_referer('mon_offre');

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
			'longitude' => $response['longitude']
		];

		wp_send_json_success($jsonData);

	}

	/**
	 * Récupère les offres d'emploi d'un utilisateur
	 */
	function get_mes_offres(){
		check_ajax_referer('mes_offres');
        
        $offres = $this->model->findMesOffres(get_current_user_id());

		$jsonData = [];
		$idx = 0;
		foreach($offres as $offre){
			$jsonData[$idx++] = ['intitule' => $offre['intitule'], 'nomVille' => $offre['ville_libelle'], 'nomEntreprise' => $offre['nom_entreprise'], 'dateCreation' => $offre['date_de_publication'], 'etat' => $offre['validation'], 'id' => $offre['id'], 'visibilite' => $offre['visibilite']];
		}

        wp_send_json_success($jsonData);
	}

	/**
	 * Supprime une offre d'emploi
	 */
	function supprimer_mon_offre(){
		check_ajax_referer('mes_offres');

		$args = array(
			'id_offre' => $_POST['id_offre']
		);
		
		if(!$args['id_offre']){
			wp_send_json_error("L'id de l'offre n'a pas été envoyé. Erreure lors de la demande ajax.");
		}

		if(!$this->findOneOffre($args['id_offre'])){
            wp_send_json_error("L'offre n'existe pas.");
        }

		$reponse = $this->model->archiverMonOffre($args['id_offre']);

		if($reponse != 'Suppression réussie'){
			wp_send_json_error('Erreure lors de la supression.');
		}
	}

	/**
	 * Modifie la visibilité d'une offre d'emploi
	 */
	function toggle_visibilite_offre(){
		check_ajax_referer('mes_offres');

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
	 * Ré-écritude des routes
	 */
	function offre_emploi_rewrite_rules() {	

		add_rewrite_rule('^offres-emploi/([0-9]+)/pourvu/?', 'index.php?idOffreEmploi=$matches[1]&pourvu=1', 'top');

		add_rewrite_rule('^offres-emploi/([0-9]+)/nonPourvu/?', 'index.php?idOffreEmploi=$matches[1]&pourvu=0', 'top');

		add_rewrite_rule('^offres-emploi/([0-9]+)/?', 'index.php?idOffreEmploi=$matches[1]', 'top');

		add_rewrite_rule('^offres-emploi/categorie/([^/]+)$','index.php?offreEmploi=1&thematique=$matches[1]','top');

		add_rewrite_rule('^offres-emploi/lieu/([^/]+)$','index.php?offreEmploi=1&commune=$matches[1]','top');
		add_rewrite_rule('^offres-emploi/lieu/([^/]+)/categorie/([^/]+)$','index.php?offreEmploi=1&commune=$matches[1]&thematique=$matches[2]','top');

		add_rewrite_rule('^offres-emploi/creer/verification/?', 'index.php?verificationNouvelleOffre=1', 'top');

		add_rewrite_rule('^offres-emploi/creer/?', 'index.php?nouvelleOffre=1', 'top');

		add_rewrite_rule('^offres-emploi/mesOffres/modification/?', 'index.php?modifier=1', 'top');

		add_rewrite_rule('^offres-emploi/mesOffres/([0-9]+)/?', 'index.php?idMonOffreEmploi=$matches[1]', 'top');

		add_rewrite_rule('^offres-emploi/mesOffres/?', 'index.php?mesOffres=1', 'top');

		add_rewrite_rule('^offres-emploi/?', 'index.php?offreEmploi=1', 'top');
  		
	}
	
	/**
	 * Initialisation des variables url
	 */
	function offre_emploi_register_query_var( $vars ) {
		
		$vars[] = 'offreEmploi';
		$vars[] = 'idOffreEmploi';
		$vars[] = 'pourvu';
		$vars[] = 'commune';
		$vars[] = 'thematique';
		$vars[] = 'nouvelleOffre';
		$vars[] = 'mesOffres';
		$vars[] = 'idMonOffreEmploi';
		$vars[] = 'modifier';
		$vars[] = 'verificationNouvelleOffre';
		$vars[] = 'ville';

		return $vars;
	}
	
	/**
	 * affichage des pages du mode public
	*/
	function offre_emploi_front_end($template)
	{
		global $wp_query; //Load $wp_query object

		//affichage de la liste des offres
		if(array_key_exists('offreEmploi',$wp_query->query_vars) && $wp_query->query_vars['offreEmploi'] ==1){
			
			wp_enqueue_style( $this->plugin_name.'.pagination', plugin_dir_url( __FILE__ ) . 'css/pagination.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name.'.ui_css', '//code.jquery.com/ui/1.13.0/themes/base/jquery-ui.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name.'.google_apis', 'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name.'.google_icon', 'https://fonts.googleapis.com/icon?family=Material+Icons', array(), $this->version, 'all' );

			wp_enqueue_script( $this->plugin_name.'.pagination', plugin_dir_url( __FILE__ ) . 'js/pagination.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( $this->plugin_name.'.ui_js', 'https://code.jquery.com/ui/1.13.0/jquery-ui.min.js', array( 'jquery' ), $this->version, false );

			if( array_key_exists('thematique',$wp_query->query_vars) || array_key_exists('commune',$wp_query->query_vars) ){

				wp_enqueue_style( $this->plugin_name.'.liste_offres_css', plugin_dir_url( __FILE__ ) . 'css/liste_offres.css', array(), $this->version, 'all' );
			
				wp_enqueue_script( $this->plugin_name.'.liste_offres_js', plugin_dir_url( __FILE__ ) . 'js/liste_offres.js', array( 'jquery' ), $this->version, true );

				$thematique = "";
				if($wp_query->query_vars['thematique']){
					$thematique = $wp_query->query_vars['thematique'];
					$nb_communes = $this->get_nb_communes_1($wp_query->query_vars['thematique']);
				}else{
					$nb_communes = $this->get_nb_communes();
				}
				$commune = "";
				if($wp_query->query_vars['commune']){
					$commune = $this->get_commune_by_slug($wp_query->query_vars['commune']);
					$nb_types_contrat = $this->get_nb_types_contrat_1([$commune['id']]);
				}else{
					$nb_types_contrat = $this->get_nb_types_contrat();
				}


				$liste_offres = wp_create_nonce( 'liste_offres' );
				wp_localize_script(
					$this->plugin_name.'.liste_offres_js',
					'my_ajax_obj',
					array(
						'ajax_url'		=> admin_url( 'admin-ajax.php' ),
						'nonce'			=> $liste_offres,
						'ville'	   		=> $commune != "" ? $commune['id'] : "",
						"type_contrat"	=> $thematique,
						'nb_communes'	=> json_encode($nb_communes),
						'nb_types_contrat'	=> json_encode($nb_types_contrat),
					)
				);
				
				return plugin_dir_path( __FILE__ ) .'partials/liste_offres.php';
			}

			wp_enqueue_style( $this->plugin_name.'.liste_offres_sans_filtres_css', plugin_dir_url( __FILE__ ) . 'css/liste_offres_sans_filtres.css', array(), $this->version, 'all' );
			
			wp_enqueue_script( $this->plugin_name.'.liste_offres_sans_filtres_js', plugin_dir_url( __FILE__ ) . 'js/liste_offres_sans_filtres.js', array( 'jquery' ), $this->version, true );

			
			$nb_communes = $this->get_nb_communes();
			$nb_types_contrat = $this->get_nb_types_contrat();


			$liste_offres = wp_create_nonce( 'liste_offres' );
			wp_localize_script(
				$this->plugin_name.'.liste_offres_sans_filtres_js',
				'my_ajax_obj',
				array(
					'ajax_url'		=> admin_url( 'admin-ajax.php' ),
					'nonce'			=> $liste_offres,
					'ville'	   		=> "",
					"type_contrat"	=> "",
					'nb_communes'	=> json_encode($nb_communes),
					'nb_types_contrat'	=> json_encode($nb_types_contrat),
				)
			);
			return plugin_dir_path( __FILE__ ) .'partials/liste_offres_sans_filtres.php';
		}

		//affichage de la fiche d'une offre
		if(array_key_exists('idOffreEmploi',$wp_query->query_vars)){	

			if(array_key_exists('pourvu', $wp_query->query_vars)){

				$clef = $_GET['key'];

				if(!$clef){

					if(file_exists(plugin_dir_path( __FILE__ ) .'partials/lien_non_disponible.php')) {
						return plugin_dir_path( __FILE__ ) .'partials/lien_non_disponible.php';
					}

				}

				$result = $this->model->verifierClefOffre($wp_query->query_vars['idOffreEmploi'], $clef);

				if(!$result){

					if(file_exists(plugin_dir_path( __FILE__ ) .'partials/lien_non_disponible.php')) {
						return plugin_dir_path( __FILE__ ) .'partials/lien_non_disponible.php';
					}else{
						wp_redirect('https://koikispass.com');
						exit;
					}
				}

				if($wp_query->query_vars['pourvu'] == 1){

					$result = $this->model->archiverMonOffre($wp_query->query_vars['idOffreEmploi']);

					if($result == 'error'){

						if(file_exists(plugin_dir_path( __FILE__ ) .'partials/erreur.php')) {
							return plugin_dir_path( __FILE__ ) .'partials/erreur.php';
						}else{
							wp_redirect('https://koikispass.com');
							exit;
						}
					}

					if(file_exists(plugin_dir_path( __FILE__ ) .'partials/confirmer_archive.php')) {
						return plugin_dir_path( __FILE__ ) .'partials/confirmer_archive.php';
					}

				}elseif($wp_query->query_vars['pourvu'] == 0){

					$offre = $this->model->findOneOffre($wp_query->query_vars['idOffreEmploi']);

					if(Datetime::createFromFormat('Y-m-d', $offre['date_fin']['date']) < new \Datetime('+15 days')){
						$result = $this->model->rafraichirOffre2($wp_query->query_vars['idOffreEmploi']);
					}else{
						$result = $this->model->rafraichirOffre($wp_query->query_vars['idOffreEmploi']);
					}

					if($result == 'error'){

						if(file_exists(plugin_dir_path( __FILE__ ) .'partials/erreur.php')) {
							return plugin_dir_path( __FILE__ ) .'partials/erreur.php';
						}else{
							wp_redirect('https://koikispass.com');
							exit;
						}
						
					}

					if(file_exists(plugin_dir_path( __FILE__ ) .'partials/confirmer_mise_a_jour.php')) {
						return plugin_dir_path( __FILE__ ) .'partials/confirmer_mise_a_jour.php';
					}
					wp_redirect('https://koikispass.com');
					exit;
				}
			}

			if(file_exists(plugin_dir_path( __FILE__ ) .'partials/fiche_offre.php')) {

				wp_enqueue_style( $this->plugin_name.'offre', plugin_dir_url( __FILE__ ) . 'css/offre.css', array(), $this->version, 'all' );
				wp_enqueue_script( $this->plugin_name.'.offre', plugin_dir_url( __FILE__ ) . 'js/offre.js', array( 'jquery' ), $this->version, true);

				return plugin_dir_path( __FILE__ ) .'partials/fiche_offre.php';

			}

		}
		//formulaire de création d'une offre
		if(array_key_exists('nouvelleOffre',$wp_query->query_vars)){
			if(file_exists(plugin_dir_path( __FILE__ ) .'partials/nouvelle_offre.php')) {
				if(is_user_logged_in()){
					wp_enqueue_style( $this->plugin_name.'.formulaire_offre_emploi_css', plugin_dir_url( __FILE__ ) . 'css/formulaire_offre_emploi.css', array(), $this->version, 'all' );
					wp_enqueue_style( $this->plugin_name.'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css', array(), $this->version, 'all' );
					wp_enqueue_style( $this->plugin_name.'.leaflet', 'https://unpkg.com/leaflet@1.9.2/dist/leaflet.css', array(), $this->version, 'all' );
					wp_enqueue_script( $this->plugin_name.'.bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js', array( 'jquery' ), $this->version, false);
					wp_enqueue_script( $this->plugin_name.'.leaflet', 'https://unpkg.com/leaflet@1.9.2/dist/leaflet.js', array( 'jquery' ), $this->version, false);
					wp_enqueue_script( $this->plugin_name.'.formulaire_offre_emploi_js', plugin_dir_url( __FILE__ ) . 'js/formulaire_offre_emploi.js', array( 'jquery' ), $this->version, true);
					include(plugin_dir_path( __FILE__ ) .'partials/nouvelle_offre.php');
					return;
				}else{
					return plugin_dir_path( __FILE__ ) .'partials/page_connexion.php';
				}
			}
		}
		//vérification du formulaire et création de l'offre
		if(array_key_exists('verificationNouvelleOffre',$wp_query->query_vars)){
			if(is_user_logged_in()){
				$this->creation_offre_emploi();
				unset($_POST);
				wp_redirect("^/offres-emploi/mesOffres?creation=1");
				exit;
			}else{
				return plugin_dir_path( __FILE__ ) .'partials/page_connexion.php';
			}
		}
		//affichage des offres d'un utilisateur connecté
		if(array_key_exists('mesOffres',$wp_query->query_vars) && $wp_query->query_vars['mesOffres'] ==1){
			if(is_user_logged_in()){
				if(file_exists(plugin_dir_path( __FILE__ ) .'partials/mes_offres.php')) {

					wp_enqueue_style( $this->plugin_name.'.font-awesome', plugin_dir_url( __FILE__ ) . 'css/all.css', array(), $this->version, 'all' );
					wp_enqueue_style( $this->plugin_name.'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css', array(), $this->version, 'all' );
					wp_enqueue_style( $this->plugin_name.'datatable', 'https://cdn.datatables.net/v/bs5/dt-1.12.1/date-1.1.2/r-2.3.0/sb-1.3.4/sp-2.0.2/sl-1.4.0/datatables.min.css', array(), $this->version, 'all' );
					wp_enqueue_style( $this->plugin_name.'gestion_offre', plugin_dir_url( __FILE__ ) . 'css/gestion_offre_emploi.css', array(), $this->version, 'all' );

					wp_enqueue_script( $this->plugin_name.'.bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js', array( 'jquery' ), $this->version, false);
					wp_enqueue_script( $this->plugin_name.'.luxon', 'https://cdn.jsdelivr.net/npm/luxon@3.0.4/build/global/luxon.min.js', array( 'jquery' ), $this->version, false);
					wp_enqueue_script( $this->plugin_name.'.datatable', 'https://cdn.datatables.net/v/bs5/dt-1.12.1/date-1.1.2/r-2.3.0/sb-1.3.4/sp-2.0.2/sl-1.4.0/datatables.min.js', array( 'jquery' ), $this->version, false);
					wp_enqueue_script( $this->plugin_name.'.luxon-datatable', 'https://cdn.datatables.net/plug-ins/1.10.24/sorting/datetime-luxon.js', array( 'jquery' ), $this->version, false);
					wp_enqueue_script( $this->plugin_name.'.popperjs', 'https://unpkg.com/@popperjs/core@2.11.6/dist/umd/popper.min.js', array( 'jquery' ), $this->version, false);
					wp_enqueue_script( $this->plugin_name.'.userGestion', plugin_dir_url( __FILE__ ) . 'js/userGestion_offre_emploi.js', array( 'jquery' ), $this->version, true);

					$mesOffres = wp_create_nonce( 'mes_offres' );
					wp_localize_script(
						$this->plugin_name.'.userGestion',
						'mes_offres_ajax',
						array(
							'ajax_url' => admin_url( 'admin-ajax.php' ),
							'nonce'    => $mesOffres,
							'id_client' => get_current_user_id(),
						)
					);
					include(plugin_dir_path( __FILE__ ) .'partials/mes_offres.php');
					return;
				}
				else{
					echo 'Une erreur s\'est produite.';
				}
			}else{
				return plugin_dir_path( __FILE__ ) .'partials/page_connexion.php';
			}
		}
		//formulaire de modification d'une offre d'un utilisateur connecté
		if(array_key_exists('idMonOffreEmploi',$wp_query->query_vars)){
			if(file_exists(plugin_dir_path( __FILE__ ) .'partials/nouvelle_offre.php')) {
				if(is_user_logged_in()){
					wp_enqueue_style( $this->plugin_name.'.formulaire_offre_emploi_css', plugin_dir_url( __FILE__ ) . 'css/formulaire_offre_emploi.css', array(), $this->version, 'all' );
					wp_enqueue_style( $this->plugin_name.'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css', array(), $this->version, 'all' );
					wp_enqueue_style( $this->plugin_name.'.leaflet', 'https://unpkg.com/leaflet@1.9.2/dist/leaflet.css', array(), $this->version, 'all' );
					wp_enqueue_script( $this->plugin_name.'.bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js', array( 'jquery' ), $this->version, false);
					wp_enqueue_script( $this->plugin_name.'.leaflet', 'https://unpkg.com/leaflet@1.9.2/dist/leaflet.js', array( 'jquery' ), $this->version, false);
					wp_enqueue_script( $this->plugin_name.'.formulaire_offre_emploi_js', plugin_dir_url( __FILE__ ) . 'js/formulaire_offre_emploi.js', array( 'jquery' ), $this->version, true);
					wp_enqueue_script( $this->plugin_name.'.preremplissage_formulaire', plugin_dir_url( __FILE__ ) . 'js/preremplissage_formulaire.js', array( 'jquery' ), $this->version, true);
					$monOffre = wp_create_nonce( 'mon_offre' );
					wp_localize_script(
						$this->plugin_name.'.preremplissage_formulaire',
						'mon_offre_ajax',
						array(
							'ajax_url' => admin_url( 'admin-ajax.php' ),
							'nonce'    => $monOffre,
							'id_offre' => $wp_query->query_vars['idMonOffreEmploi'],
						)
					);
					include(plugin_dir_path( __FILE__ ) .'partials/nouvelle_offre.php');
					return;
				}else{
					return plugin_dir_path( __FILE__ ) .'partials/page_connexion.php';
				}
			}
		}
		//vérification d'une modification d'une offre
		if(array_key_exists('modifier',$wp_query->query_vars) && $wp_query->query_vars['modifier'] ==1){
			if(is_user_logged_in()){
				$this->modification_offre();
				unset($_POST);
				wp_redirect("^/offres-emploi/mesOffres?modification=1");
				exit;
				return;
			}else{
				return plugin_dir_path( __FILE__ ) .'partials/page_connexion.php';
			}
		}
		//liste offres par ville
		if(array_key_exists('ville',$wp_query->query_vars)){
			if(file_exists(plugin_dir_path( __FILE__ ) .'partials/liste_offres_valides.php')) {
				wp_enqueue_style( $this->plugin_name.'.liste_offres_valides_css', plugin_dir_url( __FILE__ ) . 'css/liste_offres_valides.css', array(), $this->version, 'all' );
				wp_enqueue_style( $this->plugin_name.'.pagination', plugin_dir_url( __FILE__ ) . 'css/pagination.css', array(), $this->version, 'all' );
				wp_enqueue_script( $this->plugin_name.'.pagination', plugin_dir_url( __FILE__ ) . 'js/pagination.js', array( 'jquery' ), $this->version, false );
				wp_enqueue_script( $this->plugin_name.'.liste_offres_valides_js', plugin_dir_url( __FILE__ ) . 'js/liste_offres_valides.js', array( 'jquery' ), $this->version, true );
				$liste_offres = wp_create_nonce( 'liste_offres' );
				$commune = $this->get_commune_by_slug($wp_query->query_vars['ville']);
				wp_localize_script(
					$this->plugin_name.'.liste_offres_valides_js',
					'my_ajax_obj',
					array(
						'ajax_url'	=> admin_url( 'admin-ajax.php' ),
						'nonce'   	=> $liste_offres,
						'ville'		=> $commune['id'],
						'test' 		=> $wp_query->query_vars['ville']
					)
				);
				include(plugin_dir_path( __FILE__ ) .'partials/liste_offres_valides.php');
				return;
			}
		}
		return $template;
	}

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
}
