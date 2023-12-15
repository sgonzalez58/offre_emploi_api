<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.koikispass.com
 * @since      1.0.0
 *
 * @package    Offre_emploi
 * @subpackage Offre_emploi/admin/partials
 */
?>
<div class="container">
    <?php if($_GET['page'] == 'gestion_offre_emploi') { echo '<a id="retour_offre">Retour à l\'offre</a>';}?>
    <h2><?php if($_GET['page'] == 'ajouter_offres_emploi') { echo 'Ajout d\'une offre d\'emploi'; } else { echo 'Modification d\'une offre d\'emploi';} ?></h2>
    <form id="formOffre" action="" method="POST">

        <div class="ligne_formulaire form-group mb-2">
            <label for="intitule">Titre de l'offre *: </label>
            <input id="intitule" class="form-control" type="text" name="intitule" required value="<?php if(isset($_POST['intitule'])){echo $_POST['intitule'];}?>">
        </div>

        <div class="ligne_formulaire form-group mb-2">
            <label for="appelation_metier">Métier *: </label>
            <input id="appelation_metier" class="form-control" type="text" name="libelle_metier" required placeholder="Ex : Responsable de boutique, Agent / Agente d'accueil, Auxiliaire de puériculture..." value="<?php if(isset($_POST['libelle_metier'])){echo $_POST['libelle_metier'];}?>">
        </div>

        <div class="ligne_formulaire form-group mb-2">
            <label for="secteur_activite">Secteur d'activité *: </label>
            <input id="secteur_activite" class="form-control" type="text" name="secteur_activite" value="<?php if(isset($_POST['secteur_activite'])){echo $_POST['secteur_activite'];}?>">
        </div>

        <div class="ligne_formulaire form-group mb-2">
            <label for="nom_entreprise">Nom du recruteur : </label>
            <input id="nom_entreprise" class="form-control" name="nom_entreprise" type="text" value="<?php if(isset($_POST['nom_entreprise'])){echo $_POST['secteur_activite'];}?>">
        </div>

        <div class="ligne_formulaire form-group mb-2">
            <label for="type_contrat">Contrat : </label>
            <select id="type_contrat" name="type_contrat">
                <option value="" <?php if(!isset($_POST['type_contrat'])){ echo "selected";}?>>Choisissez le type de contrat</option>
                <option value="CDD" <?php if(isset($_POST['type_contrat']) && $_POST['type_contrat']=='CDD'){ echo "selected";}?>>CDD</option>
                <option value="CDI" <?php if(isset($_POST['type_contrat']) && $_POST['type_contrat']=='CDI'){ echo "selected";}?>>CDI</option>
                <option value="Indépendant" <?php if(isset($_POST['type_contrat']) && $_POST['type_contrat']=='Indépendant'){ echo "selected";}?>>Indépendant</option>
                <option value="Intérim" <?php if(isset($_POST['type_contrat']) && $_POST['type_contrat']=='Intérim'){ echo "selected";}?>>Intérim</option>
                <option value="Saisonnier" <?php if(isset($_POST['type_contrat']) && $_POST['type_contrat']=='Saisonnier'){ echo "selected";}?>>Saisonnier</option>
                <option value="Alternance" <?php if(isset($_POST['type_contrat']) && $_POST['type_contrat']=='Alternance'){ echo "selected";}?>>Alternance</option>
                <option value="Stage" <?php if(isset($_POST['type_contrat']) && $_POST['type_contrat']=='Stage'){ echo "selected";}?>>Stage</option>
            </select>
        </div>

        <div class="ligne_formulaire form-group mb-2">
            <div class="d-flex align-items-center">
                <label for="montant_salaire">Salaire&nbsp;:</label>
                <input type="text" name="montant_salaire" class="form-control m-2" id="montant_salaire" value="<?php if(isset($_POST['montant_salaire'])){echo $_POST['montant_salaire'];}?>">
                <label for="periode_salaire">par</label>
                <select id="periode_salaire" class="form-control ms-2" name="periode_salaire">
                    <option value="an" <?php if(isset($_POST['periode_salaire']) && $_POST['periode_salaire']=='an'){ echo "selected";}?>>an</option>
                    <option value="mois" <?php if(isset($_POST['periode_salaire']) && $_POST['periode_salaire']=='mois'){ echo "selected";}?>>mois</option>
                    <option value="heure" <?php if(isset($_POST['periode_salaire']) && $_POST['periode_salaire']=='heure'){ echo "selected";}?>>heure</option>
                </select>
            </div>
        </div>

        <div class="ligne_formulaire form-group mb-3">
            <label for="description">Description&nbsp;*:</label>
            <textarea name="description" class="form-control" id="description"><?php if(isset($_POST['description'])){echo $_POST['description'];}?></textarea>
        </div>
    
        <div class="ligne_formulaire mb-2">
            <div class="localisation">
                <h3 class="m-1">Localisation</h3>
                <div class="commune form-group ms-2">
                    <label for="commune">Commune</label>
                    <select id="commune" class="form-control" name="commune">
                        <option value="">Veuillez choisir une commune</option>
                        <?php
                        $class = new Offre_emploi_Admin("Offre_emploi","1.0.0");
                        $communes = $class->model->findAllCommunes();
                        foreach($communes as $commune){
                        ?>
                        <option value="<?=$commune["id"]?>" <?php if(isset($_POST['commune']) && $_POST['commune']==$commune["id"]){ echo "selected";}?>><?=$commune["nom_commune"]?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="ligne_formulaire form-group mb-2">
            <label for="email">Email (recevra les notifications de validation ou désactivation)*: </label>
            <input id="email" class="form-control" type="text" name="email" required  value="<?php if(isset($_POST['email'])){echo $_POST['email'];}?>">
        </div>

        <div class="ligne_formulaire form-group mb-2">
            <label for="date_debut">Date de début de publication *: </label>
            <input id="date_debut" class="form-control" type="text" name="date_debut" required  value="<?php if(isset($_POST['date_debut'])){echo $_POST['date_debut'];}?>">
        </div>

        <div class="ligne_formulaire form-group mb-2">
            <label for="date_fin">Date de fin de publication *: </label>
            <input id="date_fin" class="form-control" type="text" name="date_fin" required  value="<?php if(isset($_POST['date_fin'])){echo $_POST['date_fin'];}?>">
        </div>

         <div class="ligne_formulaire form-group mb-2">
                <label for="image">Image :</label>
                <a href="#" class="custom-upload-button button">Envoyer une image</a>
                <a href="#" class="custom-upload-remove" style="display:none;">Supprimer une image</a>
                <input type="hidden" name="image" id="image" value="">
         </div>

        <div class="ligne_formulaire form-group mb-2">
            <label for="image">Logo :</label>
            <a href="#" class="custom-upload-button button">Envoyer une image</a>
            <a href="#" class="custom-upload-remove" style="display:none;">Supprimer une image</a>
            <input type="hidden" name="logo" id="logo" value="">
        </div>

        <div class="d-flex justify-content-center">
            <input type="hidden" name="formEmploiBackend" id="formEmploiBackend" value="1">
            <button type="submit" class="btn btn-danger">Enregistrer</button>
        </div>
    </form>
</div>
