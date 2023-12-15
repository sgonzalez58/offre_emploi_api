//on change le bouton de retour pour qu'il renvoi vers la gestion personnel des offres
document.getElementById('retour_offre').setAttribute('href', '/wp-admin/admin.php?page=gestion_offre_emploi&id_offre='+my_ajax_obj.id_offre);
//modifie le formulaire pour qu'il renvoie vers la page de modification et non de création
document.getElementById('formEmploiBackend').setAttribute('name', 'formEmploiBackendEdit');
document.getElementById('formOffre').setAttribute('action', '/wp-admin/admin.php?page=gestion_offre_emploi&id_offre='+my_ajax_obj.id_offre+'&edit=1&validation=1')
//demande au serveur de récupérer l'offre à modifier afin de préremplir le formulaire
jQuery.ajax({
    type:'POST',
    url:my_ajax_obj.ajax_url,
    data:{_ajax_nonce:my_ajax_obj.nonce, action:'get_one_offre_admin', id_offre:my_ajax_obj.id_offre},
    success:function(data){
        data = data['data'];
        document.getElementById('formOffre').insertAdjacentHTML('afterbegin', "<input type='hidden' value='"+data['id']+"' name='id_offre'>");
        document.getElementById('intitule').value == '' ? document.getElementById('intitule').value = data['intitule'] : '';
        document.getElementById('appelation_metier').value == '' ? document.getElementById('appelation_metier').value = data['metier'] : '';
        document.getElementById('secteur_activite').value == '' ? document.getElementById('secteur_activite').value = data['secteur_activite'] : '';
        document.getElementById('nom_entreprise').value == '' ? document.getElementById('nom_entreprise').value = data['nomEntreprise'] : '';
        if(!Array.from(document.getElementById('type_contrat').children).some(option => option.getAttribute('selected'))){
            document.getElementById('type_contrat').value = data['type_contrat'];
        }
        if(data['salaire']){
            document.getElementById('montant_salaire').value == '' ? document.getElementById('montant_salaire').value = data['salaire'].split('€').shift() : '';
            if(!Array.from(document.getElementById('periode_salaire').children).some(option => option.getAttribute('selected'))){
                document.getElementById('periode_salaire').value = data['salaire'].split('€').pop().split(' ').pop();
            }
        }
        document.getElementById('description').value == '' ? document.getElementById('description').value = data['description'] : '';
        if(jQuery('#commune').val() == ''){
            jQuery('#commune').val(data['commune_id']);
            jQuery('#commune').trigger('change');
        }
        document.getElementById('email').value == '' ? document.getElementById('email').value = data['email_notification'] : '';
        let date_debut = new Date(data['date_debut']);
        date_debut = date_debut.toLocaleDateString();
        document.getElementById('date_debut').value == '' ? document.getElementById('date_debut').value = date_debut : '';
        let date_fin = new Date(data['date_fin']);
        date_fin = date_fin.toLocaleDateString();
        document.getElementById('date_fin').value == '' ? document.getElementById('date_fin').value = date_fin : '';
        document.getElementById('image').value == '' ? document.getElementById('image').value = data['image'] : '';
        jQuery('.custom-upload-button').first().html('<img decoding="async" src="' + data['image'] + '" width="20%">');
			jQuery('.custom-upload-remove').first().show().next().val(data['image']);
        document.getElementById('logo').value == '' ? document.getElementById('logo').value = data['logo'] : '';
            jQuery('.custom-upload-button').last().html('<img decoding="async" src="' + data['logo'] + '" width="20%">');
                jQuery('.custom-upload-remove').last().show().next().val(data['logo']);

    },
    error:function(data){
        console.log('Erreur lors du chargment de l\'offre : '+data.responseText);
    }
})