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
            Object.entries(data).forEach(entry => {
                const [key, element] = entry;
                if(key != 'info'){
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
                }
            });
            $('.liste_offres').html(liste_html);
            if(data['info']['pageMax'] == 1){
                $('.page_actuelle').text('1');
                Object.values(document.getElementsByClassName('premiere_page')).forEach(element=>{
                    while(element.nextSibling.nodeName == '#text'){
                        element.nextSibling.remove();
                    }
                    element.remove();
                })
                Object.values(document.getElementsByClassName('derniere_page')).forEach(element=>{
                    while(element.previousSibling.nodeName == '#text'){
                        element.previousSibling.remove();
                    }
                    element.remove();
                })
                $('.page_precedente').attr('disabled', 'true');
                $('.page_suivante').attr('disabled', 'true');
            }else{
                if(data['info']['pageMax'] == 2){
                    $('.page_actuelle').text('1');
                    Object.values(document.getElementsByClassName('premiere_page')).forEach(element=>{
                        while(element.nextSibling.nodeName == '#text'){
                            element.nextSibling.remove();
                        }
                        element.remove();
                    })
                    if(document.getElementsByClassName('derniere_page').length != 0){
                        Object.values(document.getElementsByClassName('derniere_page')).forEach(element=>{
                            while(element.previousSibling.nodeName == '#text'){
                                element.previousSibling.remove();
                            }
                            element.innerText = 2;
                        })
                    }else{
                        Object.values(document.getElementsByClassName('page_actuelle')).forEach(element => {
                            let derniere_page = document.createElement('button');
                            derniere_page.setAttribute('class', 'derniere_page');
                            derniere_page.innerText = data['info']['pageMax'];
                            element.insertAdjacentElement('afterend', derniere_page);
                            derniere_page.addEventListener('click', dernierePage);    
                        })
                    }
                    $('.page_precedente').attr('disabled', 'true');
                    $('.page_suivante').removeAttr('disabled');
                }else{
                    $('.page_actuelle').text('1');
                    Object.values(document.getElementsByClassName('premiere_page')).forEach(element=>{
                        while(element.nextSibling.nodeName == '#text'){
                            element.nextSibling.remove();
                        }
                        element.remove();
                    })
                    if(document.getElementsByClassName('derniere_page').length != 0){
                        Object.values(document.getElementsByClassName('derniere_page')).forEach(element=>{
                            if(parseInt(element.innerText) == 2){
                                element.insertAdjacentText('beforebegin', ' ... ');
                            }
                            element.innerText = data['info']['pageMax'];
                        })
                    }else{
                        Object.values(document.getElementsByClassName('page_actuelle')).forEach(element => {
                            let derniere_page = document.createElement('button');
                            derniere_page.setAttribute('class', 'derniere_page');
                            derniere_page.innerText = data['info']['pageMax'];
                            element.insertAdjacentElement('afterend', derniere_page);
                            element.insertAdjacentText('afterend', ' ... ');
                            derniere_page.addEventListener('click', dernierePage);    
                        })
                    }
                    $('.page_precedente').attr('disabled', 'true');
                    $('.page_suivante').removeAttr('disabled');
                }
            }
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
            Object.entries(data).forEach(entry => {
                const [key, element] = entry;
                if(key != 'info'){
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
                }
            });
            $('.liste_offres').html(liste_html);
            if(data['info']['pageMax'] == 1){
                $('.page_actuelle').text('1');
                Object.values(document.getElementsByClassName('premiere_page')).forEach(element=>{
                    while(element.nextSibling.nodeName == '#text'){
                        element.nextSibling.remove();
                    }
                    element.remove();
                })
                Object.values(document.getElementsByClassName('derniere_page')).forEach(element=>{
                    while(element.previousSibling.nodeName == '#text'){
                        element.previousSibling.remove();
                    }
                    element.remove();
                })
                $('.page_precedente').attr('disabled', 'true');
                $('.page_suivante').attr('disabled', 'true');
            }else{
                if(data['info']['pageMax'] == 2){
                    $('.page_actuelle').text('1');
                    Object.values(document.getElementsByClassName('premiere_page')).forEach(element=>{
                        while(element.nextSibling.nodeName == '#text'){
                            element.nextSibling.remove();
                        }
                        element.remove();
                    })
                    if(document.getElementsByClassName('derniere_page').length != 0){
                        Object.values(document.getElementsByClassName('derniere_page')).forEach(element=>{
                            while(element.previousSibling.nodeName == '#text'){
                                element.previousSibling.remove();
                            }
                            element.innerText = 2;
                        })
                    }else{
                        Object.values(document.getElementsByClassName('page_actuelle')).forEach(element => {
                            let derniere_page = document.createElement('button');
                            derniere_page.setAttribute('class', 'derniere_page');
                            derniere_page.innerText = data['info']['pageMax'];
                            element.insertAdjacentElement('afterend', derniere_page);
                            derniere_page.addEventListener('click', dernierePage);    
                        })
                    }
                    $('.page_precedente').attr('disabled', 'true');
                    $('.page_suivante').removeAttr('disabled');
                }else{
                    $('.page_actuelle').text('1');
                    Object.values(document.getElementsByClassName('premiere_page')).forEach(element=>{
                        while(element.nextSibling.nodeName == '#text'){
                            element.nextSibling.remove();
                        }
                        element.remove();
                    })
                    if(document.getElementsByClassName('derniere_page').length != 0){
                        Object.values(document.getElementsByClassName('derniere_page')).forEach(element=>{
                            if(parseInt(element.innerText) == 2){
                                element.insertAdjacentText('beforebegin', ' ... ');
                            }
                            element.innerText = data['info']['pageMax'];
                        })
                    }else{
                        Object.values(document.getElementsByClassName('page_actuelle')).forEach(element => {
                            let derniere_page = document.createElement('button');
                            derniere_page.setAttribute('class', 'derniere_page');
                            derniere_page.innerText = data['info']['pageMax'];
                            element.insertAdjacentElement('afterend', derniere_page);
                            element.insertAdjacentText('afterend', ' ... ');
                            derniere_page.addEventListener('click', dernierePage);    
                        })
                    }
                    $('.page_precedente').attr('disabled', 'true');
                    $('.page_suivante').removeAttr('disabled');
                }
            }
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
                Object.entries(data).forEach(entry => {
                    const [key, element] = entry;
                    if(key != 'info'){
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
                    }
                });
                $('.liste_offres').html(liste_html);
                if(data['info']['pageMax'] == 1){
                    $('.page_actuelle').text('1');
                    Object.values(document.getElementsByClassName('premiere_page')).forEach(element=>{
                        while(element.nextSibling.nodeName == '#text'){
                            element.nextSibling.remove();
                        }
                        element.remove();
                    })
                    Object.values(document.getElementsByClassName('derniere_page')).forEach(element=>{
                        while(element.previousSibling.nodeName == '#text'){
                            element.previousSibling.remove();
                        }
                        element.remove();
                    })
                    $('.page_precedente').attr('disabled', 'true');
                    $('.page_suivante').attr('disabled', 'true');
                }else{
                    if(data['info']['pageMax'] == 2){
                        $('.page_actuelle').text('1');
                        Object.values(document.getElementsByClassName('premiere_page')).forEach(element=>{
                            while(element.nextSibling.nodeName == '#text'){
                                element.nextSibling.remove();
                            }
                            element.remove();
                        })
                        if(document.getElementsByClassName('derniere_page').length != 0){
                            Object.values(document.getElementsByClassName('derniere_page')).forEach(element=>{
                                while(element.previousSibling.nodeName == '#text'){
                                    element.previousSibling.remove();
                                }
                                element.innerText = 2;
                            })
                        }else{
                            Object.values(document.getElementsByClassName('page_actuelle')).forEach(element => {
                                let derniere_page = document.createElement('button');
                                derniere_page.setAttribute('class', 'derniere_page');
                                derniere_page.innerText = data['info']['pageMax'];
                                element.insertAdjacentElement('afterend', derniere_page);
                                derniere_page.addEventListener('click', dernierePage);    
                            })
                        }
                        $('.page_precedente').attr('disabled', 'true');
                        $('.page_suivante').removeAttr('disabled');
                    }else{
                        $('.page_actuelle').text('1');
                        Object.values(document.getElementsByClassName('premiere_page')).forEach(element=>{
                            while(element.nextSibling.nodeName == '#text'){
                                element.nextSibling.remove();
                            }
                            element.remove();
                        })
                        if(document.getElementsByClassName('derniere_page').length != 0){
                            Object.values(document.getElementsByClassName('derniere_page')).forEach(element=>{
                                while(element.previousSibling.nodeName == '#text'){
                                    element.previousSibling.remove();
                                }
                                element.insertAdjacentText('beforebegin', ' ... ')
                                element.innerText = data['info']['pageMax'];
                            })
                        }else{
                            Object.values(document.getElementsByClassName('page_actuelle')).forEach(element => {
                                let derniere_page = document.createElement('button');
                                derniere_page.setAttribute('class', 'derniere_page');
                                derniere_page.innerText = data['info']['pageMax'];
                                while(element.nextSibling.nodeName == '#text'){
                                    element.nextSibling.remove();
                                }
                                element.insertAdjacentElement('afterend', derniere_page);
                                element.insertAdjacentText('afterend', ' ... ');
                                derniere_page.addEventListener('click', dernierePage);    
                            })
                        }
                        $('.page_precedente').attr('disabled', 'true');
                        $('.page_suivante').removeAttr('disabled');
                    }
                }
            },
            error:function(data){
                console.log(data.jsonMessage);
            }
        })
    }
})

