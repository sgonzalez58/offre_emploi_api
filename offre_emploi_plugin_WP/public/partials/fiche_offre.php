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
$offre = $class->findOneOffre($wp_query->query_vars['idOffreEmploi']);
date_default_timezone_set('Europe/Paris');
setlocale (LC_TIME, 'fr_FR');

$nom_offre = $offre['intitule'];
$entreprise_offre = $offre['nom_entreprise'];
$ville_offre = $offre['ville_libelle'];
$description_offre = nl2br($offre['description']);

$lien_retour = '/offres-emploi/';

if($_GET['commune']){
    $lien_retour .= 'lieu/'.$_GET['commune'].'/';
}
if($_GET['thematique']){
    $lien_retour .= 'categorie/'.$_GET['thematique'].'/';
}
if($_GET['distance']){
    $lien_retour .= '?distance='.$_GET['distance'];
    if($_GET['motClef']){
        $lien_retour .= '&motClef='.$_GET['motClef'];
    }
    if($_GET['page']){
        $lien_retour .= '&page='.$_GET['page'];
    }
}else{
    if($_GET['motClef']){
        $lien_retour .= '?motClef='.$_GET['motClef'];
        if($_GET['page']){
            $lien_retour .= '&page='.$_GET['page'];
        }
    }else{
        if($_GET['page']){
            $lien_retour .= '?page='.$_GET['page'];
        }
    }
}


add_action('wp_head', 'fc_opengraph');
function fc_opengraph() {

    global $nom_offre;
    global $entreprise_offre;
    global $ville_offre;

    //$image = 'https://agenda.koikispass.com/public/'.$dateSEO[0]['image'];
    $titre =  $nom_offre;
    if($entreprise_offre!=''){$titre .= ' chez ' . $entreprise_offre;}
    if($ville_offre!='Non renseigné'){$titre .=  ' à ' . $ville_offre;}

    echo '<meta property="og:title" content="' . esc_attr($titre).'" />';		


    //if($image) echo '<meta property="og:image" content="' . esc_url($image) . '" />';

}

add_filter('wpseo_title','date_title');
function date_title( $title ) {
    
    global $nom_offre;
    global $entreprise_offre;
    global $ville_offre;

    //$image = 'https://agenda.koikispass.com/public/'.$dateSEO[0]['image'];
    $titre =  $nom_offre;
    if($entreprise_offre!=''){$titre .= ' chez ' . $entreprise_offre;}
    if($ville_offre!='Non renseigné'){$titre .=  ' à ' . $ville_offre;}
    
    return $titre;
}

add_filter( 'wpseo_metadesc', 'date_metadesc', 10, 1 );
function date_metadesc( $wpseo_replace_vars ) { 

    global $description_offre;

    return strlen($description_offre) > 160 ? substr($description_offre, 0, 156) . '...' : $description_offre;
}; 


get_header();

?>

		<div id="primary" <?php generate_do_element_classes( 'content' ); ?>>
            <?php
					if($_GET['postule'] == 1){
						echo "<p class='text-success'>Votre candidature a bien été envoyée.</p>";
					}
                    ?>
