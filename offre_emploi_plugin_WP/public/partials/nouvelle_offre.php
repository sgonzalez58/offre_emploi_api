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
$communes = $class->model->findAllCommunes();
date_default_timezone_set('Europe/Paris');
setlocale (LC_TIME, 'fr_FR');
get_header(); ?>

		<div id="primary" <?php generate_do_element_classes( 'content' ); ?>>
			<main id="main" <?php generate_do_element_classes( 'main' ); ?>>
<?php
    
?>
<a href='/offres-emploi' id='retour_offres'> retour </a>
<br>
<div class='conteneur_formulaire'>
    <form id='formOffre' action='/offres-emploi/creer/verification' method='POST'>

        <h2>Nouvelle offre d'emploi</h2>

        <div class='ligne_formulaire form-group mb-2'>
            <label for='intitule'>Titre de l'offre *: </label>
            <input id='intitule' class='form-control' type='text' name='intitule' required>
        </div>

        <div class='ligne_formulaire form-group mb-2'>
            <label for='appelation_metier'>Métier *: </label>
            <input id='appelation_metier' class='form-control' type='text' name='libelle_metier' required placeholder="Ex : Responsable de boutique, Agent / Agente d'accueil, Auxiliaire de puériculture...">
        </div>

        <div class='ligne_formulaire form-group mb-2'>
            <label for='secteur_activite'>Secteur d'activité *: </label>
            <input id='secteur_activite' class='form-control' type='text' name='secteur_activite'>
        </div>

        <div class='ligne_formulaire form-group mb-2'>
            <label for='nom_entreprise'>Nom du recruteur : </label>
            <input id='nom_entreprise' class='form-control' name='nom_entreprise' type='text' value='<?=wp_get_current_user()->first_name.' '.wp_get_current_user()->last_name?>'>
        </div>

        <div class='ligne_formulaire form-group mb-2'>
            <label for='type_contrat'>Contrat : </label>
            <select id='type_contrat' name='type_contrat'>
                <option value='CDD'>CDD</option>
                <option value='CDI'>CDI</option>
                <option value='Indépendant'>Indépendant</option>
                <option value='Intérim'>Intérim</option>
                <option value='Saisonnier'>Saisonnier</option>
                <option value='Alternance'>Alternance</option>
                <option value='Stage'>Stage</option>
            </select>
        </div>

        <div class='ligne_formulaire form-group mb-2'>
            <div class='d-flex align-items-center'>
                <label for='montant_salaire'>Salaire&nbsp;:</label>
                <input type='text' name='montant_salaire' class='form-control m-2' id='montant_salaire'>
                <label for='periode_salaire'>par</label>
                <select id='periode_salaire' class='form-control ms-2' name='periode_salaire'>
                    <option value='an'>an</option>
                    <option value='mois'>mois</option>
                    <option value='heure'>heure</option>
                </select>
            </div>
        </div>

        <div class='ligne_formulaire form-group mb-3'>
            <label for='description'>Description&nbsp;*:</label>
            <textarea name='description' class='form-control' id='description'></textarea>
        </div>
    
        <div class='ligne_formulaire mb-2'>
            <div class='localisation'>
                <h3 class='m-1'>Localisation</h3>
                <div class='commune form-group ms-2'>
                    <label for='commune'>Commune</label>
                    <select id='commune' class='form-control' name='commune'>
                        <option value=''>Veuillez choisir une commune</option>
                        <?php
                        foreach($communes as $commune){
                        ?>
                        <option value='<?=$commune['id']?>'><?=$commune['nom_commune']?></option>
                        <?php
                        }
                        ?>
                    </select>
                    <p>Si vous ne trouvez pas votre commune, vous pouvez la rentrer manuellement ci-dessous.</p>
                </div>
                <div class='villeLibelle form-group ms-2'>
                    <label for='ville_libelle'>Commune:</label>
                    <input type='text' class='form-control' name='ville_libelle' id='ville_libelle'>
                </div>
                <input type='hidden' name='latitude' id='formulaire_offre_emploi_latitude'>
                <input type='hidden' name='longitude' id='formulaire_offre_emploi_longitude'>
                <div id="map"></div>
            </div>
        </div>

        <div class='d-flex justify-content-center'>
            <button type='submit' class='btn btn-danger'>Enregistrer</button>
        </div>
    </form>
</div>

            </main><!-- #main -->
        </div><!-- #primary -->

		<?php

		generate_construct_sidebars();

	get_footer();
?>