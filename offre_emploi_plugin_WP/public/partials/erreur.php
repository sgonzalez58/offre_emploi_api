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

        <p>Une erreur est survenue. Veuillez réessayer dans quelques minutes svp.</p>

    </main><!-- #main -->

</div><!-- #primary -->

<?php

		generate_construct_sidebars();

	get_footer();
?>