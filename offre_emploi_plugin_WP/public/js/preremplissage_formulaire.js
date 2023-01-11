//on change le bouton de retour pour qu'il renvoi vers la gestion personnel des offres
document.getElementById('retour_offres').setAttribute('href', '/offreEmploi/mesOffres');
//modifie le formulaire pour qu'il renvoie vers la page de modification et non de création
document.getElementById('formOffre').setAttribute('action', '/offreEmploi/mesOffres/modification');
//demande au serveur de récupérer l'offre à modifier afin de préremplir le formulaire
jQuery.ajax({
    type:'POST',
    url:mon_offre_ajax.ajax_url,
    data:{_ajax_nonce:mon_offre_ajax.nonce, action:'get_one_offre', id_offre:mon_offre_ajax.id_offre},
    success:function(data){
        data = data['data'];
        document.getElementById('formOffre').insertAdjacentHTML('afterbegin', "<input type='hidden' value='"+data['id']+"' name='id_offre'>");
        document.getElementById('intitule').value = data['intitule'];
        document.getElementById('appelation_metier').value = data['metier'];
        document.getElementById('nom_entreprise').value = data['nomEntreprise'];
        document.getElementById('mail_entreprise').value = data['mailEntreprise'];
        document.getElementById('numero_entreprise').value = data['telephone_contact'];
        document.getElementById('type_contrat').value = data['type_contrat'];
        document.getElementById('nature_contrat').value = data['nature_contrat'];
        document.getElementById('numero_entreprise').value = data['telephone_contact'];
        if(data['alternance'] != 0){
            document.getElementById('alternance').checked = true;
        }
        if(data['temps_contrat'] == 'Durée indeterminée'){
            document.getElementById('indetermine').checked = true;
        }else{
            if(data['temps_contrat'].split(' - ')[1].split(' ').pop() == 'Mois'){
                document.getElementById('mois').value = data['temps_contrat'].split(' - ')[1].split(' ').shift();
            }else{
                document.getElementById('jours').value = data['temps_contrat'].split(' - ')[1].split(' ').shift();
            }
        }
        if(data['salaire']){
            document.getElementById('montant_salaire').value = data['salaire'].split('€').shift();
            document.getElementById('periode_salaire').value = data['salaire'].split('€').pop().split(' ').pop();
        }
        document.getElementById('duree_travail').value = data["duree"];
        document.getElementById('experience_libelle').value = data['experience'];
        document.getElementById('nb_postes').value = data['nb_poste'];
        document.getElementById('description').value = data['description'];
        document.getElementById('commune').value = data['commune_id'];
        document.getElementById('ville_libelle').value = data['ville'];
        document.getElementById('formulaire_offre_emploi_latitude').value = data['latitude'];
        document.getElementById('formulaire_offre_emploi_longitude').value = data['longitude'];
    },
    error:function(data){
        console.log('Erreure lors du chargment de l\'offre : '+data.responseText);
    }
})