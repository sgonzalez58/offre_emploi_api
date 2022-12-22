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
date_default_timezone_set('Europe/Paris');
setlocale (LC_TIME, 'fr_FR');
get_header(); ?>

		<div id="primary" <?php generate_do_element_classes( 'content' ); ?>>
			<main id="main" <?php generate_do_element_classes( 'main' ); ?>>
<?php
    
?>
<div class='reponse'>
    <p>Votre demande à bien été envoyée.</p>
    <div>
        <p>Cliquez sur ce bouton pour revenir sur les offres d'emploi.</p>
        <a href='^/offreEmploi'><button>Offres d'emploi</button><a>
    </div>
    <div>
        <p>Cliquez sur ce bouton pour vous rendre dans votre espace de gestion d'offres.</p>
        <button>Gestion</button>
    </div>
</div>

            </main><!-- #main -->
        </div><!-- #primary -->

		<?php

		generate_construct_sidebars();

	get_footer();
?>