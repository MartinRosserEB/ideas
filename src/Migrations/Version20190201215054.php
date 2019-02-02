<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190201215054 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE idea ADD collection_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE idea ADD CONSTRAINT FK_A8BCA45514956FD FOREIGN KEY (collection_id) REFERENCES collection (id)');
        $this->addSql('CREATE INDEX IDX_A8BCA45514956FD ON idea (collection_id)');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE idea DROP FOREIGN KEY FK_A8BCA45514956FD');
        $this->addSql('DROP INDEX IDX_A8BCA45514956FD ON idea');
        $this->addSql('ALTER TABLE idea DROP collection_id');
    }
}
