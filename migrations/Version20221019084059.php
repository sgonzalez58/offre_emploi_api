<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221019084059 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE offre_emploi DROP code_formation, DROP domaine_formation, DROP niveau_formation, DROP commentaire_formation, DROP exigence_formation, DROP competences, DROP nom_contact, DROP lien_contact, DROP mail_contact, DROP offre_manque_candidats, CHANGE alternance alternance TINYINT(1) DEFAULT NULL, CHANGE accessible_th accessible_th TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE offre_emploi ADD code_formation INT DEFAULT NULL, ADD domaine_formation VARCHAR(255) NOT NULL, ADD niveau_formation VARCHAR(255) NOT NULL, ADD commentaire_formation VARCHAR(255) DEFAULT NULL, ADD exigence_formation VARCHAR(255) DEFAULT NULL, ADD competences VARCHAR(255) NOT NULL, ADD nom_contact VARCHAR(255) NOT NULL, ADD lien_contact VARCHAR(255) DEFAULT NULL, ADD mail_contact VARCHAR(255) NOT NULL, ADD offre_manque_candidats TINYINT(1) DEFAULT NULL, CHANGE alternance alternance TINYINT(1) NOT NULL, CHANGE accessible_th accessible_th VARCHAR(255) DEFAULT NULL');
    }
}
