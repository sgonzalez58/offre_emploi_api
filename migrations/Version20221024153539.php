<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221024153539 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE offre_emploi ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE offre_emploi ADD CONSTRAINT FK_132AD0D1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_132AD0D1A76ED395 ON offre_emploi (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE offre_emploi DROP FOREIGN KEY FK_132AD0D1A76ED395');
        $this->addSql('DROP INDEX IDX_132AD0D1A76ED395 ON offre_emploi');
        $this->addSql('ALTER TABLE offre_emploi DROP user_id');
    }
}
