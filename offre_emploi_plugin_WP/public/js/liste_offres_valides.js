//transforme la liste des communes en select2
jQuery('#liste_ville').select2({
    placeholder: 'Ville',
    allowClear : true,
    width:'resolve',
});

if(my_ajax_obj.ville){
    jQuery('#liste_ville').val(my_ajax_obj.ville.toString());
}

recherche_mot_clef();

//lance la récupère des offres d'emploi lorsqu'on choisi une commune
jQuery('#liste_ville').on('select2:select', recherche_mot_clef)

//récupère la totalité des offres d'emploi quand on supprime la recherche par commune
jQuery('#liste_ville').on('select2:clear', recherche_mot_clef)

jQuery('#liste_ville').on('focus', ()=>{
    jQuery('#liste_ville').select2('open')
})

//transforme la liste des type de contrat en select2
jQuery('#liste_type_contrat').select2({
    placeholder: 'Type de contrat',
    allowClear : true,
    width:'resolve',
});

//récupère les offres d'emploi du type de contrat choisi
jQuery('#liste_type_contrat').on('select2:select', recherche_mot_clef)

jQuery('#liste_type_contrat').on('select2:clear', recherche_mot_clef)

jQuery('#liste_type_contrat').on('focus', ()=>{
    jQuery('#liste_type_contrat').select2('open')
})

document.getElementById('recherche').addEventListener('click', recherche_mot_clef);

