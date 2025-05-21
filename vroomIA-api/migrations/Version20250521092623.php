<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250521092623 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE operation (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, duration INT NOT NULL, price DOUBLE PRECISION NOT NULL, category VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE reservation_operation (reservation_id INT NOT NULL, operation_id INT NOT NULL, INDEX IDX_3C6C7F2B83297E7 (reservation_id), INDEX IDX_3C6C7F244AC3583 (operation_id), PRIMARY KEY(reservation_id, operation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation_operation ADD CONSTRAINT FK_3C6C7F2B83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation_operation ADD CONSTRAINT FK_3C6C7F244AC3583 FOREIGN KEY (operation_id) REFERENCES operation (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation DROP operation
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation_operation DROP FOREIGN KEY FK_3C6C7F2B83297E7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation_operation DROP FOREIGN KEY FK_3C6C7F244AC3583
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE operation
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE reservation_operation
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation ADD operation LONGTEXT NOT NULL COMMENT '(DC2Type:array)'
        SQL);
    }
}
