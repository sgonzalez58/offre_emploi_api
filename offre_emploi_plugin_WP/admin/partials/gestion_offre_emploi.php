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

<div class='container'>
    <div class='modal fade' id='modalMail' tabindex='-1' aria-labelledby='modalMailLabel' aria-hidden='true'>
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h5 class='modal-title' id='modalMailLabel'></h5>
                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                </div>
                <div class='modal-body'>
                    <label for='localisation'>
                        <p>
                            <input type='checkbox' id='localisation'>
                            La localisation et le nom de la commune ne sont pas renseignés.
                        </p>
                    </label>
                    <label for='entreprise'>
                        <p>
                            <input type='checkbox' id='entreprise'>
                            Le nom de l'entreprise n'est pas renseigné.
                        </p>
                    </label>
                    <br>
                    <div class='d-flex align-items-start justify-content-stretch'>
                        <label for='raison_personnalisee'>
                            <p>Autres:</p>
                        </label>
                        <textarea id='raison_personnalisee' class='form-control ms-1'></textarea>
                    </div>
                </div>
                <div class='modal-footer'>
                    <button id='closeModal' type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Annuler</button>
                    <button onclick='confirmerSuppression()' type='button' class='btn btn-primary'>Envoyer et supprimer</button>
                </div>
            </div>
        </div>
    </div>
    <table id='liste_offre_en_attente' class='table table-striped'>
        <thead>
            <tr>
                <th>Titre de l'offre</th>
                <th>Ville</th>
                <th>Entreprise</th>
                <th>Date</th>
                <th>Etat</th>
                <th>Fiche / Liste</th>
                <th>Clics / Postuler</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>