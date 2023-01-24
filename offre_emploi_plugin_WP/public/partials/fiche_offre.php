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

<div class='fiche'>

    <?php
    if($offre['latitude']){
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
    if($offre['id_pole_emploi']){
    ?>

    <a href='<?=$offre['origine_offre']?>'><button>Postuler</button></a>
    
    <?php
    }
    ?>
    
    <h2 id='intitule' style="margin-top:24px"><?=$offre['intitule']?></h2>
    
    <?php
    if($offre['ville_libelle'] != 'Non renseigné'){
    ?>
    
    <h4 id='ville'><?=explode(' - ', $offre['ville_libelle'])[1]?></h4>

    <?php
    }
    ?>

    <p class='date'>Offre créée le <?=date_i18n('l d F o, H:i:s', strtotime($offre['date_de_creation']))?></p>
    <p class='date'>Mise à jour le <?=date_i18n('l d F o, H:i:s', strtotime($offre['date_actualisation']))?></p>
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
        <?php
        }
        ?>
    </div>
    <?php
        if($offre['id_pole_emploi']){
            ?>
            <div class="centre">
                <a href='<?=$offre['origine_offre']?>'><button>Postuler</button></a>
            </div>
            <?php
        }
    ?>

</div>
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