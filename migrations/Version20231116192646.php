<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231116192646 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE device_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE parameters_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE type_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE device (id INT NOT NULL, type_id INT NOT NULL, description VARCHAR(255) DEFAULT NULL, user_alias VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_92FB68EC54C8C93 ON device (type_id)');
        $this->addSql('CREATE TABLE parameters (id INT NOT NULL, type_id INT NOT NULL, name VARCHAR(255) NOT NULL, values TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_69348FEC54C8C93 ON parameters (type_id)');
        $this->addSql('COMMENT ON COLUMN parameters.values IS \'(DC2Type:array)\'');
        $this->addSql('CREATE TABLE type (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE device ADD CONSTRAINT FK_92FB68EC54C8C93 FOREIGN KEY (type_id) REFERENCES type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE parameters ADD CONSTRAINT FK_69348FEC54C8C93 FOREIGN KEY (type_id) REFERENCES type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE device_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE parameters_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE type_id_seq CASCADE');
        $this->addSql('ALTER TABLE device DROP CONSTRAINT FK_92FB68EC54C8C93');
        $this->addSql('ALTER TABLE parameters DROP CONSTRAINT FK_69348FEC54C8C93');
        $this->addSql('DROP TABLE device');
        $this->addSql('DROP TABLE parameters');
        $this->addSql('DROP TABLE type');
    }
}
