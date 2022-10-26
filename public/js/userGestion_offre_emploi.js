var DateTime = luxon.DateTime;
let id_client = location.href.split('/');
id_client.pop();
id_client = id_client.pop();
let tableOffres = $('#liste_offre_en_attente').DataTable({
    "language": {
        "url": "https://cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json",
    },
    ajax:{
        url: '/user/'+id_client+'/offreEmploi/getOffres',     //route pour récuperer les données
        dataSrc:'',
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
                bouton_visible = `<button type='button' class='btn rounded ms-2' onclick="toggleVisibilite('`+ data +`')"><i class='fa-solid fa-eye'></i></button>`;
            else
                bouton_visible = `<button type='button' class='btn rounded ms-2' onclick="toggleVisibilite('`+ data +`')"><i class='fa-solid fa-eye-slash'></i></i></button>`;
            return bouton_visible+`<a type='button' class='btn btn-success rounded ms-2' href='/user/`+id_client+`/offreEmploi/`+data+`'><i class='fa-regular fa-file-lines'></i></a><button type='button' class='btn btn-danger rounded ms-2' onclick="supprimerOffre('`+data+`')"><i class='fa-solid fa-xmark'></i></button>`; 
        }},
        {targets: [3], render:function (data, type){                    //mise en page de la colonne date de création
            return type === 'display' ?
                DateTime.fromISO(new Date(data['date']).toISOString()).setLocale('fr').toFormat('dd MMMM y') :
                DateTime.fromISO(new Date(data['date']).toISOString()).toFormat('dd MMMM y')
        }},
        {targets: [0,5], responsivePriority : 1}                //priorité pour le nom du site et les boutons en responsive.
    ],
    "createdRow":function(row, data){                           //ajout de class pour les lignes <tr>
        $(row).attr('id', 'offre-' + data['id']);
    },
    "dom":"lfrtip",
    "autoWidth":false,                                          //desactivation des tailles automatiques pour les lignes (sinon le tableau casse lorsque la fenetre change de taille)
    responsive:true,                                            //activation du responsive (colonne disparait lorsqu'il n'y a plus de place et ajoute un bouton (+) en debut de ligne pour voir la colonne sur une ligne adjacente)
})

function toggleVisibilite(offre_id){
    $.ajax({
        type:'POST',
        url:'/user/offreEmploi/toggleVisibilite/'+offre_id,
        success:function(){
            tableOffres.ajax.reload();
        }
    })
}

function supprimerOffre(offre_id){
    if(confirm('Êtes-vous sûr(e) de vouloir supprimer cette offre d\'emploi ?')){
        $.ajax({
            type:'POST',
            url:'/user/'+id_client+'/offreEmploi/supprimer/'+offre_id,
            success:function(){
                tableOffres.ajax.reload();
            }
        })
    }
}