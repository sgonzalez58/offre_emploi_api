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
$offre = $class->model->findOneOffre($wp_query->query_vars['idOffreEmploi']);
date_default_timezone_set('Europe/Paris');
setlocale (LC_TIME, 'fr_FR');
get_header(); ?>

		<div id="primary" <?php generate_do_element_classes( 'content' ); ?>>
			<main id="main" <?php generate_do_element_classes( 'main' ); ?>>
<?php
    if(!empty($offre)){
?>

<div class='conteneur_formulaire'>
    <form id='form' action='/offreEmploi/creer/verification' method='POST'>

        <h2>Nouvelle offre d'emploi</h2>

        <div class='ligne_formulaire'>
            <label for='intitule'>Titre de l'offre *: </label>
            <input id='intitule' type='text' name='intitule' required>
        </div>

        <div class='ligne_formulaire'>
            <label for='appelation_metier'>Métier *: </label>
            <input id='appelation_metier' type='text' name='appelation_metier' required placeholder="Ex : Responsable de boutique, Agent / Agente d'accueil, Auxiliaire de puériculture...">
        </div>

        <div class='ligne_formulaire'>
            <label for='nom_entreprise'>Nom de l'entreprise : </label>
            <input id='nom_entreprise' type='text' name='nom_entreprise'>
        </div>

        <div class='ligne_formulaire'>
            <label for='mail_entreprise'>Mail de l'entreprise *: </label>
            <input id='mail_entreprise' type='email' name='mail_entreprise' required>
        </div>

        <div class='ligne_formulaire'>
            <label for='numero_entreprise'>Téléphone de contact : </label>
            <input id='numero_entreprise' type='email' name='numero_entreprise'>
        </div>

        <div class='ligne_formulaire'>
            <div class='contrat'>
                <h3>Contrat</h3>
                <div class='type_contrat'>
                    <label for='type_contrat'>Type *: </label>
                    <select id='type_contrat' name='type_contrat'>
                        <option></option>
                    </select>
                </div>
                <div class='nature_contrat'>
                    {{ form_label(form.natureContrat) }}
                    :
                    {{ form_widget(form.natureContrat) }}
                </div>
                <div class='alternance'>
                    {{ form_label(form.alternance) }}
                    :
                    {{ form_widget(form.alternance) }}
                </div>
                <div class='duree_contrat'>
                    {{ form_label(form.duree) }}
                    :
                    {{ form_widget(form.duree) }}
                </div>
            </div>
        </div>

    <div class='ligne_formulaire'>
        <div class='salaire'>
            Salaire :
            {{ form_widget(form.montantSalaire) }}
            par 
            {{ form_widget(form.periodeSalaire) }}
        </div>
    </div>

    <div class='ligne_formulaire'>
        <div class='horaire'>
            {{ form_label(form.dureeTravail) }}
            :
            {{ form_widget(form.dureeTravail) }}
        </div>
    </div>

    <div class='ligne_formulaire'>
        <div class='experience'>
            {{ form_label(form.experienceLibelle) }}
            :
            {{ form_widget(form.experienceLibelle) }}
        </div>
    </div>

    <div class='ligne_formulaire'>
        <div class='poste'>
            {{ form_label(form.nbPostes) }}
            :
            {{ form_widget(form.nbPostes) }}
        </div>
    </div>

    <div class='description'>
        {{ form_label(form.description) }}
        :
        {{ form_widget(form.description) }}
    </div>
    
    <div class='ligne_formulaire'>
        <div class='localisation'>
            <h3>Localisation</h3>
            <div class='commune'>
                {{ form_row(form.commune) }}
            </div>
            <div class='villeLibelle'>
                {{ form_label(form.villeLibelle) }}
                :
                {{ form_widget(form.villeLibelle) }}
            </div>
            <div id="map"></div>
        </div>
    </div>

    <div class='submit'>
        {{ form_widget(form.submit, { 'label': 'Enregistrer l\'offre' }) }}
    </div>

{{ form_end(form) }}
</div>

            </main><!-- #main -->
        </div><!-- #primary -->

		<?php

		generate_construct_sidebars();

	get_footer();
?>