$('.page_precedente').on('click', pagePrecedente);

$('.premiere_page').on('click', premierePage);

$('.derniere_page').on('click', dernierePage);

$('.page_suivante').on('click', pageSuivante);

function pagePrecedente(){
    let urlAjax = '';
    if(document.getElementById('liste_ville').value){
        urlAjax = '/offreEmploi/getVille';
    }else{
        urlAjax = '/offreEmploi/getAll';
    }
    $.ajax({
        type:'POST',
        url: urlAjax,
        data:{page:parseInt(document.getElementsByClassName('page_actuelle')[0].innerText) - 1, ville:document.getElementById('liste_ville').value, distance:document.getElementById('liste_distance').value},
        success:function(data){
            if(parseInt(document.getElementsByClassName('page_actuelle')[0].innerText) <= '2'){
                Object.values(document.getElementsByClassName('page_actuelle')).forEach(value =>{
                    $('.premiere_page').remove();
                    value.innerText = 1;
                    value.previousElementSibling.toggleAttribute('disabled');
                });
            }else if(parseInt(document.getElementsByClassName('page_actuelle')[0].innerText) == '3'){
                Object.values(document.getElementsByClassName('page_actuelle')).forEach(value =>{
                    while(value.previousSibling.nodeName == '#text'){
                        value.previousSibling.remove();
                    }
                    value.innerText = 2;
                })
            }else{
                Object.values(document.getElementsByClassName('page_actuelle')).forEach(value =>{
                    value.innerText = parseInt(value.innerText) - 1;
                })
            }
            let derniere_page = document.getElementsByClassName('derniere_page');
            if(derniere_page.length == 0){
                Object.values(document.getElementsByClassName('page_suivante')).forEach(value =>{
                    let derniere_page = document.createElement('button');
                    derniere_page.setAttribute('class', 'derniere_page');
                    derniere_page.innerText = parseInt(document.getElementsByClassName('page_actuelle')[0].innerText) + 1;
                    value.removeAttribute('disabled');
                    value.insertAdjacentElement('beforebegin', derniere_page);
                    derniere_page.addEventListener('click', dernierePage);
                });
            }else{
                if(parseInt(document.getElementsByClassName('page_actuelle')[0].innerText) + 2 == derniere_page[0].innerText){
                    Object.values(derniere_page).forEach(value =>{
                        value.insertAdjacentText('beforebegin', ' ... ');
                    })
                }
            }
            let liste_html = '';
            Object.entries(data).forEach(entry => {
                const [key, element] = entry;
                if(key != 'info'){
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
                }
            });
            $('.liste_offres').html(liste_html);                
        }
    })
}

