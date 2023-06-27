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


add_action('wp_head', 'fc_opengraph');
function fc_opengraph() {

    global $nom_offre;
    global $entreprise_offre;
    global $ville_offre;

    //$image = 'https://agenda.koikispass.com/public/'.$dateSEO[0]['image'];
    $titre =  $nom_offre . ' chez ' . $entreprise_offre;
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
    $titre =  $nom_offre . ' chez ' . $entreprise_offre;
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
			<main id="main" <?php generate_do_element_classes( 'main' ); ?>>
            <?php
					if($_GET['postule'] == 1){
						echo "<p class='text-success'>Votre candidature a bien été envoyée.</p>";
					}
                    ?>
<?php
    if(!empty($offre)){
?>

<div id='fiche_head'>
    <div id='informations_principales'>
        <h1 id='intitule'><?=$offre['intitule']?></h1>
        <div id='adresse'>
            <i class="fa-solid fa-shop"></i>
            <p><?=$offre['nom_entreprise']?></p>
        </div>
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
    <?php
    if($offre['id_jobijoba']){
    ?>
        <a href='<?=$offre['lien_jj']?>'><button id='bouton_postuler'>Postuler</button></a>
    <?php
    }else{
    ?>
        <button id='bouton_postuler'>Postuler</button>
    <?php
    }
    ?>
</div>

<div id='fiche_content'>
    <p id='description'><span style='font-weight : bold'>Description</span><br><br><?=nl2br($offre['description'])?></p>
    <div class='carte'>
        <?php
        if($offre['latitude']){
        ?>
        <iframe frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.openstreetmap.org/export/embed.html?bbox=<?=($offre['longitude'] - 0.0360)?>%2C<?=($offre['latitude'] - 0.0133)?>%2C<?=($offre['longitude'] + 0.0360)?>%2C<?=($offre['latitude'] + 0.0133)?>&layer=mapnik&marker=<?=$offre['latitude']?>%2C<?=$offre['longitude']?>"></iframe>
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
<?php
    if(!$offre['id_jobijoba']){
?>
    <div id='modal'>
        <div id='overlay'></div>
        <h3 style='color:white; z-index:11'>Formulaire de candidature</h3>
        <form id="envoie_candidature" method='post' action="candidature" class="centre">
            <button id='fermer_formulaire'>X</button>
            <input type='text' name='prenom' required minlength="3" maxlength="25" placeholder="Prénom">
            <input type='text' name='nom' required minlength="3" maxlength="25" placeholder="Nom">
            <?php
                if(is_user_logged_in()){
            ?>
            <input type="mail" name='mail' id='mail_form' required value="<?=wp_get_current_user()->user_email?>" placeholder="adresse mail">
            <?php
                }else{  
            ?>
            <input type="mail" name='mail' id='mail_form' required placeholder="adresse mail">
            <?php
                }
            ?>
            <textarea name='message' required placeholder="Votre demande"></textarea>
            <input type='submit' value="Envoyer">
        </form>
    </div>
    <?php
    }
?>

<?php
}else{
?>
    <p> Cette offre n'existe pas.</p>
<?php
}
?>
            </main><!-- #main -->

            <section id='autre_offres'>
                <h2>Autres offres d’emploi à découvrir</h2>
                <div id='liste_offres'>
                <?php
                    $secteur_offre = '';
                    if(!empty($offre)){
                        if($offre['secteur_activite']){
                            $secteur_offre = $offre['secteur_activite'];
                        }
                    }
                    if($secteur_offre == ''){
                        $autre_offres = $class->getMoreOffre();
                    }else{
                        $autre_offres = $class->getMoreOffre($secteur_offre);
                    }
                    foreach($autre_offres as $autre_offre){  
                ?>
                    <div class='offre'>
                        <div class='corps_offre'>
                            <h2><?=$autre_offre['intitule']?></h2>
                            <div class='details'>
                                <?php
                                if($autre_offre['ville_libelle']){
                                ?>
                                <div class='ville'>
                                    <i class='fa-solid fa-location-pin'></i>
                                    <h4><?=$autre_offre['ville_libelle']?></h4>
                                </div>
                                <?php
                                }
                                ?>
                                <div class='contrat'>
                                    <i class='fa-solid fa-tag'></i>
                                    <h4><?=$autre_offre['type_contrat']?></h4>
                                </div>
                            </div>
                            <?php
                            if($autre_offre['nom_entreprise'] != ''){
                            ?>
                            <h3 class='nom_entreprise'>Entreprise : <?=$autre_offre['nom_entreprise']?></h3>
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