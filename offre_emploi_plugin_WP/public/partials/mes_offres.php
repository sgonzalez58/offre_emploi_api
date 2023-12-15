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
                <h2 style='text-align:center'>Gestion de mes offres d'emploi</h2>
                <?php
					if($_GET['creation'] == 1){
						echo "<h4 class='text-success'>Votre offre d'emploi a bien été reçue.</h4>";
					}
					if($_GET['modification'] == 1){
						echo "<h4 class='text-success'>Votre offre d'emploi a bien été modifiée.</h4>";
					}
                    ?>

<div class='container'>
    <div id='offres_creees' style='margin-top: 0.65rem'>
        <div class='d-flex justify-content-end'>
            <a type='button 'href='/offres-emploi/creer' class='btn btn-primary'>Créer une offre d'emploi</a>
        </div>
        <table id="liste_offre_en_attente" class="table table-striped">
            <thead>
                <tr>
                    <th>Titre de l'offre</th>
                    <th>Ville</th>
                    <th>Recruteur</th>
                    <th>Date</th>
                    <th>Etat</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

</main><!-- #main -->
</div><!-- #primary -->

		<?php

		generate_construct_sidebars();

	get_footer();