function premierePage(){
    let urlAjax = '';
    if(document.getElementById('liste_ville').value){
        urlAjax = '/offreEmploi/getVille';
    }else{
        urlAjax = '/offreEmploi/getAll';
    }
    $.ajax({
        type:'POST',
        url: urlAjax,
        data:{page:1, ville:document.getElementById('liste_ville').value, distance:document.getElementById('liste_distance').value},
        success:function(data){
            let derniere_page = document.getElementsByClassName('derniere_page');
            if(parseInt(document.getElementsByClassName('page_actuelle')[0].innerText) == 2){
                if(derniere_page.length == 0){
                    Object.values(document.getElementsByClassName('page_actuelle')).forEach(value =>{
                        value.setAttribute('class', 'derniere_page');
                        value.addEventListener('click', dernierePage);
                        value.nextElementSibling.toggleAttribute('disabled');
                    });
                    Object.values(document.getElementsByClassName('premiere_page')).forEach(value =>{
                        let page_actuelle = document.createElement('button');
                        page_actuelle.setAttribute('class', 'page_actuelle');
                        page_actuelle.innerText = value.innerText;
                        value.previousElementSibling.toggleAttribute('disabled');
                        value.insertAdjacentElement('beforebegin', page_actuelle);
                        value.remove();
                    });
                }else{
                    Object.values(document.getElementsByClassName('page_actuelle')).forEach(value =>{
                        value.innerText = 1;
                        value.previousElementSibling.remove();
                        while(value.previousSibling.nodeName == '#text'){
                            value.previousSibling.remove();
                        }
                        while(value.nextSibling.nodeName == '#text'){
                            value.nextSibling.remove();
                        }
                        value.insertAdjacentText('afterend', ' ... ')
                        value.previousElementSibling.toggleAttribute('disabled');
                    });
                }
            }else{
                if(derniere_page.length == 0){
                    Object.values(document.getElementsByClassName('page_actuelle')).forEach(value =>{
                        value.setAttribute('class', 'derniere_page');
                        value.addEventListener('click', dernierePage);
                        value.nextElementSibling.toggleAttribute('disabled');
                    });
                    Object.values(document.getElementsByClassName('premiere_page')).forEach(value =>{
                        let page_actuelle = document.createElement('button');
                        page_actuelle.setAttribute('class', 'page_actuelle');
                        page_actuelle.innerText = value.innerText;
                        value.previousElementSibling.toggleAttribute('disabled');
                        value.insertAdjacentElement('beforebegin', page_actuelle);
                        value.remove();
                    });
                }else{
                    if(parseInt(document.getElementsByClassName('page_actuelle')[0].innerText) == parseInt(derniere_page[0].innerText) - 1){
                        $('.page_actuelle').remove();
                        Object.values(document.getElementsByClassName('premiere_page')).forEach(value =>{
                            let page_actuelle = document.createElement('button');
                            page_actuelle.setAttribute('class', 'page_actuelle');
                            page_actuelle.innerText = value.innerText;
                            value.previousElementSibling.toggleAttribute('disabled');
                            value.insertAdjacentElement('beforebegin', page_actuelle);
                            value.remove();
                        });
                    }else{
                        Object.values(document.getElementsByClassName('page_actuelle')).forEach(value =>{
                            while(value.previousSibling.nodeName == '#text'){
                                value.previousSibling.remove();
                            }
                            value.remove();
                        });
                        Object.values(document.getElementsByClassName('premiere_page')).forEach(value =>{
                            let page_actuelle = document.createElement('button');
                            page_actuelle.setAttribute('class', 'page_actuelle');
                            page_actuelle.innerText = value.innerText;
                            value.previousElementSibling.toggleAttribute('disabled');
                            value.insertAdjacentElement('beforebegin', page_actuelle);
                            value.remove();
                        });
                    }
                }
            }
            let liste_html = '';
            Object.entries(data).forEach(entry => {
                const [key, element] = entry;
                if(key != 'info'){
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
                }
            });
            $('.liste_offres').html(liste_html);
        }
    })
}

