<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190202125953 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Allow nullable first and family name for automatic user creation';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_collection CHANGE first_name first_name VARCHAR(180) DEFAULT NULL, CHANGE family_name family_name VARCHAR(180) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_collection CHANGE first_name first_name VARCHAR(180) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE family_name family_name VARCHAR(180) NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
