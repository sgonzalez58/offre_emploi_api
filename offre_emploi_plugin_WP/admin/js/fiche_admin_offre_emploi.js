//on récupère le modal de réponse d'offre
const modalOffre = document.getElementById('modalOffre');
//lorsque celui s'affiche :
modalOffre.addEventListener('show.bs.modal', event =>{
    //on récupère le bouton qui a été cliqué pour l'afficher
    const button = event.relatedTarget;
    //on récupère l'information permettant de déterminer si il s'agit d'une réponse positive ou négative à l'offre
    const decision = button.getAttribute('data-bs-decision');
    //on récupère l'id de l'offre
    const offre_id = location.href.split('=').pop();
    if(decision == 'valider'){
        modalOffre.getElementsByClassName('modal-title')[0].textContent = `Souhaitez-vous ajouter un commentaire ?`;
        document.getElementById('localisation').parentElement.style.display = 'none';
        document.getElementById('entreprise').parentElement.style.display = 'none';
        document.getElementById('raison_personnalisee').style.display = 'inline-block';
        document.getElementById('bouton_confirmation').innerText = 'Envoyer et accepter';
        if(document.getElementById('raison_personnalisee').previousElementSibling.firstElementChild){
            document.getElementById('raison_personnalisee').previousElementSibling.firstElementChild.remove();
        }
        //si on confirme la réponse positive, on modifie le champs "état" de l'offre et indique si la modification a fonctionné
        document.getElementById('bouton_confirmation').removeEventListener('click', refuser);
        document.getElementById('bouton_confirmation').addEventListener('click', accepter);
    }else if(decision == 'refuser'){
        modalOffre.getElementsByClassName('modal-title')[0].textContent = `Précisez la raison du refus de l'offre : ${offre_id}`;
        document.getElementById('localisation').parentElement.style.display = 'inline-block';
        document.getElementById('entreprise').parentElement.style.display = 'inline-block';
        document.getElementById('bouton_confirmation').innerText = 'Envoyer et refuser';
        if(!document.getElementById('raison_personnalisee').previousElementSibling.firstElementChild){
            document.getElementById('raison_personnalisee').previousElementSibling.insertAdjacentHTML('afterbegin', '<p>Autres:</p>');
        }
        document.getElementById('bouton_confirmation').removeEventListener('click', accepter);
        document.getElementById('bouton_confirmation').addEventListener('click', refuser);
    }else{
        modalOffre.getElementsByClassName('modal-title')[0].textContent = `Souhaitez-vous archiver l'offre ${offre_id}`;
        document.getElementById('localisation').parentElement.style.display = 'none';
        document.getElementById('entreprise').parentElement.style.display = 'none';
        if(document.getElementById('raison_personnalisee').previousElementSibling.firstElementChild){
            document.getElementById('raison_personnalisee').previousElementSibling.firstElementChild.remove();
        }
        document.getElementById('raison_personnalisee').style.display = 'none';
        document.getElementById('bouton_confirmation').innerText = 'Envoyer et archiver';
        document.getElementById('bouton_confirmation').removeEventListener('click', accepter);
        document.getElementById('bouton_confirmation').addEventListener('click', archiver);
    }
    document.getElementById('localisation').checked = false;
    document.getElementById('entreprise').checked = false;
    document.getElementById('raison_personnalisee').value = '';
})

//si on répond défavorablement à la demande d'offre, il faut préciser la raison du refus et il est impossible de refuser la demande sans raison
document.getElementById('bouton_confirmation').addEventListener('click', (e)=>{
    if(document.getElementById('bouton_confirmation').innerText == 'Envoyer et refuser'){
        if(document.getElementById('localisation').checked == false && document.getElementById('entreprise').checked == false && document.getElementById('raison_personnalisee').value == ''){
            e.preventDefault();
            alert('Veuillez préciser au moins un motif de refus.');
        }
    }
})

//envoie la demande de modification de l'offre pour qu'elle soit valide et demande un envoie de mail au demandeur
function accepter(){
    offre_id = location.href.split('=').pop();
    jQuery.ajax({
        type:'POST',
        url:confirmation_ajax.ajax_url,
        data:{_ajax_nonce:confirmation_ajax.nonce, action:'get_reponse_positive_offre', id_offre:offre_id, commentaire:document.getElementById('raison_personnalisee').value},
        success:function(){
            document.getElementsByClassName('modal-body')[0].innerHTML = '<p>L\'offre a été acceptée avec succès. Elle est désormais visible dans la liste des offres.</p><p>Vous pouvez retourner dans la liste des offres utilisateurs via <a href=\'/wp-admin/admin.php?page=gestion_offre_emploi\'>ce lien</a></p>';
            document.getElementsByClassName('modal-footer')[0].innerHTML = '';
        },
        error:function(data){
            document.getElementsByClassName('modal-body')[0].innerHTML = data.responseText;
            document.getElementsByClassName('modal-footer')[0].innerHTML = '';
        }
    })
}

//envoie la demande de modification de l'offre pour qu'elle soit refusée et demande un envoie de mail au demandeur
function refuser(){
    offre_id = location.href.split('=').pop();
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
    jQuery.ajax({
        type:'POST',
        url:confirmation_ajax.ajax_url,
        data:{_ajax_nonce:confirmation_ajax.nonce, action:'get_reponse_negative_offre', id_offre:offre_id, raison:$raison},
        success:function(){
            document.getElementsByClassName('modal-body')[0].innerHTML = '<p>L\'offre a été refusée avec succès.</p><p>Vous pouvez retourner dans la liste des offres utilisateurs via <a href=\'/wp-admin/admin.php?page=gestion_offre_emploi\'>ce lien</a></p>';
            document.getElementsByClassName('modal-footer')[0].innerHTML = '';
        },
        error:function(data){
            document.getElementsByClassName('modal-body')[0].innerHTML = data.responseText;
            document.getElementsByClassName('modal-footer')[0].innerHTML = '';
        }
    })
}

bouton_archivage = document.getElementById('bouton_archiver');

//envoie la demande de modification de l'offre pour qu'elle soit archivée
function archiver(){
    offre_id = location.href.split('=').pop();
    jQuery.ajax({
        type:'POST',
        url:confirmation_ajax.ajax_url,
        data:{_ajax_nonce:confirmation_ajax.nonce, action:'set_offre_archive', id_offre:offre_id},
        success:function(){
            document.getElementsByClassName('modal-body')[0].innerHTML = '<p>L\'offre a été archivée avec succès. Elle n\'apparaitra plus dans la liste des offres.</p><p>Vous pouvez retourner dans la liste des offres utilisateurs via <a href=\'https://new.koikispass.com/wp-admin/admin.php?page=gestion_offre_emploi\'>ce lien</a></p>';
            document.getElementsByClassName('modal-footer')[0].innerHTML = '';
        },
        error:function(data){
            console.log(data.responseText);
        }
    })
}