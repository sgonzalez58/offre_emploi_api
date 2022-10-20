<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221019074344 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE offre_emploi (id INT AUTO_INCREMENT NOT NULL, ville_id INT NOT NULL, id_pole_emploi VARCHAR(255) DEFAULT NULL, intitule VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, date_de_creation DATETIME NOT NULL, date_actualisation DATETIME NOT NULL, latitude NUMERIC(10, 4) NOT NULL, longitude NUMERIC(10, 4) NOT NULL, code_metier VARCHAR(255) NOT NULL, libelle_metier VARCHAR(255) NOT NULL, appellation_metier VARCHAR(255) NOT NULL, nom_entreprise VARCHAR(255) NOT NULL, description_entreprise VARCHAR(255) NOT NULL, url_entreprise VARCHAR(255) DEFAULT NULL, type_contrat VARCHAR(255) NOT NULL, type_contrat_libelle VARCHAR(255) NOT NULL, nature_contrat VARCHAR(255) NOT NULL, experience_exige VARCHAR(255) NOT NULL, experience_libelle VARCHAR(255) NOT NULL, code_formation INT DEFAULT NULL, domaine_formation VARCHAR(255) NOT NULL, niveau_formation VARCHAR(255) NOT NULL, commentaire_formation VARCHAR(255) DEFAULT NULL, exigence_formation VARCHAR(255) DEFAULT NULL, competences VARCHAR(255) NOT NULL, salaire VARCHAR(255) NOT NULL, duree_travail VARCHAR(255) NOT NULL, duree_travail_convertie VARCHAR(255) NOT NULL, alternance TINYINT(1) NOT NULL, nom_contact VARCHAR(255) NOT NULL, lien_contact VARCHAR(255) DEFAULT NULL, mail_contact VARCHAR(255) NOT NULL, nb_postes INT NOT NULL, accessible_th VARCHAR(255) DEFAULT NULL, code_qualification INT DEFAULT NULL, libelle_qualification VARCHAR(255) NOT NULL, secteur_activite INT DEFAULT NULL, secteur_activite_libelle VARCHAR(255) NOT NULL, origine_offre VARCHAR(255) NOT NULL, offre_manque_candidats TINYINT(1) DEFAULT NULL, INDEX IDX_132AD0D1A73F0036 (ville_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ville (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, code_postal INT NOT NULL, insee INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE offre_emploi ADD CONSTRAINT FK_132AD0D1A73F0036 FOREIGN KEY (ville_id) REFERENCES ville (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE offre_emploi DROP FOREIGN KEY FK_132AD0D1A73F0036');
        $this->addSql('DROP TABLE offre_emploi');
        $this->addSql('DROP TABLE ville');
    }
}
