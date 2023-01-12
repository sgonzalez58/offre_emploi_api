//transforme la liste des communes en select2
jQuery('#liste_ville').select2({
    placeholder: 'Selectionner une ville',
    allowClear : true,
    width:'resolve'
});

jQuery('#pagination_container').pagination({
    dataSource : my_ajax_obj.ajax_url+"?_ajax_nonce="+my_ajax_obj.nonce+"&action=get_offres_sans_filtres",
    locator: 'data.offres',
    pageSize:20,
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
            offre_html += "<a class='lien_fiche' href='/offreEmploi/"+offre['id']+"'><h2>"+offre['intitule']+"</h2></a>";
            offre_html += "<a href='"+offre['lienMap']+"' target='_blank'>";
            offre_html += "<h4 class='ville'>"+offre['nomVille']+"<i class='fa-solid fa-map-pin'></i>"+"</h4></a>";
            offre_html += "<p id='description'>"+offre['description']+"</p></div><div class='entreprise_offre'>";
            if(offre['nomEntreprise']){
                offre_html += "<p>Entreprise : "+offre['nomEntreprise']+"</p>";
            }
            if(offre['lienOrigineOffre']){
                offre_html += "<a class='lien_pole_emploi' href='"+offre['lienOrigineOffre']+"' target='_blank'>lien vers l'offre sur pole emploi.</a>";
            }
            offre_html += "</div></div>";
            template_html += offre_html;
        });
        jQuery('#liste_offres').html(template_html);
    },
    afterPaging: function(){
        document.getElementById('liste_offres').scrollIntoView();
    }
})

//lance la récupère des offres d'emploi lorsqu'on choisi une commune
jQuery('#liste_ville').on('select2:select', function(e){
    var data = e.params.data;
    jQuery('#pagination_container').pagination('destroy');
    jQuery('#pagination_container').pagination({
        dataSource : my_ajax_obj.ajax_url+"?_ajax_nonce="+my_ajax_obj.nonce+"&action=get_offres_par_commune"+"&ville="+data.id+"&distance="+document.getElementById('liste_distance').value,
        locator: 'data.offres',
        pageSize:20,
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
                offre_html += "<a class='lien_fiche' href='/offreEmploi/"+offre['id']+"'><h2>"+offre['intitule']+"</h2></a>";
                offre_html += "<a href='"+offre['lienMap']+"' target='_blank'>";
                offre_html += "<h4 class='ville'>"+offre['nomVille']+"<i class='fa-solid fa-map-pin'></i>"+"</h4></a>";
                offre_html += "<p id='description'>"+offre['description']+"</p></div><div class='entreprise_offre'>";
                if(offre['nomEntreprise']){
                    offre_html += "<p>Entreprise : "+offre['nomEntreprise']+"</p>";
                }
                if(offre['lienOrigineOffre']){
                    offre_html += "<a class='lien_pole_emploi' href='"+offre['lienOrigineOffre']+"' target='_blank'>lien vers l'offre sur pole emploi.</a>";
                }
                offre_html += "</div></div>";
                template_html += offre_html;
            });
            jQuery('#liste_offres').html(template_html);
        },
        afterPaging: function(){
            document.getElementById('liste_offres').scrollIntoView();
        }
    })
})

//récupère la totalité des offres d'emploi quand on supprime la recherche par commune
jQuery('#liste_ville').on('select2:clear', function(e){
    jQuery('#pagination_container').pagination('destroy');
    jQuery('#pagination_container').pagination({
        dataSource : my_ajax_obj.ajax_url+"?_ajax_nonce="+my_ajax_obj.nonce+"&action=get_offres_sans_filtres",
        locator: 'data.offres',
        pageSize:20,
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
                offre_html += "<a class='lien_fiche' href='/offreEmploi/"+offre['id']+"'><h2>"+offre['intitule']+"</h2></a>";
                offre_html += "<a href='"+offre['lienMap']+"' target='_blank'>";
                offre_html += "<h4 class='ville'>"+offre['nomVille']+"<i class='fa-solid fa-map-pin'></i>"+"</h4></a>";
                offre_html += "<p id='description'>"+offre['description']+"</p></div><div class='entreprise_offre'>";
                if(offre['nomEntreprise']){
                    offre_html += "<p>Entreprise : "+offre['nomEntreprise']+"</p>";
                }
                if(offre['lienOrigineOffre']){
                    offre_html += "<a class='lien_pole_emploi' href='"+offre['lienOrigineOffre']+"' target='_blank'>lien vers l'offre sur pole emploi.</a>";
                }
                offre_html += "</div></div>";
                template_html += offre_html;
            });
            jQuery('#liste_offres').html(template_html);
        },
        afterPaging: function(){
            document.getElementById('liste_offres').scrollIntoView();
        }
    })
})

//transforme la liste des kilometres en select2
jQuery('#liste_distance').select2({
    width:'resolve'
});

//récupère les offres d'emploi autour d'une commune en fonction de la distance choisie
jQuery('#liste_distance').on('select2:select', function(e){
    var data = e.params.data;
    jQuery('#pagination_container').pagination('destroy');
    jQuery('#pagination_container').pagination({
        dataSource : my_ajax_obj.ajax_url+"?_ajax_nonce="+my_ajax_obj.nonce+"&action=get_offres_par_commune"+"&ville="+document.getElementById('liste_ville').value+"&distance="+data.id,
        locator: 'data.offres',
        pageSize:20,
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
                offre_html += "<a class='lien_fiche' href='/offreEmploi/"+offre['id']+"'><h2>"+offre['intitule']+"</h2></a>";
                offre_html += "<a href='"+offre['lienMap']+"' target='_blank'>";
                offre_html += "<h4 class='ville'>"+offre['nomVille']+"<i class='fa-solid fa-map-pin'></i>"+"</h4></a>";
                offre_html += "<p id='description'>"+offre['description']+"</p></div><div class='entreprise_offre'>";
                if(offre['nomEntreprise']){
                    offre_html += "<p>Entreprise : "+offre['nomEntreprise']+"</p>";
                }
                if(offre['lienOrigineOffre']){
                    offre_html += "<a class='lien_pole_emploi' href='"+offre['lienOrigineOffre']+"' target='_blank'>lien vers l'offre sur pole emploi.</a>";
                }
                offre_html += "</div></div>";
                template_html += offre_html;
            });
            jQuery('#liste_offres').html(template_html);
        },
        afterPaging: function(){
            document.getElementById('liste_offres').scrollIntoView();
        }
    })
})