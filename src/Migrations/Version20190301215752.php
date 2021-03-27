<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\Collection;
use App\Entity\Idea;
use App\Entity\Comment;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190301215752 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function getDescription() : string
    {
        return 'Allow idea without description, introduce anonymous_vote, style for markdown';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE idea CHANGE description description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE admin_settings ADD anonymous_vote TINYINT(1) NOT NULL');
    }

    public function postUp(Schema $schema) : void
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $collections = $em->getRepository(Collection::class)->findAll();
        foreach ($collections as $collection) {
            $collection->setDescription(str_replace("<br />", "", $collection->getDescription()));
	    $collection->getAdminSettings()->setAnonymousVote(true);
        }
        $ideas = $em->getRepository(Idea::class)->findAll();
        foreach ($ideas as $idea) {
            $idea->setDescription(str_replace("<br />", "", $idea->getDescription()));
        }
        $comments = $em->getRepository(Comment::class)->findAll();
        foreach ($comments as $comment) {
            $comment->setComment(str_replace("<br />", "", $comment->getComment()));
        }
        $em->flush();
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE admin_settings DROP anonymous_vote');
        $this->addSql('ALTER TABLE idea CHANGE description description LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
