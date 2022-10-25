//on récupère le modal de création/modification de mail
const modalOffre = document.getElementById('modalOffre');
//lorsque celui s'affiche :
modalOffre.addEventListener('show.bs.modal', event =>{
    //on récupère le bouton qui a été cliqué pour l'afficher
    const button = event.relatedTarget;
    //on récupère l'information permettant de déterminer si il s'agit d'un ajout de mail ou d'une modification
    const decision = button.getAttribute('data-bs-decision');
    const offre_id = location.href.split('/').pop();
    //on modifie le nom du modal avec l'id de l'offre
    if(decision == 'valider'){
        modalOffre.getElementsByClassName('modal-title')[0].textContent = `Souhaitez-vous ajouter un commentaire ?`;
        document.getElementById('localisation').parentElement.style.display = 'none';
        document.getElementById('entreprise').parentElement.style.display = 'none';
        document.getElementById('bouton_confirmation').innerText = 'Envoyer et accepter';
        document.getElementById('raison_personnalisee').previousElementSibling.firstElementChild.remove();
        document.getElementById('formulaire').setAttribute('action', '/offreEmploi/admin/accepterOffre/'+offre_id);
    }else{
        modalOffre.getElementsByClassName('modal-title')[0].textContent = `Précisez la raison du refus de l'offre : ${offre_id}`;
        document.getElementById('localisation').parentElement.style.display = 'inline-block';
        document.getElementById('entreprise').parentElement.style.display = 'inline-block';
        document.getElementById('bouton_confirmation').innerText = 'Envoyer et refuser';
        if(!document.getElementById('raison_personnalisee').previousElementSibling.firstElementChild){
            document.getElementById('raison_personnalisee').previousElementSibling.insertAdjacentHTML('afterbegin', '<p>Autres:</p>');
        }
        document.getElementById('formulaire').setAttribute('action', '/offreEmploi/admin/refuserOffre/'+offre_id);
    }
    document.getElementById('localisation').checked = false;
    document.getElementById('entreprise').checked = false;
    document.getElementById('raison_personnalisee').value = '';
})

document.getElementById('formulaire').addEventListener('submit', (e)=>{
    if(document.getElementById('bouton_confirmation').innerText == 'Envoyer et refuser'){
        if(document.getElementById('localisation').checked == false && document.getElementById('entreprise').checked == false && document.getElementById('raison_personnalisee').value == ''){
            e.preventDefault();
            alert('Veuillez préciser au moins un motif de refus.');
        }
    }
})