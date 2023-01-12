//transforme la liste des communes en select2
jQuery('#liste_ville').select2({
    placeholder: 'Selectionner une ville',
    allowClear : true,
    width:'resolve'
});

jQuery('.liste_offres').pagination({
    dataSource : my_ajax_obj.ajax_url+"?_ajax_nonce="+my_ajax_obj.nonce+"&action=get_offres_sans_filtres_action",
    pageSize:50,
    ajax:{
        beforeSend: function(){
            dataContainer.html('Chargement des offres en cours...');
        }
    },
    callback:function(data){
        template_html = '';
        Object.data['data'].forEach(offre => {
            offre_html = "<li class='offre'>";
            offre_html += "<div class='corps_offre";
            offre_html += "<a class='lien_fiche' href='/offreEmploi/"+offre['id']+"'><h2>"+offre['intitule']+"</h2></a>";
            offre_html += "<a href='https://www.openstreetmap.org/?mlat="+offre['latitude']+"&mlon="+offre['longitude']+"#map=17/"+offre['latitude']+"/"+offre['longitude']+"&layers=N' target='_blank'>";
            offre_html += "<h4 class='ville'>";
            if(offre['id_pole_emploi']){
                offre_html = offre['ville_libelle'].split(' - ').pop();
            }else{
                offre_html = offre['ville_libelle'];
            }
            offre_html += "<i class='fa-solid fa-map-pin'></i>";
            offre_html += "</h4></a>";
            if(offre['description'].length > 150){
                offre_html += "<p id='description'>"+offre['description'].replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').substr(0, 150)+'...</p></div>';
            }else{
                offre_html += "<p id='description'>"+offre['description']+"</p></div>";
            }
            if(offre['nom_entreprise']){
                offre_html += "<p>Entreprise : "+offre['nom_entreprise']+"</p>";
            }
            if(offre['id_pole_emploi']){
                offre_html += "<a class='lien_pole_emploi' href='"+offre['origine_offre']+"' target='_blank'>lien vers l'offre sur pole emploi.</a>";
            }
            offre_html += "</div></li>";
            template_html += offre_html;
        });
        dataContainer.html(template_html);
    }
})

