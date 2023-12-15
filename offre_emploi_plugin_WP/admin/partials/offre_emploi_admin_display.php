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

    <a class='modifier' href='/wp-admin/admin.php?page=gestion_offre_emploi&id_offre=<?=$offre['id']?>&edit=1'>Modifier</a>

    <a class='valider' href='/wp-admin/admin.php?page=gestion_offre_emploi&id_offre=<?=$offre['id']?>&valider=1'>Valider</a>
<?php
    if($offre['validation'] != 'refus'){
?>
    <a class='refuser' href='/wp-admin/admin.php?page=gestion_offre_emploi&id_offre=<?=$offre['id']?>&refuser=1'>Refuser</a>
<?php
    }else{
?>
    <a class='archiver' href='/wp-admin/admin.php?page=gestion_offre_emploi&id_offre=<?=$offre['id']?>&archiver=1'>Archiver</a>
<?php
    }
?>
</div>
<?php
    }else{
        echo "cette offre n'existe pas.";
    }
    ?>
            </main>
        </div>