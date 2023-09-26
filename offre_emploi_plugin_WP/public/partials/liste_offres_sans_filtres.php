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

$retour_emploi_filtre = '';

$pageActuelle = $_GET['page'];
if(!$pageActuelle){
    $pageActuelle = 1;
}

$recherche_input = $_GET['motClef'];
if(!$recherche_input){
    $recherche_input = '';
}else{
    $retour_emploi_filtre = '?motClef='.$recherche_input;
}

$distance_max = $_GET['distance'];
if(!$distance_max){
    $distance_max = 0;
}

$limit = $_SESSION['limit_offres_liste'];
if(!$limit){
    $limit = 30;
    $_SESSION['limit_offres_liste'] = $limit;
}

$class = new Offre_emploi_Public('Offre_emploi','1.0.0');

$nb_communes = $class->get_nb_communes($recherche_input);

$new_nb_commune = [];

$nb_communes = array_values($nb_communes);

foreach($nb_communes as $info_com){
    $new_nb_commune[$info_com['id_commune']] = $info_com['NbEvent'];
}

$nb_types_contrat = $class->get_nb_types_contrat($recherche_input);

$new_nb_type_contrat = [];

$nb_types_contrat = array_values($nb_types_contrat);

foreach($nb_types_contrat as $info_cont){
    $new_nb_type_contrat[$info_cont['nom']] = $info_cont['NbEvent'];
}

$offres_valides = $class->getOffresValides($recherche_input, null, [], $pageActuelle, $limit);
$nb_total_offre = count($class->getOffresValides($recherche_input, null, [], 1, ''));
$totalPages = ceil($nb_total_offre / $limit);

$communes = $class->getAllCommunes();
$types_contrat = $class->getAllTypeContrat();
$metier = $class->getMetier();

$le_type_contrat='';
$la_commune='';

