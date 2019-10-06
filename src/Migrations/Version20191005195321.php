<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191005195321 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE note ADD topic_id INT NOT NULL, ADD title VARCHAR(255) DEFAULT NULL, ADD content VARCHAR(1024) NOT NULL');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA141F55203D FOREIGN KEY (topic_id) REFERENCES topic (id)');
        $this->addSql('CREATE INDEX IDX_CFBDFA141F55203D ON note (topic_id)');
        $this->addSql('ALTER TABLE topic CHANGE tree_root tree_root INT DEFAULT NULL, CHANGE parent_id parent_id INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA141F55203D');
        $this->addSql('DROP INDEX IDX_CFBDFA141F55203D ON note');
        $this->addSql('ALTER TABLE note DROP topic_id, DROP title, DROP content');
        $this->addSql('ALTER TABLE topic CHANGE tree_root tree_root INT DEFAULT NULL, CHANGE parent_id parent_id INT DEFAULT NULL');
    }
}
