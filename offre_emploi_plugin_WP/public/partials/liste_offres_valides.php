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
$offres_valides = $class->model->findByMotsClef();
$villes = $class->model->findAllCommunes();
$types_contrat = $class->model->getAllTypeContrat();
get_header(); ?>

		<div id="primary" <?php generate_do_element_classes( 'content' ); ?>>
			<main id="main" <?php generate_do_element_classes( 'main' ); ?>>
<?php
    if(!empty($offres_valides)){
?>

<div class="head_liste_offre">
    <div class='title'>
        <h1>Nos offres d'emploi</h1>
        <span class='sous_titre_h1'>Plus de <?= floor(count($offres_valides) / 100) * 100 ?> offres d'emploi disponibles</span>
    </div>

    <div class='ajout'>
        <a class='ajouter_offre' href=''><button id="ajout_bouton" disabled><i class="fa-solid fa-circle-plus"></i>Créer une offre</button></a>
        <h4>Bientôt disponible, l'ajout de vos offres d'emploi.</h4>
    </div>
</div>

<div class='recherche'>
    <label for="recherche_input"><i class="fa-solid fa-magnifying-glass" id='recherche_icon'></i></label>
    <input id='recherche_input' type="text" minlength="3" maxlength="30" placeholder="Rechercher un poste">

</div>
<p id='filtre_label'>Filtrer votre recherche</p>
<div id='liste_filtres'>
    <div id="filtre_recherche">
        <div id='type_contrat'>
            <label for="liste_type_contrat"><i class="fa-solid fa-suitcase" id='type_metier_icon'></i></label>
            <label class='sort_icon' for="liste_type_contrat"><i class="fa-solid fa-sort"></i></label>
            <select id='liste_type_contrat'>
                <option value='' selected></option>
                <?php
                foreach($types_contrat as $type_contrat){
                ?>
                <option value='<?=$type_contrat['type_contrat']?>'><?=$type_contrat['type_contrat']?></option>
                <?php
                }
                ?>
            </select>
        </div>

        <div id='commune'>
            <label for='liste_ville'><i class="fa-solid fa-location-dot" id='ville_icon'></i></label>
            <label class='sort_icon' for="liste_ville"><i class="fa-solid fa-sort"></i></label>
            <select class='liste_ville' id='liste_ville'>
                <option></option>
                <?php
                foreach($villes as $ville){
                    if($ville['nom_departement'] == 'Nièvre'){
                ?>
                <option value='<?=$ville['id']?>'><?=$ville['nom_commune']?></option>
                <?php
                    }
                }
                ?>
            </select>
        </div>

        <div id='distance'>
            <label for="liste_distance"><i class="fa-solid fa-paper-plane" id='distance_icon'></i></label>
            <input type='number' id='liste_distance' placeholder="Distance maximum (km)" min="0" oninput="validity.valid||(value='');">
        </div>
    </div>

    <button id='recherche'>Valider</button>
</div>

<div id='liste_offres'></div>
<div class='paginationjs-theme-red paginationjs-big pagination_container'></div>

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