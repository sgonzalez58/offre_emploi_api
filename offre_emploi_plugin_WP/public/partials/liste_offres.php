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
global $wp_query;

$class = new Offre_emploi_Public('Offre_emploi','1.0.0');
$offres_valides = $class->getOffresValides();
$communes = $class->getAllCommunes();
$types_contrat = $class->getAllTypeContrat();
$metier = $class->getMetier();

$le_type_contrat='';
$la_commune='';

if( array_key_exists('thematique',$wp_query->query_vars) ){
    $nom_type_contrat = urldecode($wp_query->query_vars['thematique']);
}else{
    $nom_type_contrat = '';
}

if( array_key_exists('commune',$wp_query->query_vars) ){
    $la_commune = $class->get_commune_by_slug($wp_query->query_vars['commune']);
    $nom_commune = 'à '.$la_commune['nom_commune'];
}else{
    $nom_commune = 'dans la Nièvre';
}

add_action('wp_head', 'fc_opengraph');
function fc_opengraph() {

    global $nom_type_contrat;
    global $nom_commune;
    
    //$image = 'https://agenda.koikispass.com/public/'.$dateSEO[0]['image'];
    $titre =  '';
    if($nom_type_contrat!=''){$titre .=  $nom_type_contrat.' : ';}
    $titre .= 'Les offres d\'emploi ';
    if($nom_commune!=''){$titre .= $nom_commune;}

    echo '<meta property="og:title" content="' . esc_attr($titre).'" />';		


    //if($image) echo '<meta property="og:image" content="' . esc_url($image) . '" />';

}

add_filter('wpseo_title','date_title');
function date_title( $title ) {
    global $nom_type_contrat;
    global $nom_commune;
    
    $titre =  '';
    if($nom_type_contrat!=''){$titre .=  $nom_type_contrat.' : ';}
    $titre .= 'Les offres d\'emploi ';
    if($nom_commune!=''){$titre .= $nom_commune;}
    
    return $titre;
}

add_filter( 'wpseo_metadesc', 'date_metadesc', 10, 1 );
function date_metadesc( $wpseo_replace_vars ) { 

    global $la_commune;

    if($la_commune != ""){
        return "Découvrez les offres d'emploi à ".$la_commune['nom_commune'].", près de chez vous dans la Nièvre !";
    }else{
        return "Les emplois dans la Nièvre ? Découvrez les offres d'emploi proposées proche de chez vous !";
    }
}; 


get_header();

$path = $_SERVER['REQUEST_URI'];
$segments = explode('/', $path);


?>

