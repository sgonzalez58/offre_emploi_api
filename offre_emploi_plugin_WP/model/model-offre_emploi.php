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
	
	/*public function findAllOffres(){

		$sql = $this->offreEmploiDB->prepare('SELECT * FROM '.$this->TableOffreEmploi);

		$this->offreEmploiDB->query( $sql );
		
		if( $this->offreEmploiDB->num_rows > 0 )
			$return = $this->offreEmploiDB->get_results($sql, ARRAY_A);
		
		return $return;
	}

	*/public function findOneOffre($idOffre){
		$sql = $this->offreEmploiDB->prepare('SELECT * FROM '.$this->TableOffreEmploi.' A
				WHERE A.id = '.$idOffre);
		
		$this->offreEmploiDB->query( $sql );
		
		if( $this->offreEmploiDB->num_rows > 0 )
			$return = $this->offreEmploiDB->get_results($sql, ARRAY_A);
		
		return $return[0];
	}

	public function findByOffreValidation($validation = 'valide', array $orderBy = null, $limit = null, $offset = null){

		$baseSql = 'SELECT * FROM '.$this->TableOffreEmploi.' A WHERE A.validation = \''.$validation.'\'';

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

	public function findByOffreCommunes(array $communes = [], array $orderBy = null, $limit = null, $offset = null){

		$baseSql = 'SELECT * FROM '.$this->TableOffreEmploi.' A WHERE A.validation = \'valide\' AND (A.commune_id IS NULL';

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

		//var_dump($baseSql);
		$sql = $this->offreEmploiDB->prepare($baseSql);
		
		$this->offreEmploiDB->query( $sql );
		
		if( $this->offreEmploiDB->num_rows > 0 )
			$return = $this->offreEmploiDB->get_results($sql, ARRAY_A);
		
		return $return;
	}
	/*

	public function findByOffreUser($user_id = null, array $orderBy = null, $limit = null, $offset = null){

		$baseSql = 'SELECT * FROM '.$this->TableOffreEmploi.' A WHERE A.id_pole_emploi IS NULL';

		if($user_id){
			$baseSql .= ' AND A.user = '.$user_id;
		}

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
	
	*/public function findAllCommunes(){
		
		$sql = $this -> offreEmploiDB ->prepare('SELECT C.id, C.code_postal, C.nom_commune, C.slug, C.latitude, C.longitude FROM
				'.$this->TableCommune.' C ORDER BY C.nom_commune ASC');

		$this->offreEmploiDB->query( $sql );
		
		if( $this->offreEmploiDB->num_rows > 0 )
			$return = $this->offreEmploiDB->get_results($sql, ARRAY_A);
		
		return $return;
	}

	public function findOneCommune($commune_id){
		
		$sql = $this->offreEmploiDB->prepare('SELECT C.id, C.code_postal, C.nom_commune, C.slug, C.latitude, C.longitude FROM
				'.$this->TableCommune.' C WHERE C.id = '.$commune_id);

		$this->offreEmploiDB->query( $sql );
		
		if( $this->offreEmploiDB->num_rows > 0 )
			$return = $this->offreEmploiDB->get_results($sql, ARRAY_A);
		
		return $return[0];
	}
}