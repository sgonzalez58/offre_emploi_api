//initiatlisation de luxon
var DateTime = luxon.DateTime;
//initialisation de popper.js
const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]')
const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl))
//initiatlisation de datatable
let tableOffres = jQuery('#liste_offre_en_attente').DataTable({
    "language": {
        "url": "https://cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json",
    },
    ajax:{
        url: mes_offres_ajax.ajax_url+"?_ajax_nonce="+mes_offres_ajax.nonce+"&action=get_mes_offres",     //route pour récuperer les données
    },
    columns:[
        {data:'intitule'},
        {data:'nomVille'},
        {data:'nomEntreprise'},
        {data:'dateCreation'},
        {data:'etat'},
        {data:'id'},
        {data:'visibilite', visible:false}
    ],
    columnDefs:[
        {targets: 5, render:function (data, type, row){                    //création des boutons modifier et supprimer pour chaque ligne
            let bouton_visible = '';
            if(row['visibilite'] == 'visible')
                bouton_visible = `<div data-toggle='tooltip' data-placement='bottom' title='visible' class='btn bulle transparent rounded ms-2' onclick="toggleVisibilite('`+ data +`', 'non visible', '`+row['etat']+`')"><i class='fa-solid fa-eye'></i></div>`;
            else
                bouton_visible = `<div data-toggle='tooltip' data-placement='bottom' title='invisible' class='btn bulle transparent rounded ms-2' onclick="toggleVisibilite('`+ data +`', 'visible', '`+row['etat']+`')"><i class='fa-solid fa-eye-slash'></i></i></div>`;
            return bouton_visible+`<a type='button' data-toggle='tooltip' data-placement='bottom' title="modifier l'offre" class='btn bulle btn-success rounded ms-2' href='/offres-emploi/mesOffres/`+data+`'><i class='fa-regular fa-file-lines'></i></a><button type='button' data-toggle='tooltip' data-placement='bottom' title="supprimer l'offre" class='btn bulle btn-danger rounded ms-2' onclick="supprimerOffre('`+data+`')"><i class='fa-solid fa-xmark'></i></button>`; 
        }},
        {targets: [3], render:function (data, type){                    //mise en page de la colonne date de création
            return type === 'display' ?
                DateTime.fromISO(new Date(data).toISOString()).setLocale('fr').toFormat('dd MMMM y') :
                DateTime.fromISO(new Date(data).toISOString()).toFormat('dd MMMM y')
        }},
        {targets: [0,5], responsivePriority : 1}                //priorité pour le nom du site et les boutons en responsive.
    ],
    "createdRow":function(row, data){                           //ajout de class pour les lignes <tr>
        jQuery(row).attr('id', 'offre-' + data['id']);
    },
    "dom":"lfrtip",
    "autoWidth":false,                                          //desactivation des tailles automatiques pour les lignes (sinon le tableau casse lorsque la fenetre change de taille)
    responsive:true,                                            //activation du responsive (colonne disparait lorsqu'il n'y a plus de place et ajoute un bouton (+) en debut de ligne pour voir la colonne sur une ligne adjacente)
})
.on('init', function(){
    jQuery(function () {
        jQuery('.bulle').tooltip()
      })
});

