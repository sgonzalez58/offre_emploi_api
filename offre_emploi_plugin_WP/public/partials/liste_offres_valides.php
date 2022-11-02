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
$offres_valides = $class->model->findByOffreValidation('valide', [], 50);
$offres_valides_max = $class->model->findByOffreValidation('valide');
$villes = $class->model->findAllCommunes();
get_header(); ?>

		<div id="primary" <?php generate_do_element_classes( 'content' ); ?>>
			<main id="main" <?php generate_do_element_classes( 'main' ); ?>>
<?php
    if(!empty($offres_valides)){
?>

<div class='tri_liste'>
    <div class='tri'>
        <div class='selection'>
            <label for='liste_ville'>
                Ville :
            </label>
            <select id='liste_ville'>
                <option></option>
                <?php
                foreach($villes as $ville){
                ?>
                <option value='<?=$ville['id']?>'><?=$ville['nom_commune']?></option>
                <?php
                }
                ?>
            </select>
        </div>

        <div class='selection'>
            <label for='liste_distance'>
                Distance maximum(km) :
            </label>
            <select id='liste_distance'>
                <option value='aucune' selected>0</option>
                <option value='10'>10</option>
                <option value='25'>25</option>
                <option value='50'>50</option>
                <option value='100'>100</option>
            </select>
        </div>
    </div>
<!-- <div class='ajout'>
        <a class='ajouter_offre' href='/offreEmploi/creer'><button>Créer une offre</button></a>
    </div>-->
</div>

<div class='liste_offre_header'>
    <button class='page_precedente bouton_pagination' disabled>precedent</button>
    <button class='page_actuelle bouton_pagination'>1</button>
    ...
    <button class='derniere_page bouton_pagination'><?=ceil(count($offres_valides_max) / 50)?></button>
    <button class='page_suivante bouton_pagination'>suivant</button>
</div>

<ul class='liste_offres'>
<?php
    foreach($offres_valides as $offre){
?>
    <li class='offre'>
        <div class='corps_offre'>
            <a class='lien_fiche' href='/offreEmploi/<?= $offre['id']?>'><h2><?=$offre['intitule']?></h2></a>
            <a href='https://www.openstreetmap.org/?mlat=<?=$offre['latitude']?>&mon=<?=$offre['longitude']?>#map=17/<?=$offre['latitude']?>/<?=$offre['longitude']?>&layers=N' target='_blank'>
                <h4 class='ville'><?=explode(' - ', $offre['ville_libelle'])[1]?>
                    <i class='fa-solid fa-map-pin'></i>
                </h4>
            </a>
        <?php
        if(strlen($offre['description']) > 150){
            $description = substr(htmlentities($offre['description']), 0, 150).'...';
        }else{
            $description = $offre['description'];
        }
        ?>
            <p id='description'><?=$description?></p>
        </div>
        <div class='entreprise_offre'>
        <?php
        if($offre['nom_entreprise']){
        ?>
            <p>Entreprise : <?=$offre['nom_entreprise']?></p>
        <?php
        }
        ?>
            <a class='lien_pole_emploi' href='<?=$offre['origine_offre']?>' target='_blank'>lien vers l'offre sur pole emploi.</a>
        </div>
    </li>
<?php
    }
    ?>
    
</ul>
<div class='liste_offre_footer'>
    <button class='page_precedente bouton_pagination' disabled>precedent</button>
    <button class='page_actuelle bouton_pagination'>1</button>
    ...
    <button class='derniere_page bouton_pagination'><?=ceil(count($offres_valides_max) / 50)?></button>
    <button class='page_suivante bouton_pagination'>suivant</button>
</div>
<?php
}else{
    ?>
    <p> Aucunes offres disponibles en ce moment.</p>
<?php
}
?>
</main><!-- #main -->
</div><!-- #primary -->

		<?php

		generate_construct_sidebars();

	get_footer();