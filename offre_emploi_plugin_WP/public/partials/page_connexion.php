<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
global $wp_query; 

	add_filter('wpseo_robots', 'yoast_no_home_noindex', 999);
    function yoast_no_home_noindex($string= "") {
                    $string= "noindex, nofollow";
        
        return $string;
    }
	
/* 	add_action('wp_head', 'fc_opengraph');
	function fc_opengraph() {

		echo '<meta name="robots" content="noindex, nofollow">';
	}
 */
get_header(); ?>

	<div id="primary" <?php generate_do_element_classes( 'content' ); ?>>
		<main id="main" <?php generate_do_element_classes( 'main' ); ?>>
			<?php
			do_action( 'generate_before_main_content' );

			?>
			<article id="inscription" class=" post type-post status-publish format-standard has-post-thumbnail hentry">
				<div class="inside-article">
					<div class="page-header-image-single grid-container grid-parent"></div>

					<?php

						$inscription = $_GET['inscription'];

						if($inscription){
					?>

					<header class="entry-header">
						<h1 class="entry-title" itemprop="headline">Inscription</h1>			
					</header><!-- .entry-header -->

					
					<div class="entry-content" itemprop="text">
						<?php echo do_shortcode('[ultimatemember form_id="58585"]');?>
					</div><!-- .entry-content -->		

					<p style='text-align:center'>Vous avez déjà un compte ? <a href='?'>Connectez-vous</a></p>

					<div style='display:flex; justify-content:center;'>
						<a style='border-radius:3px; background-color:#D90012; padding: 15px 30px; text-transform:uppercase; margin:auto; color:white;' href='?'>Connectez-vous</a>
					</div>

					<?php
						}else{
					?>
					
					<header class="entry-header">
						<h1 class="entry-title" itemprop="headline">Connexion</h1>			
					</header><!-- .entry-header -->

					
					<div class="entry-content" itemprop="text">
						<?php echo do_shortcode('[ultimatemember form_id="58586"]');?>
					</div><!-- .entry-content -->

					<p style='text-align:center'>Vous n'avez pas encore de compte ? <a href='?inscription=1'>Inscrivez-vous</a></p>

					<div style='display:flex; justify-content:center;'>
						<a style='border-radius:3px; background-color:#D90012; padding: 15px 30px; text-transform:uppercase; margin:auto; color:white;' href='?inscription=1'>Inscrivez-vous</a>
					</div>

					<?php
						}
					?>

					<footer class="entry-meta">

					</footer><!-- .entry-meta -->
				</div><!-- .inside-article -->
			</article>
			<?php

			do_action( 'generate_after_main_content' );
			?>
		</main><!-- #main -->
	</div><!-- #primary -->

	<?php
	
	do_action( 'generate_after_primary_content_area' );

	generate_construct_sidebars();

get_footer();