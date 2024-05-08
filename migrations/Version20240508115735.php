<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240508115735 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE collection_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE custom_item_attribute (id INT AUTO_INCREMENT NOT NULL, item_collection_id INT NOT NULL, name VARCHAR(100) NOT NULL, type VARCHAR(10) NOT NULL, INDEX IDX_DC45CCD1BDE5FE26 (item_collection_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE item_collection (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, name VARCHAR(100) NOT NULL, description LONGTEXT NOT NULL, INDEX IDX_41FC4D3812469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE custom_item_attribute ADD CONSTRAINT FK_DC45CCD1BDE5FE26 FOREIGN KEY (item_collection_id) REFERENCES item_collection (id)');
        $this->addSql('ALTER TABLE item_collection ADD CONSTRAINT FK_41FC4D3812469DE2 FOREIGN KEY (category_id) REFERENCES collection_category (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE custom_item_attribute DROP FOREIGN KEY FK_DC45CCD1BDE5FE26');
        $this->addSql('ALTER TABLE item_collection DROP FOREIGN KEY FK_41FC4D3812469DE2');
        $this->addSql('DROP TABLE collection_category');
        $this->addSql('DROP TABLE custom_item_attribute');
        $this->addSql('DROP TABLE item_collection');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
