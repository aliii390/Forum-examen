<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250406184503 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bloquer (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, autre_user_id INT DEFAULT NULL, bloquer TINYINT(1) NOT NULL, INDEX IDX_C2294FBFA76ED395 (user_id), INDEX IDX_C2294FBF7C4FEE03 (autre_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bloquer ADD CONSTRAINT FK_C2294FBFA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE bloquer ADD CONSTRAINT FK_C2294FBF7C4FEE03 FOREIGN KEY (autre_user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bloquer DROP FOREIGN KEY FK_C2294FBFA76ED395');
        $this->addSql('ALTER TABLE bloquer DROP FOREIGN KEY FK_C2294FBF7C4FEE03');
        $this->addSql('DROP TABLE bloquer');
    }
}
