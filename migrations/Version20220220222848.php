<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220220222848 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, issue_id INT NOT NULL, content VARCHAR(255) NOT NULL, INDEX IDX_9474526CF675F31B (author_id), INDEX IDX_9474526C5E7AA58C (issue_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE issue (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, priority VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, INDEX IDX_12AD233EF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C5E7AA58C FOREIGN KEY (issue_id) REFERENCES issue (id)');
        $this->addSql('ALTER TABLE issue ADD CONSTRAINT FK_12AD233EF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');

        $this->addSql('
            INSERT INTO user (first_name, last_name)
            VALUES
                (\'Julien\', \'Martin\'),
                (\'Renaud\', \'Littolff\'),
                (\'Thierry\', \'Vaudelin\')
        ');

        $this->addSql('
            INSERT INTO issue (author_id, priority, status, title, description)
            VALUES (
                3,
                \'critical\',
                \'fixed\',
                \'Mon PC ne marche plus\',
                \'Je ne comprends pas, mon ordinateur portable ne fonctionne plus : quoi que je fasse mon écran reste totalement noir...\'
            )
        ');

        $this->addSql('
            INSERT INTO comment (author_id, content, issue_id)
            VALUES
                (2, \'As-tu essayer de le brancher ?\', 1),
                (3, \'My bad...\', 1)
        ');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C5E7AA58C');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CF675F31B');
        $this->addSql('ALTER TABLE issue DROP FOREIGN KEY FK_12AD233EF675F31B');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE issue');
        $this->addSql('DROP TABLE user');
    }
}
