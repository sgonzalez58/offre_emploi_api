<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.koikispass.com
 * @since      1.0.0
 *
 * @package    Offre_emploi
 * @subpackage Offre_emploi/admin/partials
 */

$class = new Offre_emploi_Admin('Offre_emploi','1.0.0');
$offre = $class->model->findOneOffre($_GET['id_offre']);
date_default_timezone_set('Europe/Paris');
setlocale (LC_TIME, 'fr_FR');
?>

		<div id="primary" <?php generate_do_element_classes( 'content' ); ?>>
			<main id="main" <?php generate_do_element_classes( 'main' ); ?>>
<?php
    if(!empty($offre)){
?>

<a href='/wp-admin/admin.php?page=gestion_offre_emploi'>retour</a>
<div id='fiche_head'>
    <div id='informations_principales'>
        <h2 id='intitule'><?=$offre['intitule']?></h2>
        <div id='adresse'>
            <i class="fa-solid fa-shop"></i>
            <p><?=$offre['nom_entreprise']?></p>
        </div>
        <?php
        if($offre['ville_libelle'] != 'Non renseigné'){
        ?>
        <div id='ville'>
            <i class="fa-solid fa-location-dot"></i>
            <p id='ville'><?=array_pop(explode(' - ', $offre['ville_libelle']))?></p>
        </div>
        <?php
        }
        ?>
        <div id='date_de_creation'>
            <i class="fa-solid fa-calendar-days"></i>
            <p class='date'>Offre créée le <?=date_i18n('l d F o, H:i:s', strtotime($offre['date_de_publication']))?></p>
        </div>
    </div>
</div>

<div id='fiche_content'>
    <p id='description'><span style='font-weight : bold'>Description</span><br><br><?=nl2br($offre['description'])?></p>
    <div class='carte'>
        <?php
        if($offre['latitude']){
        ?>
        <iframe frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.openstreetmap.org/export/embed.html?bbox=<?=($offre['longitude'] - 0.0360)?>%2C<?=($offre['latitude'] - 0.0133)?>%2C<?=($offre['longitude'] + 0.0360)?>%2C<?=($offre['latitude'] + 0.0133)?>&layer=mapnik&marker=<?=$offre['latitude']?>%2C<?=$offre['longitude']?>"></iframe>
        <br/>
        <small>
            <a href="https://www.openstreetmap.org/?mlat=<?=$offre['latitude']?>&amp;mlon=<?=$offre['longitude']?>#map=12/<?=$offre['latitude']?>/<?=$offre['longitude']?>&amp;layers=N">Afficher une carte plus grande</a>
        </small>
        <?php
        }
        ?>
    </div>
</div>

<div class='liste_boites'>
    <?php
    if($offre['libelle_metier']){
    ?>
    <div class='boite'>
        <h4 class='titre_boite'>Information métier</h4>
        <p><?=$offre['libelle_metier']?></p>
    </div>
    <?php
    }
    if($offre['type_contrat']){
    ?>
    <div class='boite'>
        <h4 class='titre_boite'>Contrat</h4>
        <p><?=$offre['type_contrat']?></p>
    </div>
    <?php
    }
    ?>
    <?php
    if($offre['nom_entreprise'] || $offre['numero_entreprise'] || $offre['mail_entreprise']){
    ?>

    <div class='boite'>
        <h4 class='titre_boite'>Entreprise</h4>
            
            <?php
            if($offre['nom_entreprise']){
            ?>

            <p><?=$offre['nom_entreprise']?></p>

            <?php
            }
            if($offre['getMailEntreprise']){
            ?>

            <p><?=$offre['getMailEntreprise']?></p>

            <?php
            }
            if($offre['numero_entreprise']){
            ?>

            <p><?=$offre['numero_entreprise']?></p>

            <?php
            }
            ?>
    </div>
    
    <?php
    }
    if($offre['salaire']){
    ?>
    
    <div class='boite'>
        <h4 class='titre_boite'>Salaire</h4>
        <p><?=$offre['salaire']?></p>
    </div>

    <?php
    }

    if($offre['secteur_activite'] ){
    ?>
    <div class='boite'>
        <h4 class='titre_boite'>Secteur d'activité</h4>
        <p><?=$offre['secteur_activite']?></p>
    </div>
    <?php
    }
    ?>
</div>
<div class='choix'>
    <button class='valider' data-bs-target='#modalOffre' data-bs-toggle='modal' data-bs-decision='valider'>Valider</button>
<?php
    if($offre['validation'] != 'refus'){
?>
    <button class='refuser' data-bs-target='#modalOffre' data-bs-toggle='modal' data-bs-decision='refuser'>Refuser</button>
<?php
    }else{
?>
    <button class='archiver' data-bs-target='#modalOffre' data-bs-toggle='modal' data-bs-decision='archiver' id='bouton_archiver'>Archiver</button>
<?php
    }
?>
</div>
<div class="modal fade" id="modalOffre" tabindex="-1" aria-labelledby="modalOffreLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalOffreLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label for='localisation'>
                    <p>
                        <input type='checkbox' id='localisation' name='localisation'>
                        La localisation et le nom de la commune ne sont pas renseignés.
                    </p>
                </label>
                <label for='entreprise'>
                    <p>
                        <input type='checkbox' id='entreprise' name='entreprise'>
                        Le nom de l'entreprise n'est pas renseigné.
                    </p>
                </label>
                <br>
                <div class='d-flex align-items-start justify-content-stretch'>
                    <label for='raison_personnalisee'>
                        <p>Autres:</p>
                    </label>
                    <textarea id='raison_personnalisee' class='form-control ms-1' name='raison_personnalisee'></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button id='closeModal' type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button id='bouton_confirmation' form='formulaire' type="button" class="btn btn-primary">Confirmer</button>
            </div>
        </div>
    </div>
</div>
<?php
    }else{
        echo "cette offre n'existe pas.";
    }
    ?>
            </main>
        </div>