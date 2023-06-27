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
	private $TableCandidature;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	function __construct() {
		
		$offreEmploiDB = new wpdb( 'emploikkp', 'qk5ou2cn3tcpj', 'emploikkp_db', 'localhost' );		
		$this->offreEmploiDB 	 = $offreEmploiDB;
		$this->TableOffreEmploi = 'offre_emploi';
		$this->TableCandidature = 'candidature';
		$this->TableCommune = 'commune';
	}
	
	/**
	 * Récupère les "libelle_metier" et les "secteurèactivité"
	 */
	public function getMetier(){

		$sql = $this->offreEmploiDB->prepare('SELECT DISTINCT libelle_metier as libelle, secteur_activite as secteur FROM '.$this->TableOffreEmploi."
			WHERE libelle_metier IS NOT NULL OR secteur_activite IS NOT NULL");

		$this->offreEmploiDB->query( $sql );
	
		if( $this->offreEmploiDB->num_rows > 0 )
			$return = $this->offreEmploiDB->get_results($sql, ARRAY_A);
		
		return $return;
	}
	
	/**
	 * Récupère toutes les offres utilisateurs non archivées
	 */
	public function findAllOffresUser(){

		$sql = $this->offreEmploiDB->prepare('SELECT * FROM '.$this->TableOffreEmploi."
			WHERE user_id IS NOT NULL AND archive = 'non'");

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
			WHERE user_id IS NOT NULL AND validation = \'en attente\'');

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
	 * Récupère jusqu'à 12 offres, nouvelles ou d'un secteur d'activité spécifique
	 */
	public function getMoreOffre($secteur_activite){
		if($secteur_activite == ''){
			$sql = $this->offreEmploiDB->prepare('SELECT * FROM '.$this->TableOffreEmploi.' A
				ORDER BY A.id DESC LIMIT 12');
		}else{
			$sql = $this->offreEmploiDB->prepare('SELECT * FROM '.$this->TableOffreEmploi.' A WHERE A.secteur_activite = "'.$secteur_activite.'"
				ORDER BY A.id DESC LIMIT 12');
		}

		$this->offreEmploiDB->query( $sql );

		if( $this->offreEmploiDB->num_rows > 0 )
			$return = $this->offreEmploiDB->get_results($sql, ARRAY_A);
		
		return $return;
	}

	public function findByMotsClef(array $mots_clef = [], $type_de_contrat = null, array $communes = []){
		$baseSql = 'SELECT * FROM '.$this->TableOffreEmploi." WHERE visibilite = 'visible'";
		foreach($mots_clef as $key=>$mot_clef){
			$baseSql .= " AND ( LOWER(intitule) LIKE %s OR LOWER(libelle_metier) LIKE %s)";
			$prepare_mots_clef[$key*3] = "%".$mot_clef."%";
			$prepare_mots_clef[$key*3 + 1] = "%".$mot_clef."%";
			$prepare_mots_clef[$key*3 + 2] = "%".$mot_clef."%";
		}
		if($type_de_contrat){
			$baseSql .= " AND type_contrat = '".$type_de_contrat."'";
		}
		if(count($communes) > 0){
			$baseSql .= " AND (commune_id IS NULL OR commune_id IN (".implode(', ', $communes)."))";
		}
		$baseSql .= " ORDER BY date_de_publication DESC";
		if(count($mots_clef) > 0){
			$sql = $this->offreEmploiDB->prepare($baseSql, $prepare_mots_clef);
		}else{
			$sql = $this->offreEmploiDB->prepare($baseSql);
		}
		if($this->offreEmploiDB->last_error){
			return 'Erreur sql : ' . $this->offreEmploiDB->last_error;
		}else{
			return $this->offreEmploiDB->get_results($sql, ARRAY_A);
		}
	}

	public function getNbCommunes(array $mots_clef = []){
		$baseSql = 'SELECT c.id as id_commune, c.nom_commune as nom_commune, COUNT(o.id) as NbEvent FROM '.$this->TableOffreEmploi." o, ".$this->TableCommune." c WHERE o.visibilite = 'visible' AND o.commune_id = c.id";

		foreach($mots_clef as $key=>$mot_clef){
			$baseSql .= " AND ( LOWER(o.intitule) LIKE %s OR LOWER(o.libelle_metier) LIKE %s)";
			$prepare_mots_clef[$key*3] = "%".$mot_clef."%";
			$prepare_mots_clef[$key*3 + 1] = "%".$mot_clef."%";
			$prepare_mots_clef[$key*3 + 2] = "%".$mot_clef."%";
		}

		$baseSql .= " GROUP BY o.commune_id";

		if(count($mots_clef) > 0){
			$baseSql = $this->offreEmploiDB->prepare($baseSql, $prepare_mots_clef);
		}else{
			$baseSql = $this->offreEmploiDB->prepare($baseSql);
		}
		
		if($this->offreEmploiDB->last_error){
			return 'Erreur sql : ' . $this->offreEmploiDB->last_error;
		}else{
			return $this->offreEmploiDB->get_results($baseSql, ARRAY_A);
		}
	}

	public function getNbTypesContrat(array $mots_clef = []){
		$baseSql = 'SELECT o.type_contrat as nom, COUNT(*) as NbEvent FROM '.$this->TableOffreEmploi." o WHERE o.visibilite = 'visible' AND o.type_contrat != '' AND o.type_contrat IS NOT NULL";

		foreach($mots_clef as $key=>$mot_clef){
			$baseSql .= " AND ( LOWER(o.intitule) LIKE %s OR LOWER(o.libelle_metier) LIKE %s)";
			$prepare_mots_clef[$key*3] = "%".$mot_clef."%";
			$prepare_mots_clef[$key*3 + 1] = "%".$mot_clef."%";
			$prepare_mots_clef[$key*3 + 2] = "%".$mot_clef."%";
		}

		$baseSql .= " GROUP BY o.type_contrat";

		if(count($mots_clef) > 0){
			$baseSql = $this->offreEmploiDB->prepare($baseSql, $prepare_mots_clef);
		}else{
			$baseSql = $this->offreEmploiDB->prepare($baseSql);
		}
		
		if($this->offreEmploiDB->last_error){
			return 'Erreur sql : ' . $this->offreEmploiDB->last_error;
		}else{
			return $this->offreEmploiDB->get_results($baseSql, ARRAY_A);
		}
	}

	public function getNbCommunes1($type, array $mots_clef = []){
		$baseSql = 'SELECT c.id as id_commune, c.nom_commune as nom_commune, COUNT(o.id) as NbEvent FROM '.$this->TableOffreEmploi." o, ".$this->TableCommune." c WHERE o.visibilite = 'visible' AND o.commune_id = c.id AND o.type_contrat = '".$type."'";
		
		foreach($mots_clef as $key=>$mot_clef){
			$baseSql .= " AND ( LOWER(o.intitule) LIKE %s OR LOWER(o.libelle_metier) LIKE %s)";
			$prepare_mots_clef[$key*3] = "%".$mot_clef."%";
			$prepare_mots_clef[$key*3 + 1] = "%".$mot_clef."%";
			$prepare_mots_clef[$key*3 + 2] = "%".$mot_clef."%";
		}

		$baseSql .= " GROUP BY o.commune_id";

		if(count($mots_clef) > 0){
			$baseSql = $this->offreEmploiDB->prepare($baseSql, $prepare_mots_clef);
		}else{
			$baseSql = $this->offreEmploiDB->prepare($baseSql);
		}
		
		if($this->offreEmploiDB->last_error){
			return 'Erreur sql : ' . $this->offreEmploiDB->last_error;
		}else{
			return $this->offreEmploiDB->get_results($baseSql, ARRAY_A);
		}
	}

	public function getNbTypesContrat1($com, array $mots_clef = []){
		$baseSql = 'SELECT o.type_contrat as nom, COUNT(*) as NbEvent FROM '.$this->TableOffreEmploi." o WHERE o.visibilite = 'visible' AND o.type_contrat != '' AND o.type_contrat IS NOT NULL AND o.commune_id = '".$com."'";

		foreach($mots_clef as $key=>$mot_clef){
			$baseSql .= " AND ( LOWER(o.intitule) LIKE %s OR LOWER(o.libelle_metier) LIKE %s)";
			$prepare_mots_clef[$key*3] = "%".$mot_clef."%";
			$prepare_mots_clef[$key*3 + 1] = "%".$mot_clef."%";
			$prepare_mots_clef[$key*3 + 2] = "%".$mot_clef."%";
		}

		$baseSql .= " GROUP BY o.type_contrat";

		if(count($mots_clef) > 0){
			$baseSql = $this->offreEmploiDB->prepare($baseSql, $prepare_mots_clef);
		}else{
			$baseSql = $this->offreEmploiDB->prepare($baseSql);
		}
		
		if($this->offreEmploiDB->last_error){
			return 'Erreur sql : ' . $this->offreEmploiDB->last_error;
		}else{
			return $this->offreEmploiDB->get_results($baseSql, ARRAY_A);
		}
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
	 * Récupères tous les types de contrat
	 */
	public function getAllTypeContrat(){
		
		$sql = $this -> offreEmploiDB ->prepare('SELECT UNIQUE(type_contrat) FROM '.$this->TableOffreEmploi.'
												WHERE type_contrat IS NOT NULL AND type_contrat != "" AND visibilite = "visible" ');

		$this->offreEmploiDB->query( $sql );
		
		if( $this->offreEmploiDB->num_rows > 0 )
			$return = $this->offreEmploiDB->get_results($sql, ARRAY_A);
		
		return $return;
	}
	
	/**
	 * Récupères toutes les communes
	 */
	public function findAllCommunes(){
		
		$sql = $this -> offreEmploiDB ->prepare('SELECT * FROM
				'.$this->TableCommune.' ORDER BY nom_commune ASC');

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
	 * Récupère une commune
	 */
	public function findCommuneByName($name){
		
		$sql = $this->offreEmploiDB->prepare('SELECT * FROM '.$this->TableCommune." WHERE nom_commune = '".str_replace("'", "''", $name)."'");

		$this->offreEmploiDB->query( $sql );
		
		if( $this->offreEmploiDB->num_rows > 0 ){
			$return = ($this->offreEmploiDB->get_results($sql, ARRAY_A))[0]['id'];
		}else{
			$return = 0;
		}
		return $return;
	}

	/**
	 * Récupère une commune
	 */
	public function findOneCommuneBySlug($name){
		
		$sql = $this->offreEmploiDB->prepare('SELECT * FROM '.$this->TableCommune." WHERE slug = '".$name."'");

		$this->offreEmploiDB->query( $sql );
		
		if( $this->offreEmploiDB->num_rows > 0 ){
			$return = ($this->offreEmploiDB->get_results($sql, ARRAY_A))[0];
		}else{
			$return = NULL;
		}
		return $return;
	}

	public function offreCommuneExist($id){

		$sql = $this->offreEmploiDB->prepare('SELECT * FROM '.$this->TableOffreEmploi." WHERE commune_id = '".$id."'");

		$this->offreEmploiDB->query( $sql );
		
		return $this->offreEmploiDB->num_rows > 0;
	}

	public function offreExist($id){

		$sql = $this->offreEmploiDB->prepare('SELECT * FROM '.$this->TableOffreEmploi." WHERE id_jobijoba = '".$id."'");

		$this->offreEmploiDB->query( $sql );
		
		return $this->offreEmploiDB->num_rows > 0;
	}

	public function setOffreNonVisible(){

		$sql = $this->offreEmploiDB->prepare('UPDATE '.$this->TableOffreEmploi." SET visibilite = 'non visible' WHERE id_jobijoba IS NOT NULL");

		$this->offreEmploiDB->query( $sql );
	}


	/**
	 * Crée une offre d'emploi
	 */
	public function addOffre($offre){

		$coordinates = explode(',',$offre->coordinates);
		if(count($coordinates) == 2){
			$latitude = $coordinates[0];
			$longitude = $coordinates[1];
		}else{
			$latitude = 'NULL';
			$longitude = 'NULL';
		}

		$ville_libelle = $offre->city;

		if(str_contains($ville_libelle, '(')){
			$ville = explode('(', str_replace(')', '', $ville_libelle));
			if(is_numeric($ville[1])){
				$ville_libelle = trim($ville[0]);
			}else{
				$ville[0] = trim($ville[0]);
				$ville_libelle = implode(' ', $ville);
			}
		}

		$commune_id = $this->findCommuneByName($ville_libelle);

		if($commune_id == 0){
			$commune_id = 'NULL';
		}

		$sql = $this->offreEmploiDB->prepare('INSERT INTO '.$this->TableOffreEmploi.' (intitule, libelle_metier, date_de_publication,
											type_contrat, latitude, longitude, nom_entreprise, salaire, commune_id, 
											description, ville_libelle, validation, visibilite, archive, id_jobijoba, secteur_activite, lien_jj)
											  VALUES ("'.str_replace('"', '""', $offre->title).'", "'.$offre->jobtitle.'", "'.$offre->publicationDate.'",
											 "'.array_shift($offre->contractType).'", '.$latitude.', '.$longitude.', "'.$offre->company.'", 
											"'.$offre->salary.'", '.$commune_id.', "'.str_replace('"', '""', $offre->description).'", "'.$ville_libelle.'", 
											"valide", "visible", "non", "'.$offre->id.'", "'.$offre->sector.'", "'.$offre->link.'")');
		
		$this->offreEmploiDB->query($sql);
	}

	/**
	 * Modifie une offre d'emploi
	 */
	public function updateOffre($offre){

		$coordinates = explode(',',$offre->coordinates);
		if(count($coordinates) == 2){
			$latitude = $coordinates[0];
			$longitude = $coordinates[1];
		}else{
			$latitude = 'NULL';
			$longitude = 'NULL';
		}

		$ville_libelle = $offre->city;

		if(str_contains($ville_libelle, '(')){
			$ville = explode('(', str_replace(')', '', $ville_libelle));
			if(is_numeric($ville[1])){
				$ville_libelle = trim($ville[0]);
			}else{
				$ville[0] = trim($ville[0]);
				$ville_libelle = implode(' ', $ville);
			}
		}

		$commune_id = $this->findCommuneByName($ville_libelle);

		if($commune_id == 0){
			$commune_id = 'NULL';
		}

		$sql = $this->offreEmploiDB->prepare('UPDATE '.$this->TableOffreEmploi.'
											SET intitule = "'.str_replace('"', '""', $offre->title).'", libelle_metier = "'.$offre->jobtitle.'", 
											type_contrat = "'.array_shift($offre->contractType).'", latitude = '.$latitude.', longitude = '.$longitude.',
											nom_entreprise = "'.$offre->company.'", salaire = "'.$offre->salary.'", commune_id = '.$commune_id.', 
											description = "'.str_replace('"', '""', $offre->description).'", ville_libelle = "'.$ville_libelle.'", validation = "valide",
											visibilite = "visible", archive = "non", secteur_activite = "'.$offre->sector.'", lien_jj = "'.$offre->link.'"
											WHERE id_jobijoba = "'.$offre->id.'"');
		
		$this->offreEmploiDB->query($sql);
	}

	public function removeOffresJbjbExpirees(){

		$sql = $this->offreEmploiDB->prepare('DELETE FROM '.$this->TableOffreEmploi." WHERE id_jobijoba IS NOT NULL AND visibilite = 'non visible'");

		$this->offreEmploiDB->query( $sql );
	}


	/**
	 * Crée une offre d'emploi utilisateur
	 */
	public function createOneOffre($intitule, $libelle_metier, $secteur_activite, $nom_entreprise, $type_contrat, $latitude = 'NULL', $longitude ='NULL', $salaire ='NULL', $commune_id = 'NULL', $user_id, $description, $ville_libelle){

		$sql = $this->offreEmploiDB->prepare('INSERT INTO '.$this->TableOffreEmploi." (intitule, date_de_publication,
											libelle_metier, secteur_activite, nom_entreprise, type_contrat, latitude, longitude, salaire, commune_id,
											user_id, description, ville_libelle, validation, visibilite, archive)
											VALUES ('".$intitule."', NOW(),
											'".$libelle_metier."', '".$secteur_activite."', '".$nom_entreprise."', '".$type_contrat."', ".$latitude.", ".$longitude.", 
											'".$salaire."',".$commune_id.", ".$user_id.", '".$description."', '".$ville_libelle."', 
											'en attente', 'non visible', 'non')");
		
		$this->offreEmploiDB->query($sql);

	}

	/**
	 * modifie une offre d'emploi utilisateur
	 */
	public function modifierOffre($id_offre, $intitule, $libelle_metier, $secteur_activite, $nom_entreprise, $type_contrat, $latitude = 'NULL', $longitude ='NULL', $salaire ='NULL', $commune_id = 'NULL', $user_id, $description, $ville_libelle){

		$sql = $this->offreEmploiDB->prepare('UPDATE '.$this->TableOffreEmploi." 
											SET intitule = '".$intitule."', libelle_metier = '".$libelle_metier."', type_contrat = '".$type_contrat."',
											latitude = ".$latitude.", longitude = ".$longitude.", secteur_activite = '".$secteur_activite."',
											nom_entreprise = '".$nom_entreprise."', salaire = '".$salaire."',
											commune_id = ".$commune_id.", description = '".$description."', ville_libelle = '".$ville_libelle."',
											validation = 'en attente', visibilite = 'non visible', archive = 'non'
											WHERE id = ".$id_offre);
		
		$this->offreEmploiDB->query($sql);

	}

	/**
	 * valide une offre d'emploi utilisateur
	 */
	public function accepterOffre($offre_id){

		$sql = $this->offreEmploiDB->prepare('UPDATE '.$this->TableOffreEmploi." 
											SET validation = 'valide', visibilite = 'visible', archive = 'non'
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
											SET validation = 'refus', visibilite = 'non visible', archive = 'non'
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

	public function createCandidature($id_offre, $mail, $id_user=NULL){
		$sql = $this->offreEmploiDB->prepare('INSERT INTO '.$this->TableCandidature." (id_offre_id, id_user, mail, date_envoi)
											values (".$id_offre.", ".$id_user.", '".$mail."', NOW())");

		$this->offreEmploiDB->query($sql);

		if($this->offreEmploiDB->last_error){
			return 'Erreur sql : '.$this->offreEmploiDB->last_error;
		}else{
			return 'Candidature créée.';
		}
	}

	public function findCandidatures($id_offre, $mail = NULL){
		if($mail){
			$sql = $this->offreEmploiDB->prepare('SELECT * FROM '.$this->TableCandidature."
												WHERE id_offre_id = ".$id_offre." AND mail = '".$mail."'");
		}else{
			$sql = $this->offreEmploiDB->prepare('SELECT * FROM '.$this->TableCandidature."
												WHERE id_offre_id = ".$id_offre);
		}
		$this->offreEmploiDB->query($sql);

		if($this->offreEmploiDB->last_error){
			return 'Erreur sql : '.$this->offreEmploiDB->last_error;
		}else{
			if($this->offreEmploiDB->num_rows > 0){
				$return = $this->offreEmploiDB->get_results($sql, ARRAY_A);
			}
			return $return;
		}
	}

	public function findMesCandidatures($id_user){
		$sql = $this->offreEmploiDB->prepare('SELECT c.*, o.* FROM '.$this->TableCandidature." c, ".$this->TableOffreEmploi." o
												WHERE c.id_user = ".$id_user." AND c.id_offre_id = o.id");
		$this->offreEmploiDB->query($sql);

		if($this->offreEmploiDB->last_error){
			return 'Erreur sql : '.$this->offreEmploiDB->last_error;
		}else{
			if($this->offreEmploiDB->num_rows > 0){
				$return = $this->offreEmploiDB->get_results($sql, ARRAY_A);
			}
			return $return;
		}
	}
}