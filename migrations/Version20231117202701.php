<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231117202701 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE kpi_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE kpi (id INT NOT NULL, parameter_id INT DEFAULT NULL, systems_id INT DEFAULT NULL, value INT DEFAULT NULL, function VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX UNIQ_A0925DD97C56DBD6 ON kpi (parameter_id)');
        $this->addSql('CREATE INDEX IDX_A0925DD9411D7F6D ON kpi (systems_id)');
        $this->addSql('ALTER TABLE kpi ADD CONSTRAINT FK_A0925DD97C56DBD6 FOREIGN KEY (parameter_id) REFERENCES parameters (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE kpi ADD CONSTRAINT FK_A0925DD9411D7F6D FOREIGN KEY (systems_id) REFERENCES systems (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE parameters ALTER "values" TYPE JSON');
        $this->addSql('COMMENT ON COLUMN parameters.values IS NULL');
        $this->addSql('ALTER TABLE systems ALTER user_owner_id SET NOT NULL');
        $this->addSql('ALTER TABLE user_systems DROP CONSTRAINT FK_39A0C6B8A76ED395');
        $this->addSql('ALTER TABLE user_systems DROP CONSTRAINT FK_39A0C6B8411D7F6D');
        $this->addSql('ALTER TABLE user_systems ADD CONSTRAINT FK_39A0C6B8A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_systems ADD CONSTRAINT FK_39A0C6B8411D7F6D FOREIGN KEY (systems_id) REFERENCES systems (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE kpi_id_seq CASCADE');
        $this->addSql('ALTER TABLE kpi DROP CONSTRAINT FK_A0925DD97C56DBD6');
        $this->addSql('ALTER TABLE kpi DROP CONSTRAINT FK_A0925DD9411D7F6D');
        $this->addSql('DROP TABLE kpi');
        $this->addSql('ALTER TABLE user_systems DROP CONSTRAINT fk_39a0c6b8a76ed395');
        $this->addSql('ALTER TABLE user_systems DROP CONSTRAINT fk_39a0c6b8411d7f6d');
        $this->addSql('ALTER TABLE user_systems ADD CONSTRAINT fk_39a0c6b8a76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_systems ADD CONSTRAINT fk_39a0c6b8411d7f6d FOREIGN KEY (systems_id) REFERENCES systems (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE parameters ALTER values TYPE TEXT');
        $this->addSql('COMMENT ON COLUMN parameters."values" IS \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE systems ALTER user_owner_id DROP NOT NULL');
    }
}
