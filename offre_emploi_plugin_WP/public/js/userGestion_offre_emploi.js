var DateTime = luxon.DateTime;
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
                bouton_visible = `<div class='btn transparent rounded ms-2' onclick="toggleVisibilite('`+ data +`', 'non visible')"><i class='fa-solid fa-eye'></i></div>`;
            else
                bouton_visible = `<div class='btn transparent rounded ms-2' onclick="toggleVisibilite('`+ data +`', 'visible')"><i class='fa-solid fa-eye-slash'></i></i></div>`;
            return bouton_visible+`<a type='button' class='btn btn-success rounded ms-2' href='/offreEmploi/mesOffres/`+data+`'><i class='fa-regular fa-file-lines'></i></a><button type='button' class='btn btn-danger rounded ms-2' onclick="supprimerOffre('`+data+`')"><i class='fa-solid fa-xmark'></i></button>`; 
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

function toggleVisibilite(offre_id, visibilite){
    jQuery.ajax({
        type:'POST',
        url:mes_offres_ajax.ajax_url,
        data:{_ajax_nonce:mes_offres_ajax.nonce, action:'toggle_visibilite_offre', id_offre: offre_id, visibilite:visibilite},
        success:function(){
            tableOffres.ajax.reload();
        },
        error:function(data){
            console.log(data.responseText);
        }
    })
}

function supprimerOffre(offre_id){
    if(confirm('Êtes-vous sûr(e) de vouloir supprimer cette offre d\'emploi ?')){
        jQuery.ajax({
            type:'POST',
            url:mes_offres_ajax.ajax_url,
            data:{_ajax_nonce:mes_offres_ajax.nonce, action:'supprimer_mon_offre', id_offre: offre_id},
            success:function(){
                tableOffres.ajax.reload();
            },
            error:function(data){
                console.log(data.responseText);
            }
        })
    }
}