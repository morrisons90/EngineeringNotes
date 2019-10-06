<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191003131029 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE note CHANGE description description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE note_module ADD note_id_id INT NOT NULL, CHANGE name name VARCHAR(255) DEFAULT NULL, CHANGE flags flags VARCHAR(255) DEFAULT NULL, CHANGE tags tags VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE note_module ADD CONSTRAINT FK_14F671FC1A543D80 FOREIGN KEY (note_id_id) REFERENCES note (id)');
        $this->addSql('CREATE INDEX IDX_14F671FC1A543D80 ON note_module (note_id_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE note CHANGE description description VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE note_module DROP FOREIGN KEY FK_14F671FC1A543D80');
        $this->addSql('DROP INDEX IDX_14F671FC1A543D80 ON note_module');
        $this->addSql('ALTER TABLE note_module DROP note_id_id, CHANGE name name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE flags flags VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE tags tags VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
    }
}
