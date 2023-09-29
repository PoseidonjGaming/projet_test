<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230929204419 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE actor (id INT AUTO_INCREMENT NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `character` (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE character_series (character_id INT NOT NULL, series_id INT NOT NULL, INDEX IDX_9952C2BE1136BE75 (character_id), INDEX IDX_9952C2BE5278319C (series_id), PRIMARY KEY(character_id, series_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE character_actor (character_id INT NOT NULL, actor_id INT NOT NULL, INDEX IDX_BAB6B19B1136BE75 (character_id), INDEX IDX_BAB6B19B10DAF24A (actor_id), PRIMARY KEY(character_id, actor_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE character_movie (character_id INT NOT NULL, movie_id INT NOT NULL, INDEX IDX_E39D150D1136BE75 (character_id), INDEX IDX_E39D150D8F93B6FC (movie_id), PRIMARY KEY(character_id, movie_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE episode (id INT AUTO_INCREMENT NOT NULL, season_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, summary LONGTEXT DEFAULT NULL, release_date DATE DEFAULT NULL, INDEX IDX_DDAA1CDA4EC001D1 (season_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE movie (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, summary LONGTEXT DEFAULT NULL, release_date DATE DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE season (id INT AUTO_INCREMENT NOT NULL, series_id INT DEFAULT NULL, number SMALLINT NOT NULL, INDEX IDX_F0E45BA95278319C (series_id), UNIQUE INDEX UNX_series_number (series_id, number), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE series (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, summary LONGTEXT DEFAULT NULL, poster VARCHAR(100) DEFAULT NULL, release_date DATE DEFAULT NULL, trailer_url VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE series_category (series_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_55A781CE5278319C (series_id), INDEX IDX_55A781CE12469DE2 (category_id), PRIMARY KEY(series_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, avatar_file VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE character_series ADD CONSTRAINT FK_9952C2BE1136BE75 FOREIGN KEY (character_id) REFERENCES `character` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE character_series ADD CONSTRAINT FK_9952C2BE5278319C FOREIGN KEY (series_id) REFERENCES series (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE character_actor ADD CONSTRAINT FK_BAB6B19B1136BE75 FOREIGN KEY (character_id) REFERENCES `character` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE character_actor ADD CONSTRAINT FK_BAB6B19B10DAF24A FOREIGN KEY (actor_id) REFERENCES actor (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE character_movie ADD CONSTRAINT FK_E39D150D1136BE75 FOREIGN KEY (character_id) REFERENCES `character` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE character_movie ADD CONSTRAINT FK_E39D150D8F93B6FC FOREIGN KEY (movie_id) REFERENCES movie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE episode ADD CONSTRAINT FK_DDAA1CDA4EC001D1 FOREIGN KEY (season_id) REFERENCES season (id)');
        $this->addSql('ALTER TABLE season ADD CONSTRAINT FK_F0E45BA95278319C FOREIGN KEY (series_id) REFERENCES series (id)');
        $this->addSql('ALTER TABLE series_category ADD CONSTRAINT FK_55A781CE5278319C FOREIGN KEY (series_id) REFERENCES series (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE series_category ADD CONSTRAINT FK_55A781CE12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE character_series DROP FOREIGN KEY FK_9952C2BE1136BE75');
        $this->addSql('ALTER TABLE character_series DROP FOREIGN KEY FK_9952C2BE5278319C');
        $this->addSql('ALTER TABLE character_actor DROP FOREIGN KEY FK_BAB6B19B1136BE75');
        $this->addSql('ALTER TABLE character_actor DROP FOREIGN KEY FK_BAB6B19B10DAF24A');
        $this->addSql('ALTER TABLE character_movie DROP FOREIGN KEY FK_E39D150D1136BE75');
        $this->addSql('ALTER TABLE character_movie DROP FOREIGN KEY FK_E39D150D8F93B6FC');
        $this->addSql('ALTER TABLE episode DROP FOREIGN KEY FK_DDAA1CDA4EC001D1');
        $this->addSql('ALTER TABLE season DROP FOREIGN KEY FK_F0E45BA95278319C');
        $this->addSql('ALTER TABLE series_category DROP FOREIGN KEY FK_55A781CE5278319C');
        $this->addSql('ALTER TABLE series_category DROP FOREIGN KEY FK_55A781CE12469DE2');
        $this->addSql('DROP TABLE actor');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE `character`');
        $this->addSql('DROP TABLE character_series');
        $this->addSql('DROP TABLE character_actor');
        $this->addSql('DROP TABLE character_movie');
        $this->addSql('DROP TABLE episode');
        $this->addSql('DROP TABLE movie');
        $this->addSql('DROP TABLE season');
        $this->addSql('DROP TABLE series');
        $this->addSql('DROP TABLE series_category');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
