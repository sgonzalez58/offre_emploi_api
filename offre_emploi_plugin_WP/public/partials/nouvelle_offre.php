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
<a href='/offreEmploi' id='retour_offres'> retour </a>
<br>
<div class='conteneur_formulaire'>
    <form id='formOffre' action='/offreEmploi/creer/verification' method='POST'>

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
            <input id='mail_entreprise' type='email' name='mail_entreprise' required value='<?=wp_get_current_user()->user_email?>'>
        </div>

        <div class='ligne_formulaire'>
            <label for='numero_entreprise'>Téléphone de contact : </label>
            <input id='numero_entreprise' type='text' name='numero_entreprise'>
        </div>

        <div class='ligne_formulaire'>
            <div class='contrat'>
                <h3>Contrat</h3>
                <div class='type_contrat'>
                    <label for='type_contrat'>Type *: </label>
                    <select id='type_contrat' name='type_contrat'>
                        <option value='CDD'>Contrat à durée déterminée</option>
                        <option value='CDI'>Contrat à durée indéterminée</option>
                        <option value='DDI'>CDD insertion</option>
                        <option value='DIN'>CDI intérimaire</option>
                        <option value='FRA'>Franchise</option>
                        <option value='LIB'>Profession libérale</option>
                        <option value='MIS'>Mission intérimaire</option>
                        <option value='SAI'>Contrat travail saisonnier</option>
                    </select>
                </div>
                <div class='nature_contrat'>
                    <label for='nature_contrat'>Nature *: </label>
                    <select id='nature_contrat' name='nature_contrat'>
                        <option value='Cont. professionnalisation'>Contrat de professionnalisation</option>
                        <option value='Contrat apprentissage'>Contrat d'apprentissage</option>
                        <option value="Contrat d'Engagement Educatif">Contrat d'engagement educatif</option>
                        <option value="Contrat d'usage">Contrat d'usage</option>
                        <option value='Contrat travail'>Contrat de travail</option>
                        <option value='CUI - CAE'>Contrat d'accompagnement dans l'emploi</option>
                        <option value='Emploi non salarié'>Emploi non salarié</option>
                        <option value="Insertion par l'activ.éco">Insertion par l'activité économique</option>
                    </select>
                </div>
                <div class='alternance'>
                    <label for='alternance'>Alternance: </label>
                    <input type='checkbox' id='alternance' name='alternance'>
                </div>
                <div class='duree_contrat'>
                    <label for='duree'>Durée *: </label>
                    <div class='duree'>
                        <div class='mois'>
                            <label for='mois'>Mois</label>
                            <select id='mois' name='mois'>
                                <option value='' selected></options>
                                <option value='0'>0</option>
                                <option value='1'>1</option>
                                <option value='2'>2</option>
                                <option value='3'>3</option>
                                <option value='4'>4</option>
                                <option value='5'>5</option>
                                <option value='6'>6</option>
                                <option value='7'>7</option>
                                <option value='8'>8</option>
                                <option value='9'>9</option>
                                <option value='10'>10</option>
                                <option value='11'>11</option>
                                <option value='12'>12</option>
                                <option value='13'>13</option>
                                <option value='14'>14</option>
                                <option value='15'>15</option>
                                <option value='16'>16</option>
                                <option value='17'>17</option>
                                <option value='18'>18</option>
                            </select>
                        </div>
                        <div class='jours'>
                            <label for='jours'>Jours</label>
                            <select id='jours' name='jours'>
                                <option value='' selected></options>
                                <option value='0'>0</option>
                                <option value='1'>1</option>
                                <option value='2'>2</option>
                                <option value='3'>3</option>
                                <option value='4'>4</option>
                                <option value='5'>5</option>
                                <option value='6'>6</option>
                                <option value='7'>7</option>
                                <option value='8'>8</option>
                                <option value='9'>9</option>
                                <option value='10'>10</option>
                                <option value='11'>11</option>
                                <option value='12'>12</option>
                                <option value='13'>13</option>
                                <option value='14'>14</option>
                                <option value='15'>15</option>
                                <option value='16'>16</option>
                                <option value='17'>17</option>
                                <option value='18'>18</option>
                                <option value='19'>19</option>
                                <option value='20'>20</option>
                                <option value='21'>21</option>
                                <option value='22'>22</option>
                                <option value='23'>23</option>
                                <option value='24'>24</option>
                                <option value='25'>25</option>
                                <option value='26'>26</option>
                                <option value='27'>27</option>
                                <option value='28'>28</option>
                                <option value='29'>29</option>
                                <option value='30'>30</option>
                                <option value='31'>31</option>
                            </select>
                        </div>
                        <div class='indetermine'>
                            <label for='indetermine'>Indeterminée</label>
                            <input type='checkbox' name='indetermine' id='indetermine'>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class='ligne_formulaire'>
            <div class='salaire'>
                <label for='montant_salaire'>Salaire:</label>
                <input type='text' name='montant_salaire' id='montant_salaire'>
                <label for='periode_salaire'>par</label>
                <select id='periode_salaire' name='periode_salaire'>
                    <option value='an'>an</option>
                    <option value='mois'>mois</option>
                    <option value='heure'>heure</option>
                </select>
            </div>
        </div>

        <div class='ligne_formulaire'>
            <div class='horaire'>
                <label for='duree_travail'>Temps de travail par semaine : </label>
                <input type='text' name='duree_travail' id='duree_travail' placeholder="Ex : 35H horaires normaux, 39H Travail en 3X8, 37H Travail samedi et dimanche...">
            </div>
        </div>

        <div class='ligne_formulaire'>
            <div class='experience'>
                <label for='experience_labelle'>Expérience *:</label>
                <input type='text' name='experience_libelle' id='experience_libelle' placeholder='Ex : 1 an exigé, Débutant accepté, Expérience souhaitée de 2 ans...'>
            </div>
        </div>

        <div class='ligne_formulaire'>
            <div class='poste'>
                <label for='nb_postes'>Nombre de poste à pourvoir *:</label>
                <input type='number' name='nb_postes' id='nb_postes'>
            </div>
        </div>

        <div class='ligne_formulaire'>
            <div class='description'>
                <label for='description'>Description&nbsp;*:</label>
                <textarea name='description' id='description'></textarea>
            </div>
        </div>
    
        <div class='ligne_formulaire'>
            <div class='localisation'>
                <h3>Localisation</h3>
                <div class='commune'>
                    <label for='commune'>Commune</label>
                    <select id='commune' name='commune'>
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
                <div class='villeLibelle'>
                    <label for='ville_libelle'>Commune:</label>
                    <input type='text' name='ville_libelle' id='ville_libelle'>
                </div>
                <input type='hidden' name='latitude' id='formulaire_offre_emploi_latitude'>
                <input type='hidden' name='longitude' id='formulaire_offre_emploi_longitude'>
                <div id="map"></div>
            </div>
        </div>

        <div class='submit'>
            <input type='submit' value='Enregistrer'>
        </div>
    </form>
</div>

            </main><!-- #main -->
        </div><!-- #primary -->

		<?php

		generate_construct_sidebars();

	get_footer();
?>