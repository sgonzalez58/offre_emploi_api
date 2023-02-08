<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://www.koikispass.com
 * @since      1.0.0
 *
 * @package    Offre_emploi
 * @subpackage Offre_emploi/public/partials
 */


$class = new Offre_emploi_Public('Offre_emploi','1.0.0');
$offre = $class->model->findOneOffre($wp_query->query_vars['idOffreEmploi']);
date_default_timezone_set('Europe/Paris');
setlocale (LC_TIME, 'fr_FR');
get_header(); ?>

		<div id="primary" <?php generate_do_element_classes( 'content' ); ?>>
			<main id="main" <?php generate_do_element_classes( 'main' ); ?>>
<?php
    if(!empty($offre)){
?>

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
            <p class='date'>Offre créée le <?=date_i18n('l d F o, H:i:s', strtotime($offre['date_de_creation']))?></p>
        </div>
    </div>
    <?php
    if($offre['id_pole_emploi']){
    ?>
        <a href='<?=$offre['origine_offre']?>'><button id='bouton_postuler'>Postuler</button></a>
    <?php
    }else{
    ?>
        <button id='bouton_postuler'>Postuler</button>
    <?php
    }
    ?>
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
            <a href="https://www.openstreetmap.org/?mlat=<?=$offre['latitude']?>&amp;mlon=<?=$offre['longitude']?>#map=16/<?=$offre['latitude']?>/<?=$offre['longitude']?>&amp;layers=N">Afficher une carte plus grande</a>
        </small>
        <?php
        }
        ?>
    </div>
</div>

<div class='liste_boites'>
    <div class='boite'>
        <h4 class='titre_boite'>Information métier</h4>
        
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
    <div class='boite'>
        <h4 class='titre_boite'>Contrat</h4>
        <p><?=$offre['type_contrat_libelle']?></p>
    </div>
    <div class='boite'>
        <h4 class='titre_boite'>Experience</h4>
        <p><?=$offre['experience_libelle']?></p>
    </div>

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
    if($offre['duree_travail'] || $offre['duree_travail_convertie']){
    ?>
    
    <div class='boite'>
        <h4 class='titre_boite'>Durée</h4>
        
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

    <?php
    }
    if($offre['libelle_qualification'] ){
    ?>

    <div class='boite'>
        <h4 class='titre_boite'>Qualification</h4>
        <p><?=$offre['libelle_qualification']?></p>
    </div>
    
    <?php
    }
    if($offre['secteur_activite_libelle'] ){
    ?>
    <div class='boite'>
        <h4 class='titre_boite'>Secteur d'activité</h4>
        <p><?=$offre['secteur_activite_libelle']?></p>
    </div>
    <?php
    }
    ?>
</div>
<?php
    if(!$offre['id_pole_emploi']){
?>
    <div id='modal'>
        <div id='overlay'></div>
        <h3 style='color:white; z-index:11'>Formulaire de candidature</h3>
        <form id="envoie_candidature" method='post' action="candidature" class="centre">
            <button id='fermer_formulaire'>X</button>
            <input type='text' name='prenom' required minlength="3" maxlength="25" placeholder="Prénom">
            <input type='text' name='nom' required minlength="3" maxlength="25" placeholder="Nom">
            <?php
                if(is_user_logged_in()){
            ?>
            <input type="mail" name='mail' id='mail_form' required value="<?=wp_get_current_user()->user_email?>" placeholder="adresse mail">
            <?php
                }else{  
            ?>
            <input type="mail" name='mail' id='mail_form' required placeholder="adresse mail">
            <?php
                }
            ?>
            <textarea name='message' required placeholder="Votre demande"></textarea>
            <input type='submit' value="Envoyer">
        </form>
    </div>
    <?php
    }
?>

<?php
}else{
?>
    <p> Cette offre n'existe pas.</p>
<?php
}
?>
            </main><!-- #main -->
        </div><!-- #primary -->

		<?php

		generate_construct_sidebars();

	get_footer();
?>