<?php

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
    require_once plugin_dir_path( __FILE__ ) . '../model/model-offre_emploi.php';
    $model = new Offre_Emploi_Model;
    $token = getToken();
    $model->setOffreNonVisible();
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
            if($model->offreExist($offre->id)){
                $model->updateOffre($offre);
            }else{
                $model->addOffre($offre);
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
    $model->removeOffresJbjbExpirees();
}