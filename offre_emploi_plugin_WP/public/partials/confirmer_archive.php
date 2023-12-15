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

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header();

?>

<div id="primary" <?php generate_do_element_classes( 'content' ); ?>>
    
    <main id="main" <?php generate_do_element_classes( 'main' ); ?>>

        <p>Votre offre à bien été mise à jour. Elle n'apparaît désormais plus dans la liste des offres d'emploi.</p>
        <p>Vous pouvez revenir sur la liste des offres d'emploi via <a href='https://koikispass.com/offres-emploi'>ce lien</a>

    </main><!-- #main -->

</div><!-- #primary -->

<?php

		generate_construct_sidebars();

	get_footer();
?>