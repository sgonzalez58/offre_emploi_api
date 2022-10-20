<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221019093924 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE offre_emploi ADD latitude NUMERIC(10, 4) NOT NULL, ADD longitude NUMERIC(10, 4) NOT NULL, ADD nom_entreprise VARCHAR(255) NOT NULL, ADD salaire VARCHAR(255) NOT NULL, ADD duree_travail VARCHAR(255) NOT NULL, ADD duree_travail_convertie VARCHAR(255) NOT NULL, DROP description');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE offre_emploi ADD description LONGTEXT NOT NULL, DROP latitude, DROP longitude, DROP nom_entreprise, DROP salaire, DROP duree_travail, DROP duree_travail_convertie');
    }
}
