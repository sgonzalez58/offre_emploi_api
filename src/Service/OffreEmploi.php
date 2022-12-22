<?php
namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class OffreEmploi
{
    private $params;
    private $token_pole_emploi;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
        $curl = curl_init();

        $pole_emploi_id = $this->params->get('pole_emploi_id');
        $pole_emploi_secret = $this->params->get('pole_emploi_secret');

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://entreprise.pole-emploi.fr/connexion/oauth2/access_token?realm=partenaire',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'grant_type=client_credentials&client_id='.$pole_emploi_id.'&client_secret='.$pole_emploi_secret.'&scope=api_offresdemploiv2 o2dsoffre',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response);
        $this->token_pole_emploi = $response->access_token;
    }

    public function getOffreNievre()
    {
        $curl = curl_init();
        $url = 'https://api.emploi-store.fr/partenaire/offresdemploi/v2/offres/search?departement=58';
        $opts = [
            CURLOPT_SSL_VERIFYPEER  => false,
            CURLOPT_HTTPHEADER      => array('Authorization: Bearer ' . $this->token_pole_emploi, 'Content-Type: application/json'),
            CURLOPT_URL             => $url,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_TIMEOUT         => 30,
            CURLOPT_CONNECTTIMEOUT  => 30,
            CURLOPT_VERBOSE         => 0,
            CURLOPT_HEADER          => 0,
            CURLOPT_CUSTOMREQUEST   => "GET"
        ];

        curl_setopt_array($curl, $opts);
        $response = curl_exec($curl);
        $offre = [];
        $idx = 0;
        $responseCurl = json_decode($response);
        foreach($responseCurl->resultats as $offre){
            $offre_intitule[$idx++] = $offre;
        }
        while(property_exists($responseCurl, 'resultats')){
            $opts = [
                CURLOPT_SSL_VERIFYPEER  => false,
                CURLOPT_HTTPHEADER      => array('Authorization: Bearer ' . $this->token_pole_emploi, 'Content-Type: application/json'),
                CURLOPT_URL             => 'https://api.emploi-store.fr/partenaire/offresdemploi/v2/offres/search?departement=58&range='.$idx.'-'.($idx+149),
                CURLOPT_RETURNTRANSFER  => true,
                CURLOPT_TIMEOUT         => 30,
                CURLOPT_CONNECTTIMEOUT  => 30,
                CURLOPT_VERBOSE         => 0,
                CURLOPT_HEADER          => 0,
                CURLOPT_CUSTOMREQUEST   => "GET"
            ];
            curl_setopt_array($curl, $opts);
            $response = curl_exec($curl);
            $responseCurl = json_decode($response);
            if(!property_exists($responseCurl, 'codeHttp')){
                foreach($responseCurl->resultats as $offre){
                    $offre_intitule[$idx++] = $offre;
                }
            }
        }
        curl_close($curl);
        
        return $offre_intitule;
    }
}