<?php

namespace App\Repository;

use App\Entity\Episode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Episode>
 *
 * @method Episode|null find($id, $lockMode = null, $lockVersion = null)
 * @method Episode|null findOneBy(array $criteria, array $orderBy = null)
 * @method Episode[]    findAll()
 * @method Episode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EpisodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Episode::class);
    }

    //    /**
    //     * @return Episode[] Returns an array of Episode objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Episode
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findEpisodeById($value): ?Episode
    {
        return $this->createQueryBuilder('e')
            ->Where('e.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }
    public function findEpisodesBySeasonIdAndBySerieId($value)
    {
        dump($value);
        return $this->createQueryBuilder('e')
            ->Join('e.season', 's')
            ->Where('s.series = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult();
    }
    public function findEpisodesBySeasonIdAndBySerieIdBySerieId($value)
    {
        return $this->createQueryBuilder('e')
            ->Join('e.season', 's')
            ->Where('s.series = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult();
    }
}
