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


get_header(); ?>

		<div id="primary" <?php generate_do_element_classes( 'content' ); ?>>
			<main id="main" <?php generate_do_element_classes( 'main' ); ?>>

<div class='container'>
    <div class='d-flex justify-content-end'>
        <a type='button 'href='offreEmploi/creer' class='btn btn-primary'>Créer une offre d'emploi</a>
    </div>
    <table id="liste_offre_en_attente" class="table table-striped">
        <thead>
            <tr>
                <th>Titre de l'offre</th>
                <th>Ville</th>
                <th>Entreprise</th>
                <th>Date</th>
                <th>Etat</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

</main><!-- #main -->
</div><!-- #primary -->

		<?php

		generate_construct_sidebars();

	get_footer();