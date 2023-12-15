const url = window.location.href;
let url_array = url.split('/');
let id = url_array[4];
_paq.push(['trackEvent', 'Emploi', 'Affichage', id]);

const bouton_postuler = document.getElementById('ouverture_formulaire_postuler');

bouton_postuler.addEventListener('click',()=>{
    _paq.push(['trackEvent', 'Emploi', 'Clic', id]);
})