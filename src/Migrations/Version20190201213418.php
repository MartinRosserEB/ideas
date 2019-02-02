<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190201213418 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE collection (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(180) NOT NULL, UNIQUE INDEX UNIQ_FC4D65325E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_collection (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, collection_id INT DEFAULT NULL, roles JSON NOT NULL, INDEX IDX_5B2AA3DEA76ED395 (user_id), INDEX IDX_5B2AA3DE514956FD (collection_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_collection ADD CONSTRAINT FK_5B2AA3DEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_collection ADD CONSTRAINT FK_5B2AA3DE514956FD FOREIGN KEY (collection_id) REFERENCES collection (id)');
        $this->addSql('ALTER TABLE user DROP roles');
        $this->addSql('ALTER TABLE admin_settings ADD collection_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE admin_settings ADD CONSTRAINT FK_71E5A7C3514956FD FOREIGN KEY (collection_id) REFERENCES collection (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_71E5A7C3514956FD ON admin_settings (collection_id)');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_collection DROP FOREIGN KEY FK_5B2AA3DE514956FD');
        $this->addSql('ALTER TABLE admin_settings DROP FOREIGN KEY FK_71E5A7C3514956FD');
        $this->addSql('DROP TABLE collection');
        $this->addSql('DROP TABLE user_collection');
        $this->addSql('DROP INDEX UNIQ_71E5A7C3514956FD ON admin_settings');
        $this->addSql('ALTER TABLE admin_settings DROP collection_id');
        $this->addSql('ALTER TABLE user ADD roles JSON NOT NULL');
    }
}
