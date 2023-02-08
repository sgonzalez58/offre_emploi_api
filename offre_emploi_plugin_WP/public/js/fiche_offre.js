jQuery('#envoie_candidature').one('submit', function(e){
    e.preventDefault();
    jQuery(this).unbind();
    jQuery.ajax({
        type:"POST",
        url:mes_candidature_ajax.ajax_url,
        data:{_ajax_nonce:mes_candidature_ajax.nonce, action:'get_candidatures', id_offre:mes_candidature_ajax.id_offre_emploi, mail:document.getElementById('mail_form').value},
        success:function(data){
            data=data['data'];
            if(data['nombre_de_demande'] >= 3){
                alert("Vous avez dépassé le nombre de candidature autorisé sur cette offre.");
            }else{
                jQuery(this).submit();
            }
        },
        error:function(data){
            console.log(data);
        }
    })
})

document.getElementById('fermer_formulaire').addEventListener('click', function(e){
    e.preventDefault();
    document.getElementById('modal').style.display = 'none';
});
document.getElementById('bouton_postuler').addEventListener('click', ()=>{document.getElementById('modal').style.display = 'flex'});
document.getElementById('overlay').addEventListener('click', ()=>{document.getElementById('modal').style.display = 'none';})