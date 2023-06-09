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
		
		add_action('wp_ajax_recherche_mot_clef', array($this,'recherche_mot_clef'));
		add_action('wp_ajax_nopriv_recherche_mot_clef', array($this,'recherche_mot_clef'));

		add_action('wp_ajax_get_one_offre', array($this,'get_one_offre'));
		add_action('wp_ajax_nopriv_get_one_offre', array($this,'get_one_offre'));

		add_action('wp_ajax_get_mes_offres', array($this,'get_mes_offres'));
		add_action('wp_ajax_nopriv_get_mes_offres', array($this,'get_mes_offres'));

		add_action('wp_ajax_supprimer_mon_offre', array($this,'supprimer_mon_offre'));
		add_action('wp_ajax_nopriv_supprimer_mon_offre', array($this,'supprimer_mon_offre'));

		add_action('wp_ajax_toggle_visibilite_offre', array($this,'toggle_visibilite_offre'));
		add_action('wp_ajax_nopriv_toggle_visibilite_offre', array($this,'toggle_visibilite_offre'));

		add_action('wp_ajax_get_candidatures', array($this,'get_candidatures'));
		add_action('wp_ajax_nopriv_get_candidatures', array($this,'get_candidatures'));

		add_action('wp_ajax_get_mes_candidatures', array($this,'get_mes_candidatures'));
		add_action('wp_ajax_nopriv_get_mes_candidatures', array($this,'get_mes_candidatures'));

		add_action('init', array($this,'offre_emploi_rewrite_rules'));
		add_filter('query_vars', array($this,'offre_emploi_register_query_var' ));
		add_filter('template_include', array($this,'offre_emploi_front_end'));

		add_shortcode('offre_emploi', array($this, 'liste_villes_offre'));

		add_filter('wpseo_title', array($this, 'prefix_filter_title'));
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
	 * SEO des titres
	 */
	function prefix_filter_title( $title ){
		global $wp_query; //Load $wp_query object
		if(array_key_exists('ville',$wp_query->query_vars)){
			$commune = $this->model->findOneCommuneBySlug($wp_query->query_vars['ville']);
			$title = "Offres d'emploi à ".$commune['nom_commune']." - Nièvre 58 - Koikispass";
		}
		if(array_key_exists('idOffreEmploi',$wp_query->query_vars)){	
			$offre = $this->model->findOneOffre($wp_query->query_vars['idOffreEmploi']);
			$commune = $this->model->findOneCommune($offre['commune_id']);
			$title = $offre['libelle_metier'].' à '.$commune['nom_commune'].' - Nièvre 58 - Koikispass';
		}
		if(array_key_exists('offreEmploi',$wp_query->query_vars)){	
			$title = "Offres d'emploi - Nièvre 58 - Koikispass";
		}
		return $title;
	}

	public function getOffresValides() {
		
		return $dates = $this->model->findByMotsClef();	
	}	

	public function getAllCommunes() {
		
		return $villes = $this->model->findAllCommunes();	
	}	

	public function getAllTypeContrat() {
		
		return $categories = $this->model->getAllTypeContrat();	
	}	

	public function get_commune_by_slug($slug) {
		
		return $commune = $this->model->findOneCommuneBySlug($slug);
		
	}

	public function get_nb_communes() {
		
		return $nb_communes = $this->model->getNbCommunes();
		
	}

	public function get_nb_types_contrat() {
		
		return $nb_types_contrat = $this->model->getNbTypesContrat();
		
	}

	public function get_nb_communes_1($them) {
		
		return $nb_communes1 = $this->model->getNbCommunes1($them);
		
	}

	public function get_nb_types_contrat_1($comm) {
		
		return $nb_types_contrat1 = $this->model->getNbTypesContrat1($comm);
		
	}
	

	/**
	 * Récupères les offres par mot clef
	 */
	function recherche_mot_clef(){
		check_ajax_referer('liste_offres');
		$args = array(
			'mots_clef' => strtolower($_GET['mots_clef']),
			'idCommune' => $_GET['ville'],
			'distance' => $_GET['distance'],
			'type_de_contrat' => $_GET['type_de_contrat'],
			'page' => $_GET['pageNumber'],
			'nombre_offres' => $_GET['pageSize']
		);
		if(!$args['page']){
			$page = 1;
		}else{
			$page = $args['page'];
		}

		if(!$args['nombre_offres']){
			$nombre_offres = 50;
		}else{
			$nombre_offres = $args['nombre_offres'];
		}

		if($args['mots_clef'] == ""){
			$args['mots_clef'] = [];
		}else{
			$args['mots_clef'] = explode(' ', $args['mots_clef']);
		}
        
		if(!$args['idCommune']){
			$offres = $this->model->findByMotsClef($args['mots_clef'], $args['type_de_contrat']);
		}else{
			$liste_distances = array();
			$liste_distances[$args['idCommune']] = 0;
			if($args['distance'] == 'aucune'){
				$offres = $this->model->findByMotsClef($args['mots_clef'], $args['type_de_contrat'], array($args['idCommune']));
			}else{
				$offres = array();
				$ville_cible = array();
				$ville_a_trier = $this->model->findAllCommunes();

				foreach($ville_a_trier as $commune){
					if($commune['id'] == $args['idCommune']){
						array_push($ville_cible, $commune['id']); 
					}else{
						$villeFrom = $this->model->findOneCommune($args['idCommune']);
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
							$liste_distances[$commune['id']] = $distanceVilles;
						}
					}
				}
				$offres = $this->model->findByMotsClef($args['mots_clef'], $args['type_de_contrat'], $ville_cible);
			}
			foreach($offres as &$offre){
				$offre['distance'] = $liste_distances[$offre['commune_id']];
				if(!$offre['commune_id']){
					$offre['distance'] = 101;
				}
			}
			
			array_multisort(array_column($offres, 'distance'), SORT_ASC,array_column($offres, 'id_pole_emploi'), SORT_ASC, $offres);
		}
		$jsonData = [];
        $idx = 0;
		$offset = ($page-1)*$nombre_offres;
        $jsonData['info'] = ['nbOffres' => count($offres), 'nbOffresPage' => $nombre_offres, 'pageActuelle' => (int)$page, 'pageMax' => ceil(count($offres) / $nombre_offres)];
		$jsonData['offres'] = [];
        while($offset < $page*$nombre_offres && isset($offres[$offset])){
			if($offres[$offset]['ville_libelle'] && $offres[$offset]['ville_libelle'] != 'Non renseigné' && $offres[$offset]['id_pole_emploi']){
				$nomVille = explode('- ', $offres[$offset]['ville_libelle'])[1];
			}else{
				if($offres[$offset]['ville_libelle'] && $offres[$offset]['ville_libelle'] != 'Non renseigné' && !$offres[$offset]['id_pole_emploi']){
					$nomVille = $offres[$offset]['ville_libelle'];
				}
			}
			if($offres[$offset]['latitude']){
				$lienMap = 'https://www.openstreetmap.org/?mlat=' . $offres[$offset]['latitude'] . '&mlon=' . $offres[$offset]['longitude'] . '#map=17/' . $offres[$offset][$offset]['latitude'] . '/' . $offres[$offset]['longitude'] . '&layers=N';
			}else{
				$lienMap = 'aucun';
			}
			if(strlen($offres[$offset]['description']) > 150){
				$description = substr($offres[$offset]['description'], 0, 149) . '...';
			}else{
				$description = $offres[$offset]['description'];
			}
			if($offres[$offset]['nom_entreprise']){
				if(strlen($offres[$offset]['nom_entreprise']) > 23){
					$nomEntreprise = substr($offres[$offset]['nom_entreprise'], 0, 22);
				}else{
					$nomEntreprise = $offres[$offset]['nom_entreprise'];
				}
			}else{
				$nomEntreprise = 'Aucun';
			}
			$jsonData['offres'][$idx++] = ['id' => $offres[$offset]['id'], 'intitule' => $offres[$offset]['intitule'], 'nomVille' => $nomVille, 'lienMap' => $lienMap, 'description' => $description, 'nomEntreprise' => $nomEntreprise, 'lienOrigineOffre' => $offres[$offset]['origine_offre'], 'distance' => $offres[$offset]['distance'], 'type_contrat' => $offres[$offset]['type_contrat'] ];
			$offset++;
        }
        wp_send_json_success($jsonData);
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
			$commune = $this->model->findOneCommune($commune_id);
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
			$commune = $this->model->findOneCommune($commune_id);
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

		$response = $this->model->findOneOffre($args['id_offre']);

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

		if(!$this->model->findOneOffre($args['id_offre'])){
            wp_send_json_error("L'offre n'existe pas.");
        }

		$reponse = $this->model->supprimerMonOffre($args['id_offre']);

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

		if(!$this->model->findOneOffre($args['id_offre'])){
            wp_send_json_error("L'offre n'existe pas.");
        }

		$reponse = $this->model->toggleVisibiliteOffre($args['id_offre'], $args['visibilite']);

		if($reponse != 'Suppression réussie'){
			wp_send_json_error('Erreure lors de la supression : ' . $reponse);
		}
	}

	/**
	 * Envoie un mail au propriétaire de l'offre d'emploi et ajoute une candidature dans la table correspondante
	 */
	function envoie_candidature($id_offre_emploi){
		if(!$id_offre_emploi){
			echo('Erreur à l\'envoie de la candidature. L\'id de l\'offre n\'a pas été précisé. A voir avec l\'administrateur.');
		}else{
			$args = array(
				'id_user' => get_current_user_id(),
				'prenom' => $_POST['prenom'],
				'nom' => $_POST['nom'],
				'mail' => $_POST['mail'],
				'message' => $_POST['message']
			);
			$form_complet = true;
			if(!$args['prenom']){
				echo('Le prénom n\'a pas été fourni');
				$form_complet = false;
			}
			if(!$args['nom']){
				echo('Le nom n\'a pas été fourni');
				$form_complet = false;
			}
			if(!$args['mail']){
				echo('Le mail n\'a pas été fourni');
				$form_complet = false;
			}
			if(!$args['message']){
				echo('Le message n\'a pas été fourni');
				$form_complet = false;
			}
			if($form_complet){
				if($args['id_user']){
					$this->model->createCandidature($id_offre_emploi, $args['mail'], $args['id_user']);
				}else{
					$this->model->createCandidature($id_offre_emploi, $args['mail']);
				}
				$offre = $this->model->findOneOffre($id_offre_emploi);
				$mail_offre = wp_get_current_user()->user_email;

				$this->envoi_email_utilisateur($mail_offre, $offre['intitule'].' - '.$args['prenom'].' '.$args['nom'].' - '.$args['mail'].' - '.$args['message'] , 'candidature');
			}
		}
	}

	/**
	 * Récupère le nombre de candidature d'un mail sur une offre
	 */
	function get_candidatures(){
		check_ajax_referer('mes_candidatures');

		$args=[
			'id_offre' => $_POST['id_offre'],
			'mail' => $_POST['mail']
		];

		if(!$args['id_offre']){
			wp_send_json_error('Erreur à la demande. L\'id de l\'offre n\'a pas été précisé. A voir avec l\administrateur.');
		}
		if(!$args['mail']){
			wp_send_json_error('Le mail n\'a pas été fourni');
		}
		
		$candidatures = $this->model->findCandidatures($args['id_offre'], $args['mail']);
		
		wp_send_json_success(['nombre_de_demande' => count($candidatures)]);
	}

	/**
	 * Récupère les candidatures d'un utilisateur
	 */
	function get_mes_candidatures(){
		check_ajax_referer('mes_offres');
        
        $offres = $this->model->findMesCandidatures(get_current_user_id());

		$jsonData = [];
		$idx = 0;
		foreach($offres as $offre){
			$jsonData[$idx++] = ['intitule' => $offre['intitule'], 'nomVille' => $offre['ville_libelle'], 'nomEntreprise' => $offre['nom_entreprise'], 'dateCreation' => $offre['date_envoi'], 'mail' => $offre['mail'] , 'etat' => $offre['validation'], 'id' => $offre['id']];
		}

        wp_send_json_success($jsonData);
	}

	/**
	 * Ré-écritude des routes
	 */
	function offre_emploi_rewrite_rules() {	

		add_rewrite_rule('^offres-emploi/([0-9]+)/candidature/?', 'index.php?idOffreEmploi=$matches[1]&candidature=1', 'top');

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
		$vars[] = 'commune';
		$vars[] = 'thematique';
		$vars[] = 'nouvelleOffre';
		$vars[] = 'mesOffres';
		$vars[] = 'idMonOffreEmploi';
		$vars[] = 'modifier';
		$vars[] = 'verificationNouvelleOffre';
		$vars[] = 'candidature';
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
			
			if( ( ( array_key_exists('thematique',$wp_query->query_vars) && $wp_query->query_vars['thematique']!='toutes') || ( array_key_exists('commune',$wp_query->query_vars) && $wp_query->query_vars['commune'] != 'toutes' )) ){
				return plugin_dir_path( __FILE__ ) .'partials/liste_offres_sans_filtres.php';
			}

			wp_enqueue_style( $this->plugin_name.'.liste_offres_sans_filtres_css', plugin_dir_url( __FILE__ ) . 'css/liste_offres_sans_filtres.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name.'.pagination', plugin_dir_url( __FILE__ ) . 'css/pagination.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name.'.ui_css', '//code.jquery.com/ui/1.13.0/themes/base/jquery-ui.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name.'.google_apis', 'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name.'.google_icon', 'https://fonts.googleapis.com/icon?family=Material+Icons', array(), $this->version, 'all' );

			wp_enqueue_script( $this->plugin_name.'.pagination', plugin_dir_url( __FILE__ ) . 'js/pagination.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( $this->plugin_name.'.ui_js', 'https://code.jquery.com/ui/1.13.0/jquery-ui.min.js', array( 'jquery' ), $this->version, false );
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
			if(file_exists(plugin_dir_path( __FILE__ ) .'partials/liste_offres_valides.php')) {
				wp_enqueue_style( $this->plugin_name.'.liste_offres_valides_css', plugin_dir_url( __FILE__ ) . 'css/liste_offres_valides.css', array(), $this->version, 'all' );
				wp_enqueue_style( $this->plugin_name.'.pagination', plugin_dir_url( __FILE__ ) . 'css/pagination.css', array(), $this->version, 'all' );
				wp_enqueue_script( $this->plugin_name.'.pagination', plugin_dir_url( __FILE__ ) . 'js/pagination.js', array( 'jquery' ), $this->version, false );
				wp_enqueue_script( $this->plugin_name.'.liste_offres_valides_js', plugin_dir_url( __FILE__ ) . 'js/liste_offres_valides.js', array( 'jquery' ), $this->version, true );
				$liste_offres = wp_create_nonce( 'liste_offres' );
				wp_localize_script(
					$this->plugin_name.'.liste_offres_valides_js',
					'my_ajax_obj',
					array(
						'ajax_url' => admin_url( 'admin-ajax.php' ),
						'nonce'    => $liste_offres,
					)
				);
				include(plugin_dir_path( __FILE__ ) .'partials/liste_offres_valides.php');
				return;
			}
		}
		//affichage de la fiche d'une offre
		if(array_key_exists('offreEmploi',$wp_query->query_vars)){	
			if(array_key_exists('candidature', $wp_query->query_vars)){
				$this->envoie_candidature($wp_query->query_vars['idOffreEmploi']);
				wp_redirect("^/offres-emploi/".$wp_query->query_vars['idOffreEmploi']."?postule=1");
				exit;
				return;
			}
			if(file_exists(plugin_dir_path( __FILE__ ) .'partials/fiche_offre.php')) {
				wp_enqueue_style( $this->plugin_name.'offre', plugin_dir_url( __FILE__ ) . 'css/offre.css', array(), $this->version, 'all' );
				wp_enqueue_script( $this->plugin_name.'fiche_offre', plugin_dir_url( __FILE__ ) . 'js/fiche_offre.js', array( 'jquery'), $this->version, true);
				$mesCandidatures = wp_create_nonce( 'mes_candidatures' );
				wp_localize_script(
					$this->plugin_name.'fiche_offre',
					'mes_candidature_ajax',
					array(
						'ajax_url' => admin_url( 'admin-ajax.php' ),
						'nonce'    => $mesCandidatures,
						'id_offre_emploi' => $wp_query->query_vars['idOffreEmploi']
					)
				);
				include(plugin_dir_path( __FILE__ ) .'partials/fiche_offre.php');
				return;
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
					echo 'Vous devez être connecté(e) pour ajouter une nouvelle offre.';
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
				echo 'Vous devez être connecté(e) pour ajouter une nouvelle offre.';
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
				echo 'Vous devez être connecté(e) pour consulter vos offres.';
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
					echo 'Vous devez être connecté(e) pour modifier une offre.';
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
				echo 'Vous devez être connecté(e) pour modifier une offre.';
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
				$commune = $this->model->findOneCommuneBySlug($wp_query->query_vars['ville']);
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
			}elseif('candidature'){
				$demande = explode(' - ', $content);
				$intitule_offre = $demande[0];
				$prenom_nom_user = $demande[1];
				$mail_user = $demande[2];
				$demande = $demande[3];

				$mail->Subject = utf8_decode("Candidature pour votre offre '".$intitule_offre."' - ".$prenom_nom_user);
				$message = "<table cellpadding=0 cellspacing=0>
								<tr>
									<td width='11px'></td>
										<td width='10px'>&nbsp;</td>
										<td width='729px'>

										<p>Bonjour,</p>

										<p>".$prenom_nom_user." a candidaté(e) pour votre offre '".$intitule_offre."' :
										<br>'".nl2br($demande)."'</p>

										<p>Vous pouvez le contacter sur le mail <a href='mailto:".$mail_user."?subject=RE:Candidature [".$intitule_offre."]'>".$mail_user."</a></p>
										<p><br>Cordialement.</p>
									
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
