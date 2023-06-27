let b = 0;

function modal_thematique(){
	document.getElementById('modal_filtre_thematique').style.width = "90%";
	document.getElementById('modal_filtre_thematique').style.height = "90%";
	document.getElementById('modal_filtre_thematique').style.zIndex = "150";
	document.getElementById('modal_filtre_thematique').style.opacity = "1";
	document.getElementById('modal_filtre_thematique').style.transition = "1s";
	document.getElementById('overlay_filtre_offre_emploi').style.opacity = "1";
	document.getElementById('overlay_filtre_offre_emploi').style.zIndex = "140";
	document.getElementById('overlay_filtre_offre_emploi').style.transition = "1s";
	document.querySelector("body").style.overflow = "hidden";
	b = 0;
}

function modal_localisation(){
	document.getElementById('modal_filtre_localisation').style.width = "90%";
	document.getElementById('modal_filtre_localisation').style.height = "90%";
	document.getElementById('modal_filtre_localisation').style.zIndex = "150";
	document.getElementById('modal_filtre_localisation').style.opacity = "1";
	document.getElementById('modal_filtre_localisation').style.transition = "1s";
	document.getElementById('overlay_filtre_offre_emploi').style.opacity = "1";
	document.getElementById('overlay_filtre_offre_emploi').style.zIndex = "140";
	document.getElementById('overlay_filtre_offre_emploi').style.transition = "1s";
	document.querySelector("body").style.overflow = "hidden";
	b = 1;
}

function close_filtre(){
	document.getElementById('overlay_filtre_offre_emploi').style.opacity = "0";
	document.getElementById('overlay_filtre_offre_emploi').style.zIndex = "-140";
	document.getElementById('overlay_filtre_offre_emploi').style.transition = "1s";

	document.getElementById('modal_filtre_thematique').style.height = "0px";
	document.getElementById('modal_filtre_thematique').style.width = "0px";
	document.getElementById('modal_filtre_thematique').style.zIndex = "-150";
	document.getElementById('modal_filtre_thematique').style.opacity = "0";
	document.getElementById('modal_filtre_thematique').style.transition = "1s";

	document.getElementById('modal_filtre_localisation').style.height = "0px";
	document.getElementById('modal_filtre_localisation').style.width = "0px";
	document.getElementById('modal_filtre_localisation').style.zIndex = "-150";
	document.getElementById('modal_filtre_localisation').style.opacity = "0";
	document.getElementById('modal_filtre_localisation').style.transition = "1s";

	document.querySelector("body").style.overflow = "scroll";
}

const url_query = window.location.search;
const params = new URLSearchParams(url_query);
let mots_clef = params.get('mots_clef');
let distance = params.get('distance');

if(mots_clef){
    document.getElementById('recherche_input').value = decodeURIComponent(mots_clef);
}

if(distance){
    document.getElementById('liste_distance').value = distance;
}

recherche_mot_clef();

jQuery('#recherche_input').on('keyup', (e)=>{
    if(e.keyCode === 13){
        jQuery('#recherche').click();
    }
})

document.getElementById('recherche').addEventListener('click', recherche_mot_clef);