function recherche_mot_clef(){
    if(window.innerWidth > 1080){
        mots_clef = document.getElementById('recherche_input').value;
        url = my_ajax_obj.ajax_url+'?_ajax_nonce='+my_ajax_obj.nonce+"&action=recherche_mot_clef&mots_clef="+mots_clef+"&ville="+document.getElementById('liste_ville').value+"&distance="+document.getElementById('liste_distance').value+"&type_de_contrat="+document.getElementById('liste_type_contrat').value;
        jQuery('#pagination_container').pagination({
            dataSource : url,
            locator: 'data.offres',
            pageSize:30,
            showGoInput: true,
            showGoButton: true,
            showNavigator: true,
            formatNavigator: '<%= rangeStart %>-<%= rangeEnd %> sur <%= totalNumber %> offres disponibles',
            totalNumberLocator: function(response){
                return response['data']['info']['nbOffres'];
            },
            ajax:{
                beforeSend: function(){
                    jQuery('#liste_offres').html('Chargement des offres en cours...');
                }
            },
            callback:function(data){
                let template_html = "";
                let offre_html = '';
                data.forEach(offre => {
                    offre_html = "<div class='offre'><div class='corps_offre'>";
                    offre_html +=       "<h2>"+offre['intitule']+"</h2>";
                    offre_html +=       "<div class='details'>"
                    offre_html +=           "<a href='"+offre['lienMap']+"' target='_blank' class='ville'>";
                    offre_html +=              "<i class='fa-solid fa-location-pin'></i><h4>"+offre['nomVille']+"</h4></a>";
                    offre_html +=           "<div class='contrat'><i class='fa-solid fa-tag'></i><h4>"+offre['type_contrat']+"</h4></div></div>";
                    if(offre['nomEntreprise'] != 'Aucun'){
                        offre_html +=       "<h3 class='nom_entreprise'>Entreprise : "+offre['nomEntreprise']+"</h3>";
                    }
                    offre_html +=       "<p id='description'>"+offre['description']+"</p></div>";
                    offre_html +=   "<a class='lien_fiche' href='/offres-emploi/"+offre['id']+"'><button class='bouton_lien_fiche'>Voir l'offre</button></a>";
                    offre_html +=   "<a class='lien_fiche_big' href='/offres-emploi/"+offre['id']+"'></a>";
                    offre_html += "</div>";
                    template_html += offre_html;
                });
                jQuery('#liste_offres').html(template_html);
            }
        })
    }else if(window.innerWidth > 780){
        mots_clef = document.getElementById('recherche_input').value;
        url = my_ajax_obj.ajax_url+'?_ajax_nonce='+my_ajax_obj.nonce+"&action=recherche_mot_clef&mots_clef="+mots_clef+"&ville="+document.getElementById('liste_ville').value+"&distance="+document.getElementById('liste_distance').value+"&type_de_contrat="+document.getElementById('liste_type_contrat').value;
        jQuery('#pagination_container').pagination({
            dataSource : url,
            locator: 'data.offres',
            pageSize:20,
            showGoInput: false,
            showGoButton: false,
            showNavigator: true,
            formatNavigator: '<%= rangeStart %>-<%= rangeEnd %> sur <%= totalNumber %> offres disponibles',
            totalNumberLocator: function(response){
                return response['data']['info']['nbOffres'];
            },
            ajax:{
                beforeSend: function(){
                    jQuery('#liste_offres').html('Chargement des offres en cours...');
                }
            },
            callback:function(data){
                let template_html = "";
                let offre_html = '';
                data.forEach(offre => {
                    offre_html = "<div class='offre'><div class='corps_offre'>";
                    offre_html +=       "<h2>"+offre['intitule']+"</h2>";
                    offre_html +=       "<div class='details'>"
                    offre_html +=           "<a href='"+offre['lienMap']+"' target='_blank' class='ville'>";
                    offre_html +=              "<i class='fa-solid fa-location-pin'></i><h4>"+offre['nomVille']+"</h4></a>";
                    offre_html +=           "<div class='contrat'><i class='fa-solid fa-tag'></i><h4>"+offre['type_contrat']+"</h4></div></div>";
                    if(offre['nomEntreprise'] != 'Aucun'){
                        offre_html +=       "<h3 class='nom_entreprise'>Entreprise : "+offre['nomEntreprise']+"</h3>";
                    }
                    offre_html +=       "<p id='description'>"+offre['description']+"</p></div>";
                    offre_html +=   "<a class='lien_fiche' href='/offres-emploi/"+offre['id']+"'><button class='bouton_lien_fiche'>Voir l'offre</button></h2></a>";
                    offre_html +=   "<a class='lien_fiche_big' href='/offres-emploi/"+offre['id']+"'></a>";
                    offre_html += "</div>";
                    template_html += offre_html;
                });
                jQuery('#liste_offres').html(template_html);
            }
        })
    }else{
        mots_clef = document.getElementById('recherche_input').value;
        url = my_ajax_obj.ajax_url+'?_ajax_nonce='+my_ajax_obj.nonce+"&action=recherche_mot_clef&mots_clef="+mots_clef+"&ville="+document.getElementById('liste_ville').value+"&distance="+document.getElementById('liste_distance').value+"&type_de_contrat="+document.getElementById('liste_type_contrat').value;
        jQuery('#pagination_container').pagination({
            dataSource : url,
            locator: 'data.offres',
            pageSize:10,
            showGoInput: false,
            showGoButton: false,
            showNavigator: true,
            showPageNumbers: false,
            formatNavigator: '<%= rangeStart %>-<%= rangeEnd %> sur <%= totalNumber %> offres disponibles',
            totalNumberLocator: function(response){
                return response['data']['info']['nbOffres'];
            },
            ajax:{
                beforeSend: function(){
                    jQuery('#liste_offres').html('Chargement des offres en cours...');
                }
            },
            callback:function(data){
                let template_html = "";
                let offre_html = '';
                data.forEach(offre => {
                    offre_html = "<div class='offre'><div class='corps_offre'>";
                    offre_html +=       "<h2>"+offre['intitule']+"</h2>";
                    offre_html +=       "<div class='details'>"
                    offre_html +=           "<a href='"+offre['lienMap']+"' target='_blank' class='ville'>";
                    offre_html +=              "<i class='fa-solid fa-location-pin'></i><h4>"+offre['nomVille']+"</h4></a>";
                    offre_html +=           "<div class='contrat'><i class='fa-solid fa-tag'></i><h4>"+offre['type_contrat']+"</h4></div></div>";
                    if(offre['nomEntreprise'] != 'Aucun'){
                        offre_html +=       "<h3 class='nom_entreprise'>Entreprise : "+offre['nomEntreprise']+"</h3>";
                    }
                    offre_html +=       "<p id='description'>"+offre['description']+"</p></div>";
                    offre_html +=   "<a class='lien_fiche' href='/offres-emploi/"+offre['id']+"'><button class='bouton_lien_fiche'>Voir l'offre</button></h2></a>";
                    offre_html +=   "<a class='lien_fiche_big' href='/offres-emploi/"+offre['id']+"'></a>";
                    offre_html += "</div>";
                    template_html += offre_html;
                });
                jQuery('#liste_offres').html(template_html);
            }
        })
    }
}

//window.addEventListener('resize', recherche_mot_clef);