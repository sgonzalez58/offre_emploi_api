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
$offres_valides = $class->model->findByMotsClef();
$villes = $class->model->findAllCommunes();
$types_contrat = $class->model->getAllTypeContrat();
$metier = $class->model->getMetier();
//var_dump($metier);
get_header(); ?>

		<div id="primary" <?php generate_do_element_classes( 'content' ); ?>>
			<main id="main" <?php generate_do_element_classes( 'main' ); ?>>
<?php
    if(!empty($offres_valides)){
?>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,1,0" />
<!--<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">-->

<script src="https://code.jquery.com/ui/1.13.0/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.0/themes/base/jquery-ui.css">
<!--<a href="https://www.koikispass.com/deposez-vos-dates/"><div class="ajout_agenda_bouton"><span class="material-icons">edit_note</span><p class="ajout_agenda_text">Déposer une <br>offre d'emploi</p></div></a>-->
<div class="head_liste_offre">
    <div class='title'>
        <h1>Nos offres d'emploi</h1>
    </div>

    <!--<div class='ajout'>
        <a class='ajouter_offre' href=''><button id="ajout_bouton" disabled><i class="fa-solid fa-circle-plus"></i>Créer une offre</button></a>
        <h4>Bientôt disponible, l'ajout de vos offres d'emploi.</h4>
    </div>-->
</div>

<!--<p id='filtre_label'>Filtrer votre recherche</p>-->
<div id='liste_filtres'>
    <div id="filtre_recherche">
        <div id='type_contrat'>
            <label for="liste_type_contrat"><span class="material-symbols-outlined" id='type_metier_icon'>work</span></label>
            <label class='sort_icon' for="liste_type_contrat"><span class="material-symbols-outlined">
unfold_more
</span></label>
            <select id='liste_type_contrat'>
                <option value='' selected></option>
                <?php
                foreach($types_contrat as $type_contrat){
                ?>
                <option value='<?=$type_contrat['type_contrat']?>'><?=$type_contrat['type_contrat']?></option>
                <?php
                }
                ?>
            </select>
        </div>

        <div id='commune'>
            <label for='liste_ville'><span class="material-symbols-outlined" id='ville_icon'>location_on</span></label>
            <label class='sort_icon' for="liste_ville"><span class="material-symbols-outlined">
unfold_more
</span></label>
            <select class='liste_ville' id='liste_ville'>
                <option></option>
                <?php
                foreach($villes as $ville){
                    if($ville['nom_departement'] == 'Nièvre'){
                ?>
                <option value='<?=$ville['id']?>'><?=$ville['nom_commune']?></option>
                <?php
                    }
                }
                ?>
            </select>
        </div>

        <div id='distance'>
            <label for="liste_distance"><span class="material-symbols-outlined" id='distance_icon'>near_me</span></label>
            <input type='number' id='liste_distance' placeholder="Distance max (km)" min="0" oninput="validity.valid||(value='');">
        </div>

        <div class='recherche'>
            <label for="recherche_input"><span class="material-symbols-outlined" id='recherche_secteur_icon'>location_searching</span></label>
            <input id='recherche_input' type="text" minlength="1" maxlength="50" placeholder="Rechercher par poste">
        </div>
        <script>
            jQuery(document).ready(function($) {
                let metiers = [<?php foreach($metier as $item) { echo '{ label: "' . $item['libelle'] . '", value: "' . $item['libelle'] . '" },'; } ?>];

                $('#recherche_input').autocomplete({
                source: metiers,
                position: {
                    my: "left top",
                    at: "left bottom"
                }
                });
            });
        </script>

        <button id='recherche'><span class="material-symbols-outlined">search</span>Rechercher</button>

    </div>
</div>
<div class="legende_tri">
    <span class='sous_titre_h1'>Nous trouvons plus de <?= floor(count($offres_valides) / 100) * 100 ?> offres d'emploi disponibles</span>
    <span class="supp_tri"><i style="font-size: 12px;color:#3D3D3D;margin-right:12px;" class="fa-solid fa-trash"></i>Supprimer les filtres</span>
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