<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221222083149 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE offre_emploi DROP FOREIGN KEY FK_132AD0D1A73F0036');
        $this->addSql('CREATE TABLE commune (id INT AUTO_INCREMENT NOT NULL, code_insee VARCHAR(5) NOT NULL, code_postal VARCHAR(5) NOT NULL, nom_commune VARCHAR(255) NOT NULL, code_departement VARCHAR(2) NOT NULL, nom_departement VARCHAR(255) NOT NULL, nom_region VARCHAR(255) NOT NULL, latitude VARCHAR(255) NOT NULL, longitude VARCHAR(255) NOT NULL, slug VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, titre_seo VARCHAR(255) DEFAULT NULL, meta_seo VARCHAR(255) DEFAULT NULL, description_bas_de_page LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('DROP TABLE ville');
        $this->addSql('DROP INDEX IDX_132AD0D1A73F0036 ON offre_emploi');
        $this->addSql('ALTER TABLE offre_emploi ADD commune_id INT DEFAULT NULL, ADD user_id INT DEFAULT NULL, ADD description LONGTEXT NOT NULL, ADD ville_libelle VARCHAR(255) NOT NULL, ADD validation VARCHAR(255) NOT NULL, ADD mail_entreprise VARCHAR(255) DEFAULT NULL, ADD numero_entreprise VARCHAR(255) DEFAULT NULL, ADD visibilite VARCHAR(255) DEFAULT NULL, DROP ville_id, CHANGE code_metier code_metier VARCHAR(255) DEFAULT NULL, CHANGE libelle_metier libelle_metier VARCHAR(255) DEFAULT NULL, CHANGE experience_exige experience_exige VARCHAR(255) DEFAULT NULL, CHANGE origine_offre origine_offre VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE offre_emploi ADD CONSTRAINT FK_132AD0D1131A4F72 FOREIGN KEY (commune_id) REFERENCES commune (id)');
        $this->addSql('ALTER TABLE offre_emploi ADD CONSTRAINT FK_132AD0D1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_132AD0D1131A4F72 ON offre_emploi (commune_id)');
        $this->addSql('CREATE INDEX IDX_132AD0D1A76ED395 ON offre_emploi (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE offre_emploi DROP FOREIGN KEY FK_132AD0D1131A4F72');
        $this->addSql('ALTER TABLE offre_emploi DROP FOREIGN KEY FK_132AD0D1A76ED395');
        $this->addSql('CREATE TABLE ville (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, code_postal INT DEFAULT NULL, insee INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE commune');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP INDEX IDX_132AD0D1131A4F72 ON offre_emploi');
        $this->addSql('DROP INDEX IDX_132AD0D1A76ED395 ON offre_emploi');
        $this->addSql('ALTER TABLE offre_emploi ADD ville_id INT NOT NULL, DROP commune_id, DROP user_id, DROP description, DROP ville_libelle, DROP validation, DROP mail_entreprise, DROP numero_entreprise, DROP visibilite, CHANGE code_metier code_metier VARCHAR(255) NOT NULL, CHANGE libelle_metier libelle_metier VARCHAR(255) NOT NULL, CHANGE experience_exige experience_exige VARCHAR(255) NOT NULL, CHANGE origine_offre origine_offre VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE offre_emploi ADD CONSTRAINT FK_132AD0D1A73F0036 FOREIGN KEY (ville_id) REFERENCES ville (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_132AD0D1A73F0036 ON offre_emploi (ville_id)');
    }
}
