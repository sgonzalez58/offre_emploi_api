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
		
		add_action('wp_ajax_get_offres_par_commune', array($this,'get_offres_par_commune_action'));
		add_action('wp_ajax_nopriv_get_offres_par_commune', array($this,'get_offres_par_commune_action'));

		add_action('wp_ajax_get_offres_sans_filtres', array($this,'get_offres_sans_filtres_action'));
		add_action('wp_ajax_nopriv_get_offres_sans_filtres', array($this,'get_offres_sans_filtres_action'));

		add_action('wp_ajax_get_one_offre', array($this,'get_one_offre'));
		add_action('wp_ajax_nopriv_get_one_offre', array($this,'get_one_offre'));

		add_action('wp_ajax_get_mes_offres', array($this,'get_mes_offres'));
		add_action('wp_ajax_nopriv_get_mes_offres', array($this,'get_mes_offres'));

		add_action('wp_ajax_supprimer_mon_offre', array($this,'supprimer_mon_offre'));
		add_action('wp_ajax_nopriv_supprimer_mon_offre', array($this,'supprimer_mon_offre'));

		add_action('wp_ajax_toggle_visibilite_offre', array($this,'toggle_visibilite_offre'));
		add_action('wp_ajax_nopriv_toggle_visibilite_offre', array($this,'toggle_visibilite_offre'));

		add_action('init', array($this,'offre_emploi_rewrite_rules'));
		add_filter('query_vars', array($this,'offre_emploi_register_query_var' ));
		add_filter('template_include', array($this,'offre_emploi_front_end'));
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
	 * R??cup??res les offres d'autour d'une commune
	 */
	function get_offres_par_commune_action(){
		check_ajax_referer('liste_offres');
		$args = array(
			'idCommune' => $_GET['ville'],
			'distance' => $_GET['distance'],
			'page' => $_GET['pageNumber'],
			'nombre_offres' => $_GET['pageSize']
		);
		
		if(!$args['idCommune']){
			wp_send_json_error("L'id de la ville n'a pas ??t?? envoy??. Erreure lors de la demande ajax.");
		}
		if(!$args['distance']){
            wp_send_json_error("La distance maximale de la recherche n'a pas ??t?? envoy??e. Erreure lors de la demande ajax.");
        }

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
        
		$liste_distances = array();
		$liste_distances[$args['idCommune']] = 0;
		
        if($args['distance'] == 'aucune'){
            $offres = $this->model->findByOffreCommunes(array($args['idCommune']));
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
            $offres = $this->model->findByOffreCommunes($ville_cible);
        }
		
		foreach($offres as &$offre){
			$offre['distance'] = $liste_distances[$offre['commune_id']];
			if(!$offre['commune_id']){
				$offre['distance'] = 101;
			}
		}
		
		array_multisort(array_column($offres, 'distance'), SORT_ASC,array_column($offres, 'id_pole_emploi'), SORT_ASC, $offres);


		$jsonData = [];
        $idx = 0;
		$offset = ($page-1)*$nombre_offres;
        $jsonData['info'] = ['nbOffres' => count($offres), 'nbOffresPage' => $nombre_offres, 'pageActuelle' => (int)$page, 'pageMax' => ceil(count($offres) / $nombre_offres)];
        while($offset < $page*$nombre_offres){
			if($offres[$offset]['ville_libelle'] && $offres[$offset]['ville_libelle'] != 'Non renseign??' && $offres[$offset]['id_pole_emploi']){
				$nomVille = explode('- ', $offres[$offset]['ville_libelle'])[1];
			}else{
				if($offres[$offset]['ville_libelle'] && $offres[$offset]['ville_libelle'] != 'Non renseign??' && !$offres[$offset]['id_pole_emploi']){
					$nomVille = $offres[$offset]['ville_libelle'];
				}
			}
			if($offres[$offset]['latitude']){
				$lienMap = 'https://www.openstreetmap.org/?mlat=' . $offres[$offset]['latitude'] . '&mlon=' . $offres[$offset]['longitude'] . '#map=17/' . $offres[$offset][$offset]['latitude'] . '/' . $offres[$offset]['longitude'] . '&layers=N';
			}else{
				$lienMap = 'aucun';
			}
			if(strlen($offres[$offset]['description']) > 150){
				$description = substr(htmlentities($offres[$offset]['description']), 0, 149) . '...';
			}else{
				$description = $offres[$offset]['description'];
			}
			if($offres[$offset]['nom_entreprise']){
				$nomEntreprise = $offres[$offset]['nom_entreprise'];
			}else{
				$nomEntreprise = 'Aucun';
			}
			$jsonData['offres'][$idx++] = ['id' => $offres[$offset]['id'], 'intitule' => $offres[$offset]['intitule'], 'nomVille' => $nomVille, 'lienMap' => $lienMap, 'description' => $description, 'nomEntreprise' => $nomEntreprise, 'lienOrigineOffre' => $offres[$offset]['origine_offre'], 'distance' => $offres[$offset]['distance'] ];
			$offset++;
        }
        wp_send_json_success($jsonData);
	}

	/**
	 * R??cup??res toutes les offres
	 */
	function get_offres_sans_filtres_action(){
		check_ajax_referer('liste_offres');

		$args = array(
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

		$offres = $this->model->findByOffreVisibles('visible', [], $nombre_offres, ($page - 1) * $nombre_offres);
        $nb_offres_demandees = count($this->model->findByOffreVisibles());
        $jsonData = [];
        $idx = 0;
        $jsonData['info'] = ['nbOffres' => $nb_offres_demandees, 'nbOffresPage' => $nombre_offres, 'pageActuelle' => (int)$page, 'pageMax' => ceil($nb_offres_demandees / $nombre_offres)];
        foreach($offres as $offre){
            if($offre['ville_libelle'] && $offre['ville_libelle'] != 'Non renseign??' && $offre['id_pole_emploi']){
                $nomVille = explode('- ', $offre['ville_libelle'])[1];
            }else{
				if($offre['ville_libelle'] && $offre['ville_libelle'] != 'Non renseign??' && !$offre['id_pole_emploi']){
					$nomVille = $offre['ville_libelle'];
				}
			}
            if($offre['latitude']){
                $lienMap = 'https://www.openstreetmap.org/?mlat=' . $offre['latitude'] . '&mlon=' . $offre['longitude'] . '#map=17/' . $offre['latitude'] . '/' . $offre['longitude'] . '&layers=N';
            }else{
                $lienMap = 'aucun';
            }
            if(strlen($offre['description']) > 150){
                $description = substr(htmlentities($offre['description']), 0, 149) . '...';
            }else{
                $description = $offre['description'];
            }
            if($offre['nom_entreprise']){
                $nomEntreprise = $offre['nom_entreprise'];
            }else{
                $nomEntreprise = 'Aucun';
            }
            $jsonData['offres'][$idx++] = ['id' => $offre['id'], 'intitule' => $offre['intitule'], 'nomVille' => $nomVille, 'lienMap' => $lienMap, 'description' => $description, 'nomEntreprise' => $nomEntreprise, 'lienOrigineOffre' => $offre['origine_offre']];
        }
        wp_send_json_success($jsonData);
	}

	/**
	 * S??curise partiellement les champs du formulaire
	 */
	function secureInput($input){
		return htmlspecialchars(trim($input));
	}

	/**
	 * Cr??e une offre d'emploi
	 */
	function creation_offre_emploi(){
		$intitule = $this->secureInput($_POST['intitule']);
		$appelation_metier = $this->secureInput($_POST['appelation_metier']);
		$nom_entreprise = $this->secureInput($_POST['nom_entreprise']);
		$mail_entreprise = $this->secureInput($_POST['mail_entreprise']);
		$numero_entreprise = $this->secureInput($_POST['numero_entreprise']);
		$type_contrat = $this->secureInput($_POST['type_contrat']);
		$nature_contrat = $this->secureInput($_POST['nature_contrat']);
		if($_POST['alternance'] == 'on'){
			$alternance = 1;
		}else{
			$alternance = 0;
		}
		switch($type_contrat){
			case 'CDD':
				$contratLibelle = 'Contrat ?? dur??e d??termin??e';
				break;
			case 'CDI':
				$contratLibelle = 'Contrat ?? dur??e ind??termin??e';
				break;
			case 'DDI':
				$contratLibelle = 'CDD insertion';
				break;
			case 'DIN':
				$contratLibelle = 'CDI int??rimaire';
				break;
			case 'FRA':
				$contratLibelle = 'Franchise';
				break;
			case 'LIB':
				$contratLibelle = 'Profession lib??rale';
				break;
			case 'MIS':
				$contratLibelle = 'Mission int??rimaire';
				break;
			case 'SAI':
				$contratLibelle = 'Contrat travail saisonnier';
				break;
		}
		if($_POST['mois'] != ''){
			$type_contrat_libelle = ( $contratLibelle . ' - ' . $_POST['mois'] .' Mois');
		}else if ($_POST['jours'] != ''){
			$type_contrat_libelle = ( $contratLibelle . ' - ' . $_POST['jours'] .' Jours(s)');
		}else{
			$type_contrat_libelle = 'Dur??e indetermin??e';
		}
		if(!empty($_POST['montant_salaire'])){
			$salaire = $_POST['montant_salaire'].'??? par '.$_POST['periode_salaire'];
		}
		$duree_travail = $this->secureInput($_POST['duree_travail']);
		$experience_libelle = $this->secureInput($_POST['experience_libelle']);
		$nb_postes = $_POST['nb_postes'];
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
		$this->model->createOneOffre($intitule, $appelation_metier, $type_contrat, $type_contrat_libelle, $nature_contrat, $experience_libelle, $alternance, $nb_postes, $latitude, $longitude, $nom_entreprise, $salaire, $duree_travail, $commune_id, get_current_user_id(), $description, $ville_libelle, $mail_entreprise, $numero_entreprise);
	}

	/**
	 * Modifie une offre d'emploi
	 */
	function modification_offre(){
		$intitule = $this->secureInput($_POST['intitule']);
		$appelation_metier = $this->secureInput($_POST['appelation_metier']);
		$nom_entreprise = $this->secureInput($_POST['nom_entreprise']);
		$mail_entreprise = $this->secureInput($_POST['mail_entreprise']);
		$numero_entreprise = $this->secureInput($_POST['numero_entreprise']);
		$type_contrat = $this->secureInput($_POST['type_contrat']);
		$nature_contrat = $this->secureInput($_POST['nature_contrat']);
		if($_POST['alternance'] == 'on'){
			$alternance = 1;
		}else{
			$alternance = 0;
		}
		switch($type_contrat){
			case 'CDD':
				$contratLibelle = 'Contrat ?? dur??e d??termin??e';
				break;
			case 'CDI':
				$contratLibelle = 'Contrat ?? dur??e ind??termin??e';
				break;
			case 'DDI':
				$contratLibelle = 'CDD insertion';
				break;
			case 'DIN':
				$contratLibelle = 'CDI int??rimaire';
				break;
			case 'FRA':
				$contratLibelle = 'Franchise';
				break;
			case 'LIB':
				$contratLibelle = 'Profession lib??rale';
				break;
			case 'MIS':
				$contratLibelle = 'Mission int??rimaire';
				break;
			case 'SAI':
				$contratLibelle = 'Contrat travail saisonnier';
				break;
		}
		if($_POST['mois'] != ''){
			$type_contrat_libelle = ( $contratLibelle . ' - ' . $_POST['mois'] .' Mois');
		}else if ($_POST['jours'] != ''){
			$type_contrat_libelle = ( $contratLibelle . ' - ' . $_POST['jours'] .' Jours(s)');
		}else{
			$type_contrat_libelle = 'Dur??e indetermin??e';
		}
		if(!empty($_POST['montant_salaire'])){
			$salaire = $_POST['montant_salaire'].'??? par '.$_POST['periode_salaire'];
		}
		$duree_travail = $this->secureInput($_POST['duree_travail']);
		$experience_libelle = $this->secureInput($_POST['experience_libelle']);
		$nb_postes = $_POST['nb_postes'];
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

		$this->model->modifierOffre($_POST['id_offre'], $intitule, $appelation_metier, $type_contrat, $type_contrat_libelle, $nature_contrat, $experience_libelle, $alternance, $nb_postes, $latitude, $longitude, $nom_entreprise, $salaire, $duree_travail, $commune_id, get_current_user_id(), $description, $ville_libelle, $mail_entreprise, $numero_entreprise);
	}

	/**
	 * R??cup??re une offre d'emploi
	 */
	function get_one_offre(){
		check_ajax_referer('mon_offre');

		$args = array(
			'id_offre' => $_POST['id_offre']
		);
		
		if(!$args['id_offre']){
			wp_send_json_error("L'id de l'offre n'a pas ??t?? envoy??. Erreure lors de la demande ajax.");
		}

		$response = $this->model->findOneOffre($args['id_offre']);

		if(!$response){
            wp_send_json_error("L'offre n'existe pas.");
        }

		$jsonData = [
			'id' => $response['id'], 
			'intitule' => $response['intitule'], 
			'metier' => $response['appellation_metier'], 
			'nomEntreprise' => $response['nom_entreprise'], 
			'mailEntreprise' => $response['mail_entreprise'], 
			'telephone_contact' => $response['numero_entreprise'], 
			'type_contrat' => $response['type_contrat'], 
			'nature_contrat' => $response['nature_contrat'], 
			'alternance' => $response['alternance'], 
			'temps_contrat' => $response['type_contrat_libelle'], 
			'salaire' => $response['salaire'], 
			'duree' => $response['duree_travail'], 
			'experience' => $response['experience_libelle'], 
			'nb_poste' => $response['nb_postes'],
			'description' => $response['description'], 
			'commune_id' => $response['commune_id'], 
			'ville' => $response['ville_libelle'], 
			'latitude' => $response['latitude'], 
			'longitude' => $response['longitude']
		];

		wp_send_json_success($jsonData);

	}

	/**
	 * R??cup??re les offres d'emploi d'un utilisateur
	 */
	function get_mes_offres(){
		check_ajax_referer('mes_offres');
        
        $offres = $this->model->findMesOffres(get_current_user_id());

		$jsonData = [];
		$idx = 0;
		foreach($offres as $offre){
			$jsonData[$idx++] = ['intitule' => $offre['intitule'], 'nomVille' => $offre['ville_libelle'], 'nomEntreprise' => $offre['nom_entreprise'], 'dateCreation' => $offre['date_de_creation'], 'etat' => $offre['validation'], 'id' => $offre['id'], 'visibilite' => $offre['visibilite']];
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
			wp_send_json_error("L'id de l'offre n'a pas ??t?? envoy??. Erreure lors de la demande ajax.");
		}

		if(!$this->model->findOneOffre($args['id_offre'])){
            wp_send_json_error("L'offre n'existe pas.");
        }

		$reponse = $this->model->supprimerMonOffre($args['id_offre']);

		if($reponse != 'Suppression r??ussie'){
			wp_send_json_error('Erreure lors de la supression.');
		}
	}

	/**
	 * Modifie la visibilit?? d'une offre d'emploi
	 */
	function toggle_visibilite_offre(){
		check_ajax_referer('mes_offres');

		$args = array(
			'id_offre' => $_POST['id_offre'],
			'visibilite' => $_POST['visibilite']
		);
		
		if(!$args['id_offre']){
			wp_send_json_error("L'id de l'offre n'a pas ??t?? envoy??. Erreure lors de la demande ajax.");
		}

		if(!$args['visibilite']){
			wp_send_json_error("La visibilit?? souhait??e n'a pas ??t?? envoy??e. Erreure lors de la demande ajax.");
		}

		if(!$this->model->findOneOffre($args['id_offre'])){
            wp_send_json_error("L'offre n'existe pas.");
        }

		$reponse = $this->model->toggleVisibiliteOffre($args['id_offre'], $args['visibilite']);

		if($reponse != 'Suppression r??ussie'){
			wp_send_json_error('Erreure lors de la supression : ' . $reponse);
		}
	}

	/**
	 * R??-??critude des routes
	 */
	function offre_emploi_rewrite_rules() {	

		add_rewrite_rule('^offreEmploi/([0-9]+)/?', 'index.php?idOffreEmploi=$matches[1]', 'top');

		add_rewrite_rule('^offreEmploi/creer/verification/?', 'index.php?verificationNouvelleOffre=1', 'top');

		add_rewrite_rule('^offreEmploi/creer/?', 'index.php?nouvelleOffre=1', 'top');

		add_rewrite_rule('^offreEmploi/mesOffres/modification/?', 'index.php?modifier=1', 'top');

		add_rewrite_rule('^offreEmploi/mesOffres/([0-9]+)/?', 'index.php?idMonOffreEmploi=$matches[1]', 'top');

		add_rewrite_rule('^offreEmploi/mesOffres/?', 'index.php?mesOffres=1', 'top');

		add_rewrite_rule('^offreEmploi/?', 'index.php?offreEmploi=1', 'top');
  		
	}
	
	/**
	 * Initialisation des variables url
	 */
	function offre_emploi_register_query_var( $vars ) {
		
		$vars[] = 'offreEmploi';
		$vars[] = 'idOffreEmploi';
		$vars[] = 'nouvelleOffre';
		$vars[] = 'mesOffres';
		$vars[] = 'idMonOffreEmploi';
		$vars[] = 'modifier';
		$vars[] = 'verificationNouvelleOffre';

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
		if(array_key_exists('idOffreEmploi',$wp_query->query_vars)){
			if(file_exists(plugin_dir_path( __FILE__ ) .'partials/fiche_offre.php')) {
				wp_enqueue_style( $this->plugin_name.'offre', plugin_dir_url( __FILE__ ) . 'css/offre.css', array(), $this->version, 'all' );
				wp_enqueue_script( $this->plugin_name.'masonry', "https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.js", array( 'jquery' ), $this->version, false);
				include(plugin_dir_path( __FILE__ ) .'partials/fiche_offre.php');
				return;
			}
		}
		//formulaire de cr??ation d'une offre
		if(array_key_exists('nouvelleOffre',$wp_query->query_vars)){
			if(file_exists(plugin_dir_path( __FILE__ ) .'partials/nouvelle_offre.php')) {
				if(is_user_logged_in()){
					wp_enqueue_style( $this->plugin_name.'.formulaire_offre_emploi_css', plugin_dir_url( __FILE__ ) . 'css/formulaire_offre_emploi.css', array(), $this->version, 'all' );
					wp_enqueue_style( $this->plugin_name.'.leaflet', 'https://unpkg.com/leaflet@1.9.2/dist/leaflet.css', array(), $this->version, 'all' );
					wp_enqueue_script( $this->plugin_name.'.leaflet', 'https://unpkg.com/leaflet@1.9.2/dist/leaflet.js', array( 'jquery' ), $this->version, false);
					wp_enqueue_script( $this->plugin_name.'.formulaire_offre_emploi_js', plugin_dir_url( __FILE__ ) . 'js/formulaire_offre_emploi.js', array( 'jquery' ), $this->version, true);
					include(plugin_dir_path( __FILE__ ) .'partials/nouvelle_offre.php');
					return;
				}else{
					echo 'Vous devez ??tre connect??(e) pour ajouter une nouvelle offre.';
				}
			}
		}
		//v??rification du formulaire et cr??ation de l'offre
		if(array_key_exists('verificationNouvelleOffre',$wp_query->query_vars)){
			if(is_user_logged_in()){
				$this->creation_offre_emploi();
				unset($_POST);
				if(file_exists(plugin_dir_path( __FILE__ ) .'partials/reponse_nouvelle_offre.php')) {
					include(plugin_dir_path( __FILE__ ) .'partials/reponse_nouvelle_offre.php');
					return;
				}else{
					echo 'Une erreur s\'est produite, votre demande de nouvelle offre a toutefois ??t?? envoy??e.';
				}
			}else{
				echo 'Vous devez ??tre connect??(e) pour ajouter une nouvelle offre.';
			}
		}
		//affichage des offres d'un utilisateur connect??
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
				echo 'Vous devez ??tre connect??(e) pour consulter vos offres.';
			}
		}
		//formulaire de modification d'une offre d'un utilisateur connect??
		if(array_key_exists('idMonOffreEmploi',$wp_query->query_vars)){
			if(file_exists(plugin_dir_path( __FILE__ ) .'partials/nouvelle_offre.php')) {
				if(is_user_logged_in()){
					wp_enqueue_style( $this->plugin_name.'.formulaire_offre_emploi_css', plugin_dir_url( __FILE__ ) . 'css/formulaire_offre_emploi.css', array(), $this->version, 'all' );
					wp_enqueue_style( $this->plugin_name.'.leaflet', 'https://unpkg.com/leaflet@1.9.2/dist/leaflet.css', array(), $this->version, 'all' );
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
					echo 'Vous devez ??tre connect??(e) pour modifier une offre.';
				}
			}
		}
		//v??rification d'une modification d'une offre
		if(array_key_exists('modifier',$wp_query->query_vars) && $wp_query->query_vars['modifier'] ==1){
			if(is_user_logged_in()){
				$this->modification_offre();
				unset($_POST);
				if(file_exists(plugin_dir_path( __FILE__ ) .'partials/modification_offre.php')) {
					include(plugin_dir_path( __FILE__ ) .'partials/modification_offre.php');
					return;
				}else{
					echo 'Une erreur s\'est produite, votre demande de nouvelle offre a toutefois ??t?? envoy??e.';
				}
				return;
			}else{
				echo 'Vous devez ??tre connect??(e) pour modifier une offre.';
			}
		}
		return $template;
	}
}
