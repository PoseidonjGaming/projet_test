<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230901184502 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE episode DROP FOREIGN KEY FK_DDAA1CDAF965414C');
        $this->addSql('CREATE TABLE actor (id INT AUTO_INCREMENT NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE actor_character (actor_id INT NOT NULL, character_id INT NOT NULL, INDEX IDX_6703401810DAF24A (actor_id), INDEX IDX_670340181136BE75 (character_id), PRIMARY KEY(actor_id, character_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `character` (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE character_series (character_id INT NOT NULL, series_id INT NOT NULL, INDEX IDX_9952C2BE1136BE75 (character_id), INDEX IDX_9952C2BE5278319C (series_id), PRIMARY KEY(character_id, series_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE season (id INT AUTO_INCREMENT NOT NULL, series_id INT DEFAULT NULL, numero SMALLINT NOT NULL, INDEX IDX_F0E45BA95278319C (series_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE series (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, resume LONGTEXT DEFAULT NULL, poster VARCHAR(100) DEFAULT NULL, url_ba VARCHAR(255) DEFAULT NULL, date_diff DATE DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE actor_character ADD CONSTRAINT FK_6703401810DAF24A FOREIGN KEY (actor_id) REFERENCES actor (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE actor_character ADD CONSTRAINT FK_670340181136BE75 FOREIGN KEY (character_id) REFERENCES `character` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE character_series ADD CONSTRAINT FK_9952C2BE1136BE75 FOREIGN KEY (character_id) REFERENCES `character` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE character_series ADD CONSTRAINT FK_9952C2BE5278319C FOREIGN KEY (series_id) REFERENCES series (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE season ADD CONSTRAINT FK_F0E45BA95278319C FOREIGN KEY (series_id) REFERENCES series (id)');
        $this->addSql('ALTER TABLE acteur_personnage DROP FOREIGN KEY FK_A4F7D30B5E315342');
        $this->addSql('ALTER TABLE acteur_personnage DROP FOREIGN KEY FK_A4F7D30BDA6F574A');
        $this->addSql('ALTER TABLE personnage_serie DROP FOREIGN KEY FK_699F0E5E315342');
        $this->addSql('ALTER TABLE personnage_serie DROP FOREIGN KEY FK_699F0ED94388BD');
        $this->addSql('ALTER TABLE saison DROP FOREIGN KEY FK_C0D0D586D94388BD');
        $this->addSql('DROP TABLE acteur');
        $this->addSql('DROP TABLE acteur_personnage');
        $this->addSql('DROP TABLE personnage');
        $this->addSql('DROP TABLE personnage_serie');
        $this->addSql('DROP TABLE saison');
        $this->addSql('DROP TABLE serie');
        $this->addSql('DROP INDEX IDX_DDAA1CDAF965414C ON episode');
        $this->addSql('ALTER TABLE episode ADD season_id INT DEFAULT NULL, DROP saison_id, DROP date_prem_diff, CHANGE nom name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE episode ADD CONSTRAINT FK_DDAA1CDA4EC001D1 FOREIGN KEY (season_id) REFERENCES season (id)');
        $this->addSql('CREATE INDEX IDX_DDAA1CDA4EC001D1 ON episode (season_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE episode DROP FOREIGN KEY FK_DDAA1CDA4EC001D1');
        $this->addSql('CREATE TABLE acteur (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, prenom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE acteur_personnage (acteur_id INT NOT NULL, personnage_id INT NOT NULL, INDEX IDX_A4F7D30BDA6F574A (acteur_id), INDEX IDX_A4F7D30B5E315342 (personnage_id), PRIMARY KEY(acteur_id, personnage_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE personnage (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE personnage_serie (personnage_id INT NOT NULL, serie_id INT NOT NULL, INDEX IDX_699F0E5E315342 (personnage_id), INDEX IDX_699F0ED94388BD (serie_id), PRIMARY KEY(personnage_id, serie_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE saison (id INT AUTO_INCREMENT NOT NULL, serie_id INT NOT NULL, numero SMALLINT NOT NULL, INDEX IDX_C0D0D586D94388BD (serie_id), UNIQUE INDEX UNX_series_numero (serie_id, numero), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE serie (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, date_diff DATE DEFAULT NULL, resume LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, affiche VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, url_ba VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE acteur_personnage ADD CONSTRAINT FK_A4F7D30B5E315342 FOREIGN KEY (personnage_id) REFERENCES personnage (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acteur_personnage ADD CONSTRAINT FK_A4F7D30BDA6F574A FOREIGN KEY (acteur_id) REFERENCES acteur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE personnage_serie ADD CONSTRAINT FK_699F0E5E315342 FOREIGN KEY (personnage_id) REFERENCES personnage (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE personnage_serie ADD CONSTRAINT FK_699F0ED94388BD FOREIGN KEY (serie_id) REFERENCES serie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE saison ADD CONSTRAINT FK_C0D0D586D94388BD FOREIGN KEY (serie_id) REFERENCES serie (id)');
        $this->addSql('ALTER TABLE actor_character DROP FOREIGN KEY FK_6703401810DAF24A');
        $this->addSql('ALTER TABLE actor_character DROP FOREIGN KEY FK_670340181136BE75');
        $this->addSql('ALTER TABLE character_series DROP FOREIGN KEY FK_9952C2BE1136BE75');
        $this->addSql('ALTER TABLE character_series DROP FOREIGN KEY FK_9952C2BE5278319C');
        $this->addSql('ALTER TABLE season DROP FOREIGN KEY FK_F0E45BA95278319C');
        $this->addSql('DROP TABLE actor');
        $this->addSql('DROP TABLE actor_character');
        $this->addSql('DROP TABLE `character`');
        $this->addSql('DROP TABLE character_series');
        $this->addSql('DROP TABLE season');
        $this->addSql('DROP TABLE series');
        $this->addSql('DROP INDEX IDX_DDAA1CDA4EC001D1 ON episode');
        $this->addSql('ALTER TABLE episode ADD saison_id INT NOT NULL, ADD date_prem_diff DATE DEFAULT NULL, DROP season_id, CHANGE name nom VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE episode ADD CONSTRAINT FK_DDAA1CDAF965414C FOREIGN KEY (saison_id) REFERENCES saison (id)');
        $this->addSql('CREATE INDEX IDX_DDAA1CDAF965414C ON episode (saison_id)');
    }
}
