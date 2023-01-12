<?php

/**
 * The model of the plugin.
 *
 * @link       https://www.koikispass.com
 * @since      1.0.0
 *
 * @package    Offre_emploi
 * @subpackage Offre_emploi/models
 */

use Symfony\Component\Validator\Constraints\NotNull;

/**
 * The model of the plugin.
 *
 * Defines the plugin database 
 *
 * @package    Offre_emploi
 * @subpackage Offre_emploi/models
 * @author     dev-iticonseil <dev@iti-conseil.com>
 */
class Offre_Emploi_Model {
	
	
	/**
	 * The external database needed.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      object    $offreEmploiDB    The external database needed.
	 */	
	private $offreEmploiDB;
	
	private $TableOffreEmploi;
	private $TableCommune;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	function __construct() {
		
		$offreEmploiDB = new wpdb( 'emploikkp', 'qk5ou2cn3tcpj', 'emploikkp_db', 'localhost' );		
		$this->offreEmploiDB 	 = $offreEmploiDB;
		$this->TableOffreEmploi = 'offre_emploi';
		$this->TableCommune = 'commune';
	}
	
	/**
	 * Récupère toutes les offres utilisateurs non archivées
	 */
	public function findAllOffresUser(){

		$sql = $this->offreEmploiDB->prepare('SELECT * FROM '.$this->TableOffreEmploi."
			WHERE id_pole_emploi IS NULL AND archive = 'non'");

		$this->offreEmploiDB->query( $sql );
		
		if( $this->offreEmploiDB->num_rows > 0 )
			$return = $this->offreEmploiDB->get_results($sql, ARRAY_A);
		
		return $return;
	}

	/**
	 * Récupère le nombre d'offres d'emploi utilisateur en attente de réponse
	 */
	public function findCountPendingOffresUser(){

		$sql = $this->offreEmploiDB->prepare('SELECT * FROM '.$this->TableOffreEmploi.'
			WHERE id_pole_emploi IS NULL AND validation = \'en attente\'');

		$this->offreEmploiDB->query( $sql );
		
		return $this->offreEmploiDB->num_rows;
	}

	/**
	 * Récupère une offre d'emploi
	 */
	public function findOneOffre($idOffre){
		$sql = $this->offreEmploiDB->prepare('SELECT * FROM '.$this->TableOffreEmploi.' A
				WHERE A.id = '.$idOffre);
		
		$this->offreEmploiDB->query( $sql );
		
		if( $this->offreEmploiDB->num_rows > 0 )
			$return = $this->offreEmploiDB->get_results($sql, ARRAY_A);
		
		return $return[0];
	}

	/**
	 * Récupère les offres d'emploi visibles par les visiteurs
	 */
	public function findByOffreVisibles($visibilite = 'visible', array $orderBy = null, $limit = null, $offset = null){

		$baseSql = 'SELECT * FROM '.$this->TableOffreEmploi.' A WHERE A.visibilite = \''.$visibilite.'\' 
															ORDER BY A.id_pole_emploi';

		if($orderBy){
			$baseSql .= ' , A.'.key($orderBy).' '.$orderBy[key($orderBy)];
		}

		if($limit){
			$baseSql .= ' LIMIT '.$limit;
		}

		if($offset){
			$baseSql .= ' OFFSET '.$offset;
		}

		$sql = $this->offreEmploiDB->prepare($baseSql);
		
		$this->offreEmploiDB->query( $sql );
		
		if( $this->offreEmploiDB->num_rows > 0 )
			$return = $this->offreEmploiDB->get_results($sql, ARRAY_A);
		
		return $return;
	}

	/**
	 * Récupères les offres d'emploi présentenr autour d'une ville
	 */
	public function findByOffreCommunes(array $communes = [], array $orderBy = null, $limit = null, $offset = null){

		$baseSql = 'SELECT * FROM '.$this->TableOffreEmploi.' A WHERE A.visibilite = \'visible\' AND (A.commune_id IS NULL';

		$baseSql .= ' OR A.commune_id IN ('.implode(', ',$communes);
		$baseSql .= '))';
		if($orderBy){
			$baseSql .= ' ORDER BY A.'.key($orderBy).' '.$orderBy[key($orderBy)];
		}

		if($limit){
			$baseSql .= ' LIMIT '.$limit;
		}

		if($offset){
			$baseSql .= ' OFFSET '.$offset;
		}

		$sql = $this->offreEmploiDB->prepare($baseSql);
		
		$this->offreEmploiDB->query( $sql );
		
		if( $this->offreEmploiDB->num_rows > 0 )
			$return = $this->offreEmploiDB->get_results($sql, ARRAY_A);
		
		return $return;
	}

	/**
	 * Récupère les offres d'emploi d'un utilisateur
	 */
	public function findMesOffres($user_id){

		$baseSql = 'SELECT * FROM '.$this->TableOffreEmploi.' A WHERE A.user_id = '.$user_id;

		$sql = $this->offreEmploiDB->prepare($baseSql);
		
		$this->offreEmploiDB->query( $sql );
		
		if( $this->offreEmploiDB->num_rows > 0 )
			$return = $this->offreEmploiDB->get_results($sql, ARRAY_A);
		
		return $return;
	}
	
	/**
	 * Récupères toutes les communes
	 */
	public function findAllCommunes(){
		
		$sql = $this -> offreEmploiDB ->prepare('SELECT C.id, C.code_postal, C.nom_commune, C.slug, C.latitude, C.longitude FROM
				'.$this->TableCommune.' C ORDER BY C.nom_commune ASC');

		$this->offreEmploiDB->query( $sql );
		
		if( $this->offreEmploiDB->num_rows > 0 )
			$return = $this->offreEmploiDB->get_results($sql, ARRAY_A);
		
		return $return;
	}

	/**
	 * Récupère une commune
	 */
	public function findOneCommune($commune_id){
		
		$sql = $this->offreEmploiDB->prepare('SELECT C.id, C.code_postal, C.nom_commune, C.slug, C.latitude, C.longitude FROM
				'.$this->TableCommune.' C WHERE C.id = '.$commune_id);

		$this->offreEmploiDB->query( $sql );
		
		if( $this->offreEmploiDB->num_rows > 0 )
			$return = $this->offreEmploiDB->get_results($sql, ARRAY_A);
		
		return $return[0];
	}

	/**
	 * Crée une offre d'emploi utilisateur
	 */
	public function createOneOffre($intitule, $appellation_metier, $type_contrat, $type_contrat_libelle, $nature_contrat, $experience_libelle, $alternance = 'NULL', $nb_postes, $latitude = 'NULL', $longitude ='NULL',  $nom_entreprise = 'NULL', $salaire ='NULL', $duree_travail='NULL', $commune_id = 'NULL', $user_id, $description, $ville_libelle, $mail_entreprise = 'NULL', $numero_entreprise ='NULL'){

		$sql = $this->offreEmploiDB->prepare('INSERT INTO '.$this->TableOffreEmploi." (intitule, date_de_creation, date_actualisation,
											appellation_metier, type_contrat, type_contrat_libelle, nature_contrat,
											experience_libelle, alternance, nb_postes, latitude, longitude, nom_entreprise,
											salaire, duree_travail, commune_id, user_id, description, ville_libelle,
											validation, mail_entreprise, numero_entreprise, visibilite)
											  VALUES ('".$intitule."', NOW(), NOW(),
											'".$appellation_metier."', '".$type_contrat."', '".$type_contrat_libelle."', '".$nature_contrat."', 
											'".$experience_libelle."', ".$alternance.", ".$nb_postes.", ".$latitude.", ".$longitude.", '".$nom_entreprise."', 
											'".$salaire."', '".$duree_travail."', ".$commune_id.", ".$user_id.", '".$description."', '".$ville_libelle."', 
											'en attente', '".$mail_entreprise."', '".$numero_entreprise."', 'non visible')");
		
		$this->offreEmploiDB->query($sql);

	}

	/**
	 * modifie une offre d'emploi utilisateur
	 */
	public function modifierOffre($id_offre, $intitule, $appellation_metier, $type_contrat, $type_contrat_libelle, $nature_contrat, $experience_libelle, $alternance = 'NULL', $nb_postes, $latitude = 'NULL', $longitude ='NULL',  $nom_entreprise = 'NULL', $salaire ='NULL', $duree_travail='NULL', $commune_id = 'NULL', $user_id, $description, $ville_libelle, $mail_entreprise = 'NULL', $numero_entreprise ='NULL'){

		$sql = $this->offreEmploiDB->prepare('UPDATE '.$this->TableOffreEmploi." 
											SET intitule = '".$intitule."', date_actualisation = NOW(),
											appellation_metier = '".$appellation_metier."', type_contrat = '".$type_contrat."',
											type_contrat_libelle = '".$type_contrat_libelle."', nature_contrat = '".$nature_contrat."',
											experience_libelle = '".$experience_libelle."', alternance = ".$alternance.",
											nb_postes = ".$nb_postes.", latitude = ".$latitude.", longitude = ".$latitude.", 
											nom_entreprise = '".$nom_entreprise."', salaire = '".$salaire."', duree_travail = '".$duree_travail."',
											commune_id = ".$commune_id.", description = '".$description."', ville_libelle = '".$ville_libelle."',
											validation = 'en attente', mail_entreprise = '".$mail_entreprise."',
											numero_entreprise = '".$numero_entreprise."', visibilite = 'non visible', archive = 'non'
											WHERE id = ".$id_offre);
		
		$this->offreEmploiDB->query($sql);

	}

	/**
	 * valide une offre d'emploi utilisateur
	 */
	public function accepterOffre($offre_id){

		$sql = $this->offreEmploiDB->prepare('UPDATE '.$this->TableOffreEmploi." 
											SET validation = 'valide', visibilite = 'visible', date_actualisation = NOW(), archive = 'non'
											WHERE id = ".$offre_id);

		$this->offreEmploiDB->query($sql);

		if($this->offreEmploiDB->last_error){
			return 'Erreur sql : ' . $this->offreEmploiDB->last_error;
		}else{
			return 'Sql succès';
		}
	}

	/**
	 * refuse une offre d'emploi utilisateur
	 */
	public function refuserOffre($offre_id){

		$sql = $this->offreEmploiDB->prepare('UPDATE '.$this->TableOffreEmploi." 
											SET validation = 'refus', visibilite = 'non visible', date_actualisation = NOW(), archive = 'non'
											WHERE id = ".$offre_id);

		$this->offreEmploiDB->query($sql);

		if($this->offreEmploiDB->last_error){
			return 'Erreur sql : ' . $this->offreEmploiDB->last_error;
		}else{
			return 'Sql succès';
		}
	}

	/**
	 * supprime une offre d'emploi utilisateur
	 */
	public function supprimerMonOffre($offre_id){

		$sql = $this->offreEmploiDB->prepare('DELETE FROM '.$this->TableOffreEmploi."
											WHERE id = ".$offre_id);
		
		$this->offreEmploiDB->query($sql);

		if($this->offreEmploiDB->last_error){
			return 'Erreur sql : ' . $this->offreEmploiDB->last_error;
		}else{
			return 'Suppression réussie';
		}
	}

	/**
	 * Modifie la visibilité d'une offre d'emploi utilisateur
	 */
	public function toggleVisibiliteOffre($offre_id, $visibilite){

		$sql = $this->offreEmploiDB->prepare('UPDATE '.$this->TableOffreEmploi."
											SET visibilite = '".$visibilite."'
											WHERE id = ".$offre_id);
		
		$this->offreEmploiDB->query($sql);

		if($this->offreEmploiDB->last_error){
			return 'Erreur sql : ' . $this->offreEmploiDB->last_error;
		}else{
			return 'Suppression réussie';
		}
	}

	/**
	 * Archive une offre d'emploi utilisateur refusée
	 */
	public function setOffreArchive($id_offre){

		$sql = $this->offreEmploiDB->prepare('UPDATE '.$this->TableOffreEmploi."
											SET archive = 'oui'
											WHERE id = ".$id_offre);
										
		$this->offreEmploiDB->query($sql);

		if($this->offreEmploiDB->last_error){
			return 'Erreur sql : '.$this->offreEmploiDB->last_error;
		}else{
			return 'archivé';
		}
	}
}