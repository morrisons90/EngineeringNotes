<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191005165700 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE topic (id INT AUTO_INCREMENT NOT NULL, tree_root INT DEFAULT NULL, parent_id INT DEFAULT NULL, title VARCHAR(64) NOT NULL, lft INT NOT NULL, lvl INT NOT NULL, rgt INT NOT NULL, INDEX IDX_9D40DE1BA977936C (tree_root), INDEX IDX_9D40DE1B727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE topic ADD CONSTRAINT FK_9D40DE1BA977936C FOREIGN KEY (tree_root) REFERENCES topic (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE topic ADD CONSTRAINT FK_9D40DE1B727ACA70 FOREIGN KEY (parent_id) REFERENCES topic (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE topic DROP FOREIGN KEY FK_9D40DE1BA977936C');
        $this->addSql('ALTER TABLE topic DROP FOREIGN KEY FK_9D40DE1B727ACA70');
        $this->addSql('DROP TABLE topic');
    }
}
