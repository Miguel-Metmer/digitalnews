<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200203162024 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962A23EDC87');
        $this->addSql('ALTER TABLE comments CHANGE subject_id subject_id INT NOT NULL');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A23EDC87 FOREIGN KEY (subject_id) REFERENCES subjects (id)');
        $this->addSql('ALTER TABLE subjects CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', DROP role');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962A23EDC87');
        $this->addSql('ALTER TABLE comments CHANGE subject_id subject_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A23EDC87 FOREIGN KEY (subject_id) REFERENCES subjects (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE subjects CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD role VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP roles');
    }
}