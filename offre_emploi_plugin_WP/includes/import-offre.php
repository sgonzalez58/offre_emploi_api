<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.koikispass.com
 * @since      1.0.0
 *
 * @package    Offre_emploi
 * @subpackage Offre_emploi/import
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Offre_emploi
 * @subpackage Offre_emploi/import
 * @author     dev-iticonseil <dev@iti-conseil.com>
 */

class Offre_emploi_Import {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		require_once plugin_dir_path( __FILE__ ) . '../model/model-offre_emploi.php';
		$this->model = new Offre_Emploi_Model();
	}


	function getToken(){
        $clientId = '6e4baabca2568f73cfefcb59a1b1a6d8';
        $clientSecret = '47f7049dc3e21a3921740a2803f51bd3';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://api.jobijoba.com/v3/fr/login');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(['client_id' => $clientId, 'client_secret' => $clientSecret]));
        $response = json_decode(curl_exec($curl));
        curl_close($curl);
        
        $token = $response->token;
    
        return $token;
    }

    
    function getAnnonce(){
        $token = $this->getToken();
        $this->model->setOffreNonVisible();
        $page = 1;
        $params = [
            'page' => $page,
            'limit' => 200
        ];
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://api.jobijoba.com/v3/fr/ads/search?'.http_build_query($params));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['authorization: Bearer '.$token]);
        $response = json_decode(curl_exec($curl));
        while(!empty($response->data->ads)){
            foreach($response->data->ads as $offre){
                if($this->model->offreExist($offre->id)){
                    $this->model->updateOffre($offre);
                }else{
                    $this->model->addOffre($offre);
                }
            }
            $page++;
            $params = [
                'page' => $page,
                'limit' => 200
            ];
            curl_setopt($curl, CURLOPT_URL, 'https://api.jobijoba.com/v3/fr/ads/search?'.http_build_query($params));
            $response = json_decode(curl_exec($curl));
        }
        $this->model->removeOffresJbjbExpirees();
    }
}
