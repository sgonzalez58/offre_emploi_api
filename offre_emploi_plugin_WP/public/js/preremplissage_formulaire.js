//on change le bouton de retour pour qu'il renvoi vers la gestion personnel des offres
document.getElementById('retour_offres').setAttribute('href', '/offres-emploi/mesOffres');
//modifie le formulaire pour qu'il renvoie vers la page de modification et non de création
document.getElementById('formOffre').setAttribute('action', '/offres-emploi/mesOffres/modification');
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
        document.getElementById('secteur_activite').value = data['secteur_activite'];
        document.getElementById('nom_entreprise').value = data['nomEntreprise'];
        document.getElementById('type_contrat').value = data['type_contrat'];
        if(data['salaire']){
            document.getElementById('montant_salaire').value = data['salaire'].split('€').shift();
            document.getElementById('periode_salaire').value = data['salaire'].split('€').pop().split(' ').pop();
        }
        document.getElementById('description').value = data['description'];
        jQuery('#commune').val(data['commune_id']);
        jQuery('#commune').trigger('change');
        document.getElementById('ville_libelle').value = data['ville'];
        document.getElementById('formulaire_offre_emploi_latitude').value = data['latitude'];
        document.getElementById('formulaire_offre_emploi_longitude').value = data['longitude'];
    },
    error:function(data){
        console.log('Erreur lors du chargment de l\'offre : '+data.responseText);
    }
})