//lance la récupère des offres d'emploi lorsqu'on choisi une commune
jQuery('#liste_ville').on('select2:select', function(e){
    var data = e.params.data;
    var this2 = this;
    jQuery.ajax({
        type:'POST',
        url:my_ajax_obj.ajax_url,
        data:{_ajax_nonce: my_ajax_obj.nonce, action: "get_offres_par_commune", ville:data.id, distance:document.getElementById('liste_distance').value},
        success:function(data){
            data = data.data;
            console.log(data);
            let liste_html = '';
            Object.entries(data).forEach(entry => {
                const [key, element] = entry;
                if(key != 'info'){
                    let lien_fiche = "<a class='lien_fiche' href='/offreEmploi/"+ element['id']+"'><h2>"+ element['intitule']+"</h2></a>";
                    if(element['lienMap'] != 'aucun'){
                        ville_offre = "<a href='" + element['lienMap']+"' target='_blank'><h4 class='ville'>"+element['nomVille']+"<i class='fa-solid fa-map-pin'></i></h4></a>";
                    }else{
                        ville_offre = "<h4 class='ville'>"+element['nomVille']+"</h4>";
                    }
                    let description_offre = "<p id='description'>"+element['description']+'</p>';
                    let corps_offre = "<div class='corps_offre'>"+lien_fiche+ville_offre+description_offre+"</div>"
                    let entreprise_offre = '';
                    if(element['nomEntreprise'] != 'Aucun'){
                        entreprise_offre = "<p> Entreprise : "+element['nomEntreprise']+'</p>'
                    }
                    let lien_pole_emploi = "<a class='lien_pole_emploi' href='"+element['lienOrigineOffre']+"'>lien vers l'offre sur pole emploi.</a>"
                    let pied_offre= "<div class='entreprise_offre'>"+entreprise_offre+lien_pole_emploi+'</div>';
                    liste_html += "<div class='offre'>"+corps_offre+pied_offre+"</div>";
                }
            });
            jQuery('.liste_offres').html(liste_html);
            if(data['info']['pageMax'] == 1){
                jQuery('.page_actuelle').text('1');
                Object.values(document.getElementsByClassName('premiere_page')).forEach(element=>{
                    while(element.nextSibling.nodeName == '#text'){
                        element.nextSibling.remove();
                    }
                    element.remove();
                })
                Object.values(document.getElementsByClassName('derniere_page')).forEach(element=>{
                    while(element.previousSibling.nodeName == '#text'){
                        element.previousSibling.remove();
                    }
                    element.remove();
                })
                jQuery('.page_precedente').attr('disabled', 'true');
                jQuery('.page_suivante').attr('disabled', 'true');
            }else{
                if(data['info']['pageMax'] == 2){
                    jQuery('.page_actuelle').text('1');
                    Object.values(document.getElementsByClassName('premiere_page')).forEach(element=>{
                        while(element.nextSibling.nodeName == '#text'){
                            element.nextSibling.remove();
                        }
                        element.remove();
                    })
                    if(document.getElementsByClassName('derniere_page').length != 0){
                        Object.values(document.getElementsByClassName('derniere_page')).forEach(element=>{
                            while(element.previousSibling.nodeName == '#text'){
                                element.previousSibling.remove();
                            }
                            element.innerText = 2;
                        })
                    }else{
                        Object.values(document.getElementsByClassName('page_actuelle')).forEach(element => {
                            let derniere_page = document.createElement('button');
                            derniere_page.setAttribute('class', 'derniere_page bouton_pagination');
                            derniere_page.innerText = data['info']['pageMax'];
                            element.insertAdjacentElement('afterend', derniere_page);
                            derniere_page.addEventListener('click', dernierePage); 
                            derniere_page.addEventListener('click', ()=>{
                                document.getElementById('main').scrollIntoView({behavior:'smooth'});   
                            })
                        })
                    }
                    jQuery('.page_precedente').attr('disabled', 'true');
                    jQuery('.page_suivante').removeAttr('disabled');
                }else{
                    jQuery('.page_actuelle').text('1');
                    Object.values(document.getElementsByClassName('premiere_page')).forEach(element=>{
                        while(element.nextSibling.nodeName == '#text'){
                            element.nextSibling.remove();
                        }
                        element.remove();
                    })
                    if(document.getElementsByClassName('derniere_page').length != 0){
                        Object.values(document.getElementsByClassName('derniere_page')).forEach(element=>{
                            if(parseInt(element.innerText) == 2){
                                element.insertAdjacentText('beforebegin', ' ... ');
                            }
                            element.innerText = data['info']['pageMax'];
                        })
                    }else{
                        Object.values(document.getElementsByClassName('page_actuelle')).forEach(element => {
                            let derniere_page = document.createElement('button');
                            derniere_page.setAttribute('class', 'derniere_page bouton_pagination');
                            derniere_page.innerText = data['info']['pageMax'];
                            element.insertAdjacentElement('afterend', derniere_page);
                            element.insertAdjacentText('afterend', ' ... ');
                            derniere_page.addEventListener('click', dernierePage);
                            derniere_page.addEventListener('click', ()=>{
                                document.getElementById('main').scrollIntoView({behavior:'smooth'});   
                            })   
                        })
                    }
                    jQuery('.page_precedente').attr('disabled', 'true');
                    jQuery('.page_suivante').removeAttr('disabled');
                }
            }
        },
        error:function(data){
            console.log(data.responseText);
        }
    })
})

