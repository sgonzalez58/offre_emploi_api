//activation de la librairie luxon
var DateTime = luxon.DateTime;
//activation de la librairie popper
const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]')
const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl))
//on crée la dataTable
let tableOffres = jQuery('#liste_offre_en_attente').DataTable({
    "language": {
        "url": "https://cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json",
    },
    ajax:{
        url: my_ajax_obj.ajax_url + '?_ajax_nonce='+my_ajax_obj.nonce+'&action=get_nouvelles_offres',     //route pour récuperer les données
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
        {targets: 5, render:function (data, type, row){                    //création des boutons de lien vers la fiche, refus de l'offre et archivage de l'offre pour chaque ligne
            if(row['etat'] == 'refus'){
                second_bouton = `</button><button type='button' data-toggle='tooltip' data-placement='bottom' title="archiver l'offre" class='btn bulle btn-secondary rounded me-2' onclick="archiver('`+data+`', '`+row['etat']+`')"><i class="fa-solid fa-box-archive"></i></button>`;
            }else{
                second_bouton = `<button type='button' data-toggle='tooltip' data-placement='bottom' title="refuser l'offre" class='btn bulle btn-danger rounded me-2' data-bs-toggle='modal' data-bs-target='#modalMail' data-bs-offre='`+ data +`'><i class='fa-solid fa-xmark'></i>`
            }
            return "<a href='/wp-admin/admin.php?page=gestion_offre_emploi&id_offre="+ data +`' data-toggle='tooltip' data-placement='bottom' title="voir la fiche d'offre" class='btn bulle btn-success rounded me-2'><i class='fa-regular fa-file-lines'></i></a>`+second_bouton; 
        }},
        {targets: [3], render:function (data, type){                    //mise en page de la colonne date d'actualisation
            return type === 'display' ?
                DateTime.fromISO(new Date(data).toISOString()).setLocale('fr').toFormat('dd MMMM y') :
                DateTime.fromISO(new Date(data).toISOString()).toFormat('dd MMMM y')
        }},
        {targets: [0,5], responsivePriority : 1}
    ],
    "createdRow":function(row, data){                           //ajout de class pour les lignes <tr>
        jQuery(row).attr('id', 'offre-' + data['id']);
    },
    "dom":"Plfrtip",
    "autoWidth":false,                                          //desactivation des tailles automatiques pour les lignes (sinon le tableau casse lorsque la fenetre change de taille)
    responsive:true,                                            //activation du responsive (colonne disparait lorsqu'il n'y a plus de place et ajoute un bouton (+) en debut de ligne pour voir la colonne sur une ligne adjacente)
})
.on('init', function(){ //une fois le tableau rempli, on active les informations au survol des boutons
    jQuery(function () {
        jQuery('.bulle').tooltip()
      })
});

jQuery(function () {  //on active les boutons popovers
    jQuery('#bulle_ajout').tooltip()
})

//on récupère le modal de réponse de l'offre
const modalMail = document.getElementById('modalMail');
//lorsque celui s'affiche :
modalMail.addEventListener('show.bs.modal', event =>{
    //on récupère le bouton qui a été cliqué pour l'afficher
    const button = event.relatedTarget;
    //on récupère les informations du boutons
    const offre_id = button.getAttribute('data-bs-offre');
    //on modifie le nom du modal avec l'id de l'offre
    modalMail.getElementsByClassName('modal-title')[0].textContent = `Précisez la raison du refus de l'offre : ${offre_id}`;
    
    document.getElementById('localisation').checked = false;
    document.getElementById('entreprise').checked = false;
    document.getElementById('raison_personnalisee').value = '';
})

//fonction qui se lance lorsqu'on confirme le refus
//auto complétion de la réponse lorsqu'on clique sur les raisons et envoie de la réponse avec changement d'état de l'offre
function confirmerSuppression(){
    if(document.getElementById('localisation').checked || document.getElementById('entreprise').checked || document.getElementById('raison_personnalisee').value != ''){
        $raison = '';
        if(document.getElementById('localisation').checked){
            $raison += 'La localisation et le nom de la commune ne sont pas renseignés.\n';
        }
        if(document.getElementById('entreprise').checked){
            $raison += "Le nom de l'entreprise n'est pas renseigné.\n";
        }
        if(document.getElementById('raison_personnalisee').value != ''){
            $raison += document.getElementById('raison_personnalisee').value+'\n';
        }
        $offre_id = document.getElementsByClassName('modal-header')[0].firstElementChild.innerText.split(' : ').pop();
        jQuery.ajax({
            type:'POST',
            url:confirmation_ajax.ajax_url,
            data:{_ajax_nonce:confirmation_ajax.nonce, action:'get_reponse_negative_offre', id_offre:$offre_id, raison: $raison},
            success:function(){
                modalMail.getElementsByClassName('modal-header')[0].lastElementChild.click();
                tableOffres.ajax.reload(function(){
                    jQuery(function () {
                        jQuery('.bulle').tooltip()
                    })
                });
            }
        })
    }else{
        alert('Ajoutez au moins une raison de refus.')
    }
}
//envoie la demande de modification de l'offre pour qu'elle soit archivée
function archiver(id_offre, etat){
    if(etat != 'refus'){
        alert('Vous ne pouvez pas archiver une demande en attente de réponse ou valide.');
    }else if(confirm("Voulez-vous archiver cette offre ?")){
        jQuery.ajax({
            type:'POST',
            url:confirmation_ajax.ajax_url,
            data:{_ajax_nonce:confirmation_ajax.nonce, action:'set_offre_archive', id_offre:id_offre},
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