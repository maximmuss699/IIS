<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231117113342 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_systems (user_id INT NOT NULL, systems_id INT NOT NULL, PRIMARY KEY(user_id, systems_id))');
        $this->addSql('CREATE INDEX IDX_39A0C6B8A76ED395 ON user_systems (user_id)');
        $this->addSql('CREATE INDEX IDX_39A0C6B8411D7F6D ON user_systems (systems_id)');
        $this->addSql('ALTER TABLE user_systems ADD CONSTRAINT FK_39A0C6B8A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_systems ADD CONSTRAINT FK_39A0C6B8411D7F6D FOREIGN KEY (systems_id) REFERENCES systems (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE user_systems DROP CONSTRAINT FK_39A0C6B8A76ED395');
        $this->addSql('ALTER TABLE user_systems DROP CONSTRAINT FK_39A0C6B8411D7F6D');
        $this->addSql('DROP TABLE user_systems');
    }
}
