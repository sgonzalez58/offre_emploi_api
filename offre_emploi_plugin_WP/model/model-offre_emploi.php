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
	private $TableHistorique;
	private $TableCommune;
	private $TableCandidature;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	function __construct() {
		
		$offreEmploiDB = new wpdb( 'emploikkp2023', 'dq05qvhvu5ogv', 'emploikkp2023_db', 'localhost' );		
		$this->offreEmploiDB 	 = $offreEmploiDB;
		$this->TableOffreEmploi = 'offre_emploi';
		$this->TableHistorique = 'offre_emploi_historique';
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
	public function getMoreOffre($secteur_activite, $id){
		$sql = $this->offreEmploiDB->prepare('SELECT * FROM '.$this->TableOffreEmploi.' A');
		$complement_sql = '';
		if($id != ''){
			$complement_sql = ' WHERE id <> '.$id;
		}
		if($secteur_activite != ''){
			$complement_sql .= $complement_sql == '' ? ' WHERE A.secteur_activite = "'.$secteur_activite.'"' : ' AND A.secteur_activite = "'.$secteur_activite.'"';
		}

		$sql .= ($complement_sql == '' ? ' WHERE A.visibilite = "visible" AND A.validation = "valide" AND archive = "non"' : $complement_sql . ' AND A.visibilite = "visible" AND A.validation = "valide"  AND archive = "non"') . ' ORDER BY A.id DESC LIMIT 12';

		$this->offreEmploiDB->query( $sql );

		if( $this->offreEmploiDB->num_rows > 0 )
			$return = $this->offreEmploiDB->get_results($sql, ARRAY_A);
		
		return $return;
	}

	public function findByMotsClef($mots_clef = '', $type_de_contrat = null, array $communes = [], $page = 1, $limit = ''){
		$baseSql = 'SELECT * FROM '.$this->TableOffreEmploi." WHERE visibilite = 'visible' AND archive = 'non'";
		if($mots_clef != ""){
			$baseSql .= " AND LOWER(libelle_metier) LIKE %s";
			$prepare_mots_clef[0] = "%".urldecode($mots_clef)."%";
		}
		if($type_de_contrat){
			$baseSql .= " AND type_contrat = '".$type_de_contrat."'";
		}
		if(count($communes) > 0){
			$baseSql .= " AND (commune_id IN (".implode(', ', $communes)."))";
		}else{
			$baseSql .= " AND commune_id is not null ";
		}
		$baseSql .= " ORDER BY user_id IS NULL, date_de_publication DESC";

		if($limit){
			$baseSql .= " LIMIT ".$limit;
			if($page > 1){
				$baseSql .= " OFFSET ".($page - 1) * $limit;
			}
		}

		if($mots_clef != ""){
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

	public function getNbCommunes($mots_clef = ''){
		$baseSql = 'SELECT c.id as id_commune, c.nom_commune as nom_commune, COUNT(o.id) as NbEvent FROM '.$this->TableOffreEmploi." o, ".$this->TableCommune." c WHERE o.visibilite = 'visible' AND o.commune_id = c.id";

		if($mots_clef != ""){
			$baseSql .= " AND LOWER(o.libelle_metier) LIKE %s";
			$prepare_mots_clef[0] = "%".urldecode($mots_clef)."%";
		}

		$baseSql .= " GROUP BY o.commune_id";

		if($mots_clef != ""){
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

	public function getNbTypesContrat($mots_clef = ''){
		$baseSql = 'SELECT o.type_contrat as nom, COUNT(*) as NbEvent FROM '.$this->TableOffreEmploi." o WHERE o.visibilite = 'visible' AND o.type_contrat != '' AND o.type_contrat IS NOT NULL";

		if($mots_clef != ""){
			$baseSql .= " AND LOWER(o.libelle_metier) LIKE %s";
			$prepare_mots_clef[0] = "%".urldecode($mots_clef)."%";
		}

		$baseSql .= " GROUP BY o.type_contrat";

		if($mots_clef != ""){
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

	public function getNbCommunes1($type, $mots_clef = ''){
		$baseSql = 'SELECT c.id as id_commune, c.nom_commune as nom_commune, COUNT(o.id) as NbEvent FROM '.$this->TableOffreEmploi." o, ".$this->TableCommune." c WHERE o.visibilite = 'visible' AND o.commune_id = c.id AND o.type_contrat = '".$type."'";
		
		if($mots_clef != ""){
			$baseSql .= " AND LOWER(o.libelle_metier) LIKE %s";
			$prepare_mots_clef[0] = "%".urldecode($mots_clef)."%";
		}

		$baseSql .= " GROUP BY o.commune_id";

		if($mots_clef != ""){
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

	public function getNbTypesContrat1(array $com, $mots_clef = ''){
		$baseSql = 'SELECT o.type_contrat as nom, COUNT(*) as NbEvent FROM '.$this->TableOffreEmploi." o WHERE o.visibilite = 'visible' AND o.type_contrat != '' AND o.type_contrat IS NOT NULL";

		$baseSql .= " AND o.commune_id in (";

		foreach($com as $key=>$commune_id){
			if($key == 0){
				$baseSql .= $commune_id;
			}else{
				$baseSql .= ','.$commune_id;
			}
		}
		$baseSql .= ")";

		if($mots_clef != ""){
			$baseSql .= " AND LOWER(o.libelle_metier) LIKE %s";
			$prepare_mots_clef[0] = "%".urldecode($mots_clef)."%";
		}

		$baseSql .= " GROUP BY o.type_contrat";

		if($mots_clef != ""){
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

		$baseSql = 'SELECT * FROM '.$this->TableOffreEmploi.' A WHERE archive = "non" AND A.user_id = '.$user_id;

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

	public function getOffreAVerifier(){

		$date_verification = new \Datetime('15 days ago');

		$sql = $this->offreEmploiDB->prepare('SELECT * from '.$this->TableOffreEmploi."
											  WHERE date_de_publication <= '" . $date_verification->format('Y-m-d') . "'
											  AND date_debut <= NOW() AND date_fin > NOW()
											  AND validation = 'valide' AND user_id IS NOT NULL AND clef IS NULL;");

		$this->offreEmploiDB->query($sql);

		if( $this->offreEmploiDB->num_rows > 0 ){

			$return = $this->offreEmploiDB->get_results($sql, ARRAY_A);

		}else{

			$return = NULL;
			
		}

		return $return;
	}

	public function genererClefMail($id){

		$key = substr(str_shuffle(md5(microtime())),0,16);

		$sql = $this->offreEmploiDB->prepare('UPDATE ' . $this->TableOffreEmploi . "
											  SET clef = '" . $key . "' WHERE id = " . $id);

		$result = $this->offreEmploiDB->query($sql);

		if( FALSE !== $result){

			return $key;

		}

		return 'error';
	}

	public function rafraichirOffre($id){

		$sql = $this->offreEmploiDB->prepare('UPDATE ' . $this->TableOffreEmploi . "
											  SET date_de_publication = NOW(), visibilite = 'visible', clef = NULL WHERE id = " . $id);

		$result = $this->offreEmploiDB->query($sql);

		if( FALSE !== $result){

			return 'sql réussi';

		}

		return 'error';
	}

	public function rafraichirOffre2($id){

		$date = new Datetime('+15 days');

		$sql = $this->offreEmploiDB->prepare('UPDATE ' . $this->TableOffreEmploi . "
											  SET date_de_publication = NOW(), clef = NULL,
											  date_fin = '" . $date->format('Y-m-d') . "' WHERE id = " . $id);

		$result = $this->offreEmploiDB->query($sql);

		if( FALSE !== $result){

			return 'sql réussi';

		}

		return 'error';
	}

	public function verifierClefOffre($id, $clef){

		$sql = $this->offreEmploiDB->prepare('SELECT * FROM '. $this->TableOffreEmploi . "
											  WHERE id = " . $id . " AND clef = '" . $clef ."'
											  AND archive = 'non' AND validation = 'valide'");

		$result = $this->offreEmploiDB->query($sql);

		if( $this->offreEmploiDB->num_rows > 0 ){

			$return = $this->offreEmploiDB->get_results($sql, ARRAY_A)[0];

		}else{

			$return = NULL;
			
		}

		return $return;

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
	 * Crée une offre d'emploi utilisateur
	 */
	public function createOneOffreInterne($intitule, $libelle_metier, $secteur_activite, $nom_entreprise, $type_contrat, $latitude = 'NULL', $longitude ='NULL', $salaire ='NULL', $commune_id = 'NULL', $user_id, $description, $ville_libelle, $email_notification, $date_debut, $date_fin, $image, $logo){

		$sql = $this->offreEmploiDB->prepare('INSERT INTO '.$this->TableOffreEmploi." (intitule, date_de_publication,
											libelle_metier, secteur_activite, nom_entreprise, type_contrat, latitude, longitude, salaire, commune_id,
											user_id, description, ville_libelle, validation, visibilite, archive, email_notification, date_debut, date_fin, image, logo)
											VALUES ('".$intitule."', NOW(),
											'".$libelle_metier."', '".$secteur_activite."', '".$nom_entreprise."', '".$type_contrat."', ".$latitude.", ".$longitude.", 
											'".$salaire."',".$commune_id.", ".$user_id.", '".$description."', '".$ville_libelle."', 
											'en attente', 'non visible', 'non', '".$email_notification."', '".$date_debut."', '".$date_fin."', '".$image."', '".$logo."')");
		
		$this->offreEmploiDB->query($sql);

	}
	
	/**
	 * modifie une offre d'emploi utilisateur
	 */
	public function modifierOffreInterne($id_offre, $intitule, $libelle_metier, $secteur_activite, $nom_entreprise, $type_contrat, $latitude = 'NULL', $longitude ='NULL', $salaire ='NULL', $commune_id = 'NULL', $description, $ville_libelle, $email_notification, $date_debut, $date_fin, $image, $logo){

		$date_debut_modif = Datetime::createFromFormat('m/d/Y', $date_debut);
		$date_fin_modif = Datetime::createFromFormat('m/d/Y', $date_fin);

		$sql = $this->offreEmploiDB->prepare('UPDATE '.$this->TableOffreEmploi." 
											SET intitule = '".$intitule."', libelle_metier = '".$libelle_metier."', type_contrat = '".$type_contrat."',
											latitude = ".$latitude.", longitude = ".$longitude.", secteur_activite = '".$secteur_activite."',
											nom_entreprise = '".$nom_entreprise."', salaire = '".$salaire."',
											commune_id = ".$commune_id.", description = '".$description."', ville_libelle = '".$ville_libelle."',
											validation = 'en attente', visibilite = 'non visible', archive = 'non', email_notification = '".$email_notification."',
											date_debut = '".$date_debut_modif->format('Y-m-d')."', date_fin = '".$date_fin_modif->format('Y-m-d')."', image = '".$image."', logo = '".$logo."'
											WHERE id = ".$id_offre);
		
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
	 * archive une offre d'emploi utilisateur
	 */
	public function archiverMonOffre($offre_id){

		$sql = $this->offreEmploiDB->prepare('UPDATE '.$this->TableOffreEmploi."
											SET archive = 'oui', visibilite = 'non visible', clef = NULL WHERE id = ".$offre_id);
		
		$this->offreEmploiDB->query($sql);

		if($this->offreEmploiDB->last_error){
			return 'Erreur sql : ' . $this->offreEmploiDB->last_error;
		}else{
			return 'Suppression réussie';
		}
	}

	public function getOffredepassee(){
		$sql = $this->offreEmploiDB->prepare('SELECT * FROM '.$this->TableOffreEmploi."
											WHERE date_fin < NOW() AND archive = 'non'");
		
		$this->offreEmploiDB->query($sql);

		if( $this->offreEmploiDB->num_rows > 0 ){
			$return = $this->offreEmploiDB->get_results($sql, ARRAY_A);
		}else{
			$return = NULL;
		}
		
		return $return;
	}

	/**
	 * archive une offre d'emploi utilisateur
	 */
	public function archiverOffreAuto($offre_id){

		$sql = $this->offreEmploiDB->prepare('UPDATE '.$this->TableOffreEmploi."
											SET archive = 'oui', visibilite = 'non visible'
											WHERE id = ".$offre_id);
		
		$this->offreEmploiDB->query($sql);

		if($this->offreEmploiDB->last_error){
			return 'Erreur sql : ' . $this->offreEmploiDB->last_error;
		}else{
			return 'Suppression réussie';
		}
	}

	public function getOffresStats(){

		$date_hier = new \DateTime('yesterday');

		$sql = $this->offreEmploiDB->prepare('SELECT * FROM '.$this->TableOffreEmploi."
											  WHERE user_id IS NOT NULL
											  AND date_fin >= '" . $date_hier->format('Y-m-d') . "'");

		$this->offreEmploiDB->query($sql);

		if( $this->offreEmploiDB->num_rows > 0 ){
			$return = $this->offreEmploiDB->get_results($sql, ARRAY_A);
		}else{
			$return = NULL;
		}
		
		return $return;

	}

	public function getHistoriques($id){
		
		$sql = $this->offreEmploiDB->prepare('SELECT * FROM '.$this->TableHistorique."
											  WHERE offre_emploi_id = ".$id);

		$this->offreEmploiDB->query($sql);

		if( $this->offreEmploiDB->num_rows > 0 ){
			$return = $this->offreEmploiDB->get_results($sql, ARRAY_A);
		}else{
			$return = NULL;
		}
		
		return $return;

	}
	public function getHistorique($id, $mois){
		
		$sql = $this->offreEmploiDB->prepare('SELECT * FROM '.$this->TableHistorique."
											  WHERE offre_emploi_id = ".$id." AND mois = '".$mois."'");

		$this->offreEmploiDB->query($sql);

		if( $this->offreEmploiDB->num_rows > 0 ){
			$return = $this->offreEmploiDB->get_results($sql, ARRAY_A)[0];
		}else{
			$return = NULL;
		}
		
		return $return;

	}

	public function ajouterHistorique($id, $mois, $clics, $vues, $vues_liste, $postuler){

		$sql = $this->offreEmploiDB->prepare('INSERT INTO '.$this->TableHistorique."
											  (offre_emploi_id, mois, clics, vues, vues_liste, postuler)
											  VALUES (".$id.", '".$mois."', ".$clics.", ".$vues.",
											  ".$vues_liste.", ".$postuler.")");

		$this->offreEmploiDB->query($sql);
	}

	public function modifierHistorique($id, $clics, $vues, $vues_liste, $postuler){

		$sql = $this->offreEmploiDB->prepare('UPDATE '.$this->TableHistorique."
											  SET clics = ".$clics.", vues = ".$vues.",
											  vues_liste = ".$vues_liste.", postuler = ".$postuler."
											  WHERE id = ".$id);

		$this->offreEmploiDB->query($sql);
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