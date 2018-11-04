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
        return $this->createQueryBuilder('i')
            ->addSelect('COUNT(iv.id) AS HIDDEN voteCount')
            ->leftJoin('i.votes', 'iv')
            ->groupBy('i.ideaId')
            ->addOrderBy('voteCount', 'DESC')
            ->addOrderBy('i.ideaId', 'ASC')
            ->addOrderBy('i.datetime', 'DESC')
            ->getQuery()
            ->execute();
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
            ->orderBy('i.datetime', 'DESC')
            ->setParameter('ideaId', $ideaId)
            ->getQuery()
            ->execute();
    }
}