function dernierePage(){
    let urlAjax = '';
    if(document.getElementById('liste_ville').value){
        urlAjax = '/offreEmploi/getVille';
    }else{
        urlAjax = '/offreEmploi/getAll';
    }
    $.ajax({
        type:'POST',
        url: urlAjax,
        data:{page:parseInt(document.getElementsByClassName('derniere_page')[0].innerText), ville:document.getElementById('liste_ville').value, distance:document.getElementById('liste_distance').value},
        success:function(data){
            let premiere_page = document.getElementsByClassName('premiere_page');
            if(parseInt(document.getElementsByClassName('page_actuelle')[0].innerText) == parseInt(document.getElementsByClassName('derniere_page')[0].innerText) - 1){
                if(premiere_page.length == 0){
                    Object.values(document.getElementsByClassName('page_actuelle')).forEach(value =>{
                        value.setAttribute('class', 'premiere_page');
                        value.addEventListener('click', premierePage);
                        value.previousElementSibling.toggleAttribute('disabled');
                    });
                    Object.values(document.getElementsByClassName('derniere_page')).forEach(value =>{
                        let page_actuelle = document.createElement('button');
                        page_actuelle.setAttribute('class', 'page_actuelle');
                        page_actuelle.innerText = value.innerText;
                        value.nextElementSibling.toggleAttribute('disabled');
                        value.insertAdjacentElement('beforebegin', page_actuelle);
                        value.remove();
                    });
                }else{
                    Object.values(document.getElementsByClassName('page_actuelle')).forEach(value =>{
                        value.innerText = parseInt(document.getElementsByClassName('derniere_page')[0].innerText);
                        value.nextElementSibling.remove();
                        while(value.previousSibling.nodeName == '#text'){
                            value.previousSibling.remove();
                        }
                        while(value.nextSibling.nodeName == '#text'){
                            value.nextSibling.remove();
                        }
                        value.insertAdjacentText('beforebegin', ' ... ')
                        value.nextElementSibling.toggleAttribute('disabled');
                    });
                }
            }else{
                if(premiere_page.length == 0){
                    Object.values(document.getElementsByClassName('page_actuelle')).forEach(value =>{
                        value.setAttribute('class', 'premiere_page');
                        value.addEventListener('click', premierePage);
                        value.previousElementSibling.toggleAttribute('disabled');
                    });
                    Object.values(document.getElementsByClassName('derniere_page')).forEach(value =>{
                        let page_actuelle = document.createElement('button');
                        page_actuelle.setAttribute('class', 'page_actuelle');
                        page_actuelle.innerText = value.innerText;
                        value.nextElementSibling.toggleAttribute('disabled');
                        value.insertAdjacentElement('beforebegin', page_actuelle);
                        value.remove();
                    });
                }else{
                    if(parseInt(document.getElementsByClassName('page_actuelle')[0].innerText) == 2){
                        $('.page_actuelle').remove();
                        Object.values(document.getElementsByClassName('derniere_page')).forEach(value =>{
                            let page_actuelle = document.createElement('button');
                            page_actuelle.setAttribute('class', 'page_actuelle');
                            page_actuelle.innerText = value.innerText;
                            value.nextElementSibling.toggleAttribute('disabled');
                            value.insertAdjacentElement('beforebegin', page_actuelle);
                            value.remove();
                        });
                    }else{
                        Object.values(document.getElementsByClassName('page_actuelle')).forEach(value =>{
                            while(value.previousSibling.nodeName == '#text'){
                                value.previousSibling.remove();
                            }
                            value.remove();
                        });
                        Object.values(document.getElementsByClassName('derniere_page')).forEach(value =>{
                            let page_actuelle = document.createElement('button');
                            page_actuelle.setAttribute('class', 'page_actuelle');
                            page_actuelle.innerText = value.innerText;
                            value.nextElementSibling.toggleAttribute('disabled');
                            value.insertAdjacentElement('beforebegin', page_actuelle);
                            value.remove();
                        });
                    }
                }
            }
            let liste_html = '';
            Object.entries(data).forEach(entry => {
                const [key, element] = entry;
                if(key != 'info'){
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
                }
            });
            $('.liste_offres').html(liste_html);
        }
    })
}