let tableCandidatures = jQuery('#mes_candidatures').DataTable({
    "language": {
        "url": "https://cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json",
    },
    ajax:{
        url: mes_offres_ajax.ajax_url+"?_ajax_nonce="+mes_offres_ajax.nonce+"&action=get_mes_candidatures",     //route pour récuperer les données
    },
    columns:[
        {data:'intitule'},
        {data:'nomVille'},
        {data:'nomEntreprise'},
        {data:'dateCreation'},
        {data:'mail'},
        {data:'etat'},
        {data:'id'},
    ],
    columnDefs:[
        {targets: 6, render:function (data){                    //création des boutons modifier et supprimer pour chaque ligne
            return `<a type='button' data-toggle='tooltip' data-placement='bottom' title="voir l'offre" class='btn bulle btn-primary rounded ms-2' href='/offres-emploi/`+data+`'><i class='fa-regular fa-file-lines'></i></a>`; 
        }},
        {targets: [3], render:function (data, type){                    //mise en page de la colonne date de création
            return type === 'display' ?
                DateTime.fromISO(new Date(data).toISOString()).setLocale('fr').toFormat('dd MMMM y') :
                DateTime.fromISO(new Date(data).toISOString()).toFormat('dd MMMM y')
        }},
        {targets: [0,5], responsivePriority : 1}                //priorité pour le nom du site et les boutons en responsive.
    ],
    "createdRow":function(row, data){                           //ajout de class pour les lignes <tr>
        jQuery(row).attr('id', 'offre-' + data['id']);
    },
    "dom":"lfrtip",
    "autoWidth":false,                                          //desactivation des tailles automatiques pour les lignes (sinon le tableau casse lorsque la fenetre change de taille)
    responsive:true,                                            //activation du responsive (colonne disparait lorsqu'il n'y a plus de place et ajoute un bouton (+) en debut de ligne pour voir la colonne sur une ligne adjacente)
})
.on('init', function(){
    jQuery(function () {
        jQuery('.bulle').tooltip()
      })
});
//initialisation des infobulles
jQuery(function () {
    jQuery('#bulle_ajout').tooltip()
})
//demande au serveur de modifier la visibilité d'une offre valide
function toggleVisibilite(offre_id, visibilite, etat){
    if(etat == 'valide'){
        jQuery.ajax({
            type:'POST',
            url:mes_offres_ajax.ajax_url,
            data:{_ajax_nonce:mes_offres_ajax.nonce, action:'toggle_visibilite_offre', id_offre: offre_id, visibilite:visibilite},
            success:function(){
                tableOffres.ajax.reload(function(){
                    jQuery(function () {
                        jQuery('.bulle').tooltip()
                    })
                });
            },
            error:function(data){
                console.log(data.responseText);
            }
        })
    }else{
        alert("Vous ne pouvez pas rendre visible une annonce qui n'a pas été validée par un administrateur.")
    }
}
//demande au serveur de supprimer une offre
function supprimerOffre(offre_id){

    if(confirm('Êtes-vous sûr(e) de vouloir supprimer cette offre d\'emploi ?')){
        jQuery.ajax({
            type:'POST',
            url:mes_offres_ajax.ajax_url,
            data:{_ajax_nonce:mes_offres_ajax.nonce, action:'supprimer_mon_offre', id_offre: offre_id},
            success:function(){
                tableOffres.ajax.reload(function(){
                    jQuery(function () {
                        jQuery('.bulle').tooltip()
                    })
                });
            },
            error:function(data){
                console.log(data.responseText);
            }
        })
    }
}

const checkbox_offres_creees = document.getElementById('offres_creees_input');
const checkbox_candidatures = document.getElementById('candidatures_input');
const label_offres_creees = document.getElementById('offres_creees_label');
const label_candidatures = document.getElementById('candidatures_label');

label_offres_creees.addEventListener('click', (e)=>{
    if(checkbox_offres_creees.checked){
        e.preventDefault();
    }
})

label_candidatures.addEventListener('click', (e)=>{
    if(checkbox_candidatures.checked){
        e.preventDefault();
    }
})

checkbox_offres_creees.addEventListener('change', ()=>{
    checkbox_candidatures.checked = false;
    label_candidatures.setAttribute('class', 'btn btn-info');
    label_offres_creees.setAttribute('class', 'btn btn-primary');
})

checkbox_candidatures.addEventListener('change', ()=>{
    checkbox_offres_creees.checked = false;
    label_candidatures.setAttribute('class', 'btn btn-primary');
    label_offres_creees.setAttribute('class', 'btn btn-info');
})