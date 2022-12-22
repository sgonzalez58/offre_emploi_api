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

	function get_offres_par_commune_action(){
		check_ajax_referer('liste_offres');
		$args = array(
			'idCommune' => $_POST['ville'],
			'distance' => $_POST['distance'],
			'page' => $_POST['page']
		);
		
		if(!$args['idCommune']){
			wp_send_json_error("L'id de la ville n'a pas été envoyé. Erreure lors de la demande ajax.");
		}
		if(!$args['distance']){
            wp_send_json_error("La distance maximale de la recherche n'a pas été envoyée. Erreure lors de la demande ajax.");
        }

		if(!$args['page']){
			$page = 1;
		}else{
			$page = $args['page'];
		}

		$nb_offres = 50;
        
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
		
		array_multisort(array_column($offres, 'distance'), SORT_ASC, $offres);

		$jsonData = [];
        $idx = 0;
		$offset = 0;
        $jsonData['info'] = ['nbOffres' => count($offres), 'nbOffresPage' => 50, 'pageActuelle' => (int)$page, 'pageMax' => ceil(count($offres) / 50)];
        foreach($offres as $offre){
			if($offset >= ($page - 1) * 50 && $offset < $page * 50){
				if($offre['ville_libelle'] && $offre['ville_libelle'] != 'Non renseigné'){
					$nomVille = explode('- ', $offre['ville_libelle'])[1];
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
				$jsonData[$idx++] = ['id' => $offre['id'], 'intitule' => $offre['intitule'], 'nomVille' => $nomVille, 'lienMap' => $lienMap, 'description' => $description, 'nomEntreprise' => $nomEntreprise, 'lienOrigineOffre' => $offre['origine_offre'], 'distance' => $offre['distance'] ];
				$offset++;
			}else{
				$offset++;
			}
        }
        wp_send_json_success($jsonData);
	}

	function get_offres_sans_filtres_action(){
		check_ajax_referer('liste_offres');

		$args = array(
			'page' => $_POST['page']
		);

		if(!$args['page']){
			$page = 1;
		}else{
			$page = $args['page'];
		}

		$nb_offres = 50;

		$offres = $this->model->findByOffreValidation('valide', [], $nb_offres, ($page - 1) * $nb_offres);
        $nb_offres_demandees = count($this->model->findByOffreValidation());
        $jsonData = [];
        $idx = 0;
        $jsonData['info'] = ['nbOffres' => $nb_offres_demandees, 'nbOffresPage' => 50, 'pageActuelle' => (int)$page, 'pageMax' => ceil($nb_offres_demandees / 50)];
        foreach($offres as $offre){
            if($offre['ville_libelle'] && $offre['ville_libelle'] != 'Non renseigné'){
                $nomVille = explode('- ', $offre['ville_libelle'])[1];
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
            $jsonData[$idx++] = ['id' => $offre['id'], 'intitule' => $offre['intitule'], 'nomVille' => $nomVille, 'lienMap' => $lienMap, 'description' => $description, 'nomEntreprise' => $nomEntreprise, 'lienOrigineOffre' => $offre['origine_offre']];
        }
        wp_send_json_success($jsonData);
	}

	function creation_offre_emploi(){
		$intitule = $_POST['intitule'];
		$appelation_metier = $_POST['appelation_metier'];
		$nom_entreprise = $_POST['nom_entreprise'];
		$mail_entreprise = $_POST['mail_entreprise'];
		$numero_entreprise = $_POST['numero_entreprise'];
		$type_contrat = $_POST['type_contrat'];
		$nature_contrat = $_POST['nature_contrat'];
		if($_POST['alternance'] == 'on'){
			$alternance = 1;
		}else{
			$alternance = 0;
		}
		switch($type_contrat){
			case 'CDD':
				$contratLibelle = 'Contrat à durée déterminée';
				break;
			case 'CDI':
				$contratLibelle = 'Contrat à durée indéterminée';
				break;
			case 'DDI':
				$contratLibelle = 'CDD insertion';
				break;
			case 'DIN':
				$contratLibelle = 'CDI intérimaire';
				break;
			case 'FRA':
				$contratLibelle = 'Franchise';
				break;
			case 'LIB':
				$contratLibelle = 'Profession libérale';
				break;
			case 'MIS':
				$contratLibelle = 'Mission intérimaire';
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
			$type_contrat_libelle = 'Durée indeterminée';
		}
		$salaire = $_POST['montant_salaire'].'€ par '.$_POST['periode_salaire'];
		$duree_travail = $_POST['duree_travail'];
		$experience_libelle = $_POST['experience_libelle'];
		$nb_postes = $_POST['nb_postes'];
		$description = $_POST['description'];
		if($_POST['latitude'] != ''){
			$latitude = $_POST['latitude'];
			$longitude = $_POST['longitude'];
			$ville_libelle = $_POST['ville_libelle'];
		}else{
			if($_POST['commune'] != ''){
				$commune_id = $_POST['commune'];
				$commune = $this->model->findOneCommune($commune_id);
				$latitude = $commune['latitude'];
				$longitude = $commune['longitude'];
				$ville_libelle = ucwords($commune['slug']);
			}else{
				$ville_libelle = $_POST['ville_libelle'];
			}
		}
		$this->model->createOneOffre($intitule, $appelation_metier, $type_contrat, $type_contrat_libelle, $nature_contrat, $experience_libelle, $alternance, $nb_postes, $latitude, $longitude, $nom_entreprise, $salaire, $duree_travail, $commune_id, get_current_user_id(), $description, $ville_libelle, $mail_entreprise, $numero_entreprise);
	}

	function offre_emploi_rewrite_rules() {	

		add_rewrite_rule('^offreEmploi/([0-9]+)/?', 'index.php?idOffreEmploi=$matches[0]', 'top');

		add_rewrite_rule('^offreEmploi/creer/verification/?', 'index.php?verificationNouvelleOffre=1', 'top');

		add_rewrite_rule('^offreEmploi/creer/?', 'index.php?nouvelleOffre=1', 'top');

		add_rewrite_rule('^offreEmploi/?', 'index.php?offreEmploi=1', 'top');
  		
	}
	
	function offre_emploi_register_query_var( $vars ) {
		
		$vars[] = 'offreEmploi';
		$vars[] = 'idOffreEmploi';
		$vars[] = 'nouvelleOffre';
		$vars[] = 'verificationNouvelleOffre';

		return $vars;
	}
	
	
	function offre_emploi_front_end($template)
	{
		global $wp_query; //Load $wp_query object

		if(array_key_exists('offreEmploi',$wp_query->query_vars) && $wp_query->query_vars['offreEmploi'] ==1){
			if(file_exists(plugin_dir_path( __FILE__ ) .'partials/liste_offres_valides.php')) {
				wp_enqueue_style( $this->plugin_name.'.liste_offres_valides_css', plugin_dir_url( __FILE__ ) . 'css/liste_offres_valides.css', array(), $this->version, 'all' );
				wp_enqueue_script( $this->plugin_name.'.liste_offres_valides_js', plugin_dir_url( __FILE__ ) . 'js/liste_offres_valides.js', array( 'jquery' ), $this->version, true );
				$liste_offres = wp_create_nonce( 'liste_offres' );
				wp_localize_script(
					$this->plugin_name.'.liste_offres_valides',
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
		if(array_key_exists('idOffreEmploi',$wp_query->query_vars)){
			if(file_exists(plugin_dir_path( __FILE__ ) .'partials/fiche_offre.php')) {
				wp_enqueue_style( $this->plugin_name.'.offre', plugin_dir_url( __FILE__ ) . 'css/offre.css', array(), $this->version, 'all' );
				wp_enqueue_script( $this->plugin_name.'.masonry', "https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.js", array( 'jquery' ), $this->version, false);
				include(plugin_dir_path( __FILE__ ) .'partials/fiche_offre.php');
				return;
			}
		}
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
					echo 'Vous devez être connecté(e) pour ajouter une nouvelle offre.';
				}
			}
		}
		if(array_key_exists('verificationNouvelleOffre',$wp_query->query_vars)){
			if(is_user_logged_in()){
				$this->creation_offre_emploi();
				if(file_exists(plugin_dir_path( __FILE__ ) .'partials/reponse_nouvelle_offre.php')) {
					wp_enqueue_style( $this->plugin_name.'.reponse_nouvelle_offre_css', plugin_dir_url( __FILE__ ) . 'css/reponse_nouvelle_offre.css', array(), $this->version, 'all' );
					include(plugin_dir_path( __FILE__ ) .'partials/reponse_nouvelle_offre.php');
					return;
				}
				else{
					echo 'Une erreur s\'est produite, votre demande de nouvelle offre a toutefois été envoyée.';
				}
			}else{
				echo 'Vous devez être connecté(e) pour ajouter une nouvelle offre.';
			}
		}

	/* 	if( array_key_exists('listeDate',$wp_query->query_vars) ){

			return plugin_dir_path( __FILE__ ) .'partials/liste_dates.php';
		} 		
		 */


		return $template;
	}
}