function pageSuivante(){
    let urlAjax = '';
    if(document.getElementById('liste_ville').value){
        urlAjax = '/offreEmploi/getVille';
    }else{
        urlAjax = '/offreEmploi/getAll';
    }
    $.ajax({
        type:'POST',
        url: urlAjax,
        data:{page:parseInt(document.getElementsByClassName('page_actuelle')[0].innerText) + 1, ville:document.getElementById('liste_ville').value, distance:document.getElementById('liste_distance').value},
        success:function(data){
            let premiere_page = document.getElementsByClassName('premiere_page');
            if(premiere_page.length == 0){
                Object.values(document.getElementsByClassName('page_precedente')).forEach(value =>{
                    let premiere_page = document.createElement('button');
                    premiere_page.setAttribute('class', 'premiere_page');
                    premiere_page.innerText = '1';
                    value.removeAttribute('disabled');
                    value.insertAdjacentElement('afterend', premiere_page);
                    premiere_page.addEventListener('click', premierePage);
                });
            }else{
                if(parseInt(document.getElementsByClassName('page_actuelle')[0].innerText) == 2){
                    Object.values(premiere_page).forEach(value=>{
                        value.insertAdjacentText('afterend', ' ... ');
                    })
                }
            }
            Object.values(document.getElementsByClassName('page_actuelle')).forEach(value =>{
                value.innerText = parseInt(value.innerText) + 1;
                if(parseInt(value.innerText) == parseInt(document.getElementsByClassName('derniere_page')[0].innerText) - 1){
                    while(value.nextSibling.nodeName == '#text'){
                        value.nextSibling.remove();
                    }
                }else{
                    if(parseInt(value.innerText) == parseInt(document.getElementsByClassName('derniere_page')[0].innerText)){
                        value.nextElementSibling.remove();
                        value.nextElementSibling.toggleAttribute('disabled');
                    }
                }
            });
            let liste_html = '';
            Object.entries(data).forEach(entry => {
                const [key, element] = entry;
                if(key != 'info'){
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
                }
            });
            $('.liste_offres').html(liste_html);
        }
    })
}