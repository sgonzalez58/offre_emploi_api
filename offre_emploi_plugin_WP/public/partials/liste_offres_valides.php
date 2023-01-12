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
$offres_valides = $class->model->findByOffreVisibles('visible', [], 50);
$offres_valides_max = $class->model->findByOffreVisibles('visible');
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
    <div class='ajout'>
        <a class='ajouter_offre' href='/offreEmploi/creer'><button>Créer une offre</button></a>
    </div>
</div>


<ul class='liste_offres'>
</ul>
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