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

        <p>Le lien que vous essayé d'atteindre n'est plus disponible.</p>
        <p>Si vous essayez de mettre à jour votre offre d'emploi, veuillez utiliser le lien fourni dans le dernier mail reçu.</p>
        <p>Si vous n'avez plus accès à votre dernier mail ou qu'il s'agît déjà du dernier mail reçu, veuillez contacter Koikispass.</p>

    </main><!-- #main -->

</div><!-- #primary -->

<?php

		generate_construct_sidebars();

	get_footer();
?>