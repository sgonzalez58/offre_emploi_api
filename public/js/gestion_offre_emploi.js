var DateTime = luxon.DateTime;
let tableOffres = $('#liste_offre_en_attente').DataTable({
    "language": {
        "url": "https://cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json",
    },
    ajax:{
        url: '/offreEmploi/admin/getOffresUsers',     //route pour récuperer les données
        dataSrc:'',
    },
    columns:[
        {data:'intitule'},
        {data:'nomVille'},
        {data:'nomEntreprise'},
        {data:'dateDemande'},
        {data:'etat'},
        {data:'id'}
    ],
    searchPanes: {
        viewTotal: true
    },
    columnDefs:[
        {targets: [1,2,4], searchPanes:{
            show:true
        }},
        {targets: 5, render:function (data){                    //création des boutons modifier et supprimer pour chaque ligne
            return "<a href='/offreEmploi/admin/"+ data +"' ><i class='fa-solid fa-eye'></i></a><button type='button' class='btn btn-danger rounded ms-2' data-bs-toggle='modal' data-bs-target='#modalMail' data-bs-offre='"+ data +"'><i class='fa-solid fa-xmark'></i></button>"; 
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
    "dom":"Plfrtip",
    "autoWidth":false,                                          //desactivation des tailles automatiques pour les lignes (sinon le tableau casse lorsque la fenetre change de taille)
    responsive:true,                                            //activation du responsive (colonne disparait lorsqu'il n'y a plus de place et ajoute un bouton (+) en debut de ligne pour voir la colonne sur une ligne adjacente)
})


let reponse_flash = document.getElementById('reponse-flash');
if(reponse_flash){
    reponse_flash.firstElementChild.addEventListener('click', ()=>{
        reponse_flash.remove();
    })
}

//on récupère le modal de création/modification de mail
const modalMail = document.getElementById('modalMail');
//lorsque celui s'affiche :
modalMail.addEventListener('show.bs.modal', event =>{
    //on récupère le bouton qui a été cliqué pour l'afficher
    const button = event.relatedTarget;
    //on récupère l'information permettant de déterminer si il s'agit d'un ajout de mail ou d'une modification
    const offre_id = button.getAttribute('data-bs-offre');
    //on modifie le nom du modal avec l'id de l'offre
    modalMail.getElementsByClassName('modal-title')[0].textContent = `Précisez la raison du refus de l'offre : ${offre_id}`;
    
    document.getElementById('localisation').checked = false;
    document.getElementById('entreprise').checked = false;
    document.getElementById('raison_personnalisee').value = '';
})

function confirmerSuppression(){
    if(document.getElementById('localisation').checked || document.getElementById('entreprise').checked || document.getElementById('raison_personnalisee').value != ''){
        $raison = '';
        if(document.getElementById('localisation').checked){
            $raison += 'La localisation et le nom de la commune ne sont pas renseignés.\n';
        }
        if(document.getElementById('entreprise').checked){
            $raison += 'Le nom de l\'entreprise n\'est pas renseigné.\n';
        }
        if(document.getElementById('raison_personnalisee').value != ''){
            $raison += document.getElementById('raison_personnalisee').value+'\n';
        }
        $.ajax({
            type:'POST',
            url:'/offreEmploi/admin/refuserOffre/' + modalMail.getElementsByClassName('modal-title')[0].textContent.split(' : ')[1],
            data:{raison:$raison},
            success:function(){
                modalMail.getElementsByClassName('modal-header')[0].lastElementChild.click();
                tableOffres.ajax.reload();
            }
        })
    }else{
        alert('Ajoutez au moins une raison de refus.')
    }
}