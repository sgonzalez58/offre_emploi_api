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
<div class='fiche'>
    <?php
    if(!empty($offre['latitude'])){
    ?>

    <div class='carte'>
        <iframe frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.openstreetmap.org/export/embed.html?bbox=<?=($offre['longitude'] - 0.0360)?>%2C<?=($offre['latitude'] - 0.0133)?>%2C<?=($offre['longitude'] + 0.0360)?>%2C<?=($offre['latitude'] + 0.0133)?>&layer=mapnik&marker=<?=$offre['latitude']?>%2C<?=$offre['longitude']?>" style="border: 1px solid black"></iframe>
        <br/>
        <small>
            <a href="https://www.openstreetmap.org/?mlat=<?=$offre['latitude']?>&amp;mlon=<?=$offre['longitude']?>#map=16/<?=$offre['latitude']?>/<?=$offre['longitude']?>&amp;layers=N">Afficher une carte plus grande</a>
        </small>
    </div>
    
    <?php
    }
    ?>
    <h2><?=$offre['intitule']?></h2>
    <?php
    if($offre['ville_libelle'] != 'Non renseigné'){
    ?>
        <h4><?=array_pop(explode('- ', $offre['ville_libelle']))?></h4>
    <?php
    }
    ?>
    <p>Offre créée le <?=date_i18n('l d F o, H:i:s', strtotime($offre['date_de_creation']))?></p>
    <p>Mise à jour le <?=date_i18n('l d F o, H:i:s', strtotime($offre['date_actualisation']))?></p>
    <p id='postes'><?=$offre['nb_postes']?> poste(s) à pourvoir</p>
    <div class='separation2'>********************</div>
    <p><?=nl2br($offre['description'])?></p>
    <div class='liste_boites' data-masonry='{ "itemSelector": ".boite", "columnWidth":".boite"}'>
        <div class='boite'>
            <h4 class='titre_boite'>Information métier</h4>
            <div class='corps_boite'>

            <?php
            if($offre['libelle_metier']){
            ?>

                <p><?=$offre['get_libelle_metier']?></p>

            <?php
            }
            if($offre['appellation_metier']){
            ?>

                <p><?=$offre['appellation_metier']?></p>

            <?php
            }
            ?>
            </div>
        </div>
        <div class='boite'>
            <h4 class='titre_boite'>Contrat</h4>
            <div class='corps_boite'>
                <p><?=$offre['type_contrat']?></p>
                <p><?=$offre['type_contrat_libelle']?></p>
                <p><?=$offre['nature_contrat']?></p>
            </div>
        </div>
        <div class='boite'>
            <h4 class='titre_boite'>Experience</h4>
            <div class='corps_boite'>
                <p><?=$offre['experience_libelle']?></p>
            </div>
        </div>

        <?php
        if($offre['nom_entreprise'] || $offre['numero_entreprise'] || $offre['mail_entreprise']){
        ?>

        <div class='boite'>
            <h4 class='titre_boite'>Entreprise</h4>
            <div class='corps_boite'>

                <?php
                if($offre['nom_entreprise']){
                ?>

                <p><?=$offre['nom_entreprise']?></p>

                <?php
                }
                if($offre['mail_entreprise']){
                ?>

                <p><?=$offre['mail_entreprise']?></p>

                <?php
                }
                if($offre['numero_entreprise']){
                ?>

                <p><?=$offre['numero_entreprise']?></p>

                <?php
                }
                ?>

            </div>
        </div>
        
        <?php
        }
        if($offre['salaire']){
        ?>
        
        <div class='boite'>
            <h4 class='titre_boite'>Salaire</h4>
            <div class='corps_boite'>
                <p><?=$offre['salaire']?></p>
            </div>
        </div>

        <?php
        }
        if($offre['duree_travail'] || $offre['duree_travail_convertie']){
        ?>
        
        <div class='boite'>
            <h4 class='titre_boite'>Durée</h4>
            <div class='corps_boite'>
            
                <?php
                if($offre['duree_travail']){
                ?>

                <p><?=$offre['duree_travail']?></p>

                <?php
                }
                if($offre['duree_travail_convertie']){
                ?>

                <p><?=$offre['duree_travail_convertie']?></p>

                <?php
                }
                ?>

            </div>
        </div>

        <?php
        }
        if($offre['libelle_qualification'] ){
        ?>

        <div class='boite'>
            <h4 class='titre_boite'>Qualification</h4>
            <div class='corps_boite'>
                <p><?=$offre['libelle_qualification']?></p>
            </div>
        </div>
        
        <?php
        }
        if($offre['secteur_activite_libelle'] ){
        ?>
        <div class='boite'>
            <h4 class='titre_boite'>Secteur d'activité</h4>
            <div class='corps_boite'>
                <p><?=$offre['secteur_activite_libelle']?></p>
            </div>
        </div>
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