async function recherche_mot_clef(){
    mots_clef = document.getElementById('recherche_input').value;
    distance = document.getElementById('liste_distance').value;
    if(window.innerWidth > 1080){
        url = my_ajax_obj.ajax_url+'?_ajax_nonce='+my_ajax_obj.nonce+"&action=recherche_mot_clef&mots_clef="+mots_clef+"&ville="+my_ajax_obj.ville+"&distance="+distance+"&type_de_contrat="+my_ajax_obj.type_contrat
        jQuery('#pagination_container').pagination({
            dataSource : url,
            locator: 'data.offres',
            pageSize:30,
            showSizeChanger: true,
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
                    offre_html +=           "<div class='ville'>";
                    offre_html +=              "<i class='fa-solid fa-location-pin'></i><h4>"+offre['nomVille']+"</h4></div>";
                    offre_html +=           "<div class='contrat'><i class='fa-solid fa-tag'></i><h4>"+offre['type_contrat']+"</h4></div></div>";
                    if(offre['nomEntreprise'] != 'Aucun'){
                        offre_html +=       "<h3 class='nom_entreprise'>Entreprise : "+offre['nomEntreprise']+"</h3>";
                    }
                    offre_html +=       "<p class='description'>"+offre['description']+"</p></div>";
                    offre_html +=   "<a class='lien_fiche' href='/offres-emploi/"+offre['id']+"'><button class='bouton_lien_fiche'>Voir l'offre</button></a>";
                    offre_html +=   "<a class='lien_fiche_big' href='/offres-emploi/"+offre['id']+"'></a>";
                    offre_html += "</div>";
                    template_html += offre_html;
                });
                jQuery('#liste_offres').html(template_html);
            }
        })
    }else if(window.innerWidth > 780){
        url = my_ajax_obj.ajax_url+'?_ajax_nonce='+my_ajax_obj.nonce+"&action=recherche_mot_clef&mots_clef="+mots_clef+"&ville="+my_ajax_obj.ville+"&distance="+document.getElementById('liste_distance').value+"&type_de_contrat="+my_ajax_obj.type_contrat;
        jQuery('#pagination_container').pagination({
            dataSource : url,
            locator: 'data.offres',
            pageSize:20,
            showSizeChanger: true,
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
                    offre_html +=           "<div class='ville'>";
                    offre_html +=              "<i class='fa-solid fa-location-pin'></i><h4>"+offre['nomVille']+"</h4></div>";
                    offre_html +=           "<div class='contrat'><i class='fa-solid fa-tag'></i><h4>"+offre['type_contrat']+"</h4></div></div>";
                    if(offre['nomEntreprise'] != 'Aucun'){
                        offre_html +=       "<h3 class='nom_entreprise'>Entreprise : "+offre['nomEntreprise']+"</h3>";
                    }
                    offre_html +=       "<p class='description'>"+offre['description']+"</p></div>";
                    offre_html +=   "<a class='lien_fiche' href='/offres-emploi/"+offre['id']+"'><button class='bouton_lien_fiche'>Voir l'offre</button></h2></a>";
                    offre_html +=   "<a class='lien_fiche_big' href='/offres-emploi/"+offre['id']+"'></a>";
                    offre_html += "</div>";
                    template_html += offre_html;
                });
                jQuery('#liste_offres').html(template_html);
            }
        })
    }else{
        url = my_ajax_obj.ajax_url+'?_ajax_nonce='+my_ajax_obj.nonce+"&action=recherche_mot_clef&mots_clef="+mots_clef+"&ville="+my_ajax_obj.ville+"&distance="+document.getElementById('liste_distance').value+"&type_de_contrat="+my_ajax_obj.type_contrat;
        jQuery('#pagination_container').pagination({
            dataSource : url,
            locator: 'data.offres',
            pageSize:10,
            showSizeChanger: true,
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
                    offre_html +=           "<div class='ville'>";
                    offre_html +=              "<i class='fa-solid fa-location-pin'></i><h4>"+offre['nomVille']+"</h4></div>";
                    offre_html +=           "<div class='contrat'><i class='fa-solid fa-tag'></i><h4>"+offre['type_contrat']+"</h4></div></div>";
                    if(offre['nomEntreprise'] != 'Aucun'){
                        offre_html +=       "<h3 class='nom_entreprise'>Entreprise : "+offre['nomEntreprise']+"</h3>";
                    }
                    offre_html +=       "<p class='description'>"+offre['description']+"</p></div>";
                    offre_html +=   "<a class='lien_fiche' href='/offres-emploi/"+offre['id']+"'><button class='bouton_lien_fiche'>Voir l'offre</button></h2></a>";
                    offre_html +=   "<a class='lien_fiche_big' href='/offres-emploi/"+offre['id']+"'></a>";
                    offre_html += "</div>";
                    template_html += offre_html;
                });
                jQuery('#liste_offres').html(template_html);
            }
        })
    }

	let comm = document.querySelectorAll('.commune_filtre_tri_emploi');
	let nb_communes = JSON.parse(my_ajax_obj.nb_communes);
    let nb_types_contrat = JSON.parse(my_ajax_obj.nb_types_contrat);
    if(mots_clef != ''){
        nb_communes = await new Promise((resolve, error)=>{
            jQuery.ajax({
                'method' : 'GET',
                'url' : my_ajax_obj.ajax_url+'?_ajax_nonce='+my_ajax_obj.nonce+"&action=nb_communes_filtres&mots_clef="+mots_clef+"&ville="+my_ajax_obj.ville+"&type_de_contrat="+encodeURIComponent(my_ajax_obj.type_contrat),
                'success' : function(data){
                    data = data['data'];
                    resolve(JSON.parse(data));
                },
                error : function(data){
                    error(data);
                    console.log(data);
                }
            })
        })

        nb_types_contrat = await new Promise((resolve, error)=>{
            jQuery.ajax({
                'method' : 'GET',
                'url' : my_ajax_obj.ajax_url+'?_ajax_nonce='+my_ajax_obj.nonce+"&action=nb_types_contrat_filtres&mots_clef="+mots_clef+"&ville"+my_ajax_obj.ville+"&type_de_contrat="+encodeURIComponent(my_ajax_obj.type_contrat),
                'success' : function(data){
                    data = data['data'];
                    resolve(JSON.parse(data));
                },
                error : function(data){
                    error(data);
                    console.log(data);
                }
            })
        })
    }

	for(let a = 0; a<comm.length; a++){
        comm[a].nextElementSibling.innerText = '(0)';
    }
    
    for(let a = 0; a < nb_communes.length; a++){
        document.getElementById('commune-'+nb_communes[a]['id_commune']).nextElementSibling.innerText = '('+nb_communes[a]['NbEvent']+')';
    }

    for(let a = 0; a<comm.length; a++){

        let array_url = comm[a].parentElement.getAttribute('href').split('?');
        if(distance){
            comm[a].parentElement.setAttribute('href', array_url[0] + '?distance=' + distance);
            if(mots_clef){
                comm[a].parentElement.setAttribute('href', comm[a].parentElement.getAttribute('href') + '&mots_clef=' + encodeURIComponent(mots_clef));
            }
        }else{
            if(mots_clef){
                comm[a].parentElement.setAttribute('href', array_url[0] + '?mots_clef=' + encodeURIComponent(mots_clef));
            }
        }

        let number = comm[a].nextElementSibling.innerText;
        
        if(number == '(0)'){
            document.querySelector('.com_filtre_liste_emploi' + a).style.display = "none";
        }else{
            document.querySelector('.com_filtre_liste_emploi' + a).style.display = "flex";
        }
    }

    let type_contrat = document.querySelectorAll('.type_contrat_filtre_tri_emploi');

    for(let b = 0; b<type_contrat.length; b++){
        type_contrat[b].nextElementSibling.innerText = '(0)';
    }

    for(let b = 0; b < nb_types_contrat.length; b++){
        document.getElementById('type_contrat-'+nb_types_contrat[b]['nom']).nextElementSibling.innerText = '('+nb_types_contrat[b]['NbEvent']+')';
    }

    for(let b = 0; b<type_contrat.length; b++){

        let array_url = type_contrat[b].parentElement.getAttribute('href').split('?');

        if(distance){
            type_contrat[b].parentElement.setAttribute('href', array_url[0] + '?distance=' + distance);
            if(mots_clef){
                type_contrat[b].parentElement.setAttribute('href', type_contrat[b].parentElement.getAttribute('href') + '&mots_clef=' + encodeURIComponent(mots_clef));
            }
        }else{
            if(mots_clef){
                type_contrat[b].parentElement.setAttribute('href', array_url[0] + '?mots_clef=' + encodeURIComponent(mots_clef));
            }
        }
        
        let number = type_contrat[b].nextElementSibling.innerText;

        if(number == '(0)'){
            document.querySelector('.type_filtre_tri_emploi' + b).style.display = "none";
        }else{
            document.querySelector('.type_filtre_tri_emploi' + b).style.display = "flex";
        }
    }
}