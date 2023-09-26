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

	document.querySelector("body").style.overflow = "auto";
}

const url_query = window.location.search;
const params = new URLSearchParams(url_query);
let mots_clef = params.get('motClef');
let distance = params.get('distance');


if(mots_clef){
    document.getElementById('recherche_input').value = decodeURIComponent(mots_clef);
}

if(distance){
    document.getElementById('liste_distance').value = distance;
}

document.getElementById('recherche_input').addEventListener('blur', modifier_liens);

document.getElementById('liste_distance').addEventListener('blur', modifier_liens);

async function modifier_liens(){
    
	let nb_communes = JSON.parse(my_ajax_obj.nb_communes);
    let nb_types_contrat = JSON.parse(my_ajax_obj.nb_types_contrat);
    mots_clef = encodeURIComponent(document.getElementById('recherche_input').value.toLowerCase());
    distance = document.getElementById('liste_distance').value != '' ? document.getElementById('liste_distance').value : 0;
    if(mots_clef != '' || distance != ''){
        let info_nb_communes = await new Promise((resolve, error)=>{
            jQuery.ajax({
                'method' : 'GET',
                'url' : my_ajax_obj.ajax_url+'?_ajax_nonce='+my_ajax_obj.nonce+"&action=info_nb_com_cont_filtres&mots_clef="+mots_clef+"&distance="+distance+"&ville="+my_ajax_obj.ville+"&type_de_contrat="+my_ajax_obj.type_contrat,
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

        nb_communes = info_nb_communes['com'];
        nb_types_contrat = info_nb_communes['cont'];
    }

    let comm = document.querySelectorAll('.commune_filtre_tri_emploi');

    let new_nb_communes = [];

    nb_communes = nb_communes.values();

    for(const $info_cont of nb_communes){
        new_nb_communes[$info_cont['id_commune']] = $info_cont['NbEvent'];
    }

	for(let a = 0; a<comm.length; a++){

        if(new_nb_communes[comm[a].value] > 0){
            comm[a].parentElement.parentElement.style.display = "flex";
        }else{
            comm[a].parentElement.parentElement.style.display = "none";
        }


        let array_url = comm[a].parentElement.getAttribute('href').split('?');

        if(distance > 0){
            comm[a].parentElement.setAttribute('href', array_url[0] + '?distance=' + distance);
            if(mots_clef){
                comm[a].parentElement.setAttribute('href', comm[a].parentElement.getAttribute('href') + '&motClef=' + mots_clef);
            }
        }else{
            if(mots_clef){
                comm[a].parentElement.setAttribute('href', array_url[0] + '?motClef=' + mots_clef);
            }else{
                comm[a].parentElement.setAttribute('href', array_url[0]);
            }
        }
    }

    let type_contrat = document.querySelectorAll('.type_contrat_filtre_tri_emploi');

    let new_nb_type_contrat = [];

    nb_types_contrat = nb_types_contrat.values();

    for(const $info_cont of nb_types_contrat){
        new_nb_type_contrat[$info_cont['nom']] = $info_cont['NbEvent'];
    }

    for(let b = 0; b<type_contrat.length; b++){
        if(new_nb_type_contrat[type_contrat[b].value] > 0){
            type_contrat[b].parentElement.parentElement.style.display = "flex";
        }else{
            type_contrat[b].parentElement.parentElement.style.display = "none";
        }

        let array_url = type_contrat[b].parentElement.getAttribute('href').split('?');

        if(distance > 0){
            type_contrat[b].parentElement.setAttribute('href', array_url[0] + '?distance=' + distance);
            if(mots_clef){
                type_contrat[b].parentElement.setAttribute('href', type_contrat[b].parentElement.getAttribute('href') + '&motClef=' + mots_clef);
            }
        }else{
            if(mots_clef){
                type_contrat[b].parentElement.setAttribute('href', array_url[0] + '?motClef=' + mots_clef);
            }else{
                type_contrat[b].parentElement.setAttribute('href', array_url[0]);
            }
        }
    }
    document.getElementById('recherche').click();
}


Array.from(document.getElementsByClassName('pagination_nb_offre_select')).forEach(select => {
    select.addEventListener('change', async function(){
        if(await new Promise((resolve, error)=>{
            jQuery.ajax({
                'method' : 'GET',
                'url' : my_ajax_obj.ajax_url+'?_ajax_nonce='+my_ajax_obj.nonce+"&action=change_limit_offre&limit="+select.value,
                'success' : function(data){
                    data = data['data'];
                    resolve(true);
                },
                error : function(data){
                    error(false);
                    console.log(data);
                }
            })
        })){
            let array_location = window.location.href.split('?');
            if(array_location.length > 1){
                let array_get_location = array_location[1].split('&');
                array_get_location.forEach((property, key) => {
                    if(property.includes('page')){
                        array_get_location.splice(key, 1);
                    }
                })
                window.location.href = array_location[0] + '?' + array_get_location.join('&');
            }else{
                window.location.href = array_location[0];
            }
        }
    })
})