<?php
    if(!empty($offre)){
?>
            <main id="main" <?php generate_do_element_classes( 'main' ); ?>>

<div id='offre'>

    <div id='fiche_offre'>
        <a id='retour_offre_liste' href='<?=$lien_retour?>'><span class="material-symbols-outlined">turn_left</span>Offres d'emploi</a>
        <div id='informations_principales'>
            <?php 
                if($offre['image']!=''){
                    echo '<img src="'.$offre['image'].'"/>';
                }
            ?>
            <h1 id='intitule'><?=$offre['intitule']?></h1>
            <div class='info_secondaire'>
                <?php
                if($entreprise_offre != ''){
                ?>
                <div id='adresse'>
                    <i class="fa-solid fa-shop"></i>
                    <p><?=$offre['nom_entreprise']?></p>
                </div>
                <?php
                }
                ?>
                <?php
                if($offre['ville_libelle'] != 'Non renseigné'){
                ?>
                <div id='ville'>
                    <i class="fa-solid fa-location-dot"></i>
                    <p id='ville'><?=array_pop(explode(' - ', $offre['ville_libelle']))?></p>
                </div>
                <?php
                }
                ?>
                <div id='date_de_creation'>
                    <i class="fa-solid fa-calendar-days"></i>
                    <p class='date'>Offre créée le <?=date_i18n('l d F o, H:i:s', strtotime($offre['date_de_publication']))?></p>
                </div>
            </div>
        </div>
        <?php
        if($offre['id_jobijoba']){
        ?>
            <a href='<?=$offre['lien_jj']?>'><button id='bouton_postuler'>Postuler</button></a>
        <?php
        }else{
        ?>
            <div class="elementor-element elementor-element-1281c89 elementor-align-justify elementor-widget elementor-widget-button" data-id="1281c89" data-element_type="widget" data-widget_type="button.default">
				<div class="elementor-widget-container">
					<div class="elementor-button-wrapper">
                        <a id='ouverture_formulaire_postuler' class="elementor-button elementor-button-link elementor-size-sm lien_postuler" href="#elementor-action%3Aaction%3Dpopup%3Aopen%26settings%3DeyJpZCI6IjExNDU3NiIsInRvZ2dsZSI6ZmFsc2V9">
                            <span class="elementor-button-content-wrapper">
                                <span class="elementor-button-text">Postuler</span>
                            </span>
                        </a>
		            </div>
				</div>
            </div>
        <?php
        }
        ?>

        <p id='description'>Description</p>
        <p><?=nl2br($offre['description'])?></p>

        <div class='carte'>
            <?php
            if($offre['latitude']){
            ?>
            <iframe frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.openstreetmap.org/export/embed.html?bbox=<?=($offre['longitude'] - 0.073)?>%2C<?=($offre['latitude'] - 0.025)?>%2C<?=($offre['longitude'] + 0.073)?>%2C<?=($offre['latitude'] + 0.025)?>&layer=mapnik&marker=<?=$offre['latitude']?>%2C<?=$offre['longitude']?>"></iframe>
            <br/>
            <small>
                <a href="https://www.openstreetmap.org/?mlat=<?=$offre['latitude']?>&amp;mlon=<?=$offre['longitude']?>#map=12/<?=$offre['latitude']?>/<?=$offre['longitude']?>&amp;layers=N">Afficher une carte plus grande</a>
            </small>
            <?php
            }
            ?>
        </div>
    </div>

    <div class='liste_boites'>
        <?php
        if($offre['libelle_metier']){
        ?>
        <div class='boite'>
            <p class='titre_boite'>Information métier</p>
            <p><?=$offre['libelle_metier']?></p>
        </div>
        <?php
        }
        if($offre['type_contrat']){
        ?>
        <div class='boite'>
            <p class='titre_boite'>Contrat</p>
            <p><?=$offre['type_contrat']?></p>
        </div>
        <?php
        }
        ?>
        <?php
        if($offre['nom_entreprise'] || $offre['numero_entreprise'] || $offre['mail_entreprise']){
        ?>

        <div class='boite'>
            <p class='titre_boite'>Entreprise</p>
                
                <?php
                if($offre['nom_entreprise']){
                ?>

                <p><?=$offre['nom_entreprise']?></p>

                <?php
                }
                if($offre['getMailEntreprise']){
                ?>

                <p><?=$offre['getMailEntreprise']?></p>

                <?php
                }
                if($offre['numero_entreprise']){
                ?>

                <p><?=$offre['numero_entreprise']?></p>

                <?php
                }
                ?>
        </div>
        
        <?php
        }
        if($offre['salaire']){
        ?>
        
        <div class='boite'>
            <p class='titre_boite'>Salaire</p>
            <p><?=$offre['salaire']?></p>
        </div>

        <?php
        }

        if($offre['secteur_activite'] ){
        ?>
        <div class='boite'>
            <p class='titre_boite'>Secteur d'activité</p>
            <p><?=$offre['secteur_activite']?></p>
        </div>
        <?php
        }
        ?>
    </div>

</div>

<?php
    if(!$offre['id_jobijoba']){

        echo do_shortcode('[elementor-template id="114576"]');

    }
    ?>

    <?=do_shortcode('[elementor-template id="113838"]')?>

    </main><!-- #main -->

    <?php
}else{
?>
    <p> Cette offre n'existe pas.</p>
<?php
}
?>

            <section id='autre_offres'>
                <p>Autres offres d’emploi à découvrir</p>
                <div id='liste_offres'>
                <?php
                    $secteur_offre = '';
                    $id_offre = "";
                    if(!empty($offre)){
                        if($offre['secteur_activite']){
                            $secteur_offre = $offre['secteur_activite'];
                        }
                        $id_offre = $offre['id'];
                    }
                    $autre_offres = $class->getMoreOffre($secteur_offre, $id_offre);
                    foreach($autre_offres as $autre_offre){  
                ?>
                    <div class='offre'>
                        <div class='corps_offre'>
                            <p class='titre_offre'><?=$autre_offre['intitule']?></p>
                            <div class='details'>
                                <?php
                                if($autre_offre['ville_libelle']){
                                ?>
                                <div class='ville'>
                                    <i class='fa-solid fa-location-pin'></i>
                                    <p><?=$autre_offre['ville_libelle']?></p>
                                </div>
                                <?php
                                }
                                ?>
                                <div class='contrat'>
                                    <i class='fa-solid fa-tag'></i>
                                    <p><?=$autre_offre['type_contrat']?></p>
                                </div>
                            </div>
                            <?php
                            if($autre_offre['nom_entreprise'] != ''){
                            ?>
                            <p class='nom_entreprise'>Entreprise : <?=$autre_offre['nom_entreprise']?></p>
                            <?php
                            }
                            ?>
                            <p class='description'><?=strlen(nl2br($autre_offre['description'])) > 160 ? substr(nl2br($autre_offre['description']), 0, 156) . '...' : nl2br($autre_offre['description']);?></p>
                        </div>
                        <a class='lien_fiche' href='/offres-emploi/<?=$autre_offre["id"]?>'>
                            <button class='bouton_lien_fiche'>Voir l'offre</button>
                        </a>
                        <a class='lien_fiche_big' href='/offres-emploi/<?=$autre_offre["id"]?>'></a>
                    </div>
                <?php
                    }
                ?>
                </div>
            </section>

        </div><!-- #primary -->

		<?php

		generate_construct_sidebars();

	get_footer();
?>