<div id="primary" <?php generate_do_element_classes( 'content' ); ?>>
    <main id="main" <?php generate_do_element_classes( 'main' ); ?>>

    <div class="modal_filtre_offre_emploi" id="modal_filtre_localisation">
        <div class="close" style="position:relative;"><button onclick="close_filtre()"><span class="material-symbols-outlined">close</span></button></div>
        <div class="intertitre">Rechercher dans un secteur</div>
        <div class="search" style="position:relative;padding: 2em;">
            <input id="commune-autocomplete" placeholder="Où recherchez-vous ?"/>
            <span class="material-symbols-outlined">search</span>
        </div>
        <script>
            jQuery(document).ready(function() {
                let communes = [<?php foreach($communes as $commune) { echo '"' . $commune['nom_commune'] . '",'; } ?>];
                jQuery('#commune-autocomplete').autocomplete({
                    source: communes,
                    position: {
                        my: "left top",
                        at: "left bottom"
                    },
                    open: function(event, ui) {
                        jQuery(this).autocomplete('widget').addClass('invisible-suggestions');
                    }
                }).on('input', function() {
                    filtrerCommunes(jQuery(this).val());
                });

                filtrerCommunes('');

                jQuery('#commune-autocomplete').autocomplete({
                    source: communes,
                    position: {
                        my: "left top",
                        at: "left bottom"
                    },
                    select: function(event, ui) {
                        filtrerCommunes(ui.item.value);
                    }
                }).on('input', function() {
                    filtrerCommunes(jQuery(this).val());
                });
            });

            

            function filtrerCommunes(motCle) {
                jQuery('.liste_commune_filtre').hide();
                jQuery('.liste_commune_filtre').each(function() {
                    if (jQuery(this).text().toLowerCase().indexOf(motCle.toLowerCase()) !== -1) {
                        jQuery(this).show();
                    }
                });

                // Sélectionner toutes les div avec la classe ".liste_commune_filtre"
                let divsCommune = document.querySelectorAll('.liste_commune_filtre');

                // Parcourir chaque div
                divsCommune.forEach(function(div) {
                    // Récupérer le contenu affiché à l'écran
                    let contenu = div.innerText;
                    
                    // Vérifier si le contenu contient "(0)"
                    if (contenu.includes("(0)")) {
                        // Masquer la div en définissant la propriété CSS "display" sur "none"
                        div.style.display = "none";
                    }
                });
            }
        </script>
        <div class="filtre">
            <div class="intertitre2">Les plus recherchées</div>
            <div class="ui-group" id="filtre_flex_recherche">
                <?php
                $i = 0;
                    foreach( $communes as $commune ){
                        if($commune['nom_commune'] == 'Nevers' || $commune['nom_commune'] == 'Coulanges-lès-Nevers' || $commune['nom_commune'] == 'Cosne-Cours-sur-Loire' || $commune['nom_commune'] == 'La Charité-sur-Loire'){
                            if($segments[2] == 'categorie' || $segments[4] == "categorie"){
                                echo '<div class="liste_commune_filtre com_filtre_liste_emploi'.$i.' '.($segments[3] == $commune['slug'] ? "couleur_filtre" : "").'" value-group="commune"><a href="https://www.koikispass.com/offres-emploi/lieu/'.$commune['slug'].'/categorie/'.$segments[5].'">'.$commune['nom_commune'].'<input style="display:none" class="commune_filtre_tri_emploi com_filtre_tri'.$i.'" type="checkbox" name="commune[]" value="'. $commune['id'] .'" id="commune-'. $commune['id'] .'"><label style="width:100%;" for="commune-'. $commune['id'] .'">  </label></a></div>';
                                $i++;     
                            }else{
                                echo '<div class="liste_commune_filtre com_filtre_liste_emploi'.$i.' '.($segments[3] == $commune['slug'] ? "couleur_filtre" : "").'" value-group="commune"><a href="https://www.koikispass.com/offres-emploi/lieu/'.$commune['slug'].'/">'.$commune['nom_commune'].'<input style="display:none" class="commune_filtre_tri_emploi com_filtre_tri'.$i.'" type="checkbox" name="commune[]" value="'. $commune['id'] .'" id="commune-'. $commune['id'] .'"><label style="width:100%;" for="commune-'. $commune['id'] .'">  </label></a></div>';
                                $i++;  
                            }                     
                        }
                    }
                ?>
            </div>
        <div class="intertitre2">Communes</div>
        <div class="ui-group" id="filtre_flex">
            <?php
                $o = 4;
                foreach( $communes as $commune ){
                    if($commune['nom_commune'] == 'Nevers' || $commune['nom_commune'] == 'Coulanges-lès-Nevers' || $commune['nom_commune'] == 'Cosne-Cours-sur-Loire' || $commune['nom_commune'] == 'La Charité-sur-Loire'){
                    }else{
                        if($segments[2] == 'categorie' || $segments[4] == "categorie"){
                            echo '<div class="liste_commune_filtre com_filtre_liste_emploi'.$i.' '.($segments[3] == $commune['slug'] ? "couleur_filtre" : "").'" value-group="commune"><a href="https://www.koikispass.com/offres-emploi/lieu/'.$commune['slug'].'/categorie/'.$segments[5].'">'.$commune['nom_commune'].'<input style="display:none" class="commune_filtre_tri_emploi com_filtre_tri'.$i.'" type="checkbox" name="commune[]" value="'. $commune['id'] .'" id="commune-'. $commune['id'] .'"><label style="width:100%;" for="commune-'. $commune['id'] .'">  </label></a></div>';
                            $i++;     
                        }else{
                            echo '<div class="liste_commune_filtre com_filtre_liste_emploi'.$i.' '.($segments[3] == $commune['slug'] ? "couleur_filtre" : "").'" value-group="commune"><a href="https://www.koikispass.com/offres-emploi/lieu/'.$commune['slug'].'/">'.$commune['nom_commune'].'<input style="display:none" class="commune_filtre_tri_emploi com_filtre_tri'.$i.'" type="checkbox" name="commune[]" value="'. $commune['id'] .'" id="commune-'. $commune['id'] .'"><label style="width:100%;" for="commune-'. $commune['id'] .'">  </label></a></div>';
                            $i++;  
                        }   
                    }
                }
            ?>
        </div>
    </div>
