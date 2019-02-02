<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190202010215 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, api_token VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D6497BA2F5EB (api_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_collection (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, collection_id INT DEFAULT NULL, first_name VARCHAR(180) NOT NULL, family_name VARCHAR(180) NOT NULL, role VARCHAR(20) NOT NULL, INDEX IDX_5B2AA3DEA76ED395 (user_id), INDEX IDX_5B2AA3DE514956FD (collection_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE collection (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(180) NOT NULL, UNIQUE INDEX UNIQ_FC4D65325E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE idea (id INT AUTO_INCREMENT NOT NULL, creator_id INT DEFAULT NULL, collection_id INT DEFAULT NULL, idea_id INT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, datetime DATETIME DEFAULT NULL, INDEX IDX_A8BCA4561220EA6 (creator_id), INDEX IDX_A8BCA45514956FD (collection_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE admin_settings (id INT AUTO_INCREMENT NOT NULL, collection_id INT DEFAULT NULL, mail_text LONGTEXT DEFAULT NULL, mail_subject LONGTEXT DEFAULT NULL, voting_active TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_71E5A7C3514956FD (collection_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vote (id INT AUTO_INCREMENT NOT NULL, voter_id INT DEFAULT NULL, idea_id INT DEFAULT NULL, value INT NOT NULL, datetime DATETIME DEFAULT NULL, INDEX IDX_5A108564EBB4B8AD (voter_id), INDEX IDX_5A1085645B6FEF7D (idea_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, creator_id INT DEFAULT NULL, idea_id INT DEFAULT NULL, comment LONGTEXT NOT NULL, datetime DATETIME DEFAULT NULL, INDEX IDX_9474526C61220EA6 (creator_id), INDEX IDX_9474526C5B6FEF7D (idea_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_collection ADD CONSTRAINT FK_5B2AA3DEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_collection ADD CONSTRAINT FK_5B2AA3DE514956FD FOREIGN KEY (collection_id) REFERENCES collection (id)');
        $this->addSql('ALTER TABLE idea ADD CONSTRAINT FK_A8BCA4561220EA6 FOREIGN KEY (creator_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE idea ADD CONSTRAINT FK_A8BCA45514956FD FOREIGN KEY (collection_id) REFERENCES collection (id)');
        $this->addSql('ALTER TABLE admin_settings ADD CONSTRAINT FK_71E5A7C3514956FD FOREIGN KEY (collection_id) REFERENCES collection (id)');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A108564EBB4B8AD FOREIGN KEY (voter_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A1085645B6FEF7D FOREIGN KEY (idea_id) REFERENCES idea (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C61220EA6 FOREIGN KEY (creator_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C5B6FEF7D FOREIGN KEY (idea_id) REFERENCES idea (id)');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_collection DROP FOREIGN KEY FK_5B2AA3DEA76ED395');
        $this->addSql('ALTER TABLE idea DROP FOREIGN KEY FK_A8BCA4561220EA6');
        $this->addSql('ALTER TABLE vote DROP FOREIGN KEY FK_5A108564EBB4B8AD');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C61220EA6');
        $this->addSql('ALTER TABLE user_collection DROP FOREIGN KEY FK_5B2AA3DE514956FD');
        $this->addSql('ALTER TABLE idea DROP FOREIGN KEY FK_A8BCA45514956FD');
        $this->addSql('ALTER TABLE admin_settings DROP FOREIGN KEY FK_71E5A7C3514956FD');
        $this->addSql('ALTER TABLE vote DROP FOREIGN KEY FK_5A1085645B6FEF7D');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C5B6FEF7D');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_collection');
        $this->addSql('DROP TABLE collection');
        $this->addSql('DROP TABLE idea');
        $this->addSql('DROP TABLE admin_settings');
        $this->addSql('DROP TABLE vote');
        $this->addSql('DROP TABLE comment');
    }
}
