$('#liste_ville').select2({
    placeholder: 'Selectionner une ville',
    allowClear : true
});

$('#liste_ville').on('select2:select', function(e){
    var data = e.params.data;
    $.ajax({
        type:'POST',
        url:'/offreEmploi/getVille',
        data:{ville:data.id, distance:document.getElementById('liste_distance').value},
        success:function(data){
            let liste_html = '';
            data.forEach(element => {
                let lien_fiche = "<a class='lien_fiche' href='/offreEmploi/"+ element['id']+"'><h2>"+ element['intitule']+"</h2></a>";
                if(element['lienMap'] != 'aucun'){
                    ville_offre = "<h4 class='ville'>"+element['nomVille']+"<a href='" + element['lienMap']+"' target='_blank'><i class='fa-solid fa-map-pin'></i></a></h4>";
                }else{
                    ville_offre = "<h4 class='ville'>"+element['nomVille']+"</h4>";
                }
                let description_offre = "<p>"+element['description']+'</p>';
                let corps_offre = "<div class='corps_offre'>"+lien_fiche+ville_offre+description_offre+"</div>"
                let entreprise_offre = '';
                if(element['nomEntreprise'] != 'Aucun'){
                    entreprise_offre = "<p>"+element['nomEntreprise']+'</p>'
                }
                let lien_pole_emploi = "<a class='lien_pole_emploi' href='"+element['lienOrigineOffre']+"'>lien vers l'offre sur pole emploi.</a>"
                let pied_offre= "<div class='entreprise_offre'>"+entreprise_offre+lien_pole_emploi+'</div>';
                liste_html += "<div class='offre'>"+corps_offre+pied_offre+"</div>";
            });
            $('.liste_offres').html(liste_html);
        },
        error:function(data){
            console.log(data.jsonMessage);
        }
    })
})

$('#liste_ville').on('select2:clear', function(e){
    $.ajax({
        type:'POST',
        url:'/offreEmploi/getAll',
        data:{},
        success:function(data){
            let liste_html = '';
            data.forEach(element => {
                let lien_fiche = "<a class='lien_fiche' href='/offreEmploi/"+ element['id']+"'><h2>"+ element['intitule']+"</h2></a>";
                if(element['lienMap'] != 'aucun'){
                    ville_offre = "<h4 class='ville'>"+element['nomVille']+"<a href='" + element['lienMap']+"' target='_blank'><i class='fa-solid fa-map-pin'></i></a></h4>";
                }else{
                    ville_offre = "<h4 class='ville'>"+element['nomVille']+"</h4>";
                }
                let description_offre = "<p>"+element['description']+'</p>';
                let corps_offre = "<div class='corps_offre'>"+lien_fiche+ville_offre+description_offre+"</div>"
                let entreprise_offre = '';
                if(element['nomEntreprise'] != 'Aucun'){
                    entreprise_offre = "<p>"+element['nomEntreprise']+'</p>'
                }
                let lien_pole_emploi = "<a class='lien_pole_emploi' href='"+element['lienOrigineOffre']+"'>lien vers l'offre sur pole emploi.</a>"
                let pied_offre= "<div class='entreprise_offre'>"+entreprise_offre+lien_pole_emploi+'</div>';
                liste_html += "<div class='offre'>"+corps_offre+pied_offre+"</div>";
            });
            $('.liste_offres').html(liste_html);
        },
        error:function(data){
            console.log(data.jsonMessage);
        }
    })
})

$('#liste_distance').select2();

$('#liste_distance').on('select2:select', function(e){
    var data = e.params.data;
    if(document.getElementById('liste_ville').value){
        $.ajax({
            type:'POST',
            url:'/offreEmploi/getVille',
            data:{ville:document.getElementById('liste_ville').value, distance:data.id},
            success:function(data){
                let liste_html = '';
                data.forEach(element => {
                    let lien_fiche = "<a class='lien_fiche' href='/offreEmploi/"+ element['id']+"'><h2>"+ element['intitule']+"</h2></a>";
                    let ville_offre = '';
                    if(element['lienMap'] != 'aucun'){
                        ville_offre = "<h4 class='ville'>"+element['nomVille']+"<a href='" + element['lienMap']+"' target='_blank'><i class='fa-solid fa-map-pin'></i></a></h4>";
                    }else{
                        ville_offre = "<h4 class='ville'>"+element['nomVille']+"</h4>";
                    }
                    let description_offre = "<p>"+element['description']+'</p>';
                    let corps_offre = "<div class='corps_offre'>"+lien_fiche+ville_offre+description_offre+"</div>"
                    let entreprise_offre = '';
                    if(element['nomEntreprise'] != 'Aucun'){
                        entreprise_offre = "<p>"+element['nomEntreprise']+'</p>'
                    }
                    let lien_pole_emploi = "<a class='lien_pole_emploi' href='"+element['lienOrigineOffre']+"'>lien vers l'offre sur pole emploi.</a>"
                    let pied_offre= "<div class='entreprise_offre'>"+entreprise_offre+lien_pole_emploi+'</div>';
                    liste_html += "<div class='offre'>"+corps_offre+pied_offre+"</div>";
                });
                $('.liste_offres').html(liste_html);
            },
            error:function(data){
                console.log(data.jsonMessage);
            }
        })
    }
})