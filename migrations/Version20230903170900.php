<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230903170900 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE character_actor (character_id INT NOT NULL, actor_id INT NOT NULL, INDEX IDX_BAB6B19B1136BE75 (character_id), INDEX IDX_BAB6B19B10DAF24A (actor_id), PRIMARY KEY(character_id, actor_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE character_actor ADD CONSTRAINT FK_BAB6B19B1136BE75 FOREIGN KEY (character_id) REFERENCES `character` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE character_actor ADD CONSTRAINT FK_BAB6B19B10DAF24A FOREIGN KEY (actor_id) REFERENCES actor (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE actor_character DROP FOREIGN KEY FK_6703401810DAF24A');
        $this->addSql('ALTER TABLE actor_character DROP FOREIGN KEY FK_670340181136BE75');
        $this->addSql('DROP TABLE actor_character');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE actor_character (actor_id INT NOT NULL, character_id INT NOT NULL, INDEX IDX_670340181136BE75 (character_id), INDEX IDX_6703401810DAF24A (actor_id), PRIMARY KEY(actor_id, character_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE actor_character ADD CONSTRAINT FK_6703401810DAF24A FOREIGN KEY (actor_id) REFERENCES actor (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE actor_character ADD CONSTRAINT FK_670340181136BE75 FOREIGN KEY (character_id) REFERENCES `character` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE character_actor DROP FOREIGN KEY FK_BAB6B19B1136BE75');
        $this->addSql('ALTER TABLE character_actor DROP FOREIGN KEY FK_BAB6B19B10DAF24A');
        $this->addSql('DROP TABLE character_actor');
    }
}