</div>
<div class="modal_filtre_offre_emploi" id="modal_filtre_thematique">
    <div class="close" style="position:relative;"><button onclick="close_filtre()"><span class="material-symbols-outlined">close</span></button></div>
    <div class="intertitre">Rechercher une catégorie</div>
    <div class="search" style="position:relative;padding: 2em;">
        <input id="thematique-autocomplete" placeholder="Que recherchez-vous ?"/>
        <span class="material-symbols-outlined">search</span>
    </div>
    <script>
        jQuery(document).ready(function() {
            let thematiques = [<?php foreach($types_contrat as $thematique) { echo '"' . $thematique['nom'] . '",'; } ?>];

            jQuery('#thematique-autocomplete').autocomplete({
                source: thematiques,
                position: {
                    my: "left top",
                    at: "left bottom"
                },
                open: function(event, ui) {
                    jQuery(this).autocomplete('widget').addClass('invisible-suggestions');
                }
            }).on('input', function() {
                filtrerThematiques(jQuery(this).val());
            });

            filtrerThematiques('');

            jQuery('#thematique-autocomplete').autocomplete({
                source: thematiques,
                position: {
                    my: "left top",
                    at: "left bottom"
                },
                select: function(event, ui) {
                    filtrerThematiques(ui.item.value);
                }
            }).on('input', function() {
                filtrerThematiques(jQuery(this).val());
            });
        });

        

        function filtrerThematiques(motCle) {
            jQuery('.liste_thematique_filtre').hide();
            jQuery('.liste_thematique_filtre').each(function() {
                if (jQuery(this).text().toLowerCase().indexOf(motCle.toLowerCase()) !== -1) {
                    jQuery(this).show();
                }
            });

            // Sélectionner toutes les div avec la classe ".liste_thematique_filtre"
            let divsTheme = document.querySelectorAll('.liste_thematique_filtre');

            // Parcourir chaque div
            divsTheme.forEach(function(div) {
                // Récupérer le contenu affiché à l'écran
                let contenu = div.innerText;
                
                // Vérifier si le contenu contient "(0)"
                if (contenu.includes("(0)")) {
                    // Masquer la div en définissant la propriété CSS "display" sur "none"
                    div.style.display = "none";
                }
            });
        }
    </script>
    <div class="filtre">
        <div class="intertitre2">Types de contrat</div>
        <div class="ui-group" id="filtre_flex">
            <?php
                $ii = 0;
                foreach( $types_contrat as $thematique ){
                    if($segments[2] == 'lieu'){
                        if($segments[4] == 'categorie'){
                            echo '<div class="liste_type_contrat_filtre type_filtre_tri_emploi'.$ii.' '.($segments[5] == $thematique['type_contrat'] ? "couleur_filtre" : "").'" value-group="type_contrat"><a href="https://www.koikispass.com/offres-emploi/lieu/'.$segments[3].'/categorie/'.urlencode($thematique['type_contrat']).'">'.$thematique['type_contrat'].'<input style="display:none" class="type_contrat_filtre_tri_emploi" id="type_contrat-'.$thematique['type_contrat'].'" type="checkbox" name="theme[]" value="'. $thematique['type_contrat'] .'"><label style="width:100%;" for="type_contrat-'. $thematique['type_contrat'] .'">'. (0) .'</label></a></div>';
                            $ii++;   
                        }else{
                            echo '<div class="liste_type_contrat_filtre type_filtre_tri_emploi'.$ii.'" value-group="type_contrat"><a href="https://www.koikispass.com/offres-emploi/lieu/'.$segments[3].'/categorie/'.urlencode($thematique['type_contrat']).'">'.$thematique['type_contrat'].'<input style="display:none" class="type_contrat_filtre_tri_emploi" id="type_contrat-'.$thematique['type_contrat'].'" type="checkbox" name="theme[]" value="'. $thematique['type_contrat'] .'"><label style="width:100%;" for="type_contrat-'. $thematique['type_contrat'] .'">'. (0) .'</label></a></div>';
                            $ii++;  
                        }
                    }else{
                        echo '<div class="liste_type_contrat_filtre type_filtre_tri_emploi'.$ii.' '.($segments[3] == $thematique['type_contrat'] ? "couleur_filtre" : "").'" value-group="type_contrat"><a href="https://www.koikispass.com/offres-emploi/categorie/'.urlencode($thematique['type_contrat']).'">'.$thematique['type_contrat'].'<input style="display:none" class="type_contrat_filtre_tri_emploi" id="type_contrat-'.$thematique['type_contrat'].'" type="checkbox" name="theme[]" value="'. $thematique['type_contrat'] .'"><label style="width:100%;" for="type_contrat-'. $thematique['type_contrat'] .'">'. (0) .'</label></a></div>';
                        $ii++;	
                    }
                }
            ?>
        </div>
    </div>