//récupère la totalité des offres d'emploi quand on supprime la recherche par commune
jQuery('#liste_ville').on('select2:clear', function(e){
    jQuery.ajax({
        type:'POST',
        url:my_ajax_obj.ajax_url,
        data:{_ajax_nonce: my_ajax_obj.nonce, action: "get_offres_sans_filtres"},
        success:function(data){
            data=data.data;
            console.log(data);
            let liste_html = '';
            Object.entries(data).forEach(entry => {
                const [key, element] = entry;
                if(key != 'info'){
                    let lien_fiche = "<a class='lien_fiche' href='/offreEmploi/"+ element['id']+"'><h2>"+ element['intitule']+"</h2></a>";
                    if(element['lienMap'] != 'aucun'){
                        ville_offre = "<a href='" + element['lienMap']+"' target='_blank'><h4 class='ville'>"+element['nomVille']+"<i class='fa-solid fa-map-pin'></i></h4></a>";
                    }else{
                        ville_offre = "<h4 class='ville'>"+element['nomVille']+"</h4>";
                    }
                    let description_offre = "<p id='description'>"+element['description']+'</p>';
                    let corps_offre = "<div class='corps_offre'>"+lien_fiche+ville_offre+description_offre+"</div>"
                    let entreprise_offre = '';
                    if(element['nomEntreprise'] != 'Aucun'){
                        entreprise_offre = "<p> Entreprise : "+element['nomEntreprise']+'</p>'
                    }
                    let lien_pole_emploi = "<a class='lien_pole_emploi' href='"+element['lienOrigineOffre']+"'>lien vers l'offre sur pole emploi.</a>"
                    let pied_offre= "<div class='entreprise_offre'>"+entreprise_offre+lien_pole_emploi+'</div>';
                    liste_html += "<div class='offre'>"+corps_offre+pied_offre+"</div>";
                }
            });
            jQuery('.liste_offres').html(liste_html);
            if(data['info']['pageMax'] == 1){
                jQuery('.page_actuelle').text('1');
                Object.values(document.getElementsByClassName('premiere_page')).forEach(element=>{
                    while(element.nextSibling.nodeName == '#text'){
                        element.nextSibling.remove();
                    }
                    element.remove();
                })
                Object.values(document.getElementsByClassName('derniere_page')).forEach(element=>{
                    while(element.previousSibling.nodeName == '#text'){
                        element.previousSibling.remove();
                    }
                    element.remove();
                })
                jQuery('.page_precedente').attr('disabled', 'true');
                jQuery('.page_suivante').attr('disabled', 'true');
            }else{
                if(data['info']['pageMax'] == 2){
                    jQuery('.page_actuelle').text('1');
                    Object.values(document.getElementsByClassName('premiere_page')).forEach(element=>{
                        while(element.nextSibling.nodeName == '#text'){
                            element.nextSibling.remove();
                        }
                        element.remove();
                    })
                    if(document.getElementsByClassName('derniere_page').length != 0){
                        Object.values(document.getElementsByClassName('derniere_page')).forEach(element=>{
                            while(element.previousSibling.nodeName == '#text'){
                                element.previousSibling.remove();
                            }
                            element.innerText = 2;
                        })
                    }else{
                        Object.values(document.getElementsByClassName('page_actuelle')).forEach(element => {
                            let derniere_page = document.createElement('button');
                            derniere_page.setAttribute('class', 'derniere_page bouton_pagination');
                            derniere_page.innerText = data['info']['pageMax'];
                            element.insertAdjacentElement('afterend', derniere_page);
                            derniere_page.addEventListener('click', dernierePage);
                            derniere_page.addEventListener('click', ()=>{
                                document.getElementById('main').scrollIntoView({behavior:'smooth'});   
                            })
                        })
                    }
                    jQuery('.page_precedente').attr('disabled', 'true');
                    jQuery('.page_suivante').removeAttr('disabled');
                }else{
                    jQuery('.page_actuelle').text('1');
                    Object.values(document.getElementsByClassName('premiere_page')).forEach(element=>{
                        while(element.nextSibling.nodeName == '#text'){
                            element.nextSibling.remove();
                        }
                        element.remove();
                    })
                    if(document.getElementsByClassName('derniere_page').length != 0){
                        Object.values(document.getElementsByClassName('derniere_page')).forEach(element=>{
                            if(parseInt(element.innerText) == 2){
                                element.insertAdjacentText('beforebegin', ' ... ');
                            }
                            element.innerText = data['info']['pageMax'];
                        })
                    }else{
                        Object.values(document.getElementsByClassName('page_actuelle')).forEach(element => {
                            let derniere_page = document.createElement('button');
                            derniere_page.setAttribute('class', 'derniere_page bouton_pagination');
                            derniere_page.innerText = data['info']['pageMax'];
                            element.insertAdjacentElement('afterend', derniere_page);
                            element.insertAdjacentText('afterend', ' ... ');
                            derniere_page.addEventListener('click', dernierePage);
                            derniere_page.addEventListener('click', ()=>{
                                document.getElementById('main').scrollIntoView({behavior:'smooth'});   
                            })
                        })
                    }
                    jQuery('.page_precedente').attr('disabled', 'true');
                    jQuery('.page_suivante').removeAttr('disabled');
                }
            }
        },
        error:function(data){
            console.log(data.jsonMessage);
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
    if(document.getElementById('liste_ville').value){
        jQuery.ajax({
            type:'POST',
            url:my_ajax_obj.ajax_url,
            data:{_ajax_nonce: my_ajax_obj.nonce, action: "get_offres_par_commune", ville:document.getElementById('liste_ville').value, distance:data.id},
            success:function(data){
                data = data.data;
                console.log(data);
                let liste_html = '';
                Object.entries(data).forEach(entry => {
                    const [key, element] = entry;
                    if(key != 'info'){
                        let lien_fiche = "<a class='lien_fiche' href='/offreEmploi/"+ element['id']+"'><h2>"+ element['intitule']+"</h2></a>";
                        let ville_offre = '';
                        if(element['lienMap'] != 'aucun'){
                            ville_offre = "<a href='" + element['lienMap']+"' target='_blank'><h4 class='ville'>"+element['nomVille']+"<i class='fa-solid fa-map-pin'></i></h4></a>";
                        }else{
                            ville_offre = "<h4 class='ville'>"+element['nomVille']+"</h4>";
                        }
                        let description_offre = "<p id='description'>"+element['description']+'</p>';
                        let corps_offre = "<div class='corps_offre'>"+lien_fiche+ville_offre+description_offre+"</div>"
                        let entreprise_offre = '';
                        if(element['nomEntreprise'] != 'Aucun'){
                            entreprise_offre = "<p> Entreprise : "+element['nomEntreprise']+'</p>'
                        }
                        let lien_pole_emploi = "<a class='lien_pole_emploi' href='"+element['lienOrigineOffre']+"'>lien vers l'offre sur pole emploi.</a>"
                        let pied_offre= "<div class='entreprise_offre'>"+entreprise_offre+lien_pole_emploi+'</div>';
                        liste_html += "<div class='offre'>"+corps_offre+pied_offre+"</div>";
                    }
                });
                jQuery('.liste_offres').html(liste_html);
                if(data['info']['pageMax'] == 1){
                    jQuery('.page_actuelle').text('1');
                    Object.values(document.getElementsByClassName('premiere_page')).forEach(element=>{
                        while(element.nextSibling.nodeName == '#text'){
                            element.nextSibling.remove();
                        }
                        element.remove();
                    })
                    Object.values(document.getElementsByClassName('derniere_page')).forEach(element=>{
                        while(element.previousSibling.nodeName == '#text'){
                            element.previousSibling.remove();
                        }
                        element.remove();
                    })
                    jQuery('.page_precedente').attr('disabled', 'true');
                    jQuery('.page_suivante').attr('disabled', 'true');
                }else{
                    if(data['info']['pageMax'] == 2){
                        jQuery('.page_actuelle').text('1');
                        Object.values(document.getElementsByClassName('premiere_page')).forEach(element=>{
                            while(element.nextSibling.nodeName == '#text'){
                                element.nextSibling.remove();
                            }
                            element.remove();
                        })
                        if(document.getElementsByClassName('derniere_page').length != 0){
                            Object.values(document.getElementsByClassName('derniere_page')).forEach(element=>{
                                while(element.previousSibling.nodeName == '#text'){
                                    element.previousSibling.remove();
                                }
                                element.innerText = 2;
                            })
                        }else{
                            Object.values(document.getElementsByClassName('page_actuelle')).forEach(element => {
                                let derniere_page = document.createElement('button');
                                derniere_page.setAttribute('class', 'derniere_page bouton_pagination');
                                derniere_page.innerText = data['info']['pageMax'];
                                element.insertAdjacentElement('afterend', derniere_page);
                                derniere_page.addEventListener('click', dernierePage);
                                derniere_page.addEventListener('click', ()=>{
                                    document.getElementById('main').scrollIntoView({behavior:'smooth'});   
                                })
                            })
                        }
                        jQuery('.page_precedente').attr('disabled', 'true');
                        jQuery('.page_suivante').removeAttr('disabled');
                    }else{
                        jQuery('.page_actuelle').text('1');
                        Object.values(document.getElementsByClassName('premiere_page')).forEach(element=>{
                            while(element.nextSibling.nodeName == '#text'){
                                element.nextSibling.remove();
                            }
                            element.remove();
                        })
                        if(document.getElementsByClassName('derniere_page').length != 0){
                            Object.values(document.getElementsByClassName('derniere_page')).forEach(element=>{
                                while(element.previousSibling.nodeName == '#text'){
                                    element.previousSibling.remove();
                                }
                                element.insertAdjacentText('beforebegin', ' ... ')
                                element.innerText = data['info']['pageMax'];
                            })
                        }else{
                            Object.values(document.getElementsByClassName('page_actuelle')).forEach(element => {
                                let derniere_page = document.createElement('button');
                                derniere_page.setAttribute('class', 'derniere_page bouton_pagination');
                                derniere_page.innerText = data['info']['pageMax'];
                                while(element.nextSibling.nodeName == '#text'){
                                    element.nextSibling.remove();
                                }
                                element.insertAdjacentElement('afterend', derniere_page);
                                element.insertAdjacentText('afterend', ' ... ');
                                derniere_page.addEventListener('click', dernierePage);
                                derniere_page.addEventListener('click', ()=>{
                                    document.getElementById('main').scrollIntoView({behavior:'smooth'});   
                                })
                            })
                        }
                        jQuery('.page_precedente').attr('disabled', 'true');
                        jQuery('.page_suivante').removeAttr('disabled');
                    }
                }
            },
            error:function(data){
                console.log(data.jsonMessage);
            }
        })
    }
})

jQuery('.page_precedente').on('click', pagePrecedente);

jQuery('.premiere_page').on('click', premierePage);

jQuery('.derniere_page').on('click', dernierePage);

jQuery('.page_suivante').on('click', pageSuivante);

//affiche les offres de la page précédente
function pagePrecedente(){
    let action = '';
    if(document.getElementById('liste_ville').value){
        action = 'get_offres_par_commune';
    }else{
        action = 'get_offres_sans_filtres';
    }
    jQuery.ajax({
        type:'POST',
        url: my_ajax_obj.ajax_url,
        data:{_ajax_nonce: my_ajax_obj.nonce, action: action, page:parseInt(document.getElementsByClassName('page_actuelle')[0].innerText) - 1, ville:document.getElementById('liste_ville').value, distance:document.getElementById('liste_distance').value},
        success:function(data){
            data=data.data;
            if(parseInt(document.getElementsByClassName('page_actuelle')[0].innerText) <= '2'){
                Object.values(document.getElementsByClassName('page_actuelle')).forEach(value =>{
                    jQuery('.premiere_page').remove();
                    value.innerText = 1;
                    value.previousElementSibling.toggleAttribute('disabled');
                });
            }else if(parseInt(document.getElementsByClassName('page_actuelle')[0].innerText) == '3'){
                Object.values(document.getElementsByClassName('page_actuelle')).forEach(value =>{
                    while(value.previousSibling.nodeName == '#text'){
                        value.previousSibling.remove();
                    }
                    value.innerText = 2;
                })
            }else{
                Object.values(document.getElementsByClassName('page_actuelle')).forEach(value =>{
                    value.innerText = parseInt(value.innerText) - 1;
                })
            }
            let derniere_page = document.getElementsByClassName('derniere_page');
            if(derniere_page.length == 0){
                Object.values(document.getElementsByClassName('page_suivante')).forEach(value =>{
                    let derniere_page = document.createElement('button');
                    derniere_page.setAttribute('class', 'derniere_page bouton_pagination');
                    derniere_page.innerText = parseInt(document.getElementsByClassName('page_actuelle')[0].innerText) + 1;
                    value.removeAttribute('disabled');
                    value.insertAdjacentElement('beforebegin', derniere_page);
                    derniere_page.addEventListener('click', dernierePage);
                    derniere_page.addEventListener('click', ()=>{
                        document.getElementById('main').scrollIntoView({behavior:'smooth'});   
                    })
                });
            }else{
                if(parseInt(document.getElementsByClassName('page_actuelle')[0].innerText) + 2 == derniere_page[0].innerText){
                    Object.values(derniere_page).forEach(value =>{
                        value.insertAdjacentText('beforebegin', ' ... ');
                    })
                }
            }
            let liste_html = '';
            Object.entries(data).forEach(entry => {
                const [key, element] = entry;
                if(key != 'info'){
                    let lien_fiche = "<a class='lien_fiche' href='/offreEmploi/"+ element['id']+"'><h2>"+ element['intitule']+"</h2></a>";
                    let ville_offre = '';
                    if(element['lienMap'] != 'aucun'){
                        ville_offre = "<a href='" + element['lienMap']+"' target='_blank'><h4 class='ville'>"+element['nomVille']+"<i class='fa-solid fa-map-pin'></i></h4></a>";
                    }else{
                        ville_offre = "<h4 class='ville'>"+element['nomVille']+"</h4>";
                    }
                    let description_offre = "<p id='description'>"+element['description']+'</p>';
                    let corps_offre = "<div class='corps_offre'>"+lien_fiche+ville_offre+description_offre+"</div>"
                    let entreprise_offre = '';
                    if(element['nomEntreprise'] != 'Aucun'){
                        entreprise_offre = "<p> Entreprise : "+element['nomEntreprise']+'</p>'
                    }
                    let lien_pole_emploi = "<a class='lien_pole_emploi' href='"+element['lienOrigineOffre']+"'>lien vers l'offre sur pole emploi.</a>"
                    let pied_offre= "<div class='entreprise_offre'>"+entreprise_offre+lien_pole_emploi+'</div>';
                    liste_html += "<div class='offre'>"+corps_offre+pied_offre+"</div>";
                }
            });
            jQuery('.liste_offres').html(liste_html);                
        }
    })
}

//affiche les offres de la premiere page
function premierePage(){
    let action = '';
    if(document.getElementById('liste_ville').value){
        action = 'get_offres_par_commune';
    }else{
        action = 'get_offres_sans_filtres';
    }
    jQuery.ajax({
        type:'POST',
        url: my_ajax_obj.ajax_url,
        data:{_ajax_nonce: my_ajax_obj.nonce, action: action, page:1, ville:document.getElementById('liste_ville').value, distance:document.getElementById('liste_distance').value},
        success:function(data){
            data=data.data;
            let derniere_page = document.getElementsByClassName('derniere_page');
            if(parseInt(document.getElementsByClassName('page_actuelle')[0].innerText) == 2){
                if(derniere_page.length == 0){
                    Object.values(document.getElementsByClassName('page_actuelle')).forEach(value =>{
                        value.setAttribute('class', 'derniere_page bouton_pagination');
                        value.addEventListener('click', dernierePage);
                        value.nextElementSibling.toggleAttribute('disabled');
                    });
                    Object.values(document.getElementsByClassName('premiere_page')).forEach(value =>{
                        let page_actuelle = document.createElement('button');
                        page_actuelle.setAttribute('class', 'page_actuelle bouton_pagination');
                        page_actuelle.innerText = value.innerText;
                        value.previousElementSibling.toggleAttribute('disabled');
                        value.insertAdjacentElement('beforebegin', page_actuelle);
                        value.remove();
                    });
                }else{
                    Object.values(document.getElementsByClassName('page_actuelle')).forEach(value =>{
                        value.innerText = 1;
                        value.previousElementSibling.remove();
                        while(value.previousSibling.nodeName == '#text'){
                            value.previousSibling.remove();
                        }
                        while(value.nextSibling.nodeName == '#text'){
                            value.nextSibling.remove();
                        }
                        value.insertAdjacentText('afterend', ' ... ')
                        value.previousElementSibling.toggleAttribute('disabled');
                    });
                }
            }else{
                if(derniere_page.length == 0){
                    Object.values(document.getElementsByClassName('page_actuelle')).forEach(value =>{
                        value.setAttribute('class', 'derniere_page bouton_pagination');
                        value.addEventListener('click', dernierePage);
                        value.nextElementSibling.toggleAttribute('disabled');
                    });
                    Object.values(document.getElementsByClassName('premiere_page')).forEach(value =>{
                        let page_actuelle = document.createElement('button');
                        page_actuelle.setAttribute('class', 'page_actuelle bouton_pagination');
                        page_actuelle.innerText = value.innerText;
                        value.previousElementSibling.toggleAttribute('disabled');
                        value.insertAdjacentElement('beforebegin', page_actuelle);
                        value.remove();
                    });
                }else{
                    if(parseInt(document.getElementsByClassName('page_actuelle')[0].innerText) == parseInt(derniere_page[0].innerText) - 1){
                        jQuery('.page_actuelle').remove();
                        Object.values(document.getElementsByClassName('premiere_page')).forEach(value =>{
                            let page_actuelle = document.createElement('button');
                            page_actuelle.setAttribute('class', 'page_actuelle bouton_pagination');
                            page_actuelle.innerText = value.innerText;
                            value.previousElementSibling.toggleAttribute('disabled');
                            value.insertAdjacentElement('beforebegin', page_actuelle);
                            value.remove();
                        });
                    }else{
                        Object.values(document.getElementsByClassName('page_actuelle')).forEach(value =>{
                            while(value.previousSibling.nodeName == '#text'){
                                value.previousSibling.remove();
                            }
                            value.remove();
                        });
                        Object.values(document.getElementsByClassName('premiere_page')).forEach(value =>{
                            let page_actuelle = document.createElement('button');
                            page_actuelle.setAttribute('class', 'page_actuelle bouton_pagination');
                            page_actuelle.innerText = value.innerText;
                            value.previousElementSibling.toggleAttribute('disabled');
                            value.insertAdjacentElement('beforebegin', page_actuelle);
                            value.remove();
                        });
                    }
                }
            }
            let liste_html = '';
            Object.entries(data).forEach(entry => {
                const [key, element] = entry;
                if(key != 'info'){
                    let lien_fiche = "<a class='lien_fiche' href='/offreEmploi/"+ element['id']+"'><h2>"+ element['intitule']+"</h2></a>";
                    let ville_offre = '';
                    if(element['lienMap'] != 'aucun'){
                        ville_offre = "<a href='" + element['lienMap']+"' target='_blank'><h4 class='ville'>"+element['nomVille']+"<i class='fa-solid fa-map-pin'></i></h4></a>";
                    }else{
                        ville_offre = "<h4 class='ville'>"+element['nomVille']+"</h4>";
                    }
                    let description_offre = "<p id='description'>"+element['description']+'</p>';
                    let corps_offre = "<div class='corps_offre'>"+lien_fiche+ville_offre+description_offre+"</div>"
                    let entreprise_offre = '';
                    if(element['nomEntreprise'] != 'Aucun'){
                        entreprise_offre = "<p> Entreprise : "+element['nomEntreprise']+'</p>'
                    }
                    let lien_pole_emploi = "<a class='lien_pole_emploi' href='"+element['lienOrigineOffre']+"'>lien vers l'offre sur pole emploi.</a>"
                    let pied_offre= "<div class='entreprise_offre'>"+entreprise_offre+lien_pole_emploi+'</div>';
                    liste_html += "<div class='offre'>"+corps_offre+pied_offre+"</div>";
                }
            });
            jQuery('.liste_offres').html(liste_html);
        }
    })
}

//affiche les offres de la derniere page
function dernierePage(){
    let action = '';
    if(document.getElementById('liste_ville').value){
        action = 'get_offres_par_commune';
    }else{
        action = 'get_offres_sans_filtres';
    }
    jQuery.ajax({
        type:'POST',
        url: my_ajax_obj.ajax_url,
        data:{_ajax_nonce: my_ajax_obj.nonce, action: action, page:parseInt(document.getElementsByClassName('derniere_page')[0].innerText), ville:document.getElementById('liste_ville').value, distance:document.getElementById('liste_distance').value},
        success:function(data){
            data=data.data;
            let premiere_page = document.getElementsByClassName('premiere_page');
            if(parseInt(document.getElementsByClassName('page_actuelle')[0].innerText) == parseInt(document.getElementsByClassName('derniere_page')[0].innerText) - 1){
                if(premiere_page.length == 0){
                    Object.values(document.getElementsByClassName('page_actuelle')).forEach(value =>{
                        value.setAttribute('class', 'premiere_page bouton_pagination');
                        value.addEventListener('click', premierePage);
                        value.previousElementSibling.toggleAttribute('disabled');
                    });
                    Object.values(document.getElementsByClassName('derniere_page')).forEach(value =>{
                        let page_actuelle = document.createElement('button');
                        page_actuelle.setAttribute('class', 'page_actuelle bouton_pagination');
                        page_actuelle.innerText = value.innerText;
                        value.nextElementSibling.toggleAttribute('disabled');
                        value.insertAdjacentElement('beforebegin', page_actuelle);
                        value.remove();
                    });
                }else{
                    Object.values(document.getElementsByClassName('page_actuelle')).forEach(value =>{
                        value.innerText = parseInt(document.getElementsByClassName('derniere_page')[0].innerText);
                        value.nextElementSibling.remove();
                        while(value.previousSibling.nodeName == '#text'){
                            value.previousSibling.remove();
                        }
                        while(value.nextSibling.nodeName == '#text'){
                            value.nextSibling.remove();
                        }
                        value.insertAdjacentText('beforebegin', ' ... ')
                        value.nextElementSibling.toggleAttribute('disabled');
                    });
                }
            }else{
                if(premiere_page.length == 0){
                    Object.values(document.getElementsByClassName('page_actuelle')).forEach(value =>{
                        value.setAttribute('class', 'premiere_page bouton_pagination');
                        value.addEventListener('click', premierePage);
                        value.previousElementSibling.toggleAttribute('disabled');
                    });
                    Object.values(document.getElementsByClassName('derniere_page')).forEach(value =>{
                        let page_actuelle = document.createElement('button');
                        page_actuelle.setAttribute('class', 'page_actuelle bouton_pagination');
                        page_actuelle.innerText = value.innerText;
                        value.nextElementSibling.toggleAttribute('disabled');
                        value.insertAdjacentElement('beforebegin', page_actuelle);
                        value.remove();
                    });
                }else{
                    if(parseInt(document.getElementsByClassName('page_actuelle')[0].innerText) == 2){
                        jQuery('.page_actuelle').remove();
                        Object.values(document.getElementsByClassName('derniere_page')).forEach(value =>{
                            let page_actuelle = document.createElement('button');
                            page_actuelle.setAttribute('class', 'page_actuelle bouton_pagination');
                            page_actuelle.innerText = value.innerText;
                            value.nextElementSibling.toggleAttribute('disabled');
                            value.insertAdjacentElement('beforebegin', page_actuelle);
                            value.remove();
                        });
                    }else{
                        Object.values(document.getElementsByClassName('page_actuelle')).forEach(value =>{
                            while(value.previousSibling.nodeName == '#text'){
                                value.previousSibling.remove();
                            }
                            value.remove();
                        });
                        Object.values(document.getElementsByClassName('derniere_page')).forEach(value =>{
                            let page_actuelle = document.createElement('button');
                            page_actuelle.setAttribute('class', 'page_actuelle bouton_pagination');
                            page_actuelle.innerText = value.innerText;
                            value.nextElementSibling.toggleAttribute('disabled');
                            value.insertAdjacentElement('beforebegin', page_actuelle);
                            value.remove();
                        });
                    }
                }
            }
            let liste_html = '';
            Object.entries(data).forEach(entry => {
                const [key, element] = entry;
                if(key != 'info'){
                    let lien_fiche = "<a class='lien_fiche' href='/offreEmploi/"+ element['id']+"'><h2>"+ element['intitule']+"</h2></a>";
                    let ville_offre = '';
                    if(element['lienMap'] != 'aucun'){
                        ville_offre = "<a href='" + element['lienMap']+"' target='_blank'><h4 class='ville'>"+element['nomVille']+"<i class='fa-solid fa-map-pin'></i></h4></a>";
                    }else{
                        ville_offre = "<h4 class='ville'>"+element['nomVille']+"</h4>";
                    }
                    let description_offre = "<p id='description'>"+element['description']+'</p>';
                    let corps_offre = "<div class='corps_offre'>"+lien_fiche+ville_offre+description_offre+"</div>"
                    let entreprise_offre = '';
                    if(element['nomEntreprise'] != 'Aucun'){
                        entreprise_offre = "<p> Entreprise : "+element['nomEntreprise']+'</p>'
                    }
                    let lien_pole_emploi = "<a class='lien_pole_emploi' href='"+element['lienOrigineOffre']+"'>lien vers l'offre sur pole emploi.</a>"
                    let pied_offre= "<div class='entreprise_offre'>"+entreprise_offre+lien_pole_emploi+'</div>';
                    liste_html += "<div class='offre'>"+corps_offre+pied_offre+"</div>";
                }
            });
            jQuery('.liste_offres').html(liste_html);
        }
    })
}

//affiche les offres de la page suivante
function pageSuivante(){
    let action = '';
    if(document.getElementById('liste_ville').value){
        action = 'get_offres_par_commune';
    }else{
        action = 'get_offres_sans_filtres';
    }
    jQuery.ajax({
        type:'POST',
        url: my_ajax_obj.ajax_url,
        data:{_ajax_nonce: my_ajax_obj.nonce, action: action, page:parseInt(document.getElementsByClassName('page_actuelle')[0].innerText) + 1, ville:document.getElementById('liste_ville').value, distance:document.getElementById('liste_distance').value},
        success:function(data){
            data=data.data;
            let premiere_page = document.getElementsByClassName('premiere_page');
            if(premiere_page.length == 0){
                Object.values(document.getElementsByClassName('page_precedente')).forEach(value =>{
                    let premiere_page = document.createElement('button');
                    premiere_page.setAttribute('class', 'premiere_page bouton_pagination');
                    premiere_page.innerText = '1';
                    value.removeAttribute('disabled');
                    value.insertAdjacentElement('afterend', premiere_page);
                    premiere_page.addEventListener('click', premierePage);
                    premiere_page.addEventListener('click', ()=>{
                        document.getElementById('main').scrollIntoView({behavior:'smooth'});   
                    })
                });
            }else{
                if(parseInt(document.getElementsByClassName('page_actuelle')[0].innerText) == 2){
                    Object.values(premiere_page).forEach(value=>{
                        value.insertAdjacentText('afterend', ' ... ');
                    })
                }
            }
            Object.values(document.getElementsByClassName('page_actuelle')).forEach(value =>{
                value.innerText = parseInt(value.innerText) + 1;
                if(parseInt(value.innerText) == parseInt(document.getElementsByClassName('derniere_page')[0].innerText) - 1){
                    while(value.nextSibling.nodeName == '#text'){
                        value.nextSibling.remove();
                    }
                }else{
                    if(parseInt(value.innerText) == parseInt(document.getElementsByClassName('derniere_page')[0].innerText)){
                        value.nextElementSibling.remove();
                        value.nextElementSibling.toggleAttribute('disabled');
                    }
                }
            });
            let liste_html = '';
            Object.entries(data).forEach(entry => {
                const [key, element] = entry;
                if(key != 'info'){
                    let lien_fiche = "<a class='lien_fiche' href='/offreEmploi/"+ element['id']+"'><h2>"+ element['intitule']+"</h2></a>";
                    let ville_offre = '';
                    if(element['lienMap'] != 'aucun'){
                        ville_offre = "<a href='" + element['lienMap']+"' target='_blank'><h4 class='ville'>"+element['nomVille']+"<i class='fa-solid fa-map-pin'></i></h4></a>";
                    }else{
                        ville_offre = "<h4 class='ville'>"+element['nomVille']+"</h4>";
                    }
                    let description_offre = "<p id='description'>"+element['description']+'</p>';
                    let corps_offre = "<div class='corps_offre'>"+lien_fiche+ville_offre+description_offre+"</div>"
                    let entreprise_offre = '';
                    if(element['nomEntreprise'] != 'Aucun'){
                        entreprise_offre = "<p> Entreprise : "+element['nomEntreprise']+'</p>'
                    }
                    let lien_pole_emploi = "<a class='lien_pole_emploi' href='"+element['lienOrigineOffre']+"'>lien vers l'offre sur pole emploi.</a>"
                    let pied_offre= "<div class='entreprise_offre'>"+entreprise_offre+lien_pole_emploi+'</div>';
                    liste_html += "<div class='offre'>"+corps_offre+pied_offre+"</div>";
                }
            });
            jQuery('.liste_offres').html(liste_html);
        }
    })
}