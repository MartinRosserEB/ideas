<?php

namespace App\Repository;

use App\Entity\Idea;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class IdeaRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Idea::class);
    }

    public function findLatestDistinctIdeas()
    {
        $ideasOrdered = $this->createQueryBuilder('i')
            ->addOrderBy('i.ideaId', 'ASC')
            ->orderBy('i.datetime', 'DESC')
            ->getQuery()
            ->execute();

        $distinctIdeas = [];
        $distinctIdeasIdeaIds = [];
        foreach ($ideasOrdered as $idea) {
            if (!in_array($idea->getIdeaId(), $distinctIdeasIdeaIds)) {
                $distinctIdeas[] = $idea;
                $distinctIdeasIdeaIds[] = $idea->getIdeaId();
            }
        }

        return $distinctIdeas;
    }

    public function findNextAvailableIdeaId()
    {
        $dbIdeaId = $this->createQueryBuilder('i')
            ->select('i.ideaId')
            ->orderBy('i.ideaId', 'DESC')
            ->getQuery()
            ->setMaxResults(1)
            ->execute();
        if ($dbIdeaId) {
            return $dbIdeaId[0]['ideaId'] + 1;
        } else {
            return 1;
        }
    }

    public function findAllForIdeaId($ideaId)
    {
        return $this->createQueryBuilder('i')
            ->where('i.ideaId = :ideaId')
            ->orderBy('i.datetime', 'ASC')
            ->setParameter('ideaId', $ideaId)
            ->getQuery()
            ->execute();
    }
}
