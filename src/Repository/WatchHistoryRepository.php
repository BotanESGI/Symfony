<?php

namespace App\Repository;

use App\Entity\WatchHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WatchHistory>
 */
class WatchHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WatchHistory::class);
    }


    public function countViewsByMediaId($mediaId)
    {
        return $this->createQueryBuilder('w')
            ->select('SUM(w.numberOfViews)')
            ->where('w.media = :mediaId')
            ->setParameter('mediaId', $mediaId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    //    /**
    //     * @return WatchHistory[] Returns an array of WatchHistory objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('w')
    //            ->andWhere('w.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('w.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?WatchHistory
    //    {
    //        return $this->createQueryBuilder('w')
    //            ->andWhere('w.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
