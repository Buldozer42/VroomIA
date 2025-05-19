<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250519141514 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE adress (id INT AUTO_INCREMENT NOT NULL, street VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, postal_code INT NOT NULL, country VARCHAR(255) NOT NULL, region VARCHAR(255) NOT NULL, additional_info VARCHAR(255) NOT NULL, latitude VARCHAR(255) NOT NULL, longitude VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE driver (id INT AUTO_INCREMENT NOT NULL, person_id INT NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, phone_number VARCHAR(255) NOT NULL, INDEX IDX_11667CD9217BBB47 (person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE garage (id INT AUTO_INCREMENT NOT NULL, adress_id INT NOT NULL, name VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, phone_number VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_9F26610B8486F9AC (adress_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE person_garage (person_id INT NOT NULL, garage_id INT NOT NULL, INDEX IDX_38351BD217BBB47 (person_id), INDEX IDX_38351BDC4FFF555 (garage_id), PRIMARY KEY(person_id, garage_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE driver ADD CONSTRAINT FK_11667CD9217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE garage ADD CONSTRAINT FK_9F26610B8486F9AC FOREIGN KEY (adress_id) REFERENCES adress (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE person_garage ADD CONSTRAINT FK_38351BD217BBB47 FOREIGN KEY (person_id) REFERENCES person (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE person_garage ADD CONSTRAINT FK_38351BDC4FFF555 FOREIGN KEY (garage_id) REFERENCES garage (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE person ADD adress_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE person ADD CONSTRAINT FK_34DCD1768486F9AC FOREIGN KEY (adress_id) REFERENCES adress (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_34DCD1768486F9AC ON person (adress_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE person DROP FOREIGN KEY FK_34DCD1768486F9AC
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE driver DROP FOREIGN KEY FK_11667CD9217BBB47
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE garage DROP FOREIGN KEY FK_9F26610B8486F9AC
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE person_garage DROP FOREIGN KEY FK_38351BD217BBB47
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE person_garage DROP FOREIGN KEY FK_38351BDC4FFF555
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE adress
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE driver
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE garage
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE person_garage
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_34DCD1768486F9AC ON person
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE person DROP adress_id
        SQL);
    }
}
