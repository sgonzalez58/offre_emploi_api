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

recherche_mot_clef();

jQuery('#recherche_input').on('keyup', (e)=>{
    if(e.keyCode === 13){
        jQuery('#recherche').click();
    }
})

document.getElementById('recherche').addEventListener('click', recherche_mot_clef);

function recherche_mot_clef(){
    if(window.innerWidth > 1080){
        mots_clef = document.getElementById('recherche_input').value;
        url = my_ajax_obj.ajax_url+'?_ajax_nonce='+my_ajax_obj.nonce+"&action=recherche_mot_clef&mots_clef="+mots_clef+"&ville="+my_ajax_obj.ville+"&distance="+document.getElementById('liste_distance').value+"&type_de_contrat="+my_ajax_obj.type_contrat
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

	let comm = document.querySelectorAll('.commune_filtre_tri_emploi');
	let blocsUrl = window.location.href.split('/');
	let nb_communes = JSON.parse(my_ajax_obj.nb_communes);
	if(blocsUrl[4] != 'lieu' && blocsUrl[4] != 'categorie'){
		for(let a = 0; a<comm.length; a++){
			comm[a].nextElementSibling.innerText = '(0)';
		}
		
		for(let a = 0; a < nb_communes.length; a++){
			document.getElementById('commune-'+nb_communes[a]['id_commune']).nextElementSibling.innerText = '('+nb_communes[a]['NbEvent']+')';
		}
		for(let a = 0; a<comm.length; a++){
			let number = comm[a].nextElementSibling.innerText;
			if(number == '(0)'){
				document.querySelector('.com_filtre_liste_emploi' + a).style.display = "none";
			}else{
				document.querySelector('.com_filtre_liste_emploi' + a).style.display = "flex";
			}
		}

		let theme = document.querySelectorAll('.thematique_filtre_tri');

		for(let b = 0; b<theme.length; b++){
			let number = 0;
			for(let ii = 0; ii<bloc1.length; ii++){
				if(bloc1[ii].classList.contains(theme[b].value.substring(1))){
					number++;
				}
			}
			if(number == 0){
				document.querySelector('.them_filtre_liste' + b).style.display = "none";
			}else{
				document.querySelector('.them_filtre_liste' + b).style.display = "flex";
				//document.querySelector('.com_filtre_tri' + (a)).nextElementSibling.innerHTML += " ("+number+")";
				var element = document.querySelector('.them_filtre_tri' + b);
				var content = element.nextElementSibling.innerHTML;

				// Rechercher la position des parenthèses ouvrante et fermante
				var startIndex = content.indexOf('(');
				var endIndex = content.indexOf(')');

				// Construire la nouvelle chaîne de caractères avec le nouveau nombre
				var updatedContent = content.substring(0, startIndex + 1) + number + content.substring(endIndex);

				// Mettre à jour le contenu avec le nouveau nombre
				element.nextElementSibling.innerHTML = updatedContent;					}
		}
	}else if(blocsUrl[4] == "lieu" && blocsUrl[6] == ""){
		let theme = document.querySelectorAll('.thematique_filtre_tri');

		for(let b = 0; b<theme.length; b++){
			let number = 0;
			for(let ii = 0; ii<bloc1.length; ii++){
				if(bloc1[ii].classList.contains(theme[b].value.substring(1))){
					number++;
				}
			}
			if(number == 0){
				document.querySelector('.them_filtre_liste' + b).style.display = "none";
			}else{
				document.querySelector('.them_filtre_liste' + b).style.display = "flex";
				//document.querySelector('.com_filtre_tri' + (a)).nextElementSibling.innerHTML += " ("+number+")";
				var element = document.querySelector('.them_filtre_tri' + b);
				var content = element.nextElementSibling.innerHTML;

				// Rechercher la position des parenthèses ouvrante et fermante
				var startIndex = content.indexOf('(');
				var endIndex = content.indexOf(')');

				// Construire la nouvelle chaîne de caractères avec le nouveau nombre
				var updatedContent = content.substring(0, startIndex + 1) + number + content.substring(endIndex);

				// Mettre à jour le contenu avec le nouveau nombre
				element.nextElementSibling.innerHTML = updatedContent;					}
		}
	}else if(blocsUrl[4] == "categorie" && blocsUrl[6] == ""){
		for(let a = 0; a<comm.length; a++){
			let number = 0;
			for(let i = 0; i<bloc1.length; i++){
				if(bloc1[i].classList.contains(comm[a].value.substring(1))){
					number++;
				}
			}
			if(number == 0){
				document.querySelector('.com_filtre_liste' + a).style.display = "none";
			}else{
				document.querySelector('.com_filtre_liste' + a).style.display = "flex";

				//document.querySelector('.com_filtre_tri' + (a)).nextElementSibling.innerHTML += " ("+number+")";
				var element = document.querySelector('.com_filtre_tri' + a);
				var content = element.nextElementSibling.innerHTML;

				// Rechercher la position des parenthèses ouvrante et fermante
				var startIndex = content.indexOf('(');
				var endIndex = content.indexOf(')');

				// Construire la nouvelle chaîne de caractères avec le nouveau nombre
				var updatedContent = content.substring(0, startIndex + 1) + number + content.substring(endIndex);

				// Mettre à jour le contenu avec le nouveau nombre
				element.nextElementSibling.innerHTML = updatedContent;
			}
		}
		
	}else if(blocsUrl[4] == "lieu" && blocsUrl[6] == "categorie"){
		
	}
}