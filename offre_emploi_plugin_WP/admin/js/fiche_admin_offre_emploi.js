//on récupère le modal de création/modification de mail
const modalOffre = document.getElementById('modalOffre');
//lorsque celui s'affiche :
modalOffre.addEventListener('show.bs.modal', event =>{
    //on récupère le bouton qui a été cliqué pour l'afficher
    const button = event.relatedTarget;
    //on récupère l'information permettant de déterminer si il s'agit d'un ajout de mail ou d'une modification
    const decision = button.getAttribute('data-bs-decision');
    const offre_id = location.href.split('=').pop();
    //on modifie le nom du modal avec l'id de l'offre
    if(decision == 'valider'){
        modalOffre.getElementsByClassName('modal-title')[0].textContent = `Souhaitez-vous ajouter un commentaire ?`;
        document.getElementById('localisation').parentElement.style.display = 'none';
        document.getElementById('entreprise').parentElement.style.display = 'none';
        document.getElementById('bouton_confirmation').innerText = 'Envoyer et accepter';
        document.getElementById('raison_personnalisee').previousElementSibling.firstElementChild.remove();
        document.getElementById('bouton_confirmation').addEventListener('click', ()=>{
            jQuery.ajax({
                type:'POST',
                url:confirmation_ajax.ajax_url,
                data:{_ajax_nonce:confirmation_ajax.nonce, action:'get_reponse_positive_offre', id_offre:offre_id},
                success:function(){
                    document.getElementsByClassName('modal-body')[0].innerHTML = '<p>L\'offre a été acceptée avec succès. Elle est désormais visible dans la liste des offres.</p><p>Vous pouvez retourner dans la liste des offres utilisateurs via </p><a href=\'https://new.koikispass.com/wp-admin/admin.php?page=gestion_offre_emploi\'>ce lien</a>';
                },
                error:function(data){
                    document.getElementsByClassName('modal-body')[0].innerHTML = data.responseText;
                }
            })
        })
    }else{
        modalOffre.getElementsByClassName('modal-title')[0].textContent = `Précisez la raison du refus de l'offre : ${offre_id}`;
        document.getElementById('localisation').parentElement.style.display = 'inline-block';
        document.getElementById('entreprise').parentElement.style.display = 'inline-block';
        document.getElementById('bouton_confirmation').innerText = 'Envoyer et refuser';
        if(!document.getElementById('raison_personnalisee').previousElementSibling.firstElementChild){
            document.getElementById('raison_personnalisee').previousElementSibling.insertAdjacentHTML('afterbegin', '<p>Autres:</p>');
        }
        document.getElementById('bouton_confirmation').addEventListener('click', ()=>{
            jQuery.ajax({
                type:'POST',
                url:confirmation_ajax.ajax_url,
                data:{_ajax_nonce:confirmation_ajax.nonce, action:'get_reponse_negative_offre', id_offre:offre_id},
                success:function(){
                    document.getElementsByClassName('modal-body')[0].innerHTML = '<p>L\'offre a été refusée avec succès.</p><p>Vous pouvez retourner dans la liste des offres utilisateurs via </p><a href=\'https://new.koikispass.com/wp-admin/admin.php?page=gestion_offre_emploi\'>ce lien</a>';
                },
                error:function(data){
                    document.getElementsByClassName('modal-body')[0].innerHTML = data.responseText;
                }
            })
        })
    }
    document.getElementById('localisation').checked = false;
    document.getElementById('entreprise').checked = false;
    document.getElementById('raison_personnalisee').value = '';
})

document.getElementById('bouton_confirmation').addEventListener('click', (e)=>{
    if(document.getElementById('bouton_confirmation').innerText == 'Envoyer et refuser'){
        if(document.getElementById('localisation').checked == false && document.getElementById('entreprise').checked == false && document.getElementById('raison_personnalisee').value == ''){
            e.preventDefault();
            alert('Veuillez préciser au moins un motif de refus.');
        }
    }
})