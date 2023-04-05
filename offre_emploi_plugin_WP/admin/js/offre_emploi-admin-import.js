document.getElementById('import').addEventListener('click', importer_annonces);

function importer_annonces(){
    let import_status = document.createElement('p');
    import_status.innerText = 'Import en cours...';
    document.getElementById('import').insertAdjacentElement('afterend', import_status);
    jQuery.ajax({
        type:'POST',
        url:my_ajax_obj.ajax_url,
        data:{'_ajax_nonce' : my_ajax_obj.nonce, 'action' : 'importer_offres'},
        success:function(){
            import_status.innerText = "Import terminé";
        },
        error:function(){
            import_status.innerText = "Erreur import";
        }
    })
}