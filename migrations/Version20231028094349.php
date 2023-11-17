<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231028094349 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        //user
        $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');

        //device,type, params
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

        //system
        $this->addSql('CREATE SEQUENCE systems_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE systems (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE device ADD systems_id INT NOT NULL');
        $this->addSql('ALTER TABLE device ADD CONSTRAINT FK_92FB68E411D7F6D FOREIGN KEY (systems_id) REFERENCES systems (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_92FB68E411D7F6D ON device (systems_id)');
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
        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
        //user
        $this->addSql('DROP TABLE "user"');
        $this->addSql('ALTER TABLE device DROP CONSTRAINT FK_92FB68E411D7F6D');
        //device, type, params
        $this->addSql('DROP SEQUENCE device_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE parameters_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE type_id_seq CASCADE');
        $this->addSql('ALTER TABLE device DROP CONSTRAINT FK_92FB68EC54C8C93');
        $this->addSql('ALTER TABLE parameters DROP CONSTRAINT FK_69348FEC54C8C93');
        $this->addSql('DROP TABLE device');
        $this->addSql('DROP TABLE parameters');
        $this->addSql('DROP TABLE type');
        //system
        $this->addSql('DROP SEQUENCE systems_id_seq CASCADE');
        $this->addSql('DROP TABLE systems');
        $this->addSql('DROP INDEX IDX_92FB68E411D7F6D');
        $this->addSql('ALTER TABLE device DROP systems_id');
        $this->addSql('ALTER TABLE user_systems DROP CONSTRAINT FK_39A0C6B8A76ED395');
        $this->addSql('ALTER TABLE user_systems DROP CONSTRAINT FK_39A0C6B8411D7F6D');
        $this->addSql('DROP TABLE user_systems');
    }
}
