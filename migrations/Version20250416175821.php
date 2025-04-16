<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250416175821 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ajout_ami (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, user_ajoutez_id INT DEFAULT NULL, INDEX IDX_F83FA9BEA76ED395 (user_id), INDEX IDX_F83FA9BE58FD6C45 (user_ajoutez_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ajout_ami ADD CONSTRAINT FK_F83FA9BEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ajout_ami ADD CONSTRAINT FK_F83FA9BE58FD6C45 FOREIGN KEY (user_ajoutez_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ajout_ami DROP FOREIGN KEY FK_F83FA9BEA76ED395');
        $this->addSql('ALTER TABLE ajout_ami DROP FOREIGN KEY FK_F83FA9BE58FD6C45');
        $this->addSql('DROP TABLE ajout_ami');
    }
}
