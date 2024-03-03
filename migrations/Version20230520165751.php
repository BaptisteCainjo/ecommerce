<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230520165751 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
        $this->addSql('DROP INDEX IDX_59E2507725E254A1');
        $this->addSql('CREATE TEMPORARY TABLE __temp__piste AS SELECT id, musique_id, titre, mp3 FROM piste');
        $this->addSql('DROP TABLE piste');
        $this->addSql('CREATE TABLE piste (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, musique_id VARCHAR(255) DEFAULT NULL COLLATE BINARY, titre VARCHAR(255) NOT NULL COLLATE BINARY, mp3 VARCHAR(255) NOT NULL COLLATE BINARY, CONSTRAINT FK_59E2507725E254A1 FOREIGN KEY (musique_id) REFERENCES article (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO piste (id, musique_id, titre, mp3) SELECT id, musique_id, titre, mp3 FROM __temp__piste');
        $this->addSql('DROP TABLE __temp__piste');
        $this->addSql('CREATE INDEX IDX_59E2507725E254A1 ON piste (musique_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP INDEX IDX_59E2507725E254A1');
        $this->addSql('CREATE TEMPORARY TABLE __temp__piste AS SELECT id, musique_id, titre, mp3 FROM piste');
        $this->addSql('DROP TABLE piste');
        $this->addSql('CREATE TABLE piste (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, musique_id VARCHAR(255) DEFAULT NULL, titre VARCHAR(255) NOT NULL, mp3 VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO piste (id, musique_id, titre, mp3) SELECT id, musique_id, titre, mp3 FROM __temp__piste');
        $this->addSql('DROP TABLE __temp__piste');
        $this->addSql('CREATE INDEX IDX_59E2507725E254A1 ON piste (musique_id)');
    }
}
