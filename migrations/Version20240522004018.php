<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240522004018 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE item ADD owner_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251E7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_1F1B251E7E3C61F9 ON item (owner_id)');
        $this->addSql('ALTER TABLE item_collection ADD owner_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE item_collection ADD CONSTRAINT FK_41FC4D387E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_41FC4D387E3C61F9 ON item_collection (owner_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251E7E3C61F9');
        $this->addSql('DROP INDEX IDX_1F1B251E7E3C61F9 ON item');
        $this->addSql('ALTER TABLE item DROP owner_id');
        $this->addSql('ALTER TABLE item_collection DROP FOREIGN KEY FK_41FC4D387E3C61F9');
        $this->addSql('DROP INDEX IDX_41FC4D387E3C61F9 ON item_collection');
        $this->addSql('ALTER TABLE item_collection DROP owner_id');
    }
}
