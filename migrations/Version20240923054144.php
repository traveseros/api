<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240923054144 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE control_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE equipo_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE track_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tramo_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE control (id INT NOT NULL, control_id INT NOT NULL, comment VARCHAR(255) NOT NULL, groupping VARCHAR(255) NOT NULL, lat DOUBLE PRECISION NOT NULL, lon DOUBLE PRECISION NOT NULL, distance DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE equipo (id INT NOT NULL, equipo_id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, travesia_id INT NOT NULL, media DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE track (id INT NOT NULL, type VARCHAR(255) NOT NULL, lat DOUBLE PRECISION NOT NULL, lon DOUBLE PRECISION NOT NULL, distance DOUBLE PRECISION NOT NULL, accumulated DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE tramo (id INT NOT NULL, control_inicio_id INT DEFAULT NULL, control_fin_id INT DEFAULT NULL, equipo_id INT DEFAULT NULL, tramo VARCHAR(255) NOT NULL, entrada TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, salida TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, media DOUBLE PRECISION DEFAULT NULL, status VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4F0D11315B828A31 ON tramo (control_inicio_id)');
        $this->addSql('CREATE INDEX IDX_4F0D1131ACD5102A ON tramo (control_fin_id)');
        $this->addSql('CREATE INDEX IDX_4F0D113123BFBED ON tramo (equipo_id)');
        $this->addSql('COMMENT ON COLUMN tramo.entrada IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN tramo.salida IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE tramo ADD CONSTRAINT FK_4F0D11315B828A31 FOREIGN KEY (control_inicio_id) REFERENCES control (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tramo ADD CONSTRAINT FK_4F0D1131ACD5102A FOREIGN KEY (control_fin_id) REFERENCES control (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tramo ADD CONSTRAINT FK_4F0D113123BFBED FOREIGN KEY (equipo_id) REFERENCES equipo (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE TABLE track_raw (id INT NOT NULL, type VARCHAR(255) NOT NULL, xml TEXT NOT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE control_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE equipo_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE track_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tramo_id_seq CASCADE');
        $this->addSql('ALTER TABLE tramo DROP CONSTRAINT FK_4F0D11315B828A31');
        $this->addSql('ALTER TABLE tramo DROP CONSTRAINT FK_4F0D1131ACD5102A');
        $this->addSql('ALTER TABLE tramo DROP CONSTRAINT FK_4F0D113123BFBED');
        $this->addSql('DROP TABLE control');
        $this->addSql('DROP TABLE equipo');
        $this->addSql('DROP TABLE track');
        $this->addSql('DROP TABLE tramo');
    }
}
