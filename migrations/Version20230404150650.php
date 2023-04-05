<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230404150650 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE offre_emploi (id INT AUTO_INCREMENT NOT NULL, commune_id INT DEFAULT NULL, user_id INT DEFAULT NULL, id_jobijoba VARCHAR(255) DEFAULT NULL, intitule VARCHAR(255) NOT NULL, date_de_publication DATETIME NOT NULL, latitude NUMERIC(10, 4) DEFAULT NULL, longitude NUMERIC(10, 4) DEFAULT NULL, libelle_metier VARCHAR(255) DEFAULT NULL, nom_entreprise VARCHAR(255) DEFAULT NULL, type_contrat VARCHAR(255) NOT NULL, salaire VARCHAR(255) DEFAULT NULL, secteur_activite INT DEFAULT NULL, description LONGTEXT DEFAULT NULL, ville_libelle VARCHAR(255) NOT NULL, validation VARCHAR(255) NOT NULL, visibilite VARCHAR(255) DEFAULT NULL, commercant_id INT DEFAULT NULL, lien_jj VARCHAR(255) DEFAULT NULL, INDEX IDX_132AD0D1131A4F72 (commune_id), INDEX IDX_132AD0D1A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE offre_emploi ADD CONSTRAINT FK_132AD0D1131A4F72 FOREIGN KEY (commune_id) REFERENCES commune (id)');
        $this->addSql('ALTER TABLE offre_emploi ADD CONSTRAINT FK_132AD0D1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE candidature DROP FOREIGN KEY FK_E33BD3B81C13BCCF');
        $this->addSql('ALTER TABLE offre_emploi DROP FOREIGN KEY FK_132AD0D1131A4F72');
        $this->addSql('ALTER TABLE offre_emploi DROP FOREIGN KEY FK_132AD0D1A76ED395');
        $this->addSql('DROP TABLE offre_emploi');
    }
}