</div>
<diV onclick="close_filtre()" class="overlay_filtre_offre_emploi" id="overlay_filtre_offre_emploi"></diV>
				<?php
				do_action( 'generate_before_main_content' );

				?>
				<article id="offre_emploi-liste" class="post type-post status-publish format-standard has-post-thumbnail hentry">
					<div>
	
						<div class="page-header-image-single grid-container grid-parent"></div>

						<?php
						if(!empty($offres_valides)){
						?>
						<header class="entry-header">
							<h1 class="entry-title" itemprop="headline" style="text-align:center;font-weight:bold;font-family:Helvetica, Arial, sans-serif"><?php if($nom_type_contrat!=''){echo $nom_type_contrat.' : ';}?>Les offres d'emplois <?php if($nom_commune!=''){echo $nom_commune;}?></h1>
						</header><!-- .entry-header -->
						<!--<div style="margin-top:40px;">
							<?php if($la_commune!=''){echo ($la_commune['description']);}?>
						</div>-->
						<div class="emploi-filters">
							<div class="button_tri_emploi filter-select">
								<button onclick="modal_thematique()"><span class="material-symbols-outlined">ballot</span>Types de contrat<span class="material-icons">expand_more</span></button>
								<style>
									.material-symbols-outlined {
									font-variation-settings:
									'FILL' 1,
									'wght' 400,
									'GRAD' 0,
									'opsz' 48
									}
								</style>
								<button onclick="modal_localisation()"><span class="material-icons">fmd_good</span>Localisation<span class="material-icons">expand_more</span></button>
							</div>
                            <div class="recherche_button">
                                <div id='distance'>
                                    <label for="liste_distance"><span class="material-symbols-outlined" id='distance_icon'>near_me</span></label>
                                    <input type='number' id='liste_distance' placeholder="Distance max (km)" min="0" oninput="validity.valid||(value='');">
                                </div>

                                <div class='recherche'>
                                    <label for="recherche_input"><span class="material-symbols-outlined" id='recherche_secteur_icon'>location_searching</span></label>
                                    <input id='recherche_input' type="text" minlength="1" maxlength="50" placeholder="Rechercher par poste">
                                </div>
                                <script>
                                    window.addEventListener('load', function(){
                                        let metiers = [<?php foreach($metier as $item) { echo '{ label: "' . $item['libelle'] . '", value: "' . $item['libelle'] . '" },'; } ?>];

                                        jQuery('#recherche_input').autocomplete({
                                            source: metiers,
                                            position: {
                                                my: 'left top',
                                                at: 'left bottom'
                                            }
                                        });
                                    });
                                </script>

                                <button id='recherche'><span class="material-symbols-outlined">search</span>Rechercher</button>

							</div>
						</div>
						<div class="legende_tri">
                            <span class='sous_titre_h1'>Nous trouvons plus de <?= floor(count($offres_valides) / 100) * 100 ?> offres d'emploi disponibles</span>
                            <a href='https://www.koikispass.com/offres-emploi/' id="reset_filter" class="supp_tri"><i style="font-size: 12px;color:#3D3D3D;margin-right:12px;" class="fa-solid fa-trash"></i>Supprimer les filtres</a>
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


	  