$nom_type_contrat = '';
$nom_commune = 'dans la Nièvre';

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
                let nb_offre_com = {<?php foreach($new_nb_commune as $key=>$nb_com) { echo $key . ' : ' .$nb_com . ','; } ?>};
                jQuery('.liste_commune_filtre').hide();
                jQuery('.liste_commune_filtre').each(function() {
                    if (jQuery(this).text().toLowerCase().indexOf(motCle.toLowerCase()) !== -1 && nb_offre_com[this.firstElementChild.firstElementChild.value] > 0) {
                        jQuery(this).show();
                    }
                });

                let none = 0;
						let nonVisible = 0;

						// Sélectionner toutes les div avec la classe ".liste_commune_filtre"
						let divsCommune = document.querySelectorAll('.liste_commune_filtre');

						// Parcourir chaque div
						divsCommune.forEach(function(div) {
							// Récupérer le contenu affiché à l'écran
							let contenu = div.innerText;

							if(div.innerText == "Cosne-Cours-sur-Loire" || div.innerText == "Coulanges-lès-Nevers" ||div.innerText == "La Charité-sur-Loire" || div.innerText == "Nevers"){
								if(div.style.display != "none"){
									nonVisible++;
								}
							}else{
								if(div.style.display != "none"){
									none++;
								}
							}
							
							// Vérifier si le contenu contient "(0)"
							if (contenu.includes("(0)")) {
								// Masquer la div en définissant la propriété CSS "display" sur "none"
								div.style.display = "none";
							}
						});

						if(nonVisible == 0){
							document.getElementById('recherche_resultat').innerText = "";
						}else{
							document.getElementById('recherche_resultat').innerText = "Les plus recherchées";
						}
						if(none == 0){
							if(document.getElementById('recherche_resultat').innerText == ""){
								document.getElementById('commune_resultat').innerText = "Pas de résultat";
							}else{
								document.getElementById('commune_resultat').innerText = "";
							}
						}else{
							document.getElementById('commune_resultat').innerText = "Communes";
						}
            }
        </script>
        <div class="filtre">
            <div id="recherche_resultat" class="intertitre2">Les plus recherchées</div>
            <div class="ui-group" id="filtre_flex_recherche">
                <?php
                $i = 0;
                    foreach( $communes as $commune ){
                        if($commune['nom_commune'] == 'Nevers' || $commune['nom_commune'] == 'Coulanges-lès-Nevers' || $commune['nom_commune'] == 'Cosne-Cours-sur-Loire' || $commune['nom_commune'] == 'La Charité-sur-Loire'){
                            echo '<div class="liste_commune_filtre com_filtre_liste_emploi'.$i.'" value-group="commune" '. (key_exists($commune['id'], $new_nb_commune) ? '' : 'style="display:none"').'><a href="https://www.koikispass.com/offres-emploi/lieu/'.$commune['slug'].'/'. ($recherche_input ? ( $distance_max > 0 ? '?motClef='.$recherche_input.'&distance='.$distance_max : '?motClef='.$recherche_input ) : ( $distance_max > 0 ? '?distance='.$distance_max : '' ) ) .'">'.$commune['nom_commune'].'<input style="display:none" class="commune_filtre_tri_emploi com_filtre_tri'.$i.'" type="checkbox" name="commune[]" value="'. $commune['id'] .'" id="commune-'. $commune['id'] .'"></a></div>';
                            $i++;                            
                        }
                    }
                ?>
            </div>
        <div id="commune_resultat" class="intertitre2">Communes</div>
        <div class="ui-group" id="filtre_flex">
            <?php
                $o = 4;
                foreach( $communes as $commune ){
                    if($commune['nom_commune'] == 'Nevers' || $commune['nom_commune'] == 'Coulanges-lès-Nevers' || $commune['nom_commune'] == 'Cosne-Cours-sur-Loire' || $commune['nom_commune'] == 'La Charité-sur-Loire'){
                    }else{
                        echo '<div class="liste_commune_filtre com_filtre_liste_emploi'.$o.'" value-group="commune" '. (key_exists($commune['id'], $new_nb_commune) ? '' : 'style="display:none"').'><a href="https://www.koikispass.com/offres-emploi/lieu/'.$commune['slug'].'/'. ($recherche_input ? ( $distance_max > 0 ? '?motClef='.$recherche_input.'&distance='.$distance_max : '?motClef='.$recherche_input ) : ( $distance_max > 0 ? '?distance='.$distance_max : '' ) ) .'">'.$commune['nom_commune'].'<input style="display:none" class="commune_filtre_tri_emploi com_filtre_tri'.$o.'" type="checkbox" name="commune[]" value="'. $commune['id'] .'" id="commune-'. $commune['id'] .'"></a></div>';
                        $o++;
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
            let thematiques = [<?php foreach($types_contrat as $thematique) {echo '"' . $thematique['type_contrat'] . '",'; } ?>];

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
            let nb_offre_type = {<?php foreach($new_nb_type_contrat as $key=>$nb_type) { echo '"' . $key . '": ' . $nb_type . ','; } ?>};

            jQuery('.liste_type_contrat_filtre').hide();
            jQuery('.liste_type_contrat_filtre').each(function() {
                if (jQuery(this).text().toLowerCase().indexOf(motCle.toLowerCase()) !== -1 && nb_offre_type[this.firstElementChild.firstElementChild.value] > 0) {
                    jQuery(this).show();
                }
            });

            let divsTheme = document.querySelectorAll('.liste_type_contrat_filtre');
            let none = 0;
            // Parcourir chaque div
            divsTheme.forEach(function(div) {
                // Récupérer le contenu affiché à l'écran
                let contenu = div.innerText;

                if(div.style.display != "none"){
                    none++;
                }
                
                // Vérifier si le contenu contient "(0)"
                if (contenu.includes("(0)")) {
                    // Masquer la div en définissant la propriété CSS "display" sur "none"
                    div.style.display = "none";
                }
            });

            if(none == 0){
                document.getElementById('contrat_resultat').innerText = "Pas de résultat";
            }else{
                document.getElementById('contrat_resultat').innerText = "Types de contrat";
            }
        }
    </script>
    <div class="filtre">
        <div id="contrat_resultat" class="intertitre2">Types de contrat</div>
        <div class="ui-group" id="filtre_flex">
            <?php
                $ii = 0;
                foreach( $types_contrat as $thematique ){
                    echo '<div class="liste_type_contrat_filtre type_filtre_tri_emploi'.$ii.'" value-group="type_contrat" '. (key_exists($thematique['type_contrat'], $new_nb_type_contrat) ? '' : 'style="display:none"').'><a href="https://www.koikispass.com/offres-emploi/categorie/'.urlencode($thematique['type_contrat']).'/'. ($recherche_input ? ( $distance_max > 0 ? '?motClef='.$recherche_input.'&distance='.$distance_max : '?motClef='.$recherche_input ) : ( $distance_max > 0 ? '?distance='.$distance_max : '' ) ) .'">'.$thematique['type_contrat'].'<input style="display:none" class="type_contrat_filtre_tri_emploi" id="type_contrat-'.$thematique['type_contrat'].'" type="checkbox" name="theme[]" value="'. $thematique['type_contrat'] .'"></a></div>';
                    $ii++;	
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
                            <form class="recherche_button">
                                <div id='distance'>
                                    <label for="liste_distance"><span class="material-symbols-outlined" id='distance_icon'>near_me</span></label>
                                    <input type='number' id='liste_distance' name="distance" placeholder="Distance max (km)" min="0" oninput="validity.valid||(value='');">
                                </div>

                                <div class='recherche'>
                                    <label for='recherche_input'><span class='material-symbols-outlined' id='recherche_secteur_icon'>location_searching</span></label>
                                    <input id='recherche_input' type='text' name='motClef' maxlength='50' placeholder='Rechercher par poste'>
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

                                <button type='input' id='recherche'>
                                    <span class="material-symbols-outlined">search</span>
                                    <span>Rechercher</span>
                                </button>

							</form>
						</div>
						<div class="legende_tri">
                            <span class='sous_titre_h1'>Nous trouvons <?= $nb_total_offre . ($nb_total_offre > 1 ? " offres d'emploi disponibles" : " offre d'emploi disponible")?> </span>
                            <a href='https://www.koikispass.com/offres-emploi/' id="reset_filter" class="supp_tri"><i style="font-size: 12px;color:#3D3D3D;margin-right:12px;" class="fa-solid fa-trash"></i>Supprimer les filtres</a>
                        </div>

						<div id='liste_offres'>
                            <?php
                            foreach($offres_valides as $offre){
                                if($offre['ville_libelle'] && $offre['ville_libelle'] != 'Non renseigné' && $offre['id_pole_emploi']){
                                    $nomVille = explode('- ', $offre['ville_libelle'])[1];
                                }else{
                                    if($offre['ville_libelle'] && $offre['ville_libelle'] != 'Non renseigné' && !$offre['id_pole_emploi']){
                                        $nomVille = $offre['ville_libelle'];
                                    }
                                    else{
                                        $nomVille = 'Non renseigné';
                                    }
                                }
                                if(strlen($offre['description']) > 150){
                                    $description = substr($offre['description'], 0, 149) . '...';
                                }else{
                                    $description = $offre['description'];
                                }
                                if($offre['nom_entreprise']){
                                    if(strlen($offre['nom_entreprise']) > 23){
                                        $nomEntreprise = substr($offre['nom_entreprise'], 0, 22);
                                    }else{
                                        $nomEntreprise = $offre['nom_entreprise'];
                                    }
                                }else{
                                    $nomEntreprise = 'Aucun';
                                }
                                ?>

                            <div class='offre'>
                                <div class='corps_offre'>
                                    <h2><?=$offre['intitule']?></h2>
                                    <div class='details'>
                                        <div class='ville'>
                                            <i class='fa-solid fa-location-pin'></i>
                                            <h3><?=$nomVille?></h3>
                                        </div>
                                        <div class='contrat'>
                                            <i class='fa-solid fa-tag'></i>
                                            <h3><?=$offre['type_contrat']?></h3>
                                        </div>
                                    </div>
                                    <?php if($nomEntreprise != 'Aucun'){ ?>
                                    <h3 class='nom_entreprise'>Entreprise : <?=$nomEntreprise?></h3>
                                    <?php } ?>
                                    <p class='description'><?=$description?></p>
                                </div>
                                <a class='lien_fiche' href='/offres-emploi/<?=$offre['id']?>/<?=$retour_emploi_filtre?>'>
                                    <button class='bouton_lien_fiche'>Voir l'offre</button>
                                </a>
                                <a class='lien_fiche_big' href='/offres-emploi/<?=$offre['id']?>/<?=$retour_emploi_filtre?>'></a>
                            </div>

                                <?php
                            }
                            ?>
                        </div>
                        <div class="pagination">
                        <?php
                        $startPage = max($pageActuelle - 3, 1);
                        $endPage = min($startPage + 6, $totalPages);

                        if ($startPage > 1) {
                            $complement_lien = '?';
                            if($recherche_input != ''){
                                 $complement_lien == "?" ? $complement_lien .= 'motClef='. urlencode(strtolower($recherche_input)) : $complement_lien .= '&motClef[]='. urlencode(strtolower($recherche_input));
                            }
                            if($distance_max > 0){
                                $complement_lien == "?" ? $complement_lien .= 'distance='.$distance_max : $complement_lien .= '&distance='.$distance_max;
                            }
                            $complement_lien == "?" ? $complement_lien .= 'page=1' : $complement_lien .= '&page=1';
                            
                            echo '<a href="'.$complement_lien.'"><<</a>'; // Bouton pour la première page
                        }

                        if ($pageActuelle > 1) {
                            $complement_lien = '?';
                            if($recherche_input != ''){
                                $complement_lien == "?" ? $complement_lien .= 'motClef='. urlencode(strtolower($recherche_input)) : $complement_lien .= '&motClef[]='. urlencode(strtolower($recherche_input));
                            }
                            if($distance_max > 0){
                                $complement_lien == "?" ? $complement_lien .= 'distance='.$distance_max : $complement_lien .= '&distance='.$distance_max;
                            }
                            $complement_lien == "?" ? $complement_lien .= 'page='. ($pageActuelle - 1) : $complement_lien .= '&page='. ($pageActuelle - 1);
                            
                            echo '<a href="'.$complement_lien.'">&lt;</a>'; // Flèche pour la page précédente
                        }

                        for ($i = $startPage; $i <= $endPage; $i++) {
                            if ($i == $pageActuelle) {
                                echo '<strong>' . $i . '</strong>';
                            } else {
                                $complement_lien = '?';
                                if($recherche_input != ''){
                                    $complement_lien == "?" ? $complement_lien .= 'motClef='. urlencode(strtolower($recherche_input)) : $complement_lien .= '&motClef[]='. urlencode(strtolower($recherche_input));
                                }
                                if($distance_max > 0){
                                    $complement_lien == "?" ? $complement_lien .= 'distance='.$distance_max : $complement_lien .= '&distance='.$distance_max;
                                }
                                $complement_lien == "?" ? $complement_lien .= 'page='. $i : $complement_lien .= '&page='. $i;
                                
                                echo '<a href="'.$complement_lien.'">' . $i . '</a>'; 
                            }
                        }

                        if ($pageActuelle < $totalPages) {
                            $complement_lien = '?';
                            if($recherche_input != ''){
                                $complement_lien == "?" ? $complement_lien .= 'motClef='. urlencode(strtolower($recherche_input)) : $complement_lien .= '&motClef[]='. urlencode(strtolower($recherche_input));
                            }
                            if($distance_max > 0){
                                $complement_lien == "?" ? $complement_lien .= 'distance='.$distance_max : $complement_lien .= '&distance='.$distance_max;
                            }
                            $complement_lien == "?" ? $complement_lien .= 'page=' . ($pageActuelle + 1) : $complement_lien .= '&page=' . ($pageActuelle + 1);
                            
                            echo '<a href="'.$complement_lien.'">&gt;</a>'; // Flèche pour la page suivante
                        }

                        if ($endPage < $totalPages) {
                            $complement_lien = '?';
                            if($recherche_input != ''){
                                $complement_lien == "?" ? $complement_lien .= 'motClef='. urlencode(strtolower($recherche_input)) : $complement_lien .= '&motClef='. urlencode(strtolower($recherche_input));
                            }
                            if($distance_max > 0){
                                $complement_lien == "?" ? $complement_lien .= 'distance='.$distance_max : $complement_lien .= '&distance='.$distance_max;
                            }
                            $complement_lien == "?" ? $complement_lien .= 'page=' . $totalPages : $complement_lien .= '&page=' . $totalPages;
                            
                            echo '<a href="'.$complement_lien.'">>></a>'; // Bouton pour la dernière page
                        }

                        ?>

                            <div class='pagination_nb_offre'>
                                <select class='pagination_nb_offre_select'>
                                    <option value='6' <?=$limit == 6 ? 'selected' : ''?>>6 / page</option>
                                    <option value='12' <?=$limit == 12 ? 'selected' : ''?>>12 / page</option>
                                    <option value='30' <?=$limit == 30 ? 'selected' : ''?>>30 / page</option>
                                    <option value='60' <?=$limit == 60 ? 'selected' : ''?>>60 / page</option>
                                    <option value='90' <?=$limit == 90 ? 'selected' : ''?>>90 / page</option>
                                    <option value='120' <?=$limit == 120 ? 'selected' : ''?>>120 / page</option>
                                </select>
                            </div>

                        </div>

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


	  