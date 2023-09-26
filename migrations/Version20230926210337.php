<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230926210337 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE series_category (series_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_55A781CE5278319C (series_id), INDEX IDX_55A781CE12469DE2 (category_id), PRIMARY KEY(series_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE series_category ADD CONSTRAINT FK_55A781CE5278319C FOREIGN KEY (series_id) REFERENCES series (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE series_category ADD CONSTRAINT FK_55A781CE12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category_series DROP FOREIGN KEY FK_35F8CB5612469DE2');
        $this->addSql('ALTER TABLE category_series DROP FOREIGN KEY FK_35F8CB565278319C');
        $this->addSql('DROP TABLE category_series');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category_series (category_id INT NOT NULL, series_id INT NOT NULL, INDEX IDX_35F8CB565278319C (series_id), INDEX IDX_35F8CB5612469DE2 (category_id), PRIMARY KEY(category_id, series_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE category_series ADD CONSTRAINT FK_35F8CB5612469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category_series ADD CONSTRAINT FK_35F8CB565278319C FOREIGN KEY (series_id) REFERENCES series (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE series_category DROP FOREIGN KEY FK_55A781CE5278319C');
        $this->addSql('ALTER TABLE series_category DROP FOREIGN KEY FK_55A781CE12469DE2');
        $this->addSql('DROP TABLE series_category');
    }
}
