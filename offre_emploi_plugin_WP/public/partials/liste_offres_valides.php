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
$offres_valides = $class->model->findByOffreVisibles('visible', '');
$villes = $class->model->findAllCommunes();
get_header(); ?>

		<div id="primary" <?php generate_do_element_classes( 'content' ); ?>>
			<main id="main" <?php generate_do_element_classes( 'main' ); ?>>
<?php
    if(!empty($offres_valides)){
?>

<div class='tri_liste'>
    <div class='tri'>

        <div class="selection">
            <label for='mot_clef'>
                Recherche : 
            </label>
            <input type="text" minlength="3" maxlength="30">
            <button id='recherche'>go</button>
        </div>

        <div class='selection'>
            <label for='liste_ville'>
                Ville :
            </label>
            <select id='liste_ville'>
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

        <div class='selection'>
            <label for='liste_type_contrat'>
                Type de contrat :
            </label>
            <select id='liste_type_contrat'>
                <option value='' selected></option>
                <option value='CDD'>Contrat à durée déterminée</option>
                <option value='CDI'>Contrat à durée indéterminée</option>
                <option value='DDI'>CDD insertion</option>
                <option value='DIN'>CDI intérimaire</option>
                <option value='FRA'>Franchise</option>
                <option value='LIB'>Profession libérale</option>
                <option value='MIS'>Mission intérimaire</option>
                <option value='SAI'>Contrat travail saisonnier</option>
            </select>
        </div>
    </div>
    <div class='ajout'>
        <a class='ajouter_offre' href='/offreEmploi/creer'><button>Créer une offre</button></a>
    </div>
</div>

<div id='liste_offres'></div>
<div id='pagination_container' class='paginationjs-theme-red paginationjs-